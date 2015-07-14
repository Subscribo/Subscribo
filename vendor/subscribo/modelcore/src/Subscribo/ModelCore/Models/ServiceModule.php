<?php namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Service;
use Subscribo\Support\Arr;

/**
 * Model ServiceModule - Modules for services and their configurations
 *
 * Model class for being changed and used in the application
 */
class ServiceModule extends \Subscribo\ModelCore\Bases\ServiceModule
{
    const MODULE_ACCOUNT_MERGING = 'account_merging';

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    static $defaultSettings = [
        self::MODULE_ACCOUNT_MERGING => [
            'client' => [
                'main' => [
                    'uri' => [
                        'path' => '/redirection/hash',
                        'query' => [
                            'required' => [
                                'hash'          => '{hash}',
                                'redirect_back' => '{redirect_back}',
                            ],
                            'optional' => [
                                'locale' => '{locale}',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @param int|Service $service
     * @param string $module
     * @param array $settings
     * @return static|ServiceModule
     */
    public static function enableModule($service, $module, array $settings = null)
    {
        $serviceId = ($service instanceof Service) ? $service->id : $service;
        $attributes = ['service_id' => $serviceId, 'module' => $module];
        $instance = static::query()->where($attributes)->first();
        if ( ! $instance) {
            $instance = new static($attributes);
            $defaults = isset(static::$defaultSettings[$module]) ? static::$defaultSettings[$module] : null;
            $settings = is_null($settings) ? $defaults : $settings;
        }
        if ( ! is_null($settings)) {
            $instance->settings = $settings;
        }
        $instance->status = static::STATUS_ENABLED;
        $instance->save();
        return $instance;
    }

    /**
     * @param int|Service $service
     * @param string $module
     * @return null|static|ServiceModule
     */
    public static function disableModule($service, $module)
    {
        $serviceId = ($service instanceof Service) ? $service->id : $service;
        $instance = static::firstByAttributes(['service_id' => $serviceId, 'module' => $module]);
        if (empty($instance)) {
            return null;
        }
        $instance->status = static::STATUS_DISABLED;
        $instance->save();
        return $instance;
    }

    /**
     * @param int|Service $service
     * @param string $module
     * @return null|static|ServiceModule
     */
    public static function findEnabledModule($service, $module)
    {
        $serviceId = ($service instanceof Service) ? $service->id : $service;
        $instance = static::firstByAttributes(['service_id' => $serviceId, 'module' => $module, 'status' => static::STATUS_ENABLED]);
        return $instance;
    }

    /**
     * @param int|Service $service
     * @param string $module
     * @return bool
     */
    public static function isModuleEnabled($service, $module)
    {
        $serviceModule = static::findEnabledModule($service, $module);
        $result = $serviceModule ? true : false;
        return $result;
    }

    /**
     * @param int|Service $service
     * @param string $module
     * @param null|mixed $settingKey
     * @param null|mixed $default
     * @return mixed|null
     */
    public static function retrieveSettings($service, $module, $settingKey = null, $default = null)
    {
        $serviceModule = static::findEnabledModule($service, $module);
        if (empty($serviceModule)) {
            return $default;
        }
        if (is_null($settingKey)) {
            return $serviceModule->settings;
        }
        if ( ! is_array($serviceModule->settings)) {
            return $default;
        }
        return Arr::get($serviceModule->settings, $settingKey, $default);
    }

    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    public static function retrieveUri($service, $module, array $parameters = array(), $key = 'main')
    {
        $uriData = static::retrieveSettings($service, $module, 'client.'.$key.'.uri');
        if (empty($uriData)) {
            return null;
        }
        $queryParameters = Arr::get($uriData, 'query.required', array());
        $optionalQueryParameters = Arr::get($uriData, 'query.optional', array());
        $replacements = [];
        foreach ($parameters as $key => $value) {
            $wrappedKey = '{'.$key.'}';
            $replacements[$wrappedKey] = $value;
            if (array_key_exists($key, $optionalQueryParameters)) {
                $queryParameters[$key] = $optionalQueryParameters[$key];
            }
        }
        $queryItems = [];
        foreach ($queryParameters as $key => $value) {
            $queryItems[] = $key.'='.$value;
        }
        $queryString = implode('&', $queryItems);
        $rawUri = $uriData['path'] . ($queryString ? ('?'.$queryString) : '');
        $result = strtr($rawUri, $replacements);
        return $result;
    }
}
