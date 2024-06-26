<?php

/**
 * Duplicator package row in table packages list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

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
<h3>
    <?php esc_html_e("Package Details", "duplicator-pro"); ?>
</h3>
<b><i class="fa fa-archive"></i> <?php esc_html_e("Packages » All", "duplicator-pro"); ?></b><br/>
<?php
esc_html_e(
    "The 'Packages' section is the main interface for managing all the packages that have been created.",
    "duplicator-pro"
);
esc_html_e(
    "A Package consists of two core files. The first is the 'installer.php' file and the second is the 'archive.zip/daf' file.",
    "duplicator-pro"
);
esc_html_e(
    "The installer file is a php file that when browsed to via a web browser presents a wizard that 
    redeploys or installs the website by extracting the archive file.",
    "duplicator-pro"
);
esc_html_e(
    "The archive file is a zip/daf file containing all your WordPress files and a copy of your WordPress database.",
    "duplicator-pro"
);
esc_html_e(
    "To create a package, click the 'Create New' button and follow the prompts.",
    "duplicator-pro"
); ?>
<br/><br/>
<?php esc_html_e(
    "The package [Type] column will be either 'Manual' or 'Schedule'. If a schedule type has a cog icon ",
    "duplicator-pro"
); ?>
<i class="fas fa-cog fa-sm pointer"></i>
<?php
esc_html_e(
    "then that package was created manually by clicking the 'Run Now' link on the schedules page. 
    The [Created] column shows the time the package was built and the [Size] column represents the compressed size of the archive file. 
    The [Name] column is generic and helps to identify the package. 
    The [Installer Name] column identifies the full name of the installer file.  
    If it is hashed (unique) then the lock icon will be locked to identify that the name is secure to browse to on a public facing URL.",
    "duplicator-pro"
); ?>
<br/><br/>
<b><i class="fa fa-download"></i> <?php esc_html_e("Downloads", "duplicator-pro"); ?></b><br/>
<?php esc_html_e(
    "To download the package files click on the Download button. 
    Choosing the 'Both Files' option will popup two separate save dialogs. On some browsers you may have to enable popups on this site.
    In order to download just the 'Installer' or 'Archive' click on that menu item.",
    "duplicator-pro"
); ?>
<i><?php esc_html_e("Note: the archive file will have a copy of the installer inside of it named installer-backup.php", "duplicator-pro"); ?></i>
<br/><br/>
<b><i class="fa fa-database"></i> <?php esc_html_e("Storage", "duplicator-pro"); ?></b><br/>
<?php
esc_html_e(
    "The remote storage button allows users to access the package at the remote location. 
    If a package contains remote storage endpoints then the button will be enabled. 
    A disabled button indicates that no remote packages were setup.",
    "duplicator-pro"
);
echo ' ';
_e(
    'If a red icon shows <i class="fas fa-server remote-data-fail fa-sm"></i> then one or more of the storage locations failed during the transfer phase.',
    'duplicator-pro'
);
?>
<br/><br/>
<b><i class="fas fa-chevron-down"></i> <?php esc_html_e("Details", "duplicator-pro"); ?></b><br/>
<?php
esc_html_e(
    "To see the package details and additional options click the 'Details' expand/collpase button. 
    If the Recovery menu option is disabled then the package is not enabled as a valid recovery package.",
    "duplicator-pro"
);
echo ' ';
_e(
    'You should see a valid recovery icon <i class="fa fa-undo fa-sm"></i></i> next to the package type to quickly identify packages that are recover capable.',
    'duplicator-pro'
);
?>
<br/><br/>

<b><i class="far fa-file-archive fa-sm"></i> <?php esc_html_e("Archive Types", "duplicator-pro"); ?></b><br/>
<?php esc_html_e(
    "An archive file can be saved as either a .zip file or .daf file. A zip file is a common archive format used to compress and group files.  
    The daf file short for 'Duplicator Archive Format' is a custom format used specifically for working with larger packages 
    and scale-ability issues on many shared hosting platforms.",
    "duplicator-pro"
);
printf(
    __(
        'Both formats work very similar the main difference is that the daf file can only be extracted using the installer.php file or 
        the %1$sDAF extraction tool%2$s. 
        The zip file can be used by other zip tools like winrar/7zip/winzip or other client-side tools.',
        'duplicator-pro'
    ),
    '<a href="' . DUPLICATOR_PRO_BLOG_URL .
    'knowledge-base/how-to-work-with-daf-files-and-the-duparchive-extraction-tool" class="dup-DAF-tool" target="_blank">',
    '</a>'
);
?>
<br/>
<hr/>

<h3>
    <?php esc_html_e("Tools", "duplicator-pro"); ?>
</h3>
<b><i class="fas fa-clone"></i> <?php esc_html_e("Templates", "duplicator-pro"); ?></b><br/>
<?php esc_html_e(
    'Templates are used to profile out how a package will be built and required for schedules. 
    Templates allow you to choose which files and database tables you would like to make as part of your backup process.
    It also allows for the installer to be pre-filled with the values of the template when doing manual builds.',
    "duplicator-pro"
); ?>
<br/><br/>
<b><i class="fas fa-arrow-alt-circle-down"></i> <?php esc_html_e("Import", "duplicator-pro"); ?></b><br/>
<?php esc_html_e(
    'The import features allows users to quickly upload a Duplicator Pro archive to overwrite the current site. 
    For more details check-out the import help section.',
    "duplicator-pro"
); ?>
<br/><br/>
<b><?php
    ViewHelper::disasterIcon();
    echo '&nbsp;';
    esc_html_e("Disaster Recovery", "duplicator-pro");
?></b><br/>
<?php esc_html_e(
    'The Disaster Recovery is a special feathure that allows one to quickly revert the system should it become corrupted 
    during a maintenance operation such as a plugin/theme update or an experimental file change. 
    The advantage of setting a Disaster Recovery is that you can very quickly restore a backup without having to worry 
    about uploading a package and setting the parameters such as database credentials or site paths. 
    See Help on Tools > Recovery page for more information and usage of the Disaster Recovery.',
    "duplicator-pro"
); ?>

<br/>
<hr/>

<h3>
    <?php esc_html_e("Miscellaneous", "duplicator-pro"); ?>
</h3>

<b><i class="fa fa-bolt"></i> <?php esc_html_e("How to Install a Package", "duplicator-pro"); ?></b><br/>   
<?php
printf(
    __(
        'Installing a package is pretty straight forward, however it does require a quick primer if you have never done it before. 
        To get going with a step by step guide and quick video check out the %1$squick start guide.%2$s',
        'duplicator-pro'
    ),
    '<a href="' . DUPLICATOR_PRO_BLOG_URL . 'knowledge-base-article-categories/quick-start/" class="dup-quick-start" target="_blank">',
    '</a>'
);
?>
<br/><br/>

