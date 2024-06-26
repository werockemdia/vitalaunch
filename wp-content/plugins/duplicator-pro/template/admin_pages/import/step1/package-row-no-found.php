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
<tr class="dup-pro-import-no-package-found">
    <td colspan="4" >
        <div class="dup-pro-import-no-package-found-msg">
           <b><?php esc_html_e("No archive files found!", 'duplicator-pro'); ?></b><br/><br/>
           <?php esc_html_e("Please upload a Duplicator archive.zip/daf in the area above.", 'duplicator-pro'); ?><br/>
           <?php esc_html_e("This will start the import process to overwrite the current site.", 'duplicator-pro'); ?>
           <br/><br/>
           <a href="javascript:void(0)" title="Get Help" onclick="jQuery('#contextual-help-link').trigger('click')">
               <?php esc_html_e('How does this work?', 'duplicator-pro'); ?>
           </a>
        </div>
    </td>
</tr>
