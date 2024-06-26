<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Controllers\ImportPageController;
use Duplicator\Package\Import\PackageImporter;
use Duplicator\Package\Recovery\RecoveryPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var PackageImporter $importObj
 */
$importObj = $tplData['importObj'];
/** @var string $iframeSrc */
$iframeSrc         = $tplData['iframeSrc'];
$importFailMessage = '';

if (!$importObj->isImportable($importFailMessage)) {
    ?>
    <div class="wrap">
        <h1>
            <?php _e("Install package", 'duplicator-pro'); ?>
        </h1>
        <div class="dpro-pro-import-installer-content-wrapper" >
            <p class="orangered">
                <?php echo esc_html($importFailMessage); ?>
            </p>
        </div>
    </div>
<?php } else { ?>
    <div id="dpro-pro-import-installer-wrapper"  >
        <div id="dpro-pro-import-installer-top-bar" class="dup-pro-recovery-details-max-width-wrapper" >
            <a href="<?php echo esc_url(ImportPageController::getImportPageLink()); ?>" class="button" >
                <i class="fa fa-caret-left"></i> <?php echo __("Back to Import", 'duplicator-pro'); ?>
            </a>&nbsp;
            <span class="link-style no-decoration recovery-copy-top-wrapper" >
                <?php if (($recoverPackage = RecoveryPackage::getRecoverPackage()) !== false) { ?>
                    <span class="button" 
                          data-tooltip-placement="right"
                          data-dup-copy-value="<?php echo $recoverPackage->getInstallLink(); ?>"
                          data-dup-copy-title="<?php _e("Copy Recovery URL to clipboard", 'duplicator-pro'); ?>"
                          data-dup-copied-title="<?php _e("Recovery URL copied to clipboard", 'duplicator-pro'); ?>" >
                              <?php _e("Copy Recovery URL", 'duplicator-pro'); ?>
                    </span>
                <?php } else { ?>
                    <span class="button disabled"><i class="fas fa-exclamation-circle"></i> <?php _e("Recovery Point Not Set", 'duplicator-pro'); ?></span>
                <?php } ?>
            </span>
        </div>
        <div id="dup-pro-import-installer-modal" class="no-display"></div>
        <iframe id="dpro-pro-import-installer-iframe" src="<?php echo esc_url($iframeSrc); ?>" ></iframe>
    </div>
    <?php
}
