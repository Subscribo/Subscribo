<?php
echo "(";
$isFirst = true;
foreach ($parameters as $parameter):
    if ( ! $isFirst) {
        echo ', ';
    }
    $isFirst = false;
    echo View::make('schemabuilder::helpers.php_value', array('value' => $parameter));
endforeach;
echo ')';
