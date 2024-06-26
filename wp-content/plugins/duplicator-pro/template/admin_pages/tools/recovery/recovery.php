<?php

/**
 * @package Duplicator
 */

use Duplicator\Package\Recovery\RecoveryPackage;
use Duplicator\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$blur = $tplData['blur'];

$recoverPackage     = RecoveryPackage::getRecoverPackage();
$recoverPackageId   = RecoveryPackage::getRecoverPackageId();
$recoveablePackages = RecoveryPackage::getRecoverablesPackages();

?>
<h2 class="margin-bottom-0">
    <?php ViewHelper::disasterIcon(); ?>&nbsp;<?php esc_html_e("Disaster Recovery", 'duplicator-pro'); ?>
</h2>
<hr/>

<p class="margin-bottom-1">
    <?php esc_html_e("Quickly restore this site to a specific Backup in time.", 'duplicator-pro'); ?>
    <span class="link-style dup-pro-open-help-link">
        <?php esc_html_e("Need more help?", 'duplicator-pro'); ?>
    </span>
</p>
<div class="dup-pro-recovery-details-max-width-wrapper <?php echo ($blur ? 'dup-mock-blur' : ''); ?>" >
    <?php if (DUP_PRO_CTRL_recovery::isDisallow()) { ?>
        <p>
            <?php esc_html_e("The import function is disabled", 'duplicator-pro'); ?>
        </p>
        <?php
        return;
    }
    ?>
    <form id="dpro-recovery-form" method="post">
        <?php
        DUP_PRO_CTRL_recovery::renderRecoveryWidged(array(
            'selector'   => true,
            'subtitle'   => '',
            'copyLink'   => true,
            'copyButton' => true,
            'launch'     => true,
            'download'   => true,
            'info'       => true,
        ));
        ?>
    </form>
</div>
<?php
require_once DUPLICATOR____PATH . '/views/tools/recovery/widget/recovery-widget-scripts.php';
