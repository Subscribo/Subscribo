<?php echo '<?php'; ?>


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
<?php
if ( ! empty($options['is_pivot_table'])):
    if ('simple' === $options['is_pivot_table']['type']):
        echo ' * Creates pivot table for simple many to many relation'."\n";
        echo " * Related tables: ".\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['is_pivot_table']['relation']['table_from'])
            .', '.\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['is_pivot_table']['relation']['table_to'])."\n";
    elseif ('polymorphic' === $options['is_pivot_table']['type']):
        echo ' * Pivot table for polymorphic many to many relation'."\n";
    endif;
endif;
if ( ! empty($options['model_name'])):
    echo ' * Creates table for model \\'.\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['model_namespace'].'\\'.$options['model_name'])."\n";
endif;
?>
 */
class <?php echo $migrationName; ?> extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('<?php echo addslashes($options['table_name']); ?>', function(Blueprint $table)
        {
<?php
    $methods = array('bigIncrements' => 1, 'bigInteger' => 1, 'binary' => 1, 'boolean' => 1, 'char' => 2, 'date' => 1,
        'dateTime' => 1, 'decimal' => 3, 'double' => 3, 'enum' => -2, 'float' => 3, 'increments' => 1, 'integer' => 1,
        'longText' => 1, 'mediumInteger' => 1, 'mediumText' => 1, 'smallInteger' => 1, 'tinyInteger' => 1, 'string' => 2,
        'text' => 1, 'time' => 1, 'timestamp' => 1);
    foreach($fields as $field) {
        if ( ! $field['migration_setup']) {
            continue;
        }
        if ($field['primary']) {
            if ('biginteger' == $field['db_type']) {
                $lowerCaseMethodName = 'bigincrements';
            } else {
                $lowerCaseMethodName = 'increments';
            }
        } elseif ('varchar' == $field['db_type']) {
            $lowerCaseMethodName = 'string';
        } elseif ('blob' == $field['db_type']) {
            $lowerCaseMethodName = 'binary';
        } else {
            $lowerCaseMethodName = $field['db_type'];
        }
        $filtered = array_where($methods,
            function($key, $value) use ($lowerCaseMethodName)
            { return (strtolower($key) == $lowerCaseMethodName); }
        );
        if (empty($filtered)) {
            throw new \Exception("Method '".$lowerCaseMethodName."' not defined for schema field setup (Field name: '".$field['name']."', Table name: '".$options['table_name']."').");
        }
        $methodArgumentsType = reset($filtered);
        $methodName = key($filtered);
        echo '            $table->'.$methodName."('".addslashes($field['name'])."'";
        if (  (2 <= $methodArgumentsType)
          and (3 >= $methodArgumentsType)
          and ('' !== trim($field['db_length']))) {
            echo ', '.intval($field['db_length']);
        }
        if (  (3 === $methodArgumentsType)
          and ('' !== trim($field['db_scale']))) {
            echo ', '.intval($field['db_scale']);
        }
        if (-2 === $methodArgumentsType) {
            echo ', array(';
            echo implode(', ', value(array_map(function($str) { return "'".addslashes($str)."'"; }, $field['enum_list'])));
            echo ')';
        }
        echo ')';
        if (in_array($methodName, array('bigIncrements', 'bigInteger', 'decimal', 'double',  'float', 'increments',
                'integer', 'mediumInteger', 'smallInteger' => 1, 'tinyInteger' => 1, ))
            and $field['unsigned']) {
            echo '->unsigned()';
        }
        if ($field['nullable']) {
            echo '->nullable()';
        }
        if (array_key_exists('default', $field)) {
            echo '->default(';
            echo View::make('schemabuilder::helpers.php_value', array('value' => $field['default']))->render();
            echo ')';
        }
        echo ";\n";
    }

    if ( ! empty($options['migration_timestamps'])) {
        echo '            $table->timestamps();' . "\n";
    }

    foreach($fields as $field) {
        if ( ! $field['migration_setup']) {
            continue;
        }
        if (empty($field['db_foreign_key'])) {
            continue;
        }
        echo '            $table->'."foreign('".addslashes($field['name'])
            ."', '"
            .addslashes(\Subscribo\SchemaBuilder\Helpers\DbIdentifier::forge('foreign', $options['table_name'], $field['name']))
            ."')->references('"
            .addslashes($field['db_foreign_key']['key_to'])
            ."')->on('"
            .addslashes($field['db_foreign_key']['table_to'])."')";
        if ( ! empty($field['db_foreign_key']['on_delete'])) {
            echo "->onDelete('".addslashes($field['db_foreign_key']['on_delete'])."')";
        }
        echo ";\n";
    }

    foreach($fields as $field) {
        if ( ! $field['migration_setup']) {
            continue;
        }
        if (empty($field['unique'])) {
            continue;
        }
        echo '            $table->'."unique('".addslashes($field['name'])."');\n";
    }

?>
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('<?php echo $options['table_name']; ?>');
    }
}
