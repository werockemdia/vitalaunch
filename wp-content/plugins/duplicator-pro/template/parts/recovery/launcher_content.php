<?php

/**
 * Duplicator package row in table packages list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Package\Recovery\RecoveryPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var RecoveryPackage $recoverPackage
 */

$recoverPackage = $tplData['recoverPackage'];
?><!DOCTYPE html>
<html lang="en-US" >
    <head>
        <title><?php _e('Recovery package launcher', 'duplicator-pro'); ?></title>
    </head>
    <body>
        <h2><?php printf(__('Recovery package launcher create on %s', 'duplicator-pro'), $recoverPackage->getCreated()); ?></h2>
        <p>
            <?php
            printf(
                __(
                    'If the installer does not start automatically, you can click on this <a href="%s" >link and start it manually</a>.',
                    'duplicator-pro'
                ),
                esc_url($recoverPackage->getInstallLink())
            );
            ?>
        </p>
        <script>
            window.location.href = <?php echo json_encode($recoverPackage->getInstallLink()); ?>;
        </script>
    </body>
</html>