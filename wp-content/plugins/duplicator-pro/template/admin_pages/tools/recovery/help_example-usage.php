<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

?>
<p>
    <?php _e(
        'The following is a typical scenario showing how one can quickly restore the site after a bad plugin update using the Recovery Point:',
        'duplicator-pro'
    ); ?>
    <ol>
        <li>
            <?php esc_html_e('User builds an unfiltered package.', 'duplicator-pro');?>
        </li>
        <li>
            <?php esc_html_e('User sets the Recovery Point on that package (Hamburger menu on package row.)', 'duplicator-pro'); ?>
        </li>
        <li>
            <?php esc_html_e('User copies the Recovery URL to the clipboard and pastes into a text file or other safe spot.', 'duplicator-pro'); ?>
        </li>
        <li>
            <?php esc_html_e('User updates plugins.', 'duplicator-pro');?>
        </li>
        <li>
            <?php esc_html_e('** Site crashes due to bad code in a plugin update **', 'duplicator-pro');?>
        </li>
        <li>
            <?php esc_html_e('User pastes Recovery URL into a browser and quickly restores the site using the streamlined installer.', 'duplicator-pro');?>
        </li>
    </ol>

    <?php esc_html_e('After the above sequence occurs, the site has been restored with the site experiencing minimal downtime.', 'duplicator-pro');?>
</p>