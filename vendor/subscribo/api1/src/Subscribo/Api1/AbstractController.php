<?php namespace Subscribo\Api1;

use Subscribo\Api1\Interfaces\SelfRegisteringControllerInterface;
use Subscribo\Api1\Interfaces\ControllerRegistrarInterface;
use Subscribo\Support\Str;
use Illuminate\Routing\Controller;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\Api1\Context;
use Validator;
use App;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;



/**
 * Class AbstractController
 * Base class for API v1 controllers
 *
 *
 * @package Subscribo\Api1
 */
class AbstractController extends Controller implements SelfRegisteringControllerInterface
{

    protected static $publish = true;

    protected static $descriptions = [];

    protected static $autoPublishVerbs = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];

    protected static $controllerUriStub;

    /**
     * @var Context
     */
    protected $context;


    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    protected function assembleValidator(array $data, array $rules, array $messages = array(), array $customAttributes = array())
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        return $validator;
    }

    protected function validateRequestBody(array $validationRules)
    {
        $data = array_intersect_key($this->context->getRequest()->json()->all(), $validationRules);
        $validator = $this->assembleValidator($data, $validationRules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->messages()->all());
        }
        return $validator->valid();
    }

    protected function validateRequestQuery(array $validationRules)
    {
        $data = array_intersect_key($this->context->getRequest()->query(), $validationRules);
        $validator = $this->assembleValidator($data, $validationRules);
        if ($validator->fails()) {
            throw new InvalidQueryHttpException($validator->messages()->all());
        }
        return $validator->valid();
    }

    /**
     * @param string $what
     * @return mixed
     */
    protected function applicationMake($what)
    {
        return App::make($what);
    }



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
     * @throws Exceptions\InvalidArgumentException
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
        $result = ['method' => $action, 'verb' => $verb, 'uri' => $uri];
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
