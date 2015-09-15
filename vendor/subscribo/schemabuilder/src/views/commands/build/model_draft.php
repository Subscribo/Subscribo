<?php echo "<?php"; ?>


namespace <?php echo $options['model_namespace']; ?>;

/**
 * Model <?php echo \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($modelName); ?>
<?php if($options['description']) {
    echo " - ".\Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($options['description']);
}
echo "\n";
if($options['comments']) {
    echo " *\n";
    foreach ($options['comments'] as $comment) {
        echo " * " . \Subscribo\SchemaBuilder\Helpers\MyStr::sanitizeForComment($comment) . "\n";
    }
}
?>
 *
 * Model class for being changed and used in the application
 */
class <?php echo $modelName; ?> extends \<?php echo $options['model_base_namespace'].'\\'.$modelName; ?>

{

}
