<?php echo "<?php"; ?>
 namespace <?php echo $options['model_base_namespace']; ?>;

/**
 * Model <?php echo $modelName; ?>

 * Automatically generated abstract model class
 *
 * @method \<?php echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['model_namespace'].'\\'.$modelName); ?>[] get() public static function get(array $columns = array()) returns an array of models <?php echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($modelName); ?>

 * @method null|\<?php echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['model_namespace'].'\\'.$modelName); ?> first() public static function first(array $columns = array()) returns model <?php echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($modelName); ?>

 *
<?php
foreach($fields as $field) {
    echo " * @property ".\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($field['type_hint'])
        ." $".\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($field['attribute_name']);
    if ($field['description']) {
        echo " ".\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($field['description']);
    }
    echo "\n";
}
foreach ($options['foreign_objects'] as $foreignObject) {
    echo " * @property-read ";
    if (is_array($foreignObject['foreign_model_name'])) {
        echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['base_model_extends']);
        echo $foreignObject['returns_array'] ? '[]' : '';
        foreach ($foreignObject['foreign_model_name'] as $foreignModelName) {
            echo '|';
            echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($foreignModelName);
            echo $foreignObject['returns_array'] ? '[]' : '';
        }
        echo $foreignObject['returns_array'] ? '' : '|null';
    } else {
        echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($foreignObject['foreign_model_name']);
        echo $foreignObject['returns_array'] ? '[]' : '|null';
    }
    echo " ".\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($foreignObject['name']);
    echo $foreignObject['returns_array'] ? ' A collection of foreign models' : ' Foreign model';
    echo " related via ";
    echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment(strtr($foreignObject['relation']['type'], '_', ' '));
    echo " relation \n";
};
?>
 */
abstract class <?php echo $modelName;
if ( ! empty($options['base_model_extends'])) {
    echo ' extends '.$options['base_model_extends'];
}
?>

{

<?php if ( ! empty($options['table_name'])): ?>
    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = '<?php echo addslashes($options['table_name']); ?>';

<?php endif; ?>

    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
<?php
foreach($fields as $field):
    echo '                                '."'".addslashes($field['attribute_name'])."' => array('db_type' => '".addslashes($field['db_type'])."'),\n";
endforeach;

?>
                            );


    /**
     * Property name (usually camel cased) to column (attribute) name (usually snake cased) map
     *
     * @var array
     */
    protected $attributeMap = array(
<?php
foreach($fields as $field):
    echo '                                '."'".addslashes($field['attribute_name'])."' => '".addslashes($field['name'])."',\n";
endforeach;

?>
                            );


<?php if ( ! empty($options['hidden_wrapped'])): ?>

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @property array
     */
    protected $hidden = array(<?php echo implode(', ', $options['hidden_wrapped']); ?>);

<?php endif; ?>

    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = <?php echo View::make('schemabuilder::helpers.php_value', array('value' => $options['fillable'], 'indent' => 8)); ?>;


    /**
     * Rules for validation
     *
     * @var array
     */
    public static $rules = <?php echo View::make('schemabuilder::helpers.php_value', array('value' => $options['rules'], 'indent' => 8)); ?>;


    /**
     * Rules for modifications before validation
     *
     * @var array
     */
    public static $modificationRulesBeforeValidation = <?php echo View::make('schemabuilder::helpers.php_value', array('value' => $options['modification_rules_before_validation'], 'indent' => 8)); ?>;


    /**
     * Rules for modifications after validation
     *
     * @var array
     */
    public static $modificationRulesAfterValidation = <?php echo View::make('schemabuilder::helpers.php_value', array('value' => $options['modification_rules_after_validation'], 'indent' => 8)); ?>;

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array(
<?php
if ( ! empty($options['foreign_objects'])):
    foreach ($options['foreign_objects'] as $foreignObject):
        if (empty($foreignObject)):
            continue;
        endif;
        echo '                                        '."'".addslashes($foreignObject['name'])."' => ";
        echo View::make('schemabuilder::helpers.php_value', array('value' => $foreignObject['foreign_model_name'], 'indent' => 10));
        echo ",\n";
    endforeach;
endif;
?>
                                    );

    /**
     * Properties, which could be used for filtering
     *
     * @return array
     */
    public function getFilterableByProperties()
    {
        return array(
<?php
foreach ($fields as $field):
    if ( ! empty($field['filterable_by'])):
        echo '                    '."'".addslashes($field['name'])."' => '".addslashes($field['name'])."',\n";
    endif;
endforeach;
?>
                );
    }


<?php if ( ! empty($options['foreign_objects'])): ?>

    /* Model specific methods follows */

<?php foreach ($options['foreign_objects'] as $foreignObject):
        if (empty($foreignObject)):
            continue;
        endif;
    ?>

    /**
     * Relation definition. Type: <?php echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment(strtr($foreignObject['relation']['type'], '_', ' ')); ?>

     *
     * @return \Illuminate\Database\Eloquent\Relations\<?php
        if ('morphedByMany' === $foreignObject['method']):
            echo 'MorphToMany';
        else:
            echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment(studly_case($foreignObject['method']));
        endif;
        ?>

     */
    public function <?php echo $foreignObject['name']; ?>()
    {
        return $this-><?php
        echo $foreignObject['method'];
        echo "(";
        $isFirst = true;
        foreach ($foreignObject['method_parameters'] as $methodParameter):
                if ( ! $isFirst) {
                    echo ', ';
                }
                $isFirst = false;
            echo View::make('schemabuilder::helpers.php_value', array('value' => $methodParameter));
        endforeach;
        echo ')';
        if ( ! empty($foreignObject['with_pivot'])):
            echo '
                        ->withPivot';
            echo View::make('schemabuilder::helpers.php_parameters', array('parameters' => $foreignObject['with_pivot']));
        endif;
        if ( ! empty($foreignObject['order_by'])):
            echo '
                        ->orderBy(';
            echo View::make('schemabuilder::helpers.php_value', array('value' => $foreignObject['order_by']));
            echo ')';
        endif;
        if ( ! empty($foreignObject['with_timestamps'])):
            echo '
                        ->withTimestamps()';
        endif;
        ?>;
    }
<?php endforeach;
endif;
?>

}
