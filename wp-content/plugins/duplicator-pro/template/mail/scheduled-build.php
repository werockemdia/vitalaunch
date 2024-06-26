<?php

/**
 * Duplicator schedule success mail
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<p><?php echo $tplData['messageTitle']; ?></p>
<p>
    <strong><?php _e('Package Name', 'duplicator-pro') ?>: </strong><?php echo $tplData['packageName']; ?><br/>
    <strong><?php _e('Package ID', 'duplicator-pro') ?>: </strong><?php echo $tplData['packageID']; ?><br/>
    <strong><?php _e('Date', 'duplicator-pro') ?>: </strong><?php echo date_i18n('Y-m-d H:i:s'); ?><br/>
    <strong><?php _e('Schedule', 'duplicator-pro') ?>: </strong><?php echo $tplData['scheduleName']; ?>
</p>

<?php if ($tplData['success']) : ?>
<p>
    <strong><?php _e('Number of Files', 'duplicator-pro') ?>: </strong><?php echo $tplData['fileCount']; ?><br/>
    <strong><?php _e('Package size', 'duplicator-pro') ?>: </strong><?php echo $tplData['packageSize']; ?>
</p>
<p>
    <strong><?php _e('Number of tables', 'duplicator-pro') ?>: </strong><?php echo $tplData['tableCount']; ?><br/>
    <strong><?php _e('DB dump size', 'duplicator-pro') ?>: </strong><?php echo $tplData['sqlSize']; ?>
</p>
<?php endif; ?>

<p>
    <strong><?php _e('Storages', 'duplicator-pro') ?>: </strong>
    <?php foreach ($tplData['storageNames'] as $storageName) : ?>
        <br/> - <?php echo $storageName; ?>
    <?php endforeach; ?>
</p>
<p>
    <?php echo sprintf(
        __('To go to the "Packages" screen <a href="%s" target="_blank">click here</a>.', 'duplicator-pro'),
        $tplData['packagesLink']
    ); ?>
</p>
<?php if ($tplData['logExists']) : ?>
<p>
    <?php _e('Log is attached.', 'duplicator-pro'); ?>
</p>
<?php endif; ?>
