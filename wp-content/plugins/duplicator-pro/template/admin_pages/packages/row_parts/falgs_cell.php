<?php

/**
 * @package Duplicator
 */

use Duplicator\Core\CapMng;
use Duplicator\Package\Recovery\RecoveryPackage;
use Duplicator\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var ?DUP_PRO_Package $package
 */

$package = $tplData['package'];

?>
<div class="dup-package-flags" >
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_MANUAL) || $package->hasFlag(DUP_PRO_Package::FLAG_SCHEDULE_RUN_NOW)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Manual Backup', 'duplicator-pro') ?>" >
            <i class="fa-solid fa-hand" ></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_SCHEDULE)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Schedule Backup', 'duplicator-pro') ?>" >
            <i class="fa-solid fa-clock" ></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_HAVE_LOCAL)) { ?>
        <span 
            class="icon-wrapper cursor-pointer" 
            title="<?php esc_attr_e('The Backup is in Local Storage', 'duplicator-pro') ?>" 
            onclick="DupPro.Pack.ShowRemote(<?php echo (int) $package->ID; ?>, '<?php echo esc_js($package->NameHash); ?>');"
        >
            <i class="fa-solid fa-hard-drive"></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_HAVE_REMOTE)) { ?>
        <span 
            class="icon-wrapper cursor-pointer" 
            title="<?php esc_attr_e('The Backup is in Remote Storage', 'duplicator-pro') ?>" 
            onclick="DupPro.Pack.ShowRemote(<?php echo (int) $package->ID; ?>, '<?php echo esc_js($package->NameHash); ?>');"
        >
            <i class="fa-solid fa-cloud"></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_DB_ONLY)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Database Only Backup', 'duplicator-pro') ?>" >
            <i class="fa-solid fa-database"></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_MEDIA_ONLY)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Media Only Backup', 'duplicator-pro') ?>" >
            <i class="fa-solid fa-images"></i>
        </span>
    <?php } ?>
    <?php if (CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) { ?>
        <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_DISASTER_AVAIABLE) || $package->hasFlag(DUP_PRO_Package::FLAG_DISASTER_SET)) {
            if ($package->hasFlag(DUP_PRO_Package::FLAG_DISASTER_SET)) {
                $title      = __("Disaster Recovery URL is set on this Backup", 'duplicator-pro');
                $colorClass = 'green';
            } else {
                $colorClass = '';
                $title      = __('This Backup is available for Disaster Recovery', 'duplicator-pro');
            }
            ?>
            <span 
                class="dpro-btn-open-recovery-box icon-wrapper link-style no-decoration>" 
                aria-label="<?php echo esc_attr($title); ?>"
                title="<?php echo esc_attr($title); ?>"
                data-package-id="<?php echo $package->ID; ?>"
            >
                <?php ViewHelper::disasterIcon(true, $colorClass); ?>
            </span>
        <?php } ?>
    <?php } ?>
    <?php if ($package->hasFlag(DUP_PRO_Package::FLAG_CREATED_AFTER_RESTORE)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('This Backup is created after the Last Restored Backup', 'duplicator-pro') ?>" >
            <i class="fa-solid fa-clock-rotate-left maroon"></i>
        </span>
    <?php } ?>
</div>