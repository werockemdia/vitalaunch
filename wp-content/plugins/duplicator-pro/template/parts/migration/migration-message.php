<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Core\MigrationMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

if (!isset($tplData['isMigrationSuccessNotice']) || !$tplData['isMigrationSuccessNotice']) {
    $tplMng->render('parts/migration/tool-cleanup-installer-files');
    return;
}

$safeMsg       = MigrationMng::getSaveModeWarning();
$cleanupReport = MigrationMng::getCleanupReport();

?>
<div class="notice notice-success dpro-admin-notice dup-migration-pass-wrapper">
    <div class="dup-migration-pass-title">
        <i class="fa fa-check-circle"></i> <?php
        if (MigrationMng::getMigrationData()->restoreBackupMode) {
            _e('This site has been successfully restored!', 'duplicator-pro');
        } else {
            _e('This site has been successfully migrated!', 'duplicator-pro');
        }
        ?>
    </div>
    <p>
        <?php printf(__('The following installation files are stored in the folder <b>%s</b>', 'duplicator-pro'), DUPLICATOR_PRO_SSDIR_PATH_INSTALLER); ?>
    </p>
    <ul class="dup-stored-minstallation-files">
        <?php foreach (MigrationMng::getStoredMigrationLists() as $path => $label) { ?>
            <li>
                - <?php echo esc_html($label); ?>
            </li>
        <?php } ?>
    </ul>

    <?php
    if (isset($tplData['isInstallerCleanup']) && $tplData['isInstallerCleanup']) {
        $tplMng->render('parts/migration/clean-installation-files');
    } else {
        if (count($cleanupReport['instFile']) > 0) { ?>
            <p>
                <?php _e('Security actions:', 'duplicator-pro'); ?>
            </p>
            <ul class="dup-stored-minstallation-files">
                <?php
                foreach ($cleanupReport['instFile'] as $html) { ?>
                    <li>
                        <?php echo $html; ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <p>
            <b><?php _e('Final step:', 'duplicator-pro'); ?></b><br>
            <span id="dpro-notice-action-remove-installer-files" class="link-style" onclick="DupPro.Tools.removeInstallerFiles();">
                <?php esc_html_e('Remove Installation Files Now!', 'duplicator-pro'); ?>
            </span>
        </p>
        <?php if (strlen($safeMsg) > 0) { ?>
            <div class="notice-safemode">
                <?php echo esc_html($safeMsg); ?>
            </div>
        <?php } ?>

        <p class="sub-note">
            <i><?php
                _e(
                    'Note: This message will be removed after all installer files are removed.
                    Installer files must be removed to maintain a secure site.
                    Click the link above to remove all installer files and complete the migration.',
                    'duplicator-pro'
                );
                ?><br>
                <i class="fas fa-info-circle"></i>
                <?php
                _e(
                    'If an archive.zip/daf file was intentially added to the root directory to perform an overwrite install 
                    of this site then you can ignore this message.',
                    'duplicator-pro'
                )
                ?>
            </i>
        </p>
        <?php
    }

    echo apply_filters(MigrationMng::HOOK_BOTTOM_MIGRATION_MESSAGE, '');
    ?>
</div>