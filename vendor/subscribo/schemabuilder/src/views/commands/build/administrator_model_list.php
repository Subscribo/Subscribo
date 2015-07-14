<?php echo '<?php'; ?>


/**
 * Automatically generated model configuration list for Frozennode Administrator
 *
 */

return array(
    'menu' => array(
    <?php echo View::make('schemabuilder::helpers.php_array', array('arr' => $mainMenu, 'indent' => 2)); ?>
    <?php echo View::make('schemabuilder::helpers.php_array', array('arr' => $menu, 'indent' => 2)); ?>
    ),
);
