<?php namespace Subscribo\Api1;

use Subscribo\Api1\Interfaces\ControllerRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar;
use Subscribo\Support\Arr;
use Illuminate\Http\Response;


class ControllerRegistrar implements ControllerRegistrarInterface
{
    protected $registrar;

    protected $prefix;

    protected $descriptions = [];

    protected $options = [];

    public function __construct(Registrar $registrar, $prefix = null, array $options = array())
    {
        $this->registrar = $registrar;
        $this->prefix = $prefix;
        $this->options = $options;
    }

    public function registerRoute($verb, $uri, $action)
    {
        $uri = $this->addPrefix($uri);
        $this->registrar->match($verb, $uri, $action);
    }

    public function registerDescription($uri, $description)
    {
        $fullUri = $this->addPrefix($uri);
        if ($this->isUriSimple($fullUri)) {
            $description['simpleUri'] = $fullUri;
        } else {
            $description['parametrizedUri'] = $fullUri;
        }
        if ($this->isUriSimple($uri)) {
            $description['partialSimpleUri'] = $uri;
        } else {
            $description['partialParametrizedUri'] = $uri;
        }
        $description['uri'] = $fullUri;
        $description['partialUri'] = $uri;
        $description['prefix'] = $this->prefix;
        $description['sameServer'] = true;

        $toMerge = [$fullUri => $description];
        $this->descriptions = Arr::mergeNatural($this->descriptions, $toMerge);
    }

    public function registerControllers($controllers)
    {
        $controllers = is_array($controllers) ? $controllers : [$controllers];
        foreach ($controllers as $controller) {
            call_user_func([$controller, 'registerSelf'], $this, $this->options);
        }
    }


    public function addInfoRoute($defaults = array(), $uri = '/info', $rootRedirect = true)
    {
        $infoDescriptionBase = ['name' => 'Information about this API'];
        $infoDescription = $infoDescriptionBase;
        $infoDescription['verbs']['GET'] = $infoDescriptionBase;
        $this->RegisterDescription($uri, $infoDescription);
        $infoUri = $this->addPrefix($uri);
        if ($rootRedirect) {
            $rootDescriptionBase = ['name' => 'Base API URI. Redirecting to '.$infoUri];
            $rootDescription = $rootDescriptionBase;
            $rootDescription['verbs']['GET'] = $rootDescriptionBase;
            $this->RegisterDescription('/', $rootDescription);
        }
        $toMerge = ['endpoints' => array_values($this->descriptions)];
        $info = Arr::mergeNatural($defaults, $toMerge);
        $infoAction = $this->options;
        $infoAction['uses'] = function () use ($info) {
            return $info;
        };
        $this->registrar->get($infoUri, $infoAction);
        if ($rootRedirect) {
            $rootUri = $this->prefix ?: '/';
            $rootAction = $this->options;
            $rootAction['uses'] = function () use ($infoUri) {
                $content = ["redirect" => ["location" => $infoUri]];
                return new Response($content, 303, ['Location' => $infoUri]);
            };
            $this->registrar->get($rootUri, $rootAction);
        }
    }


    public function isUriSimple($uri)
    {
        $specialCharactersContained = strpbrk($uri, '{}');
        return (false === $specialCharactersContained);
    }

    protected function addPrefix($uri)
    {
        if ($this->prefix)
        {
            $uri = rtrim($this->prefix, '/').'/'.ltrim($uri, '/');
        }
        return $uri;
    }
}
