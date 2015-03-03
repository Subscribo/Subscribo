<?php namespace Subscribo\Api1\Traits;

use ReflectionMethod;
use Subscribo\Support\Str;
use Subscribo\Api1\Interfaces\ControllerRegistrarInterface;
use Subscribo\Api1\Exceptions\InvalidArgumentException;

/**
 * Trait SelfRegisteringControllerTrait
 * Helps to implement Subscribo\Api1\Interfaces\SelfRegisteringControllerInterface
 *
 * @package Subscribo\Api1
 */
trait SelfRegisteringControllerTrait
{
    protected static $publish = true;

    protected static $descriptions = [];

    protected static $autoPublishVerbs = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];

    protected static $controllerUriStub;

    /**
     * Registering controller methods ant their descriptions to Registrar (and via that to router)
     *
     * @param ControllerRegistrarInterface $router
     * @param array $options
     */
    public static function registerSelf(ControllerRegistrarInterface $router, array $options = array())
    {
        $controllerUriStub = static::$controllerUriStub ?: static::assembleControllerUriStub();

        $actions = [];
        if (is_array(static::$publish)) {
            $actions = static::$publish;
        }

        if (true === static::$publish) {
            $actions = static::collectActions();
        }
        $generatedDescriptions = [];

        foreach ($actions as $action) {
            $parsed = static::parseAction($action);
            $uri = $controllerUriStub.'/'.$parsed['uri'];
            if ( ! empty($parsed['params'])) {
                $uri .= static::paramsToUri($parsed['params']);
            }
            $routerAction = $options;
            $routerAction['uses'] = get_called_class().'@'.$parsed['method'];
            $router->registerRoute($parsed['verb'], $uri, $routerAction);
            if ( ! empty(static::$descriptions[$uri])) {
                $router->registerDescription($uri, static::$descriptions[$uri]);
            } else {
                $generatedDescriptions = static::addGeneratedDescription($generatedDescriptions, $parsed, $uri);
            }
        }
        foreach ($generatedDescriptions as $uri => $description) {
            $router->registerDescription($uri, $description);
        }
    }

    protected static function paramsToUri(array $params)
    {
        $result = '';
        foreach ($params as $name => $paramData) {
            $result .= '/{'.$name;
            if ( ! empty($paramData['optional'])) {
                $result .= '?';
            }
            $result .= '}';
        }
        return $result;
    }

    /**
     * @param array $descriptions
     * @param array $action
     * @param string $uri
     * @return array
     */
    protected static function addGeneratedDescription(array $descriptions, array $action, $uri)
    {
        $controllerSimpleName = static::assembleControllerSimpleName();
        $name = ucfirst(Str::snake($controllerSimpleName, ' ')).' : '.ucfirst(Str::snake($action['method'], ' '));
        $base = [
            'name' => $name,
        ];
        if (empty($descriptions[$uri])) {
            $descriptions[$uri] = $base;
        }
        $descriptions[$uri]['verbs'][$action['verb']] = $base;
        return $descriptions;
    }

    /**
     * @param string|array $action
     * @return array
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    protected static function parseAction($action)
    {
        if ( ! is_string($action)) {
            return $action;
        }
        $parts = explode('_', Str::snake($action));
        if (count($parts) < 2) {
            throw new InvalidArgumentException('AbstractController::parseAction() provided method name is not in requested format verbAction()');
        }
        $verb = strtoupper(array_shift($parts));
        if ('action' === strtolower($verb)) {
            $verb = strtoupper(array_shift($parts));
        }
        if ('index' === end($parts)) {
            array_pop($parts);
        }
        $uri = implode('/', $parts);
        $params = static::analyseParams($action);
        $result = ['method' => $action, 'verb' => $verb, 'uri' => $uri, 'params' => $params];
        return $result;
    }

    protected static function analyseParams($action)
    {
        $reflection = new ReflectionMethod(get_called_class(), $action);
        if (empty($reflection)) {
            return [];
        }
        $result = [];
        foreach ($reflection->getParameters() as $parameter) {
            if ($parameter->getClass()) {
                continue;
            }
            if ($parameter->isArray()) {
                continue;
            }
            if ($parameter->isCallable()) {
                continue;
            }
            if ($parameter->isOptional()) {
                $result[$parameter->getName()] = ['optional' => true];
            } else {
                $result[$parameter->getName()] = ['optional' => false];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    protected static function collectActions()
    {
        $methods = get_class_methods(get_called_class());
        $result = [];
        foreach ($methods as $method) {
            if (0 === strpos($method, 'action')) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    private static function assembleControllerUriStub()
    {
        $controllerSimpleName = static::assembleControllerSimpleName();
        $result = Str::snake($controllerSimpleName, '/');
        return $result;
    }

    /**
     * @return string
     */
    private static function assembleControllerSimpleName()
    {
        $classFullName = get_called_class();
        $classNameParts = explode('\\', $classFullName);
        $className = end($classNameParts);
        $controllerSimpleName = strstr($className, 'Controller', true);
        return $controllerSimpleName;
    }
}
