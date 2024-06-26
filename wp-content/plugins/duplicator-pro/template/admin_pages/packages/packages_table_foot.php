<?php

/**
 * Duplicator package row in table packages list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$global             = DUP_PRO_Global_Entity::getInstance();
$maxDefaultPackages = StoragesUtil::getDefaultStorage()->getMaxPackages();
$toolTipContent     = sprintf(
    esc_attr__(
        'The number of packages to keep is set at [%d]. To change this setting go to 
        Duplicator Pro > Storage > Default > Max Packages and change the value, otherwise this note can be ignored.',
        'duplicator-pro'
    ),
    $maxDefaultPackages
);
?>
<tfoot>
    <tr>
        <th colspan="11">
            <div class="dup-pack-status-info">
                <?php if ($maxDefaultPackages < $tplData['totalElements'] && $maxDefaultPackages != 0) { ?>
                    <?php echo esc_html__("Note: max package retention enabled", 'duplicator-pro'); ?>
                    <i 
                        class="fas fa-question-circle fa-sm" 
                        data-tooltip-title="<?php esc_attr_e("Storage Packages", 'duplicator-pro'); ?>" 
                        data-tooltip="<?php echo $toolTipContent; ?>"
                    >
                    </i>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
        </th>
    </tr>
</tfoot>
