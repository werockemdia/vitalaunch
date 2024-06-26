<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapIO;

/**
 * passed values
 *
 * @var int $recoverPackageId
 * @var array<int, array{id: int, created: string, nameHash: string, name: string}> $recoveablePackages
 * @var bool $selector
 * @var string $subtitle
 * @var bool $displayCopyLink
 * @var bool $displayCopyButton
 * @var bool $displayLaunch
 * @var bool $displayDownload
 * @var bool $displayInfo
 * @var string $viewMode
 * @var string $importFailMessage
 */


/** @var \Duplicator\Package\Recovery\RecoveryPackage $recoverPackage */

switch ($viewMode) {
    case DUP_PRO_CTRL_recovery::VIEW_WIDGET_NO_PACKAGE_SET:
        ?>
        <div class="dup-pro-recovery-active-link-header">
            <i class="fa-solid fa-house-fire main-icon"></i>
            <div class="main-title">
                <?php esc_html_e('Disaster Recovery Backup is Not Set', 'duplicator-pro'); ?>
            </div>
            <div class="main-subtitle margin-bottom-1">
                <b><?php esc_html_e('Backup Age:', 'duplicator-pro'); ?></b>&nbsp;
                <span class="dup-pro-recovery-status red"><?php _e('not set', 'duplicator-pro'); ?></span>
            </div>
        </div>
        <div class="margin-bottom-1">
            <?php
            _e(
                'A Disaster Recovery feathure allows one to quickly restore the site to a prior state. 
                To use this, mark a Backup as the Disaster Recovery Backup, then copy and save off the associated Disaster Recovery URL. 
                Then, if a problem occurs, browse to the URL to launch a streamlined installer to quickly restore the site.',
                'duplicator-pro'
            );
            ?>
        </div>
        <?php
        break;
    case DUP_PRO_CTRL_recovery::VIEW_WIDGET_NOT_VALID:
        ?>
        <div class="orangered margin-bottom-1">
            <?php echo esc_html($importFailMessage); ?>
        </div>
        <?php
        break;
    case DUP_PRO_CTRL_recovery::VIEW_WIDGET_VALID:
        ?>
        <div class="dup-pro-recovery-active-link-wrapper" >
            <div class="dup-pro-recovery-active-link-header" >
                <i class="fas fa-house-fire main-icon"></i>
                <div class="main-title" >
                    <?php _e('Disaster Recovery Backup is Set', 'duplicator-pro'); ?>
                </div>
                <div class="main-subtitle margin-bottom-1" >
                    <b><?php esc_html_e('Backup Age:', 'duplicator-pro'); ?></b>&nbsp;
                    <span class="dup-pro-recovery-status green">
                        <?php echo $recoverPackage->getPackageLife('human'); ?>
                    </span>
                </div>
            </div>
            <?php if (strlen($subtitle)) { ?>
            <p>
                <?php echo $subtitle; ?>
            </p>
            <?php } ?>
            <?php if ($displayInfo) { ?>
                <div class="dup-pro-recovery-package-info margin-bottom-1" >
                    <table>
                        <tbody>
                            <tr>
                                <td><?php _e('Name', 'duplicator-pro'); ?>:</td>
                                <td><b><?php echo esc_html($recoverPackage->getPackageName()); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php _e('Date', 'duplicator-pro'); ?>:</td>
                                <td><b><?php echo esc_html($recoverPackage->getCreated()); ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        break;
    default:
        ?>
        <p class="orangered">
            <?php echo __('Invalid view mode.', 'duplicator-pro'); ?>
        </p>
        <?php
}
