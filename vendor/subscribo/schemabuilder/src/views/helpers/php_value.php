<?php

if (is_array($value) and empty($value))
{
    echo "array()";
} elseif (is_array($value)) {
    echo "array(\n";
    $indent = isset($indent) ? $indent : 0;
    $data = array(
        'arr' => $value,
        'indent' => ($indent + 1),
    );
    echo str_repeat('    ', ($indent + 1));
    echo View::make('schemabuilder::helpers.php_array', $data)->render();
    echo str_repeat('    ', $indent);
    echo ")";
} elseif (is_bool($value)) {
    echo $value ? 'true' : 'false';
} elseif (is_null($value)) {
    echo 'null';
} elseif (is_int($value) or is_float($value)) {
    echo $value.'';
} else {
    echo "'".addslashes($value)."'";
}
