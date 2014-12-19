<?php namespace Subscribo\SchemaBuilder\Helpers;


class MyStr {
    /**
     * Replacing php comment special combinations of * and /
     *
     * @param string|array $value
     * @param bool|string|array $replacement (if set to true (default), then these values are provided: array(' COMMENT END ', ' COMMENT START ', ' COMMENT START '))
     *        Note: Using empty values, or values containing * or / for replacement is not recommended, as it can lead to values being not properly sanitized
     *
     * @return string|array
     */
    public static function sanitizeForComment($value, $replacement = true) {
        if (true === $replacement) {
            $replacement = array(' COMMENT END ', ' COMMENT START ', ' COMMENT START ');
        }
        $result = str_replace(array('*/', '/*', '//'), $replacement, $value);
        return $result;
    }
}
