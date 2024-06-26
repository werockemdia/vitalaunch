<?php

/**
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
<p>
    <b><?php esc_html_e("Overview", 'duplicator-pro'); ?>:</b><br/>
    <?php
    esc_html_e(
        "The import features allows users to quickly upload a Duplicator Pro archive to overwrite the current site. To get started follow these simple steps:",
        'duplicator-pro'
    );
    ?>
</p>
<ol>
    <li><?php esc_html_e("Upload a Duplicator Pro generated archive.zip/daf file in the selected area below.", 'duplicator-pro'); ?></li>
    <li><?php esc_html_e("Follow the prompts till you reach the 'Launch Installer' button and proceed with the install wizard.", 'duplicator-pro'); ?></li>
    <li><?php esc_html_e("After install, this site will be overwritten with the uploaded archive files contents.", 'duplicator-pro'); ?></li>
</ol>
<p>
    <?php
        esc_html_e('For detailed instructions see this ', 'duplicator-pro');
        echo '<a href="' . DUPLICATOR_PRO_DRAG_DROP_GUIDE_URL . '" target="_sc-ddguide">';
        esc_html_e('online article', 'duplicator-pro');
        echo '</a>.';
    ?>                                
</p>
