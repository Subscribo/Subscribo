<?php namespace Subscribo\SchemaBuilder\Helpers;

use Exception;

class DbIdentifier {

    private static $_counter = array();

    static public function forge($keyword, $tableName = null, $fieldName = null, $maxLength = 64) {
        $parts = array();
        if ($tableName) {
            $parts[] = $tableName;
        }
        if ($fieldName) {
            $parts[] = $fieldName;
        }
        if ($keyword) {
            $parts[] = $keyword;
        }
        $identifierSuggestion = implode('_', $parts);
        if (strlen($identifierSuggestion) <= $maxLength) {
            return $identifierSuggestion;
        }
        if ( ! array_key_exists($keyword, self::$_counter)) {
            self::$_counter[$keyword] = 0;
        }
        self::$_counter[$keyword] = self::$_counter[$keyword] + 1;
        $base = '_'.$keyword.'_'.((string) self::$_counter[$keyword]);
        $start = $tableName.'_'.$fieldName;
        $allowedStartLength = $maxLength - strlen($base);
        if ($allowedStartLength < 5) {
            throw new \Exception('Can not create identifier. Base too long.');
        }
        $identifier = substr($start, 0, $allowedStartLength).$base;
        return $identifier;
    }
}
