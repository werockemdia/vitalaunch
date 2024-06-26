<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Views\ScreenBase;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$importSettingsUrl = $ctrlMng->getMenuLink(
    ControllersManager::SETTINGS_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_IMPORT
);

?>
<div class="dpro-screen-hlp-info"><b><?php esc_html_e('Resources', 'duplicator-pro'); ?>:</b> 
    <ul>
        <?php echo ScreenBase::getHelpSidebarBaseItems(); ?>
        <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
            <li>
                <i class='fas fa-cog'></i> <a href="<?php echo esc_url($importSettingsUrl); ?>">
                    <?php esc_html_e('Import Settings', 'duplicator-pro'); ?>
                </a>
            </li>
        <?php } ?>
        <li>
            <i class='fas fa-mouse-pointer'></i> 
                <a href="<?php echo esc_url(DUPLICATOR_PRO_DRAG_DROP_GUIDE_URL); ?>" target="_sc-ddguide">
                <?php esc_html_e('Drag and Drop Guide', 'duplicator-pro'); ?>
            </a>
        </li>                
    </ul>
</div>
