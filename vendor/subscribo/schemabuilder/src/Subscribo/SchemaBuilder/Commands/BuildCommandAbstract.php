<?php namespace Subscribo\SchemaBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

abstract class BuildCommandAbstract extends Command {

    const CONFIG_DIR = 'config/';
    const PACKAGES_CONFIG_DIR = 'subscribo/config/packages/';
    const MIGRATIONS_DIR = 'database/migrations/';
    const SCHEMA_DIR = 'subscribo/config/packages/schemabuilder/';


    protected function _createFile($filePath, $content, $mode = 'exception')
    {
        $directories = explode('/', $filePath);
        $fileName = array_pop($directories);
        if ($directories) {
            $path = implode('/', $directories);
            $this->_checkOrCreateDirectory($path);
        }
        if (file_exists($filePath))
        {
            switch ($mode) {
                case 'exception':
                    throw new \Exception('File "'.$filePath.'" already exists.');
                case 'overwrite':
                    $this->info('File "'.$filePath.'" already exists. Overwriting.');
                    continue;
                case 'skip':
                    $this->info('File "'.$filePath.'" already exists. Skipping.');
                    return;
                default:
                    throw new \Exception('File "'.$filePath.'" already exists. Unknown mode: "'.$mode.'"');
            }
        }
        $contentString = is_object($content) ? $content->__toString() : ((string) $content);
        file_put_contents($filePath, $contentString);
    }

    protected function _checkOrCreateDirectory($directory)
    {
        if ( ! file_exists($directory)) {
            if (mkdir($directory, 0777, true)) {
                $this->info("Directory '".$directory."' created.");
            } else {
                throw new \Exception("Attempt to create directory '".$directory."' have failed");
            }
        }
        if ( ! is_dir($directory)) {
            throw new \Exception("'".$directory."' is not a directory");
        }
        return true;
    }

    /**
     * Removes not only spaces in the beginning and in the end, but also multiple successive whitespace characters changes into one space
     * @param string $value
     * @return string
     */
    protected function _extraTrim($value)
    {
        $pattern = '#\\s+#';
        $normalized = preg_replace($pattern, ' ', $value);
        $trimmed = trim($normalized);
        return $trimmed;
    }


    protected function _acquireKeyword(array &$haystack, array $keywords, $autoLowercase = true, $autoExtraTrim = true)
    {
        $first = reset($haystack);
        if (false !== array_search($first, $keywords)) {
            return array_shift($haystack);
        }
        if ($autoLowercase) {
            $first = strtolower($first);
        }
        if ($autoExtraTrim) {
            $first = $this->_extraTrim($first);
        }
        if (false !== array_search($first, $keywords)) {
            return array_shift($haystack);
        }
        $haystackAsString = implode(' ', $haystack);
        $haystackAsString = $this->_extraTrim($haystackAsString).' ';
        usort($keywords, function ($x, $y) {return strlen($y) - strlen($x); } );
        foreach ($keywords as $keyword) {
            $pattern = "#^".strtr($keyword, array('_' => ' '))." #i";
            $haystackAsString = preg_replace($pattern, ($keyword." "), $haystackAsString, 1);
        }
        $tmpHaystack = explode(' ', trim($haystackAsString));
        $found = false;
        $first = reset($tmpHaystack);
        if (false !== array_search($first, $keywords)) {
            $found = true;
        }
        if ($autoLowercase) {
            $first = strtolower($first);
        }
        if ($autoExtraTrim) {
            $first = $this->_extraTrim($first);
        }
        if (false !== array_search($first, $keywords)) {
            $found = true;
        }
        if ( ! $found) {
            return null;
        }
        $keywordParts =  explode('_', $first);
        foreach ($keywordParts as $keywordPart) {
            $fromHaystack = array_shift($haystack);
            if ($keywordPart !== $fromHaystack) {
                throw new \Exception("Keyword part does not match haystack item.");
            }
        }
        return $first;
    }

    /**
     * Recursively adds fields from array1 to array2 on places, where these are not set
     * Second array have priority
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function _arrayMergeRecursiveNotNull(array $array1, array $array2)
    {
        $result = $array2;
        foreach ($array1 as $key => $value)
        {
            if ( ! isset($result[$key])) //If the original have either null or not set at all on this position
            {
                $result[$key] = $value;
            } else {
                $original = $result[$key];
                if (is_array($value) and is_array($original)) {
                    $result[$key] = $this->_arrayMergeRecursiveNotNull($value, $original);
                }
            }
        }
        return $result;
    }

    /**
     * Adds a value to the beginning of an array, if the value is not already present in the array
     * @param $value
     * @param array $arr
     */
    protected function _unshiftValueIfNotPresent($value, array &$arr)
    {
        if (false === array_search($value, $arr)) {
            array_unshift($arr, $value);
        }
    }

    /**
     * Adds a value to the end of an array, if the value is not already present in the array
     * @param $value
     * @param array $arr
     */
    protected function _pushValueIfNotPresent($value, array &$arr)
    {
        if (false === array_search($value, $arr)) {
            $arr[] = $value;
        }
    }


    protected function _modelNameFromTable($tableName)
    {
        return studly_case(str_singular($tableName));
    }


    protected function _foreignKeyFromTable($tableName)
    {
        $singular = str_singular($tableName);
        $keyFrom = $singular."_id";
        return $keyFrom;
    }

    protected function _findKeyByTableName(array $modelOptions, $tableName, $throwExceptionIfNotFound = false)
    {
        foreach ($modelOptions as $key => $options) {
            if ($tableName === $options['table_name']) {
                return $key;
            }
        }
        if ($throwExceptionIfNotFound) {
            throw new \Exception("_findKeyByTableName: Can not find key for table '".$tableName."'");
        }
        return null;
    }

    protected function _findOptionsByTableName(array $modelOptions, $tableName, $throwExceptionIfNotFound = false)
    {
        foreach ($modelOptions as $key => $options) {
            if ($tableName === $options['table_name']) {
                return $options;
            }
        }
        if ($throwExceptionIfNotFound) {
            throw new \Exception("_findOptionsByTableName: Can not find key for table '".$tableName."'");
        }
        return array();
    }

    protected function _findKeyByFieldName(array $fields, $fieldName, $throwExceptionIfNotFound = false)
    {
        foreach ($fields as $key => $field) {
            if ($fieldName === $field['name']) {
                return $key;
            }
        }
        if ($throwExceptionIfNotFound) {
            throw new \Exception("_findKeyByFieldName: Can not find key for field '".$fieldName."'");
        }
        return null;
    }

    protected function _collectTableNames(array $modelOptions)
    {
        $result = array();
        foreach($modelOptions as $modelKey => $options)
        {
            $result[$options['table_name']] = $modelKey;
        }
        return $result;
    }

    protected function _boolAsString($value)
    {
        if ($value) {
            return 'true';
        } else {
            return 'false';
        }
    }

    /**
     * @param string|bool|int $value
     * @param string $throwException
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function _valueAsBool($value, $throwException = 'Value is not a string representation of a boolean value.')
    {
        if (is_bool($value)) {
            return $value;
        }
        $value = trim(strtolower($value));
        if (empty($value))
        {
            return false;
        }
        if (in_array($value, array('true', 'on', 'yes', '1', 1), true))
        {
            return true;
        }
        if (in_array($value, array('false', 'off', 'no'), true))
        {
            return false;
        }
        if ($throwException) {
            throw new \InvalidArgumentException($throwException);
        }
        return false;
    }

    protected function _findPrimaryKeyField(array $modelFields, array $modelOptions, $tableName, $throwExceptionIfNotFound = false)
    {
        $modelKey = $this->_findKeyByTableName($modelOptions, $tableName, $throwExceptionIfNotFound);
        if (empty($modelKey)) {
            return null;
        }
        $fields = $modelFields[$modelKey];
        $primaryField = $this->_findPrimaryKeyFromFields($fields);
        if ($primaryField) {
            return $primaryField;
        }
        if ($throwExceptionIfNotFound) {
            throw new \Exception("Can not find primary key for table '".$tableName."' Fields with name id or primary not found");
        }
        return null;
    }

    protected function _findPrimaryKeyFromFields($fields, $default = null)
    {
        foreach ($fields as $field) {
            if ( ! empty($field['primary'])) {
                return $field;
            }
        }
        foreach ($fields as $field) {
            if ('id' === $field['name']) {
                return $field;
            }
        }
        return $default;
    }

    /**
     * Obtain a foreign key or parent key
     * (key defined on other table; for many to many relations a key defined on pivot table pointing to this table)
     *
     * @param array $relation
     * @param array|null|false $modelFields
     * @param array|null $modelOptions
     * @param bool|string $forTable
     * @throws \Exception
     * @return string|false|null
     */
    protected function _obtainKeyTo(array $relation, $modelFields = null, $modelOptions = null, $forTable = false)
    {
        if ( ! empty($relation['key_to'])) {
            return $relation['key_to'];
        }
        if ($forTable  and ( ! empty($relation['keys_to'][$forTable]))) {
            return $relation['keys_to'][$forTable];
        }
        switch($relation['type']) {
            case 'no_relation':
                return null;
            case 'has_one':
            case 'has_many':
            case 'many_to_many':
            case 'many_morphed_by_many': //Many to many polymorphic relation, type on pivot table is might refer to other model (it is expected, that this is the table related to multiple other tables (e.g. this is tags table))
                return Str::singular($relation['table_from']).'_id';
            case 'one_belongs_to_one':
            case 'many_belongs_to_one':
                if (false === $modelFields) {
                    return false;
                }
                if (is_null($modelFields) or (is_null($modelOptions))) {
                    throw new \Exception('_obtainKeyTo: $modelFields parameter should contain an array or be boolean false. $modelOptions should be an array if $modelFields not empty.');
                }
                $primaryKeyField = $this->_findPrimaryKeyField($modelFields, $modelOptions, $relation['table_to'], true);
                $primaryKeyName = $primaryKeyField ? $primaryKeyField['name'] : 'id';
                return $primaryKeyName;
            case 'polymorphic_one_has_one':       //(One) has one polymorphic (keys on other table)
            case 'polymorphic_one_has_many':       //(One) has many polymorphic (keys on other table)
            case 'polymorphic_many_belongs_to_many':   //Many to many polymorphic relation,  type on pivot table might refer to this model
                return $this->_assemblePolymorphicForeignKeyName($relation['table_to']);
            case 'polymorphic_one_belongs_to_one': //One belongs to one polymorphic (keys on this table)
            case 'polymorphic_many_belongs_to_one': //Many belongs to one polymorphic (keys on this table)
            case 'polymorphic_belongs_to_one':  //One belongs to one OR Many belongs to one (keys on this table) - we do not know the type of relation on other tables (it may be even one on one table and many on other table) so reverse relation are not automatically created
                if (false === $modelFields) {
                    return false;
                }
                if (is_null($modelFields) or (is_null($modelOptions) or ( ! $forTable))) {
                    throw new \Exception('_obtainKeyFrom: $modelFields parameter should contain an array or be boolean false. $modelOptions should be an array if $modelFields not empty $forTable need to be set if dealing with polymorphic relations.');
                }
                $primaryKeyField = $this->_findPrimaryKeyField($modelFields, $modelOptions, $forTable, true);
                $primaryKeyName = $primaryKeyField ? $primaryKeyField['name'] : 'id';
                return $primaryKeyName;
            case 'has_many_through':
                return Str::singular($relation['table_through']).'_id';
            default:
                throw new \Exception("Unknown relation type '".$relation['type']."'");
        }
    }

    /**
     * Obtain a Key Through for Has Many Through relation
     * (key defined on through table pointing to this (from) table
     *
     * @param array $relation
     * @throws \Exception
     * @return string
     */
    protected function _obtainKeyThrough(array $relation)
    {
        if ( ! empty($relation['key_through'])) {
            return $relation['key_through'];
        }
        if ('has_many_through' !== $relation['type']) {
            throw new \Exception("Attempt to obtain Key Through on relation type '".$relation['type']."'");
        }
        return Str::singular($relation['table_from']).'_id';
    }

    protected function _assemblePolymorphicForeignKeyName($tableName)
    {
        return Str::singular($tableName).'_attachable_id';
    }

    /**
     * Obtain a local key or other key
     * (key defined on local table; for many to many relations a key defined on pivot table pointing to another table)
     *
     * @param array $relation
     * @param array|null|false $modelFields
     * @param array|null $modelOptions
     * @param bool|string $forTable
     * @throws \Exception
     * @return string|null|false
     */
    protected function _obtainKeyFrom(array $relation, $modelFields = null, $modelOptions = null, $forTable = false)
    {
        if ( ! empty($relation['key_from'])) {
            return $relation['key_from'];
        }
        if ($forTable  and ( ! empty($relation['keys_from'][$forTable]))) {
            return $relation['keys_from'][$forTable];
        }
        switch($relation['type']) {
            case 'no_relation':
                return null;
            case 'has_one':
            case 'has_many':
            case 'has_many_through':
                if (false === $modelFields) {
                    return false;
                }
                if (is_null($modelFields) or (is_null($modelOptions))) {
                    throw new \Exception('_obtainKeyFrom: $modelFields parameter should contain an array or be boolean false. $modelOptions should be an array if $modelFields not empty.');
                }
                $primaryKeyField = $this->_findPrimaryKeyField($modelFields, $modelOptions, $relation['table_from'], true);
                $primaryKeyName = $primaryKeyField ? $primaryKeyField['name'] : 'id';
                return $primaryKeyName;
            case 'one_belongs_to_one':
            case 'many_belongs_to_one':
            case 'many_to_many':
            case 'polymorphic_many_belongs_to_many':   //Many to many polymorphic relation,  type on pivot table might refer to this model
                return Str::singular($relation['table_to']).'_id';
            case 'polymorphic_one_has_one':       //(One) has one polymorphic (keys on other table)
            case 'polymorphic_one_has_many':       //(One) has many polymorphic (keys on other table)
                if (false === $modelFields) {
                    return false;
                }
                if (is_null($modelFields) or (is_null($modelOptions) or ( ! $forTable))) {
                    throw new \Exception('_obtainKeyFrom: $modelFields parameter should contain an array or be boolean false. $modelOptions should be an array if $modelFields not empty $forTable need to be set if dealing with polymorphic relations.');
                }
                $primaryKeyField = $this->_findPrimaryKeyField($modelFields, $modelOptions, $forTable, true);
                $primaryKeyName = $primaryKeyField ? $primaryKeyField['name'] : 'id';
                return $primaryKeyName;
            case 'polymorphic_one_belongs_to_one': //One belongs to one polymorphic (keys on this table)
            case 'polymorphic_many_belongs_to_one': //Many belongs to one polymorphic (keys on this table)
            case 'polymorphic_belongs_to_one':  //One belongs to one OR Many belongs to one (keys on this table) - we do not know the type of relation on other tables (it may be even one on one table and many on other table) so reverse relation are not automatically created
            case 'many_morphed_by_many': //Many to many polymorphic relation, type on pivot table is might refer to other model (it is expected, that this is the table related to multiple other tables (e.g. this is tags table))
                return $this->_assemblePolymorphicForeignKeyName($relation['table_from']);
            default:
                throw new \Exception("Unknown relation type '".$relation['type']."'");
        }
    }

}
