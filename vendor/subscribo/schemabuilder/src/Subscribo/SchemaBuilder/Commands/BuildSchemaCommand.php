<?php namespace Subscribo\SchemaBuilder\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Yaml\Yaml;
use Fuel\Core\Arr;
use Subscribo\Config;

use App;
use Exception;
use Illuminate\Support\Str;

class BuildSchemaCommand extends BuildCommandAbstract {


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'build:schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This custom command parses the schema';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function fire()
    {
        $inputFileName = $this->argument('input_file');
        $outputFileName = $this->argument('output_file');
        $this->info('Schema build starting. Using input file: '. $inputFileName.' output file: '.$outputFileName);
        $this->info('Environment: '. App::environment());
        Config::loadFileForPackage('schemabuilder', $inputFileName, 'schema', true, null);
        $input = Config::getForPackage('schemabuilder', 'schema');
        $doctype = $input['doctype'];
        if (false === in_array($doctype, array('MODEL_SCHEMA-v1.0', 'PARSED_MODEL_SCHEMA-v1.0'))) {
            throw new \Exception ("Unsupported doctype. You can use for example: 'MODEL_SCHEMA-v1.0'");
        }
        $modelFields = $this->_parseModelFields($input['model_fields']);
        $defaultModelOptions = $input['default_model_options'] ?: array();
        $defaultTranslationModelOptions = $input['default_translation_model_options'] ?: array();
        $translationModelDefaults = Arr::merge($defaultModelOptions, $defaultTranslationModelOptions);
        $defaultFieldOptions = $input['default_field_options'] ?: array();

        $modelOptions = $this->_parseModelOptions($input['model_options'] ?: array(), $modelFields, $defaultModelOptions);
        $modelFields = $this->_addPrimaryKeys($modelFields, $modelOptions);
        $modelFields = $this->_addFieldsFromOptions($modelFields, $modelOptions);
        $modelOptions = $this->_addOptionsFromTranslatableFields($modelFields, $modelOptions);
        $translationModels = $this->_assembleTranslationModels($modelFields, $modelOptions, $translationModelDefaults);
        $modelOptions = Arr::merge($translationModels['model_options'], $modelOptions);
        $modelFields = $this->_addProcessedFields($modelFields, $modelOptions);
        $modelFields = $this->_addProcessedFields($translationModels['model_fields'], $modelOptions, $modelFields);

        $relations = $this->_collectRelations($modelFields, $modelOptions);
        $modelFields = $this->_addRelationsToModelFields($modelFields, $modelOptions, $relations);
        $modelFields = $this->_addRulesToModelFields($modelFields, $modelOptions);

        $modelFields = $this->_addDefaultFieldOptions($modelFields, $defaultFieldOptions);
        $modelOptions = $this->_addForeignObjects($modelFields, $modelOptions, $relations);
        $modelOptions = $this->_addOptionsFromFields($modelFields, $modelOptions);
        $this->_checkConsistency($modelFields, $modelOptions);
        $modelFields = $this->_addTimestamps($modelFields, $modelOptions);
        $modelOptions = $this->_collectRules($modelFields, $modelOptions);
        $pivotTables = $this->_assemblePivotTables($modelFields, $modelOptions, $relations);

        $data = array(
            'doctype'       => 'PARSED_MODEL_SCHEMA-v1.0',
            'model_options' => $modelOptions,
            'model_fields'  => $modelFields,
            'model_relations' => $relations,
            'pivot_tables' => $pivotTables,
        );
        $content = Yaml::dump($data, 3, 4, true, false);
        $this->_createFile($outputFileName, $content, 'overwrite');

        $this->info('Schema build finished.');
    }

    private function _assembleTranslationModels(array $modelFields, array $modelOptions, array $defaultModelOptions)
    {
        $result = ['model_options' => [], 'model_fields' => []];
        foreach($modelOptions as $modelKey => $options) {
            if ( ! empty($options['translation_model'])) {
                $fields = Arr::get($modelFields, $modelKey, array());
                $translationModelOptions = $this->_assembleTranslationModelOptions($fields, $options, $defaultModelOptions);
                $resultKey = $translationModelOptions['table_name'];
                $result['model_options'][$resultKey] = $translationModelOptions;
                $result['model_fields'][$resultKey] = $translationModelOptions['fields'];
            }
        }
        return $result;
    }


    private function _assembleTranslationModelOptions(array $fields, array $options, array $translationDefaults)
    {
        $result = [
            'model_name' => $options['translation_model'],
            'table_name' => $options['translation_table'],
            'model_namespace' => $options['model_translation_namespace'],
            'model_base_namespace' => $options['model_translation_namespace'],
            'model_base_directory' => $options['model_translation_directory'],
        ];
        $result = $this->_parseModelOptionsForModel($options['translation_table'], $result, $translationDefaults);
        $translationModelFields = Arr::get($result, 'fields', array());
        $translationForeignKey = $options['translation_foreign_key'];
        $translationModelFields[$translationForeignKey] = [
            'name' => $translationForeignKey,
            'related_to' => $options['table_name'],
            'relation_attributes' => 'cascade_delete',
        ];
        foreach ($options['translatable'] as $fieldName) {
            if (empty($fields[$fieldName])) {
                $this->error("Translatable field name '".$fieldName."' is not defined.");
            } else {
                $field = $fields[$fieldName];
                $field['translate'] = false;
                $translationModelFields[$fieldName] = $field;
            }
        }
        $translationModelFields = $this->_addPrimaryKey($translationModelFields, $result);
        $result['fields'] = $translationModelFields;
        return $result;
    }


    private function _parseModelOptions(array $modelOptions, array $modelFields, array $defaults)
    {
        foreach ($modelFields as $key => $fields) {
            if ( ! array_key_exists($key, $modelOptions)) {
                $modelOptions[$key] = array();
            }
        }
        $result = array();
        foreach ($modelOptions as $key => $options) {
            $result[$key] = $this->_parseModelOptionsForModel($key, $options, $defaults);
        }
        return $result;
    }

    private function _parseModelOptionsForModel($modelKey, array $options, array $defaults)
    {
        if ($options) {
            $item = Arr::merge($defaults, $options);
        } else {
            $item = $defaults;
        }
        if (empty($item['table_name'])) {
            $item['table_name'] = $modelKey;
        }
        if (empty($item['model_name'])) {
            $item['model_name'] = $this->_modelNameFromTable($item['table_name']);
        }
        if (empty($item['title'])) {
            $item['title'] = ucfirst(str_replace('_', ' ', $item['table_name']));
        }
        if (empty($item['singular'])) {
            $item['singular'] = str_replace('_', ' ', str_singular($item['table_name']));
        }
        if (empty($item['model_full_name'])) {
            $item['model_full_name'] = '\\'.$item['model_namespace'].'\\'.$item['model_name'];
        }
        if (empty($item['api_stub'])) {
            $item['api_stub'] = str_replace('_', '-', $item['table_name']);
        }
        return $item;
    }

    private function _checkConsistency(array $modelFields, array $modelOptions)
    {
        $forbiddenTableNames = array('migrations');
        $forbiddenModelNames = array('base', 'abstractmodel');
        foreach($modelOptions as $modelKey => $options)
        {
            $baseModelExtends = $options['base_model_extends'];
            if (empty($modelFields[$modelKey])) {
                $this->error("Warning: Key: '".$modelKey."' in modelOptions does not have counterpart in modelFields");
            }
            if ($modelKey !== $options['table_name']) {
                $this->error("Warning: Inconsistency between key: '".$modelKey."' and table_name: '".$options['table_name']."'");
            }
            if (Str::plural($options['table_name']) !== $options['table_name']) {
                $this->error("Warning: Table name: '".$options['table_name']."' is not plural (modelKey: '".$modelKey."')");
            }
            if (Str::snake($options['table_name']) !== $options['table_name']) {
                $this->error("Warning: Table name: '".$options['table_name']."' is not in snake case (modelKey: '".$modelKey."')");
            }
            if (strtolower($options['table_name']) !== $options['table_name']) {
                $this->error("Warning: Table name: '".$options['table_name']."' contains uppercase characters (modelKey: '".$modelKey."')");
            }
            if ( ! preg_match('#[a-z0-9_]+#', $options['table_name'])) {
                $this->error("Warning: Table name: '".$options['table_name']."' contains not allowed characters (modelKey: '".$modelKey."')");
            }
            if (empty($options['title'])) {
                $this->error("Warning: Title empty for model. (modelKey: '".$modelKey."')");
            }
            if (empty($options['model_name'])) {
                $this->error("Warning: Model name empty for model. (modelKey: '".$modelKey."')");
            }
            if (Str::singular($options['model_name']) !== $options['model_name']) {
                $this->error("Warning: Model name: '".$options['model_name']."' is not in singular (modelKey: '".$modelKey."')");
            }
            if (Str::studly($options['model_name']) !== $options['model_name']) {
                $this->error("Warning: Model name: '".$options['model_name']."' is not in studly case (modelKey: '".$modelKey."')");
            }
            if (false !== array_search($options['table_name'], $forbiddenTableNames)) {
                throw new \Exception("Table name '".$options['table_name']."' is not allowed. (modelKey: '".$modelKey."')");
            }
            if (false !== array_search(strtolower($options['model_name']), $forbiddenModelNames)) {
                throw new \Exception("Model name '".$options['model_name']."' is not allowed. (modelKey: '".$modelKey."')");
            }
            $foreignObjects = empty($options['foreign_objects']) ? array() : $options['foreign_objects'];
            foreach ($foreignObjects as $foreignObject) {
                if ( ! preg_match('#^[A-Za-z0-9]+$#', $foreignObject['name'])) {
                    $this->error("Warning: Relation name: '".$foreignObject['name']."' contains not allowed characters (modelKey: '".$modelKey."')");
                }
                $collision = $this->_findCollision($foreignObject['name'], $baseModelExtends);
                if ($collision) {
                    throw new \Exception(ucfirst($collision['type']). " from ".$collision['from']." '".$collision['name']."' used for a relation name (modelKey: '".$modelKey."' field name: '".$foreignObject['name']."')");
                }
            }
        }
        foreach($modelFields as $modelKey => $fields) {
            if (empty($modelOptions[$modelKey])) {
                $this->error("Warning: Key: '".$modelKey."' in modelFields does not have counterpart in modelOptions");
            }
            foreach($fields as $fieldKey => $field) {
                if ($fieldKey !== $field['name']) {
                    $this->error("Warning: Inconsistency between field key: '".$fieldKey."' and field name: '".$field['name']."' (modelKey: '".$modelKey."')");
                }
                if (( ! empty($field['primary'])) and ('id' !== $fieldKey)) {
                    $this->error("Warning: Primary key under field key: '".$fieldKey."' defined (modelKey: '".$modelKey."')");
                }
                if (strtolower($field['name']) !== $field['name']) {
                    $this->error("Warning: Field name: '".$field['name']."' contains uppercase characters (modelKey: '".$modelKey."')");
                }
                if ( ! preg_match('#^[a-z0-9_]+$#', $field['name'])) {
                    $this->error("Warning: Field name: '".$field['name']."' contains not allowed characters (modelKey: '".$modelKey."')");
                }
                if (empty($field['title'])) {
                    $this->error("Warning: Title empty for field. (modelKey: '".$modelKey."' fieldKey: '".$fieldKey."')");
                }
                $collision = $this->_findCollision($field['name'], $baseModelExtends);
                if ($collision) {
                    throw new \Exception(ucfirst($collision['type']). " from ".$collision['from']." '".$collision['name']."' used for a field name (modelKey: '".$modelKey."' field name: '".$field['name']."')");
                }
            }
            if (empty($fields['id'])) {
                $this->error("Warning: field with key 'id' not found (modelKey: '".$modelKey."')");
            } else {
                if (empty($fields['id']['primary'])) {
                    $this->error("Warning: field with key 'id' is not set as primary (modelKey: '".$modelKey."')");
                }
                if (false === array_search($fields['id']['db_type'], array('integer', 'biginteger', 'mediuminteger', 'smallinteger', 'tinyinteger'))) {
                    $this->error("Warning: field with key 'id' is not a kind of integer (modelKey: '".$modelKey."')");
                }
                if (empty($fields['id']['unsigned'])) {
                    $this->error("Warning: field with key 'id' is not set as unsigned (modelKey: '".$modelKey."')");
                }
            }
        }

    }

    private function _findCollision($name, $baseModelExtends)
    {
        $prefixes = array('get_', 'set_', 'unset_','compare_');
        $prefixed = array($name);
        foreach ($prefixes as $prefix) {
            $prefixed[] = $prefix.$name;
        }
        $declined = array();
        foreach ($prefixed as $word) {
            $declined[] = $word;
            $declined[] = Str::singular($word);
            $declined[] = Str::plural($word);
        }
        $cased = array();
        foreach ($declined as $word) {
            $cased[] = $word;
            $cased[] = strtolower($word);
            $cased[] = strtoupper($word);
            $cased[] = Str::snake($word);
            $cased[] = Str::camel($word);
            $cased[] = Str::studly($word);
        }
        $preUnderScored = array();
        foreach ($cased as $word) {
            $preUnderScored[] = $word;
            $preUnderScored[] = '_'.$word;
            $preUnderScored[] = '__'.$word;
        }
        $wordsToCheck = $preUnderScored;
        foreach ($wordsToCheck as $toCheck) {
            $collision = $this->_checkForCollisions($toCheck, $baseModelExtends);
            if (false !== $collision) {
                return $collision;
            }
        }
        return false;
    }



    private function _checkForCollisions($name, $baseModelExtends)
    {
        $keywords = array('callback', 'format', 'without', 'page', ); //Keywords for API, which are not at the same time property or method checked below
        $keywordKey = array_search($name, $keywords);
        if (false !== $keywordKey) {
            return array('type' => 'keyword', 'from' => 'keywords', 'name' => $keywords[$keywordKey]);
        }
        $classNames = array('\\Illuminate\\Database\\Eloquent\\Builder', '\\Illuminate\\Database\\Query\\Builder', '\\Illuminate\\Database\\Eloquent\\Model', $baseModelExtends);
        foreach($classNames as $className) {
            if (property_exists($className, $name)) {
                return array('from' => $className, 'type' => 'property', 'name' => $name);
            }
            if (method_exists($className, $name)) {
                return array('from' => $className, 'type' => 'method', 'name' => $name);
            }
        }
        return false;
    }


    private function _collectRelations(array $modelFields, array $modelOptions)
    {
        $relations = array();
        $reverseRelations = array();
        $tableNames = array();
        foreach ($modelOptions as $key => $options) {
            $tableNames[$key] = $options['table_name'];
        }
        foreach ($modelOptions as $key => $options)
        {
            if ( ! empty($options['relations'])) {
                if ( ! is_array($options['relations'])) {
                    $options['relations'] = array($options['relations']);
                }
                $this->_parseRelationsFromOptions($options, $tableNames, $relations, $reverseRelations, $modelFields, $modelOptions);
            }
            $fields = Arr::get($modelFields, $key, array());
            $this->_parseRelationsFromFields($options, $fields, $tableNames, $relations, $reverseRelations, $modelFields, $modelOptions);
        }
        $this->_addReverseRelations($relations, $reverseRelations, $modelFields, $modelOptions);
        return $relations;
    }

    private function _parseRelationsFromOptions(array $options, array $tableNames, array &$relations, array &$reverseRelations, array $modelFields, array $modelOptions)
    {
        foreach ($options['relations'] as $key => $relationsDefinition) {
            if (in_array(strtolower($key), array('has_one', 'has_many', 'one_belongs_to_one', 'many_belongs_to_one', 'many_to_many',
                'polymorphic_one_has_one', 'polymorphic_one_has_many',
                'polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one', 'polymorphic_belongs_to_one',
                'polymorphic_many_belongs_to_many',  'many_morphed_by_many',
                'has_many_through',
            ))) {
                $newRelations = $this->_parseRelationsByKey($key, $relationsDefinition, $options['table_name']);
            } elseif (is_int($key)) {
                $newRelations = array($this->_parseRelationString($relationsDefinition, $options['table_name']));
            } else {
                throw new \Exception("_parseRelationsFromOptions: Key '".$key.'" is nor integer nor a value from allowed set.');
            }
            $processed = $this->_processAndCheckRelations($newRelations, $tableNames);
            foreach ($processed as $item) {
                if (empty($item['process'])) {
                    continue;
                }
                if (isset($relations[$item['type']][$item['table_from']][$item['relation_set_key']])) {
                    $this->error("Warning: (_parseRelationsFromOptions) Relation item already exists: '".$item['type'].'/'.$item['table_from'].'/'.$item['relation_set_key']."'");
                }
                $relations[$item['type']][$item['table_from']][$item['relation_set_key']] = $item;
                $reverseRelationsToAdd = $this->_assembleReverseRelations($item, $modelFields, $modelOptions);
                foreach ($reverseRelationsToAdd as $reverse) {
                    $reverse = $this->_processAndCheckRelation($reverse, $tableNames);
                    $reverseRelations[] = $reverse;
                }
            }
        }
    }

    private function _parseRelationsFromFields(array $options, array $fields, array $tableNames, array &$relations, array &$reverseRelations, array $modelFields, array $modelOptions)
    {
        foreach ($fields as $fieldKey => $field) {
            $relation = $this->_deriveRelationFromField($field, $options['table_name'], $tableNames);
            if (empty($relation)) {
                continue;
            }
            $relation = $this->_processAndCheckRelation($relation, $tableNames);
            if (empty($relation['process'])) {
                continue;
            }
            if (isset($relations[$relation['type']][$relation['table_from']][$relation['relation_set_key']])) {
                $this->error("Warning: (_parseRelationsFromFields) Relation item already exists: '".$relation['type'].'/'.$relation['table_from'].'/'.$relation['relation_set_key']."'");
            }
            $relations[$relation['type']][$relation['table_from']][$relation['relation_set_key']] = $relation;
            $reverseRelationsToAdd = $this->_assembleReverseRelations($relation, $modelFields, $modelOptions);
            foreach ($reverseRelationsToAdd as $reverse) {
                $reverse = $this->_processAndCheckRelation($reverse, $tableNames);
                $reverseRelations[] = $reverse;
            }
        }
    }

    private function _addReverseRelations(array &$relations, array $reverseRelations, array $modelFields, array $modelOptions)
    {
        foreach ($reverseRelations as $reverse) {
            $this->_addReverseRelation($reverse, $relations, $modelFields, $modelOptions);
        }
    }

    private function _addReverseRelation(array $reverse, array &$relations, array $modelFields, array $modelOptions)
    {
        if (empty($reverse['process'])) {
            return;
        }
        if (empty($reverse['type'])) {
            throw new \Exception("_addReverseRelation: Reverse relation does not have defined type");
        }
        if (empty($reverse['table_from'])) {
            throw new \Exception("_addReverseRelation: Reverse relation does not have defined table_from");
        }
        if (empty($reverse['relation_set_key'])) {
            throw new \Exception("_addReverseRelation: Reverse relation does not have defined relation_set_key");
        }
        $relationToUpdate = null;
        foreach($relations as $typeKey => $typeSet) {
            if ( ! empty($typeSet[$reverse['table_from']])) {
                $relationToUpdate = $this->_relationIsAmong($reverse, $typeSet[$reverse['table_from']], $modelFields, $modelOptions);
            }
            if ($relationToUpdate) {
                break;
            }
        }
        if (empty($relationToUpdate) and isset($relations[$reverse['type']][$reverse['table_from']][$reverse['relation_set_key']])) {
            $this->error("Warning: Relation item already exists: '" . $reverse['type'] . '/' . $reverse['table_from'] . '/' . $reverse['relation_set_key'] . "'. Reverse relation not added.");
            return;
        }
        $mergedRelation = $this->_mergeRelations($reverse, $relationToUpdate);
        if ($relationToUpdate) {
            unset($relations[$relationToUpdate['type']][$relationToUpdate['table_from']][$relationToUpdate['relation_set_key']]);
            $relations[$mergedRelation['type']][$mergedRelation['table_from']][$mergedRelation['relation_set_key']] = $mergedRelation;
            $this->info("Info: Relation item updated: '" . $mergedRelation['type'] . '/' . $mergedRelation['table_from'] . '/' . $mergedRelation['relation_set_key'] . "'.");
        } else {
            $relations[$mergedRelation['type']][$mergedRelation['table_from']][$mergedRelation['relation_set_key']] = $mergedRelation;
            $this->info("Info: Reverse relation added: '" . $mergedRelation['type'] . '/' . $mergedRelation['table_from'] . '/' . $mergedRelation['relation_set_key'] . "'.");
        }
    }

    private function _mergeRelations($relation1, $relation2)
    {
        if (empty($relation2)) {
            return $relation1;
        }
        $result = $relation2;
        $tablesTo = $this->_arrayMergeSimple($this->_collectKeys(array($relation1, $relation2), array('table_to', 'tables_to')));
        $keysFrom = $this->_arrayMergeSimple($this->_collectKeys(array($relation1, $relation2), array('key_from', 'keys_from')));
        $keysTo = $this->_arrayMergeSimple($this->_collectKeys(array($relation1, $relation2), array('key_to', 'keys_to')));
        if (empty($tablesTo)) {
            $result['table_to'] = null;
            $result['tables_to'] = array();
        } elseif (1 === count($tablesTo)) {
            $result['table_to'] = reset($tablesTo);
            $result['tables_to'] = array();
        } else {
            $result['table_to'] = null;
            $result['tables_to'] = $tablesTo;
        }
        if (empty($keysFrom)) {
            $result['key_from'] = null;
            $result['keys_from'] = null;
        } elseif (1 === count($keysFrom)) {
            $result['key_from'] = reset($keysFrom);
            $result['keys_from'] = null;
        } else {
            $result['key_from'] = null;
            $result['keys_from'] = $keysFrom;
        }
        if (empty($keysTo)) {
            $result['key_to'] = null;
            $result['keys_to'] = null;
        } elseif (1 === count($keysTo)) {
            $result['key_to'] = reset($keysTo);
            $result['keys_to'] = null;
        } else {
            $result['key_to'] = null;
            $result['keys_to'] = $keysTo;
        }
        if ($relation1['type'] !== $relation2['type']
        and in_array($relation1['type'], array('polymorphic_belongs_to_one', 'polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one'), true)
        and in_array($relation2['type'], array('polymorphic_belongs_to_one', 'polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one'), true)) {
            $result['type'] = 'polymorphic_belongs_to_one';
        }
        return $result;
    }

    private function _collectKeys(array $arrays, array $keys)
    {
        $result = array();
        foreach ($arrays as $arr) {
            foreach ($keys as $key) {
                $result[] = Arr::get($arr, $key);
            }
        }
        return $result;
    }

    private function _arrayMergeSimple($arrays)
    {
        $result = array_shift($arrays);
        if ( ! is_array($result)) {
            $result = empty($result) ? array() : array($result);
        }
        while ($arrays) {
            $toAdd = array_shift($arrays);
            if (empty($toAdd)) {
                continue;
            }
            if ( ! is_array($toAdd)) {
                $toAdd  = array($toAdd);
            }
            foreach ($toAdd as $value) {
                $result[] = $value;
            }
        }
        $result = array_unique($result);
        sort($result);
        $result = array_values($result);
        return $result;
    }

    private function _relationIsAmong(array $relation, array $relations, array $modelFields, array $modelOptions)
    {
        foreach ($relations as $relationToCompare) {
            if ($this->_relationsAreSimilar($relation, $relationToCompare, $modelFields, $modelOptions))
                return $relationToCompare;
        }
        return false;
    }

    private function _relationTypesAreSimilar(array $relation1, array $relation2)
    {
        $type1 = strval($relation1['type']);
        $type2 = strval($relation2['type']);
        if ($type1 === $type2) {
            return true;
        }
        switch ($type1) {
            case 'no_relation':
            case 'has_one':
            case 'has_many':
            case 'many_to_many':
            case 'polymorphic_one_has_one':
            case 'polymorphic_one_has_many':
            case 'polymorphic_many_belongs_to_many':
            case 'many_morphed_by_many':
            case 'has_many_through':
                return false;
            case 'one_belongs_to_one':
            case 'many_belongs_to_one':
                return in_array($type2, array('one_belongs_to_one', 'many_belongs_to_one'), true);
            case 'polymorphic_belongs_to_one':
            case 'polymorphic_one_belongs_to_one':
            case 'polymorphic_many_belongs_to_one':
                return in_array($type2, array('polymorphic_belongs_to_one', 'polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one'), true);
            default:
                throw new \Exception("Unknown relation type '".$type1."'");
        }
    }

    private function _relationsAreSimilar(array $relation1, array $relation2, array $modelFields, array $modelOptions)
    {
        if ( ! $this->_relationTypesAreSimilar($relation1, $relation2)) {
            return false;
        }
        if ($relation1['table_from'] !== $relation2['table_from']) {
            return false;
        }
        if ( ! in_array($relation1['type'], array('polymorphic_belongs_to_one', 'polymorphic_many_belongs_to_one', 'polymorphic_one_belongs_to_one', 'many_morphed_by_many'), true)) {
            if ($relation1['table_to'] !== $relation2['table_to']) {
                return false;
            }
            if ($this->_obtainKeyFrom($relation1, false) !== $this->_obtainKeyFrom($relation2, false)) {
                return false;
            }
            if ($this->_obtainKeyTo($relation1, false) !== $this->_obtainKeyTo($relation2, false)) {
                return false;
            }
            return true;
        }
        $keysFrom1 = $this->_arrayMergeSimple($this->_collectKeys(array($relation1), array('key_from', 'keys_from')));
        $keysFrom2 = $this->_arrayMergeSimple($this->_collectKeys(array($relation2), array('key_from', 'keys_from')));
        if ($keysFrom1 !== $keysFrom2) {
            return false;
        }
        return true;
    }



    private function _assembleReverseRelations($relation, $modelFields, $modelOptions)
    {
        if ('no_relation' === $relation['type']
            or empty($relation['process'])
            or ( ! empty($relation['no_reverse']))
        ) {
            return array();
        }
        $prototype = array_only($relation, array('polymorphic', 'process', 'method_name_for','pivot_table', 'ordering_field_name', 'with_timestamps'));
        $prototype['reverse'] = true;
        switch($relation['type']) {
            case 'no_relation':
            case 'has_many_through':
            case 'polymorphic_belongs_to_one':  //One belongs to one OR Many belongs to one (keys on this table) - we do not know the type of relation on other tables (it may be even one on one table and many on other table) so reverse relation are not automatically created
                return array();
            case 'has_one':
                $prototype['type'] = 'one_belongs_to_one';
            break;
            case 'has_many':
                $prototype['type'] = 'many_belongs_to_one';
            break;
            case 'one_belongs_to_one':
                $prototype['type'] = 'has_one';
            break;
            case 'many_belongs_to_one':
                $prototype['type'] = 'has_many';
            break;
            case 'many_to_many':
                $prototype['type'] = 'many_to_many';
            break;
            case 'polymorphic_one_has_one':       //(One) has one polymorphic (keys on other table)
                $prototype['type'] = 'polymorphic_one_belongs_to_one';
            break;
            case 'polymorphic_one_has_many':       //(One) has many polymorphic (keys on other table)
                $prototype['type'] =  'polymorphic_many_belongs_to_one';
            break;
            case 'polymorphic_one_belongs_to_one': //One belongs to one polymorphic (keys on this table)
                $prototype['type'] = 'polymorphic_one_has_one';
            break;
            case 'polymorphic_many_belongs_to_one': //Many belongs to one polymorphic (keys on this table)
                $prototype['type'] = 'polymorphic_one_has_many';
            break;
            case 'polymorphic_many_belongs_to_many':   //Many to many polymorphic relation,  type on pivot table might refer to this model
                $prototype['type'] = 'many_morphed_by_many';
            break;
            case 'many_morphed_by_many': //Many to many polymorphic relation, type on pivot table is might refer to other model (it is expected, that this is the table related to multiple other tables (e.g. this is tags table))
                $prototype['type'] = 'polymorphic_many_belongs_to_many';
            break;
            default:
                throw new \Exception("Unknown relation type '".$relation['type']."'");
        }
        if ( ! empty($relation['table_from'])) {
            $prototype['table_to'] = $relation['table_from'];
        }
        if ( ! empty($relation['table_to'])) {
            $prototype['table_from'] = $relation['table_to'];
        }
        if ( ! empty($relation['hidden_from'])) {
            $prototype['hidden_to']   = $relation['hidden_from'];
        }
        if ( ! empty($relation['hidden_to'])) {
            $prototype['hidden_from'] = $relation['hidden_to'];
        }
        if ( ! empty($relation['key_from'])) {
            $prototype['key_to'] = $relation['key_from'];
        }
        if ( ! empty($relation['key_to'])) {
            $prototype['key_from'] = $relation['key_to'];
        }
        if ( ! empty($relation['key_from_type'])) {
            $prototype['key_to_type'] = $relation['key_from_type'];
        }
        if ( ! empty($relation['key_to_type'])) {
            $prototype['key_from_type'] = $relation['key_to_type'];
        }
        if ( ! empty($relation['keys_from'])) {
            $prototype['keys_to'] = $relation['keys_from'];
        }
        if ( ! empty($relation['keys_to'])) {
            $prototype['keys_from'] = $relation['keys_to'];
        }
        if (empty($relation['polymorphic'])) {
            return array($prototype);
        }
        if ( ! empty($relation['tables_to'])) {
            $result = array();
            foreach($relation['tables_to'] as $tableTo) {
                $prototype['table_from'] = $tableTo;
                $prototype['key_from'] = $this->_obtainKeyTo($relation, false, false, $tableTo);
                $result[] = $prototype;
            }
            return $result;
        }
        if (empty($prototype['table_from'])) {
            return array(); //We are not creating reverse relations for polymorphic relations without specified counterparts
        }
        return array($prototype);
    }


    private function _processAndCheckRelations(array $relations, array $tableNames)
    {
        $result = array();
        foreach($relations as $item) {
            $result[] = $this->_processAndCheckRelation($item, $tableNames);
        }
        return $result;
    }


    private function _processAndCheckRelation(array $relation, array $tableNames)
    {
        if ('no_relation' == $relation['type']) {
            $relation['process'] = false;
            return $relation;
        }
        $relation['process'] = true;
        $relation['polymorphic'] = ! empty($relation['polymorphic']);
        if (empty($relation['table_from'])) {
            throw new \Exception('_parseRelationsByKey: table_from missing');
        }
        if ( ! in_array($relation['table_from'], $tableNames)) {
            throw new \Exception("_parseRelationsByKey: table_from '".$relation['table_from']."' is not among defined tables");
        }
        if ((empty($relation['table_to'])) and (empty($relation['tables_to']))) {
            if ( ! empty($relation['tables_to_suggestion'])) {
                if (1 === count($relation['tables_to_suggestion'])) {
                    $relation['table_to'] = $this->_deriveTableFromSuggestion(reset($relation['tables_to_suggestion']), $tableNames);
                } elseif ('has_many_through' === $relation['type']) {
                    $relation['table_to'] = $this->_deriveTableFromSuggestion(array_shift($relation['tables_to_suggestion']), $tableNames);
                } else {
                    $relation['tables_to'] = $this->_deriveTablesFromSuggestion($relation['tables_to_suggestion'], $tableNames);
                    $relation = $this->_changeRelationToPolymorphic($relation);
                }
                if ((empty($relation['table_to'])) and (empty($relation['tables_to']))) {
                    throw new \Exception("_processAndCheckRelation: Cannot derive valid table name(s) from suggestion (Table from: '".Arr::get($relation, 'table_from', '')."' Suggestions: '".implode("', '", Arr::get($relation, 'tables_to_suggestion', array()))."' KeyFrom:  '".Arr::get($relation, 'key_from', '')."')");
                }
            } elseif ( ! in_array($relation['type'], array('polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one', 'polymorphic_belongs_to_one'), true)) {
                throw new \Exception("_processAndCheckRelation: Table name is not provided and suggestion is not available (Table from: '".Arr::get($relation, 'table_from', '')."' Suggestions: '".implode("', '", Arr::get($relation, 'tables_to_suggestion', array()))."' KeyFrom: '".Arr::get($relation, 'key_from', '')."')");
            }
        }
        if ('has_many_through' === $relation['type'] and empty($relation['table_through'])) {
            if ( ! empty($relation['tables_to_suggestion'])) {
                $relation['table_through'] = $this->_deriveTableFromSuggestion(array_shift($relation['tables_to_suggestion']), $tableNames);
            }
        }
        if ('has_many_through' === $relation['type']) {
            if (empty($relation['table_through'])) {
                throw new \Exception("_processAndCheckRelation: Cannot derive valid table through name(s) from suggestion (Table from: '".Arr::get($relation, 'table_from', '')."' Suggestions: '".implode("', '", Arr::get($relation, 'tables_to_suggestion', array()))."' KeyFrom:  '".Arr::get($relation, 'key_from', '')."')");
            } elseif ( ! in_array($relation['table_through'], $tableNames, true)) {
                throw new \Exception("_processAndCheckRelation: Through table '".$relation['table_through']."' is not a valid table (Table from: '".Arr::get($relation, 'table_from', '')."' Suggestions: '".implode("', '", Arr::get($relation, 'tables_to_suggestion', array()))."' KeyFrom:  '".Arr::get($relation, 'key_from', '')."')");
            }
        }
        if (( ! empty($relation['table_to']) and ( ! in_array($relation['table_to'], $tableNames, true)))) {
            throw new \Exception("_parseRelationsByKey: table_to '".$relation['table_to']."' is not among defined tables");
        }
        if (( ! empty($relation['tables_to']) and (array_diff($relation['tables_to'], $tableNames)))) {
            throw new \Exception("_parseRelationsByKey: tables_to '".implode("', '", array_diff($relation['tables_to'], $tableNames))."' are not among defined tables");
        }
        if (empty($relation['relation_set_key'])) {
            $relation['relation_set_key'] = Arr::get($relation, 'key_from');
        }
        if (empty($relation['relation_set_key'])) {
            $baseToTable = empty($relation['table_to']) ? reset($relation['tables_to']) : $relation['table_to'];
            $derivedRelationSetKey = $this->_foreignKeyFromTable($baseToTable);
            if (($relation['type'] === 'has_one') or ($relation['type'] === 'has_many')) {
                $keyTo = Arr::get($relation, 'key_to');
                if ($keyTo) {
                    $relation['relation_set_key'] = str_singular($baseToTable).'_'.$keyTo;
                } else {
                    $relation['relation_set_key'] = $derivedRelationSetKey;
                }
            } elseif ($relation['type'] === 'many_to_many') {
                $pivotTable = Arr::get($relation, 'pivot_table');
                if ($pivotTable) {
                    $relation['relation_set_key'] = $pivotTable.'_'.$derivedRelationSetKey;
                } else {
                    $relation['relation_set_key'] = $derivedRelationSetKey;
                }
            } else {
                $relation['relation_set_key'] = $derivedRelationSetKey;
            }
        }
        if (in_array($relation['type'], array('many_to_many', 'polymorphic_many_belongs_to_many', 'many_morphed_by_many'), true)) {
            $relation = $this->_processRelationForPivotTables($relation);
        }
        return $relation;
    }

    private function _processRelationForPivotTables($relation)
    {
        if (empty($relation['pivot_table'])) {
            $relation['pivot_table'] = $this->_assemblePivotTableName($relation);
        }
        if ( ! isset($relation['with_timestamps'])) {
            $relation['with_timestamps'] = true;
        }
        if ( ! empty($relation['ordering_field_name'])) {
            $relation['with_pivot'] = (empty($relation['with_pivot'])) ?  array() : $relation['with_pivot'];
            $relation['with_pivot'][] = $relation['ordering_field_name'];
            $relation['with_pivot'] = array_unique($relation['with_pivot']);
            $relation['order_by'] = 'pivot_'.$relation['ordering_field_name'];
        }
        return $relation;
    }

    private function _assemblePivotTableName(array $relation)
    {
        switch (strval($relation['type'])) {
            case 'many_to_many':
                $nameParts = array(Str::singular($relation['table_from']), Str::singular($relation['table_to']));
                sort($nameParts);
                $result = implode('_', $nameParts);
                return $result;
            case 'polymorphic_many_belongs_to_many':
            case 'many_morphed_by_many':
                return Str::plural($this->_obtainPolymorphicRelationName($relation));
        }
        throw new \Exception('_assemblePivotTableName(): Do not know how to assemble pivot table name');
    }

    private function _deriveTableFromSuggestion($suggestion, array $tableNames)
    {
        if (in_array($suggestion, $tableNames)) {
            return $suggestion;
        }
        $processed = snake_case($suggestion);
        if (in_array($processed, $tableNames)) {
            return $processed;
        }
        $pluralized = str_plural($processed);
        if (in_array($pluralized, $tableNames)) {
            return $pluralized;
        }
        return null;
    }

    private function _deriveTablesFromSuggestion($suggestions, array $tableNames)
    {
        $result = array();
        foreach ($suggestions as $suggestion) {
            $found = $this->_deriveTableFromSuggestion($suggestion, $tableNames);
            if ( ! $found) {
                throw new \Exception("Suggested table name was not found (Suggestion: '".$suggestion."')");
            }
            $result[] = $found;
        }
        return $result;
    }


    private function _deriveTableSuggestionFromKeyFrom($keyFrom)
    {
        $pattern = "#^([a-z0-9_]+)_id$#";
        $matches = array();
        preg_match($pattern, $keyFrom, $matches);
        if (empty($matches[1])) {
            return null;
        }
        return $matches[1];
    }

    private function _deriveRelationFromField($field, $tableName, $tableNames)
    {
        if ( ! empty($field['relation'])) {
            return $field['relation'];
        }
        if ( ! empty($field['related_to'])) {
            if ('*' === $field['related_to']) {
                $suggestions = array();
            } else {
                $suggestions = explode(' ', $this->_extraTrim($field['related_to']));
            }
        } else {
            $hint = $this->_deriveTableSuggestionFromKeyFrom($field['name']);
            $suggestion = $this->_deriveTableFromSuggestion($hint, $tableNames); //we have to be more strict, if we use heuristic to derive table name: So derived table name have to exist
            $suggestions = $suggestion ? array($suggestion) : array();
        }
        if (empty($field['related_to'])
            and empty($field['relation_attributes'])
            and empty($suggestions)
        ) {
            return null;
        }
        $result = array(
            'type' => 'many_belongs_to_one', //just a suggestion, might be modified later, as well as other fields
            'table_from' => $tableName,
            'key_from'  => $field['name'],
            'tables_to_suggestion' => $suggestions,
        );
        if (array() === $result['tables_to_suggestion']) {
            $result['type'] = 'polymorphic_belongs_to_one';
            $result['polymorphic'] = true;
        }
        if ( ! empty($field['relation_attributes'])) {
            $result = $this->_parseRelationAttributes($field['relation_attributes'], $result);
        }
        if ( ! empty($field['hidden'])) {
            $result['hidden_from'] = Arr::get('hidden_from', $result, true);
            $result['hidden_to'] = Arr::get('hidden_to', $result, true);
        }
        $result = $this->_addRelationDefaults($result);
        return $result;
    }


    private function _parseRelationsByKey($key, $relationsDefinitions, $tableName)
    {
        $relationsDefinitions = is_array($relationsDefinitions) ? $relationsDefinitions : array($relationsDefinitions);
        $result = array();
        foreach ($relationsDefinitions as $definition) {
            if (is_array($definition)) {
                $definition['type'] = Arr::get($definition, 'type', $key);
                $definition['table_from'] = Arr::get($definition, 'table_from', $tableName);
                $result[] = $this->_addRelationDefaults($definition);
            }
            else {
                $result[] = $this->_parseRelationString($definition, $tableName, $key);
            }
        }
        return $result;
    }

    private function _parseRelationString($definition, $tableName = null, $type = null, $keyFrom = null)
    {
        $normalized = $this->_extraTrim($definition);
        $pattern = '#^([^()]*)? *(\\(([^()]*)\\))? *?$#';
        $matches = array();
        preg_match($pattern, $normalized, $matches);
        if (empty($matches)) {
            throw new \Exception('Relation definition in incorrect format: '.$definition);
        }
        $main = empty($matches[1]) ? '' : trim($matches[1]);
        $inBrackets = empty($matches[3]) ? '' : trim($matches[3]);
        $keywords = array('has_one', 'has_a', 'has_an',  'has_many',
            'one_belongs_to_one', 'one_belongs_to_a', 'one_belongs_to_an',
            'many_belongs_to_one', 'many_belongs_to_a', 'many_belongs_to_an', 'many_belongs_to_many',
            'many_belongs_to','one_belongs_to',
            'belongs_to_many', 'belongs_to_one', 'belongs_to_a', 'belongs_to_an',
            'belongs_to', 'many_to_many', 'no_relation',
            'polymorphic_one_has_one', 'morph_one', 'morph_a', 'morph_an', //(One) has one polymorphic (keys on other table)
            'polymorphic_one_has_many', 'morph_many',    //(One) has many polymorphic (keys on other table)
            'polymorphic_one_belongs_to_one', 'one_morph_to',   //One belongs to one polymorphic (keys on this table)
            'polymorphic_many_belongs_to_one', 'many_morph_to', //Many belongs to one polymorphic (keys on this table)
            'polymorphic_belongs_to_one', 'morph_to', //One belongs to one OR Many belongs to one (keys on this table) - we do not know the type of relation on other tables (it may be even one on one table and many on other table) so reverse relation are not automatically created

            'polymorphic_many_belongs_to_many', 'morph_to_many', //Many to many polymorphic relation,  type on pivot table might refer to this model
            'many_morphed_by_many', 'morphed_by_many', //Many to many polymorphic relation, type on pivot table is might refer to other model (it is expected, that this is the table related to multiple other tables (e.g. this is tags table))
            'has_many_through',
        );

        $parts = explode(' ', trim($main));
        $keyword = $this->_acquireKeyword($parts, $keywords);
        if (empty($type) and empty($keyword)) {
            throw new \Exception("_parseRelationString: Unknown relation type '".$keyword."' in a definition '".$definition."'");
        }
        if ($keyword) {
            $result = $this->_setRelationTypeFromKeyword($keyword);
        } else {
            $result = $this->_setRelationTypeFromKeyword($type);
        }
        if ($tableName) {
            $result['table_from'] = $tableName;
        }
        if ($parts) {
            $result['tables_to_suggestion'] = $parts;
        }
        if ($keyFrom) {
            $result['key_from'] = $keyFrom;
        }
        if ($inBrackets) {
            $result = $this->_parseRelationAttributes($inBrackets, $result);
        }
        $result = $this->_addRelationDefaults($result);
        return $result;
    }

    private function _setRelationTypeFromKeyword($keyword, array $relation = array())
    {
        if (empty($keyword)) {
            throw new \Exception("_setRelationTypeFromKeyword(): Keyword empty.");
        }
        $type = null;
        $word = strtolower(trim($keyword));
        $polymorphic = null;
        switch ($word) {
            case 'none':
            case 'no_relation':
                $type = 'no_relation';
            break;
            case 'has_one': //One to one relation, table without key
            case 'has_a':
            case 'has_an':
                $type = 'has_one';
            break;
            case 'has_many': //one to many relation, table without key
                $type = 'has_many';
            break;
            case 'one_belongs_to': //One to one relation, table with key
            case 'one_belongs_to_one':
            case 'one_belongs_to_a':
            case 'one_belongs_to_an':
                $type = 'one_belongs_to_one';
            break;
            case 'belongs_to': //many to one relation, table with key
            case 'many_belongs_to_one':
            case 'many_belongs_to_a':
            case 'many_belongs_to_an':
            case 'many_belongs_to':
            case 'belongs_to_one':
            case 'belongs_to_a':
            case 'belongs_to_an':
                $type = 'many_belongs_to_one';
            break;
            case 'many_to_many':                  //many to many relation
            case 'many_belongs_to_many':
            case 'belongs_to_many':
                $type = 'many_to_many';
            break;
            case 'polymorphic_one_has_one':       //(One) has one polymorphic (keys on other table)
            case 'morph_one':
            case 'morph_a':
            case 'morph_an':
                $type = 'polymorphic_one_has_one';
                $polymorphic = true;
            break;
            case 'polymorphic_one_has_many':       //(One) has many polymorphic (keys on other table)
            case 'morph_many':
                $type = 'polymorphic_one_has_many';
                $polymorphic = true;
            break;
            case 'polymorphic_one_belongs_to_one': //One belongs to one polymorphic (keys on this table)
            case 'one_morph_to':
                $type = 'polymorphic_one_belongs_to_one';
                $polymorphic = true;
            break;
            case 'polymorphic_many_belongs_to_one': //Many belongs to one polymorphic (keys on this table)
            case 'many_morph_to':
                $type = 'polymorphic_many_belongs_to_one';
                $polymorphic = true;
            break;
            case 'polymorphic_belongs_to_one':  //One belongs to one OR Many belongs to one (keys on this table) - we do not know the type of relation on other tables (it may be even one on one table and many on other table) so reverse relation are not automatically created
            case 'morph_to':
                $type = 'polymorphic_belongs_to_one';
                $polymorphic = true;
            break;
            case 'polymorphic_many_belongs_to_many':   //Many to many polymorphic relation,  type on pivot table might refer to this model
            case 'morph_to_many':
                $type = 'polymorphic_many_belongs_to_many';
                $polymorphic = true;
            break;
            case 'many_morphed_by_many': //Many to many polymorphic relation, type on pivot table is might refer to other model (it is expected, that this is the table related to multiple other tables (e.g. this is tags table))
            case 'morphed_by_many':
                $type = 'many_morphed_by_many';
                $polymorphic = true;
            break;
            case 'has_many_through':
                $type = 'has_many_through';
            break;
            default:
                throw new \Exception("_setRelationTypeFromKeyword(): Unknown relation type: '".$keyword."'");
        }
        $relation['type'] = $type;
        if ($polymorphic) {
            $relation['polymorphic'] = true;
        }
        return $relation;
    }

    private function _parseRelationAttributes($attributesString, array $relation)
    {
        $result = $relation;

        $keywords = array('table_from', 'table_to', 'key_from', 'key_to', 'from_table', 'to_table', 'from_key', 'to_key',
            'pivot_table', 'table_pivot', 'type', 'relation_type', 'none', 'no_relation',
            'ordering_on', 'ordering_off', 'ordering_field_name',
            'method_name_for_key_from', 'method_name_for_key_to', 'method_name_for_table_from', 'method_name_for', 'method_name', 'foreign_object_name',
            'hidden_from', 'hidden_to', 'hide_from', 'hide_to', 'show_from', 'show_to',
            'morph', 'polymorphic',
            'no_reverse',
            'table_through', 'key_through', 'through_object_name',
            'with_timestamps', 'without_timestamps',
            'cascade_delete',
        );
        $parts = explode(' ', $attributesString);

        while ($parts) {
            $first = reset($parts);
            if (empty($first)) {
                continue;
            }
            $word = $this->_acquireKeyword($parts, $keywords);

            if (is_null($word)) {
                throw new \Exception("_parseRelationAttributes: Unknown keyword '".$first."'  in attributes '".$attributesString."'");
            }
            switch (strval($word)) {
                case 'no_relation';
                case 'none':
                    $result['type'] = 'no_relation';
                continue 2;
                case 'type':
                case 'relation_type':
                    $result = $this->_setRelationTypeFromKeyword(strtolower(array_shift($parts)), $result);
                continue 2;
                case 'morph':
                case 'polymorphic':
                    $result = $this->_changeRelationToPolymorphic($result);
                continue 2;
                case 'no_reverse':
                    $result['no_reverse'] = true;
                continue 2;
                case 'table_from':
                case 'from_table':
                    $result['table_from'] = strtolower(array_shift($parts));
                    continue 2;
                case 'table_to':
                case 'to_table':
                    $result['table_to'] = strtolower(array_shift($parts));
                    continue 2;
                case 'key_from':
                case 'from_key':
                    $result['key_from'] = strtolower(array_shift($parts));
                    continue 2;
                case 'key_to':
                case 'to_key':
                    $result['key_to'] = strtolower(array_shift($parts));
                    continue 2;
                case 'pivot_table':
                case 'table_pivot':
                    $result['pivot_table'] = strtolower(array_shift($parts));
                    continue 2;
                case 'ordering_on':
                    $result['ordering_field_name'] = 'ordering';
                    continue 2;
                case 'ordering_off':
                    $result['ordering_field_name'] = false;
                    continue 2;
                case 'ordering_field_name':
                    $result['ordering_field_name'] = array_shift($parts);
                    continue 2;
                case 'method_name':
                    $result['method_name'] = array_shift($parts);
                    continue 2;
                case 'hidden_from':
                    $result['hidden_from'] = $this->_valueAsBool(array_shift($parts));
                    continue 2;
                case 'hidden_to':
                    $result['hidden_to'] = $this->_valueAsBool(array_shift($parts));
                    continue 2;
                case 'hide_from':
                    $result['hidden_from'] = true;
                    continue 2;
                case 'hide_to':
                    $result['hidden_to'] = true;
                    continue 2;
                case 'show_from':
                    $result['hidden_from'] = false;
                    continue 2;
                case 'show_to':
                    $result['hidden_to'] = false;
                    continue 2;
                case 'method_name_for_table_from':
                case 'method_name_for':
                    $methodNameForTableName = strtolower(array_shift($parts));
                    $methodName = array_shift($parts);
                    if (empty($methodNameForTableName) or empty($methodName)) {
                        throw new \Exception("Switch 'method_name_for' / 'method_name_for_table_from' should have two arguments");
                    }
                    $result['method_name_for'][$methodNameForTableName] = $methodName;
                    continue 2;
                case 'method_name_for_key_from':
                    $keyName = strtolower(array_shift($parts));
                    $methodName = array_shift($parts);
                    if (empty($keyName) or empty($methodName)) {
                        throw new \Exception("Switch 'method_name_for_key_from' should have two arguments");
                    }
                    $result['method_name_for_key_from'][$keyName] = $methodName;
                    continue 2;
                case 'method_name_for_key_to':
                    $keyName = strtolower(array_shift($parts));
                    $methodName = array_shift($parts);
                    if (empty($keyName) or empty($methodName)) {
                        throw new \Exception("Switch 'method_name_for_key_to' should have two arguments");
                    }
                    $result['method_name_for_key_to'][$keyName] = $methodName;
                    continue 2;
                case 'foreign_object_name':
                    $result['foreign_object_name'] = array_shift($parts);
                    continue 2;
                case 'table_through':
                    $result['table_through'] = array_shift($parts);
                    continue 2;
                case 'key_through':
                    $result['key_through'] = array_shift($parts);
                    continue 2;
                case 'through_object_name':
                    $result['through_object_name'] = array_shift($parts);
                    continue 2;
                case 'with_timestamps':
                    $result['with_timestamps'] = true;
                    continue 2;
                case 'without_timestamps':
                    $result['with_timestamps'] = false;
                    continue 2;
                case 'cascade_delete':
                    $result['db_foreign_key_on_delete'] = 'cascade';
                    continue 2;
                default:
                    throw new \Exception("Action for keyword '".$word."' not defined.");

            }
        }
        return $result;
    }

    private function _changeRelationToPolymorphic($relation)
    {
        $type = $relation['type'];
        switch (strval($type)) {
            case 'no_relation':
                return $relation;
            case 'has_one': //One to one relation, table without key
            case 'polymorphic_one_has_one':       //(One) has one polymorphic (keys on other table)
                $relation['type'] = 'polymorphic_one_has_one';
            break;
            case 'has_many': //one to many relation, table without key
            case 'polymorphic_one_has_many':       //(One) has many polymorphic (keys on other table)
                $relation['type'] =  'polymorphic_one_has_many';
            break;
            case 'one_belongs_to_one': //One to one relation, table with key
            case 'polymorphic_one_belongs_to_one': //One belongs to one polymorphic (keys on this table)
                $relation['type'] = 'polymorphic_one_belongs_to_one';
            break;
            case 'many_belongs_to_one': //many to one relation, table with key
            case 'polymorphic_many_belongs_to_one': //Many belongs to one polymorphic (keys on this table)
                $relation['type'] = 'polymorphic_many_belongs_to_one';
            break;
            case 'many_to_many':                  //many to many relation
            case 'polymorphic_many_belongs_to_many':   //Many to many polymorphic relation,  type on pivot table might refer to this model
                $relation['type'] = 'polymorphic_many_belongs_to_many';
            break;
            case 'polymorphic_belongs_to_one':  //One belongs to one OR Many belongs to one (keys on this table) - we do not know the type of relation on other tables (it may be even one on one table and many on other table) so reverse relation are not automatically created
                $relation['type'] = 'polymorphic_belongs_to_one';
            break;
            case 'many_morphed_by_many': //Many to many polymorphic relation, type on pivot table is might refer to other model (it is expected, that this is the table related to multiple other tables (e.g. this is tags table))
                $relation['type'] = 'many_morphed_by_many';
            break;
            case 'has_many_through':
                throw new \Exception("Relation 'has_many_through' could not be changed into polymorphic.");
            default:
                throw new \Exception("_changeRelationToPolymorphic(): Unknown relation type: '".$type."'");
        }
        $relation['polymorphic'] = true;
        return $relation;
    }

    private function _addRelationDefaults(array $relation)
    {
        if (( ! isset($relation['ordering_field_name'])))
        {
            $relation['ordering_field_name'] = ('many_to_many' === $relation['type']) ? 'ordering' : false;
        }
        if (( ! isset($relation['hidden_from'])))
        {
            $relation['hidden_from'] = false;
        }
        if (( ! isset($relation['hidden_to'])))
        {
            $relation['hidden_to'] = false;
        }

        return $relation;
    }

    private function _parseModelFields(array $data, $defaults = array())
    {
        $result = array();
        foreach ($data as $tableName => $fields) {
            foreach ($fields as $key => $value) {
                try {
                    $item = $this->_assembleField($value, $key, $defaults);
                    $result[$tableName][$item['name']] = $item;
                } catch (\Exception $e) {
                    throw new \Exception('Problem with table "'.$tableName.'": '.$e->getMessage());
                }
            }
        }
        return $result;
    }

    private function _addProcessedFields(array $modelFields, array $modelOptions, $addTo = array())
    {
        $result = $addTo;
        foreach($modelFields as $modelKey => $fields) {
            $tableName = $modelOptions[$modelKey]['table_name'];
            foreach ($fields as $key => $field) {
                $result[$modelKey][$key] = $this->_processField($field, $tableName, $modelFields, $modelOptions);
            }
        }
        return $result;
    }

    private function _addRulesToModelFields(array $modelFields, array $modelOptions)
    {
        $result = array();
        foreach($modelFields as $modelKey => $fields) {
            $options = $modelOptions[$modelKey];
            foreach ($fields as $fieldKey => $field) {
                $result[$modelKey][$fieldKey] = $this->_deriveRules($field, $options, $modelFields, $modelOptions);
            }
        }
        return $result;
    }

    private function _processField(array $field, $tableName, array $modelFields, array $modelOptions)
    {
        $field['type'] = $this->_deriveType($field);
        $field = $this->_arrayMergeRecursiveNotNull($this->_analyseType($field), $field);
        $administratorSetting = $this->_analyseForAdministrator($field);
        $field = $this->_arrayMergeRecursiveNotNull(array('administrator' => $administratorSetting), $field);
        if (empty($field['attribute_name'])) {
            $field['attribute_name'] = camel_case($field['name']);
        }
        if (empty($field['title'])) {
            $field['title'] = ucfirst(str_replace('_', ' ', $field['name']));
        }
        if ( ! isset($field['migration_setup']))
        {
            $field['migration_setup'] = empty($field['translate']); //we turn migration setup on for fields, which do not have translation and have not migration setup manually
        }
        if ( ! empty($field['description'])) {
            $field['description'] = str_replace('*/', '', $field['description']);
        }
        if ( ! empty($field['comments'])) {
            $field['comments'] = str_replace('*/', '', $field['comments']);
        }
        if (( ! empty($field['hidden'])) and ( ! isset($field['filterable_by']))) {
            $field['filterable_by'] = false;
        }
        if (( ! empty($field['hidden'])) and ( ! isset($field['administrator']['list']))) {
            $field['administrator']['list'] = false;
        }
        if (( ! empty($field['hidden'])) and ( ! isset($field['administrator']['filter']))) {
            $field['administrator']['filter'] = false;
        }
        return $field;
    }

    private function _addOptionsFromTranslatableFields($ModelFields, $modelOptions)
    {
        $result = $modelOptions;
        foreach ($ModelFields as $modelKey => $fields) {
            $options = Arr::get($modelOptions, $modelKey, array());
            $result[$modelKey] = $this->_addOptionsFromTranslatableFieldsToModel($fields, $options);
        }
        return $result;
    }

    private function _addOptionsFromTranslatableFieldsToModel($fields, $options)
    {
        $translatableFields = Arr::get($options, 'translatable', array());
        foreach ($fields as $fieldName => $field) {
            $isTranslatable = Arr::get($field, 'translate', false);
            if ($isTranslatable) {
                $translatableFields[] = $fieldName;
            }
        }
        $translatableFields = array_unique($translatableFields);
        if (empty($translatableFields)) {
            return $options;
        }
        $result = $options;
        $result['translatable'] = $translatableFields;
        if (empty($result['translation_table'])) {
            $result['translation_table'] = str_singular($options['table_name']).$options['model_translation_table_suffix'];
        }
        if (empty($result['translation_model'])) {
            $result['translation_model'] = $this->_modelNameFromTable($result['translation_table']);
        }
        if (empty($result['translation_model_full_name'])) {
            $result['translation_model_full_name'] = $result['model_translation_namespace'].'\\'.$result['translation_model'];
        }
        if (empty($result['translation_foreign_key'])) {
            $result['translation_foreign_key'] = $this->_foreignKeyFromTable($options['table_name']);
        }
        $relation = [
            'type' => 'has_many',
            'table_from' => $options['table_name'],
            'table_to' => $result['translation_table'],
            'method_name' => 'translations',
            'key_to' => $result['translation_foreign_key'],
        //  'no_reverse' => true,
        ];
        $result['relations']['has_many'][] = $relation;
        return $result;
    }

    private function _addOptionsFromFields($ModelFields, $modelOptions)
    {
        $result = $modelOptions;
        foreach ($ModelFields as $tableName => $fields) {
            $options = Arr::get($modelOptions, $tableName, array());
            $result[$tableName] = $this->_addOptionsFromFieldsToModel($fields, $options);
        }
        return $result;
    }

    private function _addOptionsFromFieldsToModel($fields, $options)
    {
        $result = $options;
        $hiddenFields = Arr::get($options, 'hidden', array());
        $fillableFields = Arr::get($options, 'fillable', array());
        foreach ($fields as $fieldName => $field) {
            $isHidden = Arr::get($field, 'hidden', false);
            if ($isHidden) {
                $hiddenFields[] = $fieldName;
            }
            if ($this->_isFieldFillable($field)) {
                $fillableFields[] = $fieldName;
            }
        }
        foreach($options['foreign_objects'] as $foreignObject) {
            if ($foreignObject['hidden']) {
                $hiddenFields[] = $foreignObject['name'];
             }
        }
        $result['hidden'] = array_unique($hiddenFields);
        $result['fillable'] = array_unique($fillableFields);
        //For now we are purposefully ignoring guarded fields (keeping default array('*') )
        $hiddenWrapped = array();
        foreach ($result['hidden'] as $hiddenFieldName) {
            $hiddenWrapped[] = "'".addslashes($hiddenFieldName)."'";
        }
        $result['hidden_wrapped'] = $hiddenWrapped;
        if (empty($result['representative'])) {
            $representativeField = $this->_findRepresentative($fields);
            $result['representative'] = $representativeField ? $representativeField['name'] : null;
        }
        return $result;
    }

    /**
     * Do some heuristics, whether field should be fillable via mass assignement
     * @param array $field
     * @return bool
     */
    private function _isFieldFillable(array $field)
    {
        if (isset($field['fillable'])) {
            return $field['fillable'];
        }
        if (preg_match('#id$#', $field['name'])) {
            return false;
        }
        if (( ! empty($field['related_to'])) or ( ! empty($field['relation_attributes']))) {
            return false;
        }
        $typeSpecial = Arr::get($field, 'type_special');
        if ($typeSpecial === 'identifier') {
            return false;
        }
        return true;
    }

    private function _findRepresentative(array $fields, $default = null)
    {
        if (empty($fields)) {
            return $default;
        }
        //First we try, whether we find any field explicitly marked as representative
        foreach($fields as $field) {
            if ( ! empty($field['representative'])) {
                return $field;
            }
        }
        $preferredFieldNames = array('title', 'name', 'identifier', 'key');
        //Second we try, whether at least one of our fields have a preferred field name
        foreach($preferredFieldNames as $preferredFieldName) {
            foreach($fields as $field) {
                if (($field['name'] === $preferredFieldName)
                    and $field['migration_setup']
                    and ('string' === $field['type'])
                    and ( ! empty($field['required']))) {
                    return $field;
                }
            }
        }
        //Well, we did not found one
        //Let's try at least to find a field, which contain reasonable sounding word
        //and has a reasonable type and we can assume it is present
        $preferredFieldNameParts = array('title', 'name', 'identifier', 'description', 'summary', 'definition');
        foreach($preferredFieldNameParts as $preferredFieldNamePart) {
            foreach($fields as $field) {
                if ((false !== stripos($field['name'], $preferredFieldNamePart))
                    and $field['migration_setup']
                    and ('string' === $field['type'])
                    and ( ! empty($field['required']))) {
                    return $field;
                }
            }
        }
        // Let's find any field, which is string and is always present
        foreach($fields as $field) {
            if (('string' === $field['type'])
                and $field['migration_setup']
                and ( ! empty($field['required']))) {
                return $field;
            }
        }
        // Let's find any string-like field, which is always present and not a password
        $preferredDbTypes = array('varchar', 'char', 'tinytext', 'text', 'mediumtext', 'longtext');
        foreach($preferredDbTypes as $preferredDbType) {
            foreach($fields as $field) {
                if (($preferredDbType === $field['db_type'])
                    and $field['migration_setup']
                    and ('password' != $field['type_special'])
                    and ( ! empty($field['required']))) {
                    return $field;
                }
            }
        }
        //Let's find any always present non password field
        foreach($fields as $field) {
            if (('password' != $field['type_special'])
                and $field['migration_setup']
                and ( ! empty($field['required']))) {
                return $field;
            }
        }
        //Giving up always present, but at least something else as primary key
        foreach($fields as $field) {
            if (('password' != $field['type_special'])
                and $field['migration_setup']
                and empty($field['primary'])) {
                return $field;
            }
        }
        //if not found anything better, try primary key
        $primaryKeyField = $this->_findPrimaryKeyFromFields($fields);
        if ($primaryKeyField) {
            return $primaryKeyField;
        }
        //If everything failed, return default
        return $default;
    }


    private function _deriveType(array $field)
    {
        if ( ! empty($field['type'])) {
            return $field['type'];
        }
        if ($field['related_to'] or ( ! empty($field['relation_attributes']))) { //We might possibly change this to ( ! empty($field['related_to']))  or..., but so far it has been useful when it crashed here for not finding the index
            return 'unsigned int';
        }
        if ('id' === $field['name']) {
            return 'unsigned int';
        }
        if (preg_match('#_id$#', $field['name'])) {
            return 'unsigned int';
        }
        return 'string';
    }

    private function _deriveRules(array $field, array $options, array $modelFields, array $modelOptions)
    {
        $result = $field;
        $rules = empty($field['rules']) ? array() : $field['rules'];
        if ( ! is_array($rules)) {
            $rules = explode('|', $rules);
        }
        $modificationRulesBeforeValidation = empty($field['modification_rules_before_validation']) ? array() : $field['modification_rules_before_validation'];
        if ( ! is_array($modificationRulesBeforeValidation)) {
            $modificationRulesBeforeValidation = explode('|', $modificationRulesBeforeValidation);
        }
        $modificationRulesAfterValidation = empty($field['modification_rules_after_validation']) ? array() : $field['modification_rules_after_validation'];
        if ( ! is_array($modificationRulesAfterValidation)) {
            $modificationRulesAfterValidation = explode('|', $modificationRulesAfterValidation);
        }
        if ( false === array_search($field['db_type'], array('text', 'blob', 'longtext', 'mediumtext', 'varchar', 'char'))) {
            $this->_pushValueIfNotPresent('non_printable_to_null', $modificationRulesAfterValidation);
        }
        if (  ( ! array_key_exists('required', $field))
            or $field['required']) {
            $this->_unshiftValueIfNotPresent('required', $rules);
        }
        switch ($field['db_type']) {
            case 'integer':
                $this->_pushValueIfNotPresent('integer', $rules);
                if ($field['unsigned']) {
                    $this->_pushValueIfNotPresent('between:0,4294967295', $rules);
                } else {
                    $this->_pushValueIfNotPresent('between:-2147483648,2147483647', $rules);
                }
            break;
            case 'biginteger':
                $this->_pushValueIfNotPresent('integer', $rules);
                if ($field['unsigned']) {
                    $this->_pushValueIfNotPresent('between:0,18446744073709551615', $rules); //Actually, values over 9223372036854775807 would not be validated as a valid PHP integer
                } else {
                    $this->_pushValueIfNotPresent('between:-9223372036854775808,9223372036854775807', $rules);
                }
            break;
            case 'mediuminteger':
                $this->_pushValueIfNotPresent('integer', $rules);
                if ($field['unsigned']) {
                    $this->_pushValueIfNotPresent('between:0,16777215', $rules);
                } else {
                    $this->_pushValueIfNotPresent('between:-8388608,8388607', $rules);
                }
            break;
            case 'smallinteger':
                $this->_pushValueIfNotPresent('integer', $rules);
                if ($field['unsigned']) {
                    $this->_pushValueIfNotPresent('between:0,65535', $rules);
                } else {
                    $this->_pushValueIfNotPresent('between:-32768,32767', $rules);
                }
            break;
            case 'tinyinteger':
                $this->_pushValueIfNotPresent('integer', $rules);
                if ($field['unsigned']) {
                    $this->_pushValueIfNotPresent('between:0,255', $rules);
                } else {
                    $this->_pushValueIfNotPresent('between:-128,127', $rules);
                }
            break;
            case 'decimal':
                $mainNumberSize = $field['db_length'] - $field['db_scale'];
                $pattern = '#^'.($field['unsigned'] ? '' :  '\\-?').'0?[0-9]{0,'.$mainNumberSize.'}(\\.[0-9]{0,'.$field['db_scale'].'})?$#';
                $rule = array('regex', $pattern);
                $this->_pushValueIfNotPresent($rule, $rules);
            break;
            case 'double':
            case 'float':
                $this->_pushValueIfNotPresent('numeric', $rules);
                if ($field['unsigned']) {
                    $this->_pushValueIfNotPresent('min:0', $rules);
                }
            break;
            case 'boolean':
                $this->_pushValueIfNotPresent('boolean', $rules);
            break;
            case 'datetime':
            case 'timestamp':
                $this->_pushValueIfNotPresent('date', $rules);
            break;
            case 'date':
                $this->_pushValueIfNotPresent('date_format:Y-m-d', $rules);
            break;
            case 'time':
                $this->_pushValueIfNotPresent('date_format:H:i:s', $rules);
            break;
            case 'enum':
                $validationRule = $field['enum_list'];
                array_unshift($validationRule, 'in');
                $this->_pushValueIfNotPresent($validationRule, $rules);
            break;
            case 'text':
            case 'blob':
                $this->_pushValueIfNotPresent('max:65535', $rules);
            break;
            case 'longtext':
                $this->_pushValueIfNotPresent('max:4294967295', $rules);
            break;
            case 'mediumtext':
                $this->_pushValueIfNotPresent('max:16777215', $rules);
            break;
            case 'varchar':
            case 'char':
                $this->_pushValueIfNotPresent(('max:'.$field['db_length']), $rules);
            break;
        }
        if ( ! empty($field['unique'])) {
            $tableName = $options['table_name'];
            $rule = array('unique', $tableName);
            $this->_pushValueIfNotPresent($rule, $rules);
        }
        switch ($field['type_special']) {
            case 'email':
                $this->_pushValueIfNotPresent('email', $rules);
            break;
            case 'url':
                $this->_pushValueIfNotPresent('url', $rules);
            break;
            case 'identifier':
                $pattern = '#^[a-zA-Z][a-zA-Z0-9_-]*[a-zA-Z0-9]$#';
                $rule = array('regex', $pattern);
                $this->_pushValueIfNotPresent($rule, $rules);
            break;
            case 'rational':
                $pattern = '#^'.($field['unsigned'] ? '' :  '\\-?').' *[0-9]* *([0-9]+ */ *[1-9][0-9]*)?$#';
                $rule = array('regex', $pattern);
                $this->_pushValueIfNotPresent($rule, $rules);
            break;
        }
        if ( ! empty($field['db_foreign_key'])) {
            $rule = array('exists', $field['db_foreign_key']['table_to'], $field['db_foreign_key']['key_to']);
            $this->_pushValueIfNotPresent($rule, $rules);
        }
        $result['rules'] = $rules;
        $result['modification_rules_before_validation'] = $modificationRulesBeforeValidation;
        $result['modification_rules_after_validation'] = $modificationRulesAfterValidation;
        return $result;
    }

    private function _addRuleToField($rule, array $field)
    {
        $result = $field;
        $rules = empty($field['rules']) ? array() : $field['rules'];
        if ( ! is_array($rules)) {
            $rules = explode('|', $rules);
        }
        $this->_pushValueIfNotPresent($rule, $rules);
        $result['rules'] = $rules;
        return $result;
    }

    /**
     * Get some information for field, mostly from type
     *
     * @param array $field
     * @return array
     * @throws \Exception
     */
    private function _analyseType(array $field)
    {
        $typeHint   = null;
        $typeSpecial = null;
        $dbType     = null;
        $dbTypeBase = null;
        $dbTypeSize = '';
        $dbLength   = null;
        $dbScale    = null;
        $unsigned   = null;
        $enumList   = null;
        $primaryKey = null;
        $description = null;
        $key        = null;
        $type = $field['type'];
        $parts = explode(' ', $type);
        //Check for prepended size and unsigned switches
        while ($parts)
        {
            $key = strtolower(array_shift($parts));
            if ('unsigned' == $key) {
                $unsigned = true;
            } elseif (in_array($key, array('big', 'medium', 'small','tiny', 'long'))) {
                $dbTypeSize = $key;
            } else {
                break;
            }
        }
        //First we divide up some type and size combinations
        switch ($key) {
            case 'bigincrements':
                $key = 'increments';
                $dbTypeSize = 'big';
            break;
            case 'bigint':
            case 'biginteger':
                $key = 'integer';
                $dbTypeSize = 'big';
            break;
            case 'longtext':
                $key = 'text';
                $dbTypeSize = 'long';
            break;
            case 'mediumint':
            case 'mediuminteger':
                $key = 'integer';
                $dbTypeSize = 'medium';
            break;
            case 'mediumtext':
                $key = 'text';
                $dbTypeSize = 'medium';
            break;
            case 'smallint':
            case 'smallinteger':
                $key = 'integer';
                $dbTypeSize = 'small';
            break;
            case 'tinyint':
            case 'tinyinteger':
                $key = 'integer';
                $dbTypeSize = 'tiny';
            break;
        }
        //Then we check the key and derive some additional information from it
        switch ($key) {
            case 'increments':
            case 'primary':
            case 'key':
                $primaryKey = true;
                $typeHint   = 'int';
                $dbTypeBase = 'integer';
                $unsigned   = true;
                $dbLength = array_shift($parts);
                if ('key' == strtolower($dbLength)) {
                    $dbLength = array_shift($parts);
                }
            break;
            case 'natural':
                $typeHint   = 'int';
                $dbTypeBase = 'integer';
                $unsigned   = true;
                $dbLength = array_shift($parts);
                if ('number' == strtolower($dbLength)) {
                    $dbLength = array_shift($parts);
                }
            break;
            case 'rational':
                $typeHint   = 'string';
                $description = '(rational number)';
                $dbTypeBase = 'varchar';
                $typeSpecial = 'rational';
                $dbLength = array_shift($parts);
                if ('number' == strtolower($dbLength)) {
                    $dbLength = array_shift($parts);
                }
            break;
            case 'int':
            case 'integer':
                $typeHint   = 'int';
                $dbTypeBase = 'integer';
                $dbLength = array_shift($parts);
                if ('unsigned' == strtolower($dbLength)) {
                    $unsigned = true;
                    $dbLength = array_shift($parts);
                }
            break;
            case 'bool':
            case 'boolean':
                $typeHint   = 'bool';
                $dbTypeBase = 'boolean';
            break;
            case 'decimal':
                $typeHint   = 'string';
                $dbTypeBase = 'decimal';
                $dbLength = array_shift($parts);
                if ('unsigned' == strtolower($dbLength)) {
                    $unsigned = true;
                    $dbLength = array_shift($parts);
                }
                $dbScale = array_shift($parts);
                $dbLength = is_null($dbLength) ? 10 : intval($dbLength);
                $dbScale = is_null($dbScale) ? 2 : intval($dbScale);
                if ($dbScale > $dbLength) {
                    throw new \Exception("Second parameter (scale: ".$dbScale.") of type should not be bigger then first parameter (length: ".$dbLength.") (Field name: '".$field['name']."')");
                }
            break;
            case 'currency':
                $typeHint   = 'string';
                $dbTypeBase = 'decimal';
                $dbLength = array_shift($parts);
                $typeSpecial = 'currency';
                if ('unsigned' == strtolower($dbLength)) {
                    $unsigned = true;
                    $dbLength = array_shift($parts);
                }
                $dbScale = array_shift($parts);
                $dbLength = is_null($dbLength) ? 10 : intval($dbLength);
                $dbScale = is_null($dbScale) ? 2 : intval($dbScale);
                if ($dbScale > $dbLength) {
                    throw new \Exception("Second parameter (scale: ".$dbScale.") of type should not be bigger then first parameter (length: ".$dbLength.") (Field name: '".$field['name']."')");
                }
            break;
            case 'double':
                $typeHint   = 'float';
                $dbTypeBase = 'double';
                $dbLength = array_shift($parts);
                if ('unsigned' == strtolower($dbLength)) {
                    $unsigned = true;
                    $dbLength = array_shift($parts);
                }
                $dbScale = array_shift($parts);
            break;
            case 'float':
                $typeHint   = 'float';
                $dbTypeBase = 'float';
                $dbLength = array_shift($parts);
                if ('unsigned' == strtolower($dbLength)) {
                    $unsigned = true;
                    $dbLength = array_shift($parts);
                }
                $dbScale = array_shift($parts);
            break;
            case 'period':
                $typeHint   = 'int';
                $dbTypeBase = 'integer';
                $description = '(period in seconds)';
                $dbLength = array_shift($parts);
                if ('unsigned' == strtolower($dbLength)) {
                    $unsigned = true;
                    $dbLength = array_shift($parts);
                }
            break;
            case 'datetime':
                $typeHint   = 'string';
                $dbTypeBase = 'datetime';
                $description = '(datetime)';
            break;
            case 'date':
                $typeHint   = 'string';
                $dbTypeBase = 'date';
                $description = '(date)';
            break;
            case 'time':
                $typeHint   = 'string';
                $dbTypeBase = 'time';
                $description = '(time)';
            break;
            case 'timestamp':
                $typeHint   = 'int';
                $dbTypeBase = 'timestamp';
                $description = '(timestamp)';
            break;
            case 'identifier':
                $typeSpecial = 'identifier';
                $typeHint    = 'string';
                $dbTypeBase  = 'varchar';
                $dbLength = array_shift($parts);
            break;
            case 'email':
                $typeSpecial = 'email';
                $typeHint    = 'string';
                $dbTypeBase  = 'varchar';
                $dbLength = array_shift($parts);
            break;
            case 'password':
                $typeSpecial = 'password';
                $typeHint    = 'string';
                $dbTypeBase  = 'varchar';
                $dbLength = array_shift($parts);
            break;
            case 'url':
                $typeSpecial = 'url';
                $typeHint    = 'string';
                $dbTypeBase  = 'varchar';
                $dbLength = array_shift($parts);
            break;
            case 'string':
            case 'varchar':
                $typeHint   = 'string';
                $dbTypeBase = 'varchar';
                $dbLength = array_shift($parts);
            break;
            case 'char':
            case 'character':
                $typeHint   = 'string';
                $dbTypeBase = 'char';
                $dbLength = array_shift($parts);
            break;
            case 'text':
                $typeHint   = 'string';
                $dbTypeBase = 'text';
                break;
            case 'binary':
            case 'blob':
                $typeHint   = 'string';
                $dbTypeBase = 'blob';
            break;
            case 'enum':
            case 'enumeration':
                $typeHint   = 'string';
                $dbTypeBase = 'enum';
                $separator = array_shift($parts);
                if ( ! in_array($separator, array('from', 'of', ':'))) {
                    throw new \Exception("Unknown enum separator '".$separator."' in type definition '".$field['type']."'");
                }
                $enumList = $parts;
                $description = '(one value from '.implode(', ', $enumList).')';
            break;
            default:
                throw new \Exception ("Unknown type key '".$key."' in type '".$field['type']."' (Field name: '".$field['name']."')");
        }
        $dbType = $dbTypeSize.$dbTypeBase;
        if ((false !== array_search($dbTypeBase, array('char', 'varchar'))) and empty($dbLength)) {
            $dbLength = 255;
        }
        $dbLength = $this->_ensureNullOrNonNegativeInteger($dbLength, "'dbLength (first parameter in type description of field named '".$field['name']."')");
        $dbScale = $this->_ensureNullOrNonNegativeInteger($dbScale, "'dbScale (second parameter in type description of field named '".$field['name']."')");
        $result = array(
            'type_hint' => $typeHint,
            'type_special' => $typeSpecial,
            'db_type'   => $dbType,
            'db_length' => $dbLength,
            'db_scale'  => $dbScale,
            'unsigned'  => $unsigned,
            'enum_list' => $enumList,
            'description' => $description,
            'primary_key' => $primaryKey,
        );
        return $result;
    }

    private function _ensureNullOrNonNegativeInteger($value, $valueDescription = 'Provided value')
    {
        if (is_null($value)) {
            return null;
        }
        if ( ! is_numeric($value)) {
            throw new \Exception($valueDescription.' is not numeric!');
        }
        if (is_string($value)) {
            if ( ! ctype_digit($value)) {
                throw new \Exception($valueDescription.' contains other then numeric characters!');
            }
        } elseif ( ! is_int($value)) {
            throw new \Exception($valueDescription.' is neither string nor integer!');
        }
        $intValue = intval($value);
        if ($intValue < 0) {
            throw new \Exception($valueDescription.' is less then 0!');
        }
        return $intValue;
    }

    /**
     * Adds some information to field, mostly from type
     *
     * @param array $field
     * @return array
     * @throws \Exception
     */
    private function _analyseForAdministrator(array $field)
    {
        //First we check for primary key
        if ( ! empty($field['primary'])) {
            return array(
                'edit_type' => 'key',
                'filter_type' => 'key',
            );
        }

        switch ($field['type_special']) {
            case 'password':
                return array(
                    'edit_type' => 'password',
                    'filter' => false,
                );
        }
        //And then for DB types
        switch ($field['db_type']) {
            case 'integer':
            case 'biginteger':
            case 'mediuminteger':
            case 'smallinteger':
            case 'tinyinteger':
            case 'decimal':
            case 'double':
            case 'float':
                $result = array(
                    'edit_type' => 'number',
                    'filter_type' => 'number',
                );
            break;
            case 'boolean':
                $result = array(
                    'edit_type' => 'bool',
                    'filter_type' => 'bool',
                );
            break;
            case 'datetime':
            case 'timestamp':
                $result = array(
                    'edit_type' => 'datetime',
                    'filter_type' => 'datetime',
                );
            break;
            case 'date':
                $result = array(
                    'edit_type' => 'date',
                    'filter_type' => 'date',
                );
            break;
            case 'time':
                $result = array(
                    'edit_type' => 'time',
                    'filter_type' => 'time',
                );
            break;
            case 'enum':
                $result = array(
                    'edit_type' => 'enum',
                    'filter_type' => 'enum',
                );
            break;
            case 'text':
            case 'longtext':
            case 'mediumtext':
            case 'varchar':
            case 'char':
            case 'blob':
            default:
                $result = array(
                    'edit_type' => 'text',
                    'filter_type' => 'text',
                );
        }
        return $result;
    }

    private function _addPrimaryKeys(array $modelFields, array $modelOptions)
    {
        $result = $modelFields;
        foreach ($modelOptions as $modelName => $options)
        {
            $fields = Arr::get($modelFields, $modelName, array());
            $result[$modelName] = $this->_addPrimaryKey($fields, $options);
        }
        return $result;
    }

    private function _addTimestamps(array $modelFields, array $modelOptions)
    {
        $result = $modelFields;
        foreach ($modelOptions as $modelKey => $options)
        {
            $tableName = $options['table_name'];
            $fields = Arr::get($modelFields, $modelKey, array());
            $timestamps = Arr::get($options, 'timestamps');
            if (is_array($timestamps))
            {
                try {
                    $result[$modelKey] = $this->_addFields($fields, $timestamps, true, $tableName, $modelFields, $modelOptions);
                } catch (\Exception $e) {
                    throw new \Exception('_addTimestamps: Problem with modelKey "'.$modelKey.'": '.$e->getMessage());
                }
            }
        }
        return $result;
    }

    private function _addFieldsFromOptions(array $modelFields, array $modelOptions)
    {
        $result = $modelFields;
        foreach ($modelOptions as $modelKey => $options)
        {
            $tableName = $options['table_name'];
            $fields = Arr::get($modelFields, $modelKey, array());
            $fieldsFromOptions = Arr::get($options, 'fields');
            if (is_array($fieldsFromOptions))
            {
                try {
                    $result[$modelKey] = $this->_addFields($fields, $fieldsFromOptions, false, $tableName, $modelFields, $modelOptions);
                } catch (\Exception $e) {
                    throw new \Exception('_addFieldsFromOptions: Problem with modelKey "'.$modelKey.'": '.$e->getMessage());
                }
            }
        }
        return $result;
    }

    private function _addFields(array $originalFields, $fieldsToAdd, $processField, $tableName, array $modelFields, array $modelOptions)
    {
        if (empty($fieldsToAdd))
        {
            return $originalFields;
        }
        $result = $originalFields;
        foreach ($fieldsToAdd as $fieldKey => $fieldDescription)
        {
            $fieldName = $fieldKey;
            try {
                $addingField = $this->_assembleField($fieldDescription, $fieldKey);
                $fieldName = $addingField['name'];
                // If we manually define some field attributes (original field) this should get preference
                $originalField = Arr::get($originalFields, $fieldName, array());
                $newField = $this->_arrayMergeRecursiveNotNull($addingField, $originalField);
                if ($processField) {
                    $newField = $this->_processField($newField, $tableName, $modelFields, $modelOptions);
                }
                $result[$fieldName] = $newField;
            } catch (\Exception $e) {
                throw new \Exception('Field "'.$fieldName.'": '.$e->getMessage());
            }

        }
        return $result;
    }

    private function _addDefaultFieldOptions(array $modelFields, array $defaultFieldOptions)
    {
        $result = array();
        foreach ($modelFields as $tableName => $fields)
        {
            $result[$tableName] = array();
            foreach ($fields as $fieldKey => $field) {
                $result[$tableName][$fieldKey] = $this->_arrayMergeRecursiveNotNull($defaultFieldOptions, $field);
            }
        }
        return $result;
    }

    private function _collectRules(array $modelFields, array $modelOptions)
    {
        $result = $modelOptions;
        $result = $this->_addOptionFromFieldToOptions($modelFields, $result, 'rules');
        $result = $this->_addOptionFromFieldToOptions($modelFields, $result, 'modification_rules_before_validation');
        $result = $this->_addOptionFromFieldToOptions($modelFields, $result, 'modification_rules_after_validation');
        return $result;
    }

    private function _addOptionFromFieldToOptions(array $modelFields, array $modelOptions, $optionName)
    {
        $result = $modelOptions;
        foreach ($modelFields as $modelKey => $fields)
        {
            $option = empty($modelOptions[$modelKey][$optionName]) ? array() :  $modelOptions[$modelKey][$optionName];
            foreach ($fields as $fieldKey => $field) {
                $fieldName = $field['name'];
                if ( ( ! array_key_exists($fieldName, $option))
                    and ( ! empty($field[$optionName]))) {
                    $option[$fieldName] = $field[$optionName];
                }
            }
            $result[$modelKey][$optionName] = $option;
        }
        return $result;
    }

    private function _addForeignObjects(array $modelFields, array $modelOptions, array $relations)
    {
        $result = $modelOptions;
        foreach ($modelOptions as $modelKey => $options) {
            $tableName = $options['table_name'];
            $fields = $modelFields[$modelKey];
            $objects = Arr::get($options, 'foreign_objects', array());
            $baseModelExtends = $options['base_model_extends'];
            $newObjects = $this->_assembleForeignObjectsForModel($tableName, $fields, $relations, $modelOptions, $baseModelExtends);
            foreach ($newObjects as $newObject) {
                if ( ! array_key_exists($newObject['name'], $objects)) {
                    $objects[$newObject['name']] = $newObject;
                } else {
                    //todo what to do if object is already defined
                    $this->error("Warning: Not adding new foreign object '".$newObject['name']."' as object with same key is already present (modelKey: '".$modelKey."').");
                }
            }
            $result[$modelKey]['foreign_objects'] = $objects;
        }
        return $result;
    }

    private function _assembleForeignObjectsForModel($tableName, array $fields, array $relations, array $modelOptions, $baseModelExtends)
    {
        $foreignObjectsStack = array();
        foreach ($relations as $relationTypeKey => $relationTypeSet) {
            if (empty($relationTypeSet[$tableName])) {
                continue;
            }
            foreach ($relationTypeSet[$tableName] as $relationFieldKey => $relation) {
                $objectsToStack = $this->_assembleForeignObjectsFromRelation($relation, $modelOptions);
                $foreignObjectsStack = array_merge($foreignObjectsStack, array_values($objectsToStack));
            }
        }

        $result = array();
        $collidingNames = array();
        foreach($fields as $field) {
            $collidingNames[$field['name']] = $field['name'];
        }
        foreach ($foreignObjectsStack as $newObject) {
            if (empty($newObject)) {
                continue;
            }
            if (empty($collidingNames[$newObject['name']])
                and empty($result[$newObject['name']])
                and ( ! $this->_findCollision($newObject['name'], $baseModelExtends))) {
                $newObject = $this->_setMethodNameToObject($newObject, $newObject['name']);
                $result[$newObject['name']] = $newObject;
                continue;
            }
            $collidingName = $newObject['name'];
            $collidingNames[$collidingName] = $collidingName;
            $collidingObject = empty($result[$collidingName]) ? null : $result[$collidingName];
            if ($collidingObject and (empty($result[$collidingObject['fallback_name']]))) {
                unset($result[$collidingName]);
                $collidingObject['name'] = $collidingObject['fallback_name'];
                $collidingObject = $this->_setMethodNameToObject($collidingObject, $collidingObject['fallback_name']);
                $result[$collidingObject['fallback_name']] = $collidingObject;
            }
            $fallbackName = $newObject['fallback_name'];
            if (empty($result[$fallbackName])) {
                $newObject['name'] = $fallbackName;
            }
            if (empty($result[$newObject['name']])) {
                $newObject = $this->_setMethodNameToObject($newObject, $newObject['name']);
                $result[$newObject['name']] = $newObject;
                continue;
            }
            $this->error("Warning: Not adding new foreign object '".$newObject['name']."' as object with same key is already present. (Table name: '".$tableName."' Relation type: '".$relation['type']."'  Colliding name: '".$collidingName."')");
        }
        return $result;
    }

    private function _setMethodNameToObject(array $relationObject, $methodName)
    {
        switch ($relationObject['method']) {
            case 'belongsTo':
                $relationObject = $this->_setMethodNameInMethodParameters($relationObject, $methodName, 3);
                break;
            case 'belongsToMany':
                $relationObject = $this->_setMethodNameInMethodParameters($relationObject, $methodName, 4);
                break;
            default:
        }
        return $relationObject;
    }

    /**
     * @param array $relationObject
     * @param string $methodName
     * @param int $position
     * @return array
     */
    private function _setMethodNameInMethodParameters(array $relationObject, $methodName, $position)
    {
        $parameters = array_pad($relationObject['method_parameters'], $position, null);
        $parameters[$position] = $methodName;
        $relationObject['method_parameters'] = $parameters;
        return $relationObject;
    }

    private function _assembleForeignObjectsFromRelation(array $relation, array $modelOptions)
    {
        if ('many_morphed_by_many' === $relation['type']) {
            return $this->_assembleForeignObjectsFromManyMorphedByManyRelation($relation, $modelOptions);
        }
        $foreignObject = $this->_assembleForeignObjectFromRelation($relation, $modelOptions);
        return array($foreignObject);
    }

    private function _assembleForeignObjectsFromManyMorphedByManyRelation($relation, array $modelOptions)
    {
        $tablesTo = empty($relation['tables_to']) ? array() : $relation['tables_to'];
        if ( ! empty($relation['table_to'])) {
            $tablesTo[] = $relation['table_to'];
        }
        $tablesTo = array_unique($tablesTo);

        $relationPrototype = $relation;
        unset($relationPrototype['tables_to']);
        unset($relationPrototype['table_to']);

        $result = array();
        foreach ($tablesTo as $tableTo) {
            $relationToProcess = $relationPrototype;
            $relationToProcess['table_to'] = $tableTo;
            $result[] = $this->_assembleForeignObjectFromRelation($relationToProcess, $modelOptions);
        }
        return $result;
    }


    private function _assembleForeignObjectFromRelation(array $relation, array $modelOptions)
    {
        if (empty($relation)) {
            return null;
        }
        if ( ! empty($relation['do_not_create_foreign_object'])) {
            return null;
        }
        $methodDefaultName = null;
        if ( ! empty($relation['key_from'])) {
            $methodNameBase = preg_replace('#_id$#', '', $relation['key_from']);
        }
        if ( ! empty($relation['key_to'])) {
            $fallbackMethodNameBase = preg_replace('#_id$#', '', $relation['key_to']);
        }
        if (empty($fallbackMethodNameBase)) {
            $fallbackMethodNameBase = $relation['table_from'];
        }
        if ( ! empty($relation['table_to'])) {
            if (empty($methodNameBase)) {
                $methodNameBase = $relation['table_to'];
            }
            $fallbackMethodNameBase .= '_'.$relation['table_to'];
            $foreignOptions = $this->_findOptionsByTableName($modelOptions, $relation['table_to']);
            $foreignObjectDefaultName = '\\'.$foreignOptions['model_namespace'].'\\'.Str::studly(Str::singular($relation['table_to']));
        }
        if ( ! empty($relation['polymorphic'])) {
            $methodNameBase = $this->_obtainPolymorphicRelationMethodNameBase($relation);
            $fallbackMethodNameBase = 'related_';
            $fallbackMethodNameBase .= empty($relation['table_to']) ? '' : (Str::singular($relation['table_to']).'_');
            $fallbackMethodNameBase .= $methodNameBase.'_'.$relation['type'];
        }
        if (in_array($relation['type'], array('polymorphic_belongs_to_one', 'polymorphic_many_belongs_to_one', 'polymorphic_one_belongs_to_one'), true)) {
            $tablesTo = empty($relation['tables_to']) ? array() : $relation['tables_to'];
            if ( ! empty($relation['table_to'])) {
                $tablesTo[] = $relation['table_to'];
            }
            $tablesTo = array_unique($tablesTo);
            $foreignObjectDefaultName = array();
            foreach($tablesTo as $tableTo) {
                $foreignOptions = $this->_findOptionsByTableName($modelOptions, $tableTo);
                $foreignObjectDefaultName[] = '\\'.$foreignOptions['model_namespace'].'\\'.Str::studly(Str::singular($tableTo));
            }
        }
        $foreignObjectName = Arr::get($relation, 'foreign_object_name', $foreignObjectDefaultName);
        $method = null;
        $methodParameters = array();
        $returnsArray = null;
        switch($relation['type']) {
            case 'no_relation':
                return null;
            case 'has_one':
                $methodDefaultName = Str::camel(Str::singular($methodNameBase));
                $fallbackMethodName = Str::camel(Str::singular($fallbackMethodNameBase));
                $method = 'hasOne';
                $returnsArray = false;
                if ( ! empty($relation['key_from'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainKeyTo($relation, null, null), 2 => $relation['key_from']);
                } elseif ( ! empty($relation['key_to'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $relation['key_to']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName);
                }
            break;
            case 'has_many':
                $methodDefaultName = Str::camel(Str::plural($methodNameBase));
                $fallbackMethodName = Str::camel(Str::plural($fallbackMethodNameBase));
                $method = 'hasMany';
                $returnsArray = true;
                if ( ! empty($relation['key_from'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainKeyTo($relation, null, null), 2 => $relation['key_from']);
                } elseif ( ! empty($relation['key_to'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $relation['key_to']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName);
                }
            break;
            case 'one_belongs_to_one':
            case 'many_belongs_to_one':
                $methodDefaultName = Str::camel(Str::singular($methodNameBase));
                $fallbackMethodName = Str::camel(Str::singular($fallbackMethodNameBase));
                $method = 'belongsTo';
                $returnsArray = false;
                if ( ! empty($relation['key_to'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainKeyFrom($relation, null, null), 2 => $relation['key_to']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainKeyFrom($relation, null, null));
                }
            break;
            case 'many_to_many':
                $methodDefaultName = Str::camel(Str::plural($methodNameBase));
                $fallbackMethodName = Str::camel(Str::plural($fallbackMethodNameBase));
                $method = 'belongsToMany';
                $returnsArray = true;
                $methodParameters = array(0 => $foreignObjectName, 1 => $relation['pivot_table'],
                    2 => $this->_obtainKeyTo($relation, null, null), 3 => $this->_obtainKeyFrom($relation));
            break;
            case 'polymorphic_belongs_to_one':
            case 'polymorphic_many_belongs_to_one':
            case 'polymorphic_one_belongs_to_one':
                $methodDefaultName = Str::camel(Str::singular($methodNameBase));
                $fallbackMethodName = Str::camel(Str::singular($fallbackMethodNameBase));
                $method = 'morphTo';
                $returnsArray = false;
                $methodParameters = array(0 => $methodNameBase, 1 => $this->_obtainPolymorphKeyType($relation), 2 => $this->_obtainKeyFrom($relation));
            break;
            case 'polymorphic_one_has_many':
                $methodDefaultName = Str::camel(Str::plural($methodNameBase));
                $fallbackMethodName = Str::camel(Str::plural($fallbackMethodNameBase));
                $method = 'morphMany';
                $returnsArray = true;
                if ( ! empty($relation['key_from'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $this->_obtainPolymorphKeyType($relation), 3 => $this->_obtainKeyTo($relation), 4 => $relation['key_from']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $this->_obtainPolymorphKeyType($relation), 3 => $this->_obtainKeyTo($relation));
                }
            break;
            case 'polymorphic_one_has_one':
                $methodDefaultName = Str::camel(Str::singular($methodNameBase));
                $fallbackMethodName = Str::camel(Str::singular($fallbackMethodNameBase));
                $method = 'morphOne';
                $returnsArray = false;
                if ( ! empty($relation['key_from'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $this->_obtainPolymorphKeyType($relation), 3 => $this->_obtainKeyTo($relation), 4 => $relation['key_from']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $this->_obtainPolymorphKeyType($relation), 3 => $this->_obtainKeyTo($relation));
                }
            break;
            case 'polymorphic_many_belongs_to_many':
                $methodDefaultName = Str::camel(Str::plural($methodNameBase));
                $fallbackMethodName = Str::camel(Str::plural($fallbackMethodNameBase));
                $method = 'morphToMany';
                $returnsArray = true;
                if ( ! empty($relation['key_from'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $relation['pivot_table'], 3 => $this->_obtainKeyTo($relation, null, null), 4 => $relation['key_from']);
                } elseif ( ! empty($relation['key_to'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $relation['pivot_table'], 3 => $relation['key_to']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $relation['pivot_table']);
                }
            break;
            case 'many_morphed_by_many':
                $methodDefaultName = Str::camel(Str::plural($methodNameBase));
                $fallbackMethodName = Str::camel(Str::plural($fallbackMethodNameBase));
                $method = 'morphedByMany';
                $returnsArray = true;
                if ( ! empty($relation['key_from'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $relation['pivot_table'], 3 => $this->_obtainKeyTo($relation, null, null), 4 => $relation['key_from']);
                } elseif ( ! empty($relation['key_to'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $relation['pivot_table'], 3 => $relation['key_to']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $this->_obtainPolymorphicRelationName($relation), 2 => $relation['pivot_table']);
                }
            break;
            case 'has_many_through':
                $methodDefaultName = Str::camel(Str::plural($methodNameBase));
                $fallbackMethodName = Str::camel(Str::plural($fallbackMethodNameBase));
                $method = 'hasManyThrough';
                $returnsArray = true;
                $throughOptions = $this->_findOptionsByTableName($modelOptions, $relation['table_through']);
                $throughNamespace = $throughOptions['model_namespace'];
                $throughObjectDefaultName = '\\'.$throughNamespace.'\\'.Str::studly(Str::singular($relation['table_through']));
                $throughObjectName = Arr::get($relation, 'through_object_name', $throughObjectDefaultName);
                if ( ! empty($relation['key_to'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $throughObjectName, 2 => $this->_obtainKeyThrough($relation), 3 => $relation['key_to']);
                } elseif ( ! empty($relation['key_through'])) {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $throughObjectName, 2 => $relation['key_through']);
                } else {
                    $methodParameters = array(0 => $foreignObjectName, 1 => $throughObjectName);
                }
            break;
            default:
                throw new \Exception("Unknown relation type '".$relation['type']."'");
        }
        $methodName = Arr::get($relation, 'method_name', $methodDefaultName);
        if ( ! empty($relation['method_name_for'][$relation['table_from']])) {
            $methodName = $relation['method_name_for'][$relation['table_from']];
        }
        if (( ! empty($relation['key_from'])) and ( ! empty($relation['method_name_for_key_from'][$relation['key_from']]))) {
            $methodName = $relation['method_name_for_key_from'][$relation['key_from']];
        }
        if (( ! empty($relation['key_to'])) and ( ! empty($relation['method_name_for_key_to'][$relation['key_to']]))) {
            $methodName = $relation['method_name_for_key_to'][$relation['key_to']];
        }
        $result = array(
            'name' => $methodName,
            'fallback_name' => $fallbackMethodName,
            'method' => $method,
            'returns_array' => $returnsArray,
            'foreign_model_name' => $foreignObjectName,
            'method_parameters' => $methodParameters,
            'relation'  => $relation,
            'hidden'    => empty($relation['hidden_from']) ? false : true,
            'with_timestamps' => empty($relation['with_timestamps']) ? false : true,
            'with_pivot' => empty($relation['with_pivot']) ?  false : $relation['with_pivot'],
            'order_by' => empty($relation['order_by']) ?  false : $relation['order_by'],
        );
        return $result;
    }

    private function _obtainPolymorphicRelationMethodNameBase($relation)
    {
        if ( ! empty($relation['table_to'])) {
            return $relation['table_to'];
        }
        return $this->_obtainPolymorphicRelationName($relation);
    }

    private function _obtainPolymorphicRelationName($relation)
    {
        $key = null;
        switch ($relation['type'])
        {
            case 'polymorphic_one_has_many':
            case 'polymorphic_one_has_one':
            case 'polymorphic_many_belongs_to_many':
                $key = $this->_obtainKeyTo($relation);
                break;
            case 'polymorphic_belongs_to_one':
            case 'polymorphic_many_belongs_to_one':
            case 'polymorphic_one_belongs_to_one':
            case 'many_morphed_by_many':
                $key = $this->_obtainKeyFrom($relation);
        }
        if ( ! empty($key)) {
            return preg_replace('#_id$#', '', $key);
        }
        throw new \Exception('_obtainPolymorphicRelationName(): Failed obtaining relation name');
    }

    private function _obtainPolymorphKeyType($relation)
    {
        switch ($relation['type'])
        {
            case 'polymorphic_belongs_to_one':
            case 'polymorphic_many_belongs_to_one':
            case 'polymorphic_one_belongs_to_one':
            case 'many_morphed_by_many':
                if ( ! empty($relation['key_from_type'])) {
                    return $relation['key_from_type'];
                }
                $keyFrom = $this->_obtainKeyFrom($relation);
                if ( ! empty($keyFrom)) {
                    return preg_replace('#_id$#', '', $keyFrom).'_type';
                }
                throw new \Exception('_obtainPolymorphType(): Failed obtaining type');
            case 'polymorphic_one_has_many':
            case 'polymorphic_one_has_one':
            case 'polymorphic_many_belongs_to_many':
                if ( ! empty($relation['key_to_type'])) {
                    return $relation['key_to_type'];
                }
                $keyTo = $this->_obtainKeyTo($relation);
                if ( ! empty($keyTo)) {
                    return preg_replace('#_id$#', '', $keyTo).'_type';
                }
                throw new \Exception('_obtainPolymorphType(): Failed obtaining type');

        }
        throw new \Exception('_obtainPolymorphType(): Failed obtaining type');
    }


    private function _assembleField($fieldDescription, $fieldKey = null)
    {
        if (is_array($fieldDescription)) {
            $defaults = array('name' => $fieldKey);
            $field = $this->_arrayMergeRecursiveNotNull($defaults, $fieldDescription);
            if (empty($field['name'])) {
                throw new \Exception('_assembleField(): Wrong schema format: field name empty.');
            }
            if (is_numeric($field['name'])) {
                throw new \Exception("_assembleField(): Wrong schema format: field name numeric. (maybe you have symbol ':' in description line)");
            }
        } elseif (is_string($fieldDescription)) {
            $field = $this->_parseModelFieldLine($fieldDescription);
        } else {
            throw new \Exception("Field in incorrect format (".var_export($fieldDescription, true).").");
        }
        if (empty($field['name'])) {
            throw new \Exception("Field with no identified name (".var_export($fieldDescription, true).").");
        }
        return $field;
    }

    private function _addPrimaryKey(array $fields, array $options)
    {
        $name = Arr::get($options, 'primary_key.name');
        $type = Arr::get($options, 'primary_key.type');
        if (empty($name) or 'none' === $type)
        {
            $this->info('Skipping PK.');
            return $fields;
        }
        $primaryKeyDescription = Arr::get($options, 'primary_key');
        $manualPrimaryKeySettings = Arr::get($fields, $name, array());
        $primaryKey = $this->_arrayMergeRecursiveNotNull($this->_assembleField($primaryKeyDescription, $name), $manualPrimaryKeySettings);
        //We want to have primary key in the beginning of a field set, so we do a little reordering
        $result = array();
        $result[$primaryKey['name']] = $primaryKey;
        foreach ($fields as $fieldName => $fieldValue)
        {
            if ($fieldName !== $primaryKey['name'])
            {
                $result[$fieldName] = $fieldValue;
            }
        }
        return $result;
    }

    /**
     * @param array $options
     * @throws \Exception
     * @return string
     */
    private function _parseDefaultLine(array &$options)
    {
        $key = strtolower(trim(array_shift($options)));
        $base = 1;
        if (in_array($key, array('negative', 'minus'))) {
            $base = -1;
            $key = strtolower(trim(array_shift($options)));
        }

        switch ($key) {
            case 'null':
                return null;
            case 'true':
                return true;
            case 'false':
                return false;
            case 'emptystring':
                return '';
            case 'empty':
                $key = strtolower(trim(array_shift($options)));
                if ('string' === $key) {
                    return '';
                } else {
                    throw new \Exception("If using keyword empty for default, it should be followed by word string - i.e. 'empty string'. Found: '".$key."'");
                }
            case 'int':
            case 'integer':
                $result = $base * intval(trim(array_shift($options)));
                return $result;
            case 'float':
                $result = $base * floatval(trim(array_shift($options)));
                return $result;
            case 'dash':
                return '-';
            case 'word':
                $result = trim(array_shift($options));
                return $result;
            case 'words':
                $result = implode(' ', $options);
                $options = array();
                return $result;
            case 'sentence':
                $result = implode(' ', $options).".";
                $options = array();
                return $result;
        }
        if (is_numeric($key))
        {
            if ($key == intval($key))
            {
                return $base * intval($key);
            }
            return $base * floatval($key);
        }
        if (empty($key)) {
            throw new \Exception("Description for default missing.");
        }
        throw new \Exception("Unknown default keyword '".$key."'");
    }

    private function _parseModelFieldLine($line)
    {
        // Pattern should match the following field description format:
        // field_name (type) field_option1 field_option2 field_option3 -> related_to_tables (relation_attributes) // some description // some comment 1 // some comment 2
        // field_name (type) field_option1 field_option2 field_option3 → related_to_tables (relation_attributes) // some description // some comment 1 // some comment 2
        // all parts except field name are optional
        $pattern = '#^ *([^ ()]*) *(\\(([^()]*)\\))? *([.a-zA-Z0-9 _]*) *((->|→) *([*a-zA-Z0-9_ ]*) *(\\(([^()]*)\\))? *)? *(//.*)?$#';

        $matches = array();
        preg_match($pattern, $line, $matches);
        if (empty($matches)) {
            throw new \Exception('Field line in incorrect format: '.$line);
        }
        $result['name'] = array_key_exists(1, $matches) ? trim($matches[1]) : null;
        $result['type'] = array_key_exists(3, $matches) ? trim($matches[3]) : null;
        $optionsString = array_key_exists(4, $matches) ? trim($matches[4]) : '';
        $options = explode(' ', preg_replace('/ +/',' ',$optionsString));
        while ($options) {
            $option = strtolower(trim(array_shift($options)));
            switch ($option) {
                case '':
                    // we are skipping empty options
                break;
                case "optional":
                    $result['required'] = false;
                break;
                case 'nullable':
                    $result['nullable'] = true;
                break;
                case 'null':
                    $result['required'] = false;
                    $result['nullable'] = true;
                break;
                case 'hidden':
                    $result['hidden'] = true;
                break;
                case 'unique':
                    $result['unique'] = true;
                break;
                case 'representative':
                    $result['representative'] = true;
                break;
                case 'min':
                    $value = intval(trim(array_shift($options)));
                    $rule = 'min:'.$value;
                    $result = $this->_addRuleToField($rule, $result);
                break;
                case 'max':
                    $value = intval(trim(array_shift($options)));
                    $rule = 'max:'.$value;
                    $result = $this->_addRuleToField($rule, $result);
                break;
                case 'guarded':
                    $result['fillable'] = false;
                    $result['guarded'] = true;
                break;
                case 'translate':
                    $result['translate'] = true;
                    break;
                case 'default':
                    $result['default'] = $this->_parseDefaultLine($options);
                break;
                case 'migration':
                case 'migration_setup':
                    $migration_setup = strtolower(trim(array_shift($options)));
                    if ('setup' == $migration_setup) {
                        $migration_setup = strtolower(trim(array_shift($options)));
                    }
                    $result['migration_setup'] = ( ! in_array($migration_setup,
                        array('skip', 'false', '0', 'no', 'none')));
                break;
                default:
                    throw new \Exception("Unrecognized option '".$option."' in line: ".$line);
            }
        }
        $result['related_to'] = array_key_exists(7, $matches) ? trim($matches[7]) : '';
        $result['relation_attributes'] = array_key_exists(9, $matches) ? trim($matches[9]) : null;
        $descriptionAndComments = array_key_exists(10, $matches) ? trim($matches[10]) : '';
        $comments = explode('//', $descriptionAndComments);
        array_shift($comments);
        $result['description'] = trim(array_shift($comments));
        $result['comments'] = array();
        foreach ($comments as $comment) {
            $comment = trim($comment);
            if ($comment) {
                $result['comments'][] = $comment;
            }
        }
        return $result;
    }

    private function _addRelationsToModelFields(array $modelFields, array $modelOptions, array $relations)
    {
        foreach ($relations as $typeSet) {
            foreach ($typeSet as $tableSet) {
                foreach ($tableSet as $relation) {
                    $this->_addRelationToModelFields($modelFields, $modelOptions, $relation);
                }
            }
        }
        return $modelFields;
    }

    private function _addRelationToModelFields(array &$modelFields, array $modelOptions, array $relation)
    {
        $fieldsToAdd = $this->_assembleFieldsFromRelation($modelFields, $modelOptions, $relation);
        foreach ($fieldsToAdd as $fieldAddress => $field) {
            Arr::set($modelFields, $fieldAddress, $field);

        }
    }

    private function _assembleFieldsFromRelation(array $modelFields, array $modelOptions, array $relation)
    {
        $type = $relation['type'];
        if (in_array($type, array('polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one', 'polymorphic_belongs_to_one'))) {
            return $this->_assembleFieldsFromPolymorphicBelongsToRelation($modelFields, $modelOptions, $relation);
        } elseif (in_array($type, array('one_belongs_to_one', 'many_belongs_to_one'))) {
            return $this->_assembleFieldsFromBelongsToRelation($modelFields, $modelOptions, $relation);
        }
        return array();
    }

    private function _assembleFieldsFromPolymorphicBelongsToRelation(array $modelFields, array $modelOptions, array $relation)
    {
        $fieldFromName = $this->_obtainKeyFrom($relation, 'key_from');
        $fieldFromAddress = $this->_findFieldAddress($modelFields, $modelOptions, $relation['table_from'], $fieldFromName);
        if ($fieldFromAddress) {
            $fieldFrom = Arr::get($modelFields, $fieldFromAddress);
            if (('biginteger' !== $fieldFrom['db_type'])) {
                $this->info("Increasing db_type: from '".$fieldFrom['db_type']."' to 'biginteger' in '".$relation['table_from']."':'".$fieldFrom['name']."'");
                $fieldFrom['db_type'] = 'biginteger';
                $fieldFrom['type'] = 'unsigned big integer';
            }
            if (($fieldFrom['unsigned'])) {
                $this->info("Switching unsigned to true in '".$relation['table_from']."':'".$fieldFrom['name']."'");
                $fieldFrom['unsigned'] = true;
                $fieldFrom['type'] = 'unsigned big integer';
            }
        } else {
            $fieldFrom = array(
                'name' => $fieldFromName,
                'type' => 'big integer',
                'nullable' => true,
                'required' => false,
            );
            $fieldFrom = $this->_processField($fieldFrom, $relation['table_from'], $modelFields, $modelOptions);
            $fieldFromAddress = $this->_findKeyByTableName($modelOptions, $relation['table_from'], true).'.'.$fieldFrom['name'];
            $this->info("Creating new field '".$relation['table_from']."':'".$fieldFrom['name']."' because of polymorphic belongsTo relation.");
        }
        $fieldFrom['relation'] = $relation;

        $fieldFromTypeName = $this->_obtainPolymorphKeyType($relation);
        $fieldFromTypeAddress = $this->_findFieldAddress($modelFields, $modelOptions, $relation['table_from'], $fieldFromTypeName);
        if ($fieldFromTypeAddress) { //modifying existing field
            $fieldFromType = Arr::get($modelFields, $fieldFromTypeAddress);
            if (('varchar' !== $fieldFromType['db_type'])) {
                $this->info("Switching db_type: from '".$fieldFromType['db_type']."' to 'varchar' in '".$relation['table_from']."':'".$fieldFromType['name']."'");
                $fieldFromType['db_type'] = 'varchar';
                $fieldFromType['type'] = 'string';
            }
            if ($fieldFromType['db_length'] and (100 > $fieldFromType['db_length'])) {
                $this->info("Increasing db_length: from '".$fieldFromType['db_length']."' to 100 in '".$relation['table_from']."':'".$fieldFromType['name']."'");
                $fieldFromType['db_length'] = 100;
                $fieldFromType['type'] = 'string 100';
            }
            if ($fieldFromType['nullable'] !== $fieldFrom['nullable']) {
                $this->info("Synchronizing nullable (to ".$fieldFrom['nullable'] ? 'true' : 'false'.") of '".$relation['table_from']."':'".$fieldFromType['name']."' with '".$relation['table_from']."':'".$fieldFrom['name']."'");
                $fieldFromType['nullable'] = $fieldFrom['nullable'];
            }
            if ($fieldFromType['required'] !== $fieldFrom['required']) {
                $this->info("Synchronizing required (to ".$fieldFrom['required'] ? 'true' : 'false'.") of '".$relation['table_from']."':'".$fieldFromType['name']."' with '".$relation['table_from']."':'".$fieldFrom['name']."'");
                $fieldFromType['required'] = $fieldFrom['required'];
            }
            if (empty($fieldFromType['modification_rules_after_validation'])) {
                $fieldFromType['modification_rules_after_validation'] = array();
            }
            if ( ! is_array($fieldFromType['modification_rules_after_validation'])) {
                $fieldFromType['modification_rules_after_validation'] = explode('|', $fieldFromType['modification_rules_after_validation']);
            }
            if ( ! in_array('non_printable_to_null', $fieldFromType['modification_rules_after_validation'], true)) {
                $this->info("Adding rule 'non_printable_to_null' to 'modification_rules_after_validation' in '".$relation['table_from']."':'".$fieldFromType['name']."'");
                $fieldFromType['modification_rules_after_validation'][] = 'non_printable_to_null';
            }
        } else {
            $fieldFromType = array(
                'name' => $fieldFromTypeName,
                'type' => 'string',
                'nullable' => ! empty($fieldFrom['nullable']),
                'required' => ! empty($fieldFrom['required']),
                'modification_rules_after_validation' => array('non_printable_to_null'),
            );
            $fieldFromType = $this->_processField($fieldFromType, $relation['table_from'], $modelFields, $modelOptions);
            $fieldFromTypeAddress = $this->_findKeyByTableName($modelOptions, $relation['table_from'], true).'.'.$fieldFromType['name'];
            $this->info("Creating new field '".$relation['table_from']."':'".$fieldFromType['name']."' because of polymorphic belongsTo relation.");
        }
        return array($fieldFromAddress => $fieldFrom, $fieldFromTypeAddress => $fieldFromType);
    }


    private function _assembleFieldsFromBelongsToRelation(array $modelFields, array $modelOptions, array $relation)
    {
        $keyTo = Arr::get($relation, 'key_to');
        if ($keyTo) {
            $fieldTo = $this->_findFieldByTableAndFieldName($modelFields, $modelOptions, $relation['table_to'], $keyTo, true);
        } else {
            $fieldTo = $this->_findPrimaryKeyField($modelFields, $modelOptions, $relation['table_to'], true);
        }
        $fieldFromName = Arr::get($relation, 'key_from', $this->_foreignKeyFromTable($relation['table_to']));
        $fieldFromAddress = $this->_findFieldAddress($modelFields, $modelOptions, $relation['table_from'], $fieldFromName);
        if ($fieldFromAddress) {
            $fieldFrom = Arr::get($modelFields, $fieldFromAddress);
            $newField = $this->_addSynchronizeForRelation($relation, $fieldFrom, $fieldTo);
            $newFieldAddress = $fieldFromAddress;
        } else {
            $newField = $this->_assembleFieldFromRelation($modelFields, $modelOptions, $relation, $fieldTo);
            $newFieldAddress = $this->_findKeyByTableName($modelOptions, $relation['table_from'], true).'.'.$newField['name'];
        }
        if (false === array_search($newField['db_type'], array('integer', 'biginteger'))) {
            $this->info("Increasing db_type: from '".$newField['db_type']."' to 'integer' in '".$relation['table_from']."':'".$newField['name']."'");
            $newField['db_type'] = 'integer';
        }
        $newField['relation'] = $relation;
        if ('one_belongs_to_one' === $relation['type']) {
            $newField['unique'] = true;
        }
        $dbForeignKey = array('table_to' => $relation['table_to'], 'key_to' => $fieldTo['name']);
        if ( ! empty($relation['db_foreign_key_on_delete'])) {
            $dbForeignKey['on_delete'] = $relation['db_foreign_key_on_delete'];
        }
        $newField['db_foreign_key'] = $dbForeignKey;
        return array($newFieldAddress => $newField);
    }

    private function _addSynchronizeForRelation(array $relation, array $field, array $fieldTo)
    {
        if (empty($field)) {
            throw new \Exception("_addSynchronizeForRelation: Can not add relation to an empty field.");
        }
        if ( ! empty($field['primary'])) {
            throw new \Exception("_addSynchronizeForRelation: Can not add relation to primary key '".$field['name']."'");
        }
        if (false === array_search($field['db_type'], array('integer', 'biginteger', 'mediuminteger', 'smallinteger', 'tinyinteger'))) {
            throw new \Exception("_addSynchronizeForRelation: Wrong type defined for foreign key '".$field['name']."'");
        }
        if ($field['db_type'] !== $fieldTo['db_type']) {
            $this->info("Synchronizing db_type: from '".$field['db_type']."' to '".$fieldTo['db_type']."' because field '".$relation['table_from']."':'".$field['name']."' is related to '".$relation['table_to']."':'".$fieldTo['name']."'");
            $field['db_type'] = $fieldTo['db_type'];
        }
        if ($field['unsigned'] !== $fieldTo['unsigned']) {
            $this->info("Synchronizing unsigned: from ".$this->_boolAsString($field['unsigned'])." to ".$this->_boolAsString($fieldTo['unsigned'])." because field '".$relation['table_from']."':'".$field['name']."' is related to '".$relation['table_to']."':'".$fieldTo['name']."'");
            $field['unsigned'] = $fieldTo['unsigned'];
        }
        return $field;
    }



    private function _findFieldByTableAndFieldName(array $modelFields, array $modelOptions, $tableName, $fieldName, $throwExceptionIfNotFound = false)
    {
        $modelKey = $this->_findKeyByTableName($modelOptions, $tableName, $throwExceptionIfNotFound);
        if (empty($modelKey)) {
            return null;
        }
        $fields = $modelFields[$modelKey];
        foreach ($fields as $field) {
            if ($fieldName === $field['name']) {
                return $field;
            }
        }
        if ($throwExceptionIfNotFound) {
            throw new \Exception("Can not find field with name '".$fieldName."' for table '".$tableName."'");
        }
        return null;
    }

    private function _findFieldAddress(array $modelFields, array $modelOptions, $tableName, $fieldName)
    {
        $modelKey = $this->_findKeyByTableName($modelOptions, $tableName);
        if (empty($modelKey)) {
            return null;
        }
        $fieldKey = $this->_findKeyByFieldName($modelFields[$modelKey], $fieldName);
        if (empty($fieldKey)) {
            return null;
        }
        $result = $modelKey.'.'.$fieldKey;
        return $result;
    }


    private function _assembleFieldFromRelation($modelFields, $modelOptions, $relation, $fieldTo)
    {
        $suggestedFieldName = Arr::get($relation, 'key_from', $this->_foreignKeyFromTable($relation['table_to']));
        $modelToKey = $this->_findKeyByTableName($modelOptions, $relation['table_to'], true);
        $modelFromKey = $this->_findKeyByTableName($modelOptions, $relation['table_from'], true);
        if ( ! empty($modelFields[$modelFromKey][$suggestedFieldName])) {
            throw new \Exception("_assembleFieldFromRelation: Can not add new field, as field key: '".$suggestedFieldName."' is already used (model key: '".$modelFromKey."')");
        }
        $result = array_only($fieldTo, array('type', 'type_hint', 'db_type', 'db_length', 'db_scale', 'unsigned', 'enum_list'));
        $result['name'] = $suggestedFieldName;
        $result['required'] = false;
        $result['nullable'] = true;
        $result['description'] = "Foreign key to '".strtolower(Arr::get($fieldTo, 'title', 'Field without title'))
                ."' in '".Arr::get($modelOptions, $modelToKey.'.singular', 'Model without title')."' ("
                .Arr::get($modelOptions, $modelToKey.'.model_name', 'UNKNOWN')."::".Arr::get($fieldTo, 'name', 'UNKNOWN').")";
        $result['comments'] = array('Automatically generated using relation.');
        $result = $this->_processField($result, $relation['table_from'], $modelFields, $modelOptions);
        return $result;
    }


    private function _assemblePivotTables(array $modelFields, array $modelOptions, array $relations)
    {
        $relationStack = array();
        foreach ($relations as $typeKey => $typeSet) {
            if (empty($typeSet)) {
                continue;
            }
            if ( ! in_array($typeKey, array('many_to_many', 'polymorphic_many_belongs_to_many', 'many_morphed_by_many'))) {
                continue;
            }
            foreach ($typeSet as $tableSet) {
                foreach ($tableSet as $relation) {
                    $relationStack[] = $relation;
                }
            }
        }
        $result = array();
        foreach ($relationStack as $relation) {
            $pivotTableName = $relation['pivot_table'];
            if ( ! empty($modelOptions[$pivotTableName])) {
                if ($pivotTableName !== $modelOptions[$pivotTableName]['table_name']) {
                    throw new \Exception ("Under key '".$pivotTableName."' is defined model with table_name '".$modelOptions[$pivotTableName]['table_name']."'");
                } else {
                    $this->info("Pivot table '".$pivotTableName."' is already defined. Skipping.");
                    continue;
                }
            }
            $options = empty($result[$pivotTableName]) ? array() : $result[$pivotTableName];
            $fields = empty($options['fields']) ? array() : $options['fields'];
            $options = $this->_arrayMergeRecursiveNotNull($options, $this->_assemblePivotTableOptions($relation));
            $fields = $this->_arrayMergeRecursiveNotNull($fields, $this->_assemblePivotTableFields($modelFields, $modelOptions, $relation));
            $options['fields'] = $fields;
            $result[$pivotTableName] = $options;
        }
        return $result;
    }

    private function _assemblePivotTableOptions(array $relation)
    {
        $result = array(
            'table_name' => $relation['pivot_table'],
            'migration_timestamps' => (empty($relation['with_timestamps']) ? false : true),
            'generate' => array('migration' => 'overwrite'),
            'is_pivot_table' => array(
                'type' => empty($relation['polymorphic']) ? 'simple' : 'polymorphic',
                'relation' => $relation,
            ),
        );
        return $result;
    }

    private function _assemblePivotTableFields(array $modelFields, array $modelOptions, array $relation)
    {
        $defaultFieldOptions = array(
            'migration_setup'   => true,
            'primary'           => false,
            'type'              => 'big int',
            'db_type'           => 'biginteger',
            'db_length'         => null,
            'db_scale'          => null,
            'db_foreign_key'    => null,
            'enum_list'         => array(),
            'unsigned'          => true,
            'nullable'          => false,
        );
        $idField = $defaultFieldOptions;
        $idField['name'] = 'id';
        $idField['primary'] = true;

        $toField = $defaultFieldOptions;
        $toField['name'] = $this->_obtainKeyTo($relation, $modelFields, $modelOptions);
        if ('polymorphic_many_belongs_to_many' !== $relation['type']) {
            $toFieldPrototype = $this->_findPrimaryKeyField($modelFields, $modelOptions, $relation['table_from'], true);
            if ('integer' === $toFieldPrototype['db_type']) {
                $toField['db_type'] = 'integer';
            }
            $toField['unsigned'] = $toFieldPrototype['unsigned'];
            $toField['db_foreign_key'] = array('table_to' => $relation['table_from'], 'key_to' => $toFieldPrototype['name'], 'on_delete' => 'cascade');
        }

        $fromField = $defaultFieldOptions;
        $fromField['name'] = $this->_obtainKeyFrom($relation, $modelFields, $modelOptions);
        if ('many_morphed_by_many' !== $relation['type']) {
            $fromFieldPrototype = $this->_findPrimaryKeyField($modelFields, $modelOptions, $relation['table_to'], true);
            if ('integer' === $fromFieldPrototype['db_type']) {
                $fromField['db_type'] = 'integer';
            }
            $fromField['unsigned'] = $fromFieldPrototype['unsigned'];
            $fromField['db_foreign_key'] = array('table_to' => $relation['table_to'], 'key_to' => $fromFieldPrototype['name'], 'on_delete' => 'cascade');
        }
        $result = array(
            $idField['name'] => $idField,
            $toField['name'] => $toField,
            $fromField['name'] => $fromField,
        );
        if ('many_to_many' !== $relation['type']) {
            $typeField = $defaultFieldOptions;
            $typeField['name'] = $this->_obtainPolymorphKeyType($relation);
            $typeField['type'] = 'string';
            $typeField['db_type'] = 'varchar';
            $typeField['unsigned'] = null;
            $result[$typeField['name']] = $typeField;
        }
        if ( ! empty($relation['ordering_field_name'])) {
            $orderingField = $defaultFieldOptions;
            $orderingField['name'] = $relation['ordering_field_name'];
            $orderingField['db_type'] = 'biginteger';
            $orderingField['nullable'] = true;
            $result[$orderingField['name']] = $orderingField;
        }
        return $result;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('input_file', InputArgument::OPTIONAL, 'File used for input schema.', $this::SCHEMA_DIR.'schema.yml'),
            array('output_file', InputArgument::OPTIONAL, 'File used for output schema.', $this::SCHEMA_DIR.'parsed_schema.yml'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('stdin', null, InputOption::VALUE_NONE, 'Use stdin instead of file (Not implemented).', null),
        );
    }

}
