<?php

/**
 * Duplicator package row in table packages list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Addons\ProBase\License\License;
use Duplicator\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<ul class="dup-status-icons-list" >
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-hand" ></i></span>
        <?php esc_html_e('Manual Backup', 'duplicator-pro'); ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-clock" ></i></span>
        <?php esc_attr_e('Schedule Backup', 'duplicator-pro') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-hard-drive"></i></span>
        <?php _e('The Backup is in a Local Storage <b>[clickable]</b>', 'duplicator-pro') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-cloud"></i></span>
        <?php _e('The Backup is in a Remote Storage <b>[clickable]</b>', 'duplicator-pro') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-database"></i></span>
    <?php esc_attr_e('Database Only Backup', 'duplicator-pro') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-images"></i></span>
        <?php esc_attr_e('Media Only Backup', 'duplicator-pro') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><?php ViewHelper::disasterIcon(true, 'link-style no-decoration'); ?></span>
        <?php _e('This Backup is available for Disaster Recovery <b>[clickable]</b>', 'duplicator-pro'); ?>
    </li>
    <li>
        <span class="icon-wrapper" ><?php ViewHelper::disasterIcon(true, 'green'); ?></span>
        <?php _e('Disaster Recovery URL is set on this Backup <b>[clickable]</b>', 'duplicator-pro'); ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-clock-rotate-left maroon"></i></span>
        <?php esc_attr_e('This Backup is created after the Last Restored Backup', 'duplicator-pro') ?>
    </li>
</ul>