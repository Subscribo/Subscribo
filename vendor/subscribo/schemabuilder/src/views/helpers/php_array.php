<?php
$assoc = \Fuel\Core\Arr::is_assoc($arr);
$newLine = false;
foreach ($arr as $key => $value) {
    if ($newLine)
    {
        echo "\n";
    }
    $newLine = false;
    echo str_repeat('    ', $indent);
    if ($assoc) {
        if (is_int($key) or is_float($key)) {
            echo $key;
        } else {
            echo "'".addslashes($key)."'";
        }
        echo " => ";
    }
    if (is_array($value) and empty($value))
    {
        echo "array(),";
    } elseif (is_array($value)) {
        echo "array (\n";
        $data = array(
            'arr' => $value,
            'indent' => $indent + 1,
        );
        echo str_repeat('    ', $indent + 1);
        echo View::make('schemabuilder::helpers.php_array', $data)->render();
        echo str_repeat('    ', $indent);
        echo "),";
        $newLine = true;
    } elseif (is_bool($value)) {
        echo $value ? 'true,' : 'false,';
    } elseif (is_null($value)) {
        echo 'null,';
    } elseif (is_int($value) or is_float($value)) {
        echo $value.',';
    } else {
        echo "'".addslashes($value)."',";
    }
    echo "\n";
}
