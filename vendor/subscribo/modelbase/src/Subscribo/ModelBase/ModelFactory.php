<?php namespace Subscribo\ModelBase;


use Subscribo\ModelBase\AbstractModel;

class ModelFactory {

    /**
     * Return an instance of a model based on URI Stub string
     *
     * @param string $modelUriStub
     * @return null|\Subscribo\ModelBase\AbstractModel
     */
    public static function resolveModelFromUriStub($modelUriStub)
    {
        $modelsConfiguration = \Config::get('apiconfiguration.models');
        if (empty($modelsConfiguration[$modelUriStub]['model_full_name'])) {
            return null;
        }
        $modelClassName = $modelsConfiguration[$modelUriStub]['model_full_name'];
        $result = new $modelClassName();
        return $result;
    }

    /**
     * Lists available Model Uri Stubs
     *
     * @return array
     */
    public static function listUriStubs()
    {
        $modelsConfiguration = \Config::get('apiconfiguration.models');
        $result = array();
        foreach ($modelsConfiguration as $stub => $configuration) {
            if ( ! empty($configuration['model_full_name'])) {
                $result[] = $stub;
            }
        }
        return $result;
    }

}
