<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Views;

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use WP_Screen;

class PackageScreen extends ScreenBase
{
    /**
     * Class contructor
     *
     * @param string $page page
     */
    public function __construct($page)
    {
        add_action('load-' . $page, array($this, 'init'));
        add_filter('screen_settings', array($this, 'showOptions'), 10, 2);
    }

    /**
     * Init package screen
     *
     * @return void
     */
    public function init()
    {
        $this->screen = get_current_screen();

        add_action('admin_head', array(self::class, 'displayColsCss'));

        switch (PackagesPageController::getInstance()->getPackagesInnerPage()) {
            case PackagesPageController::LIST_INNER_PAGE_DETAILS:
                $content = $this->getDetailsHelp();
                break;
            case PackagesPageController::LIST_INNER_PAGE_TRANSFER:
                $content = $this->getListHelp();
                break;
            case PackagesPageController::LIST_INNER_PAGE_NEW_STEP1:
                $content = $this->getStep1Help();
                break;
            case PackagesPageController::LIST_INNER_PAGE_NEW_STEP2:
                $content = $this->getStep2Help();
                break;
            case PackagesPageController::LIST_INNER_PAGE_LIST:
            default:
                $content = $this->getListHelp();
                break;
        }

        $guide    = '#guide-packs';
        $faq      = '#faq-package';
        $content .= "<b>References:</b><br/>"
            . "<a href='" . DUPLICATOR_PRO_DUPLICATOR_DOCS_URL . $guide . "' class='dup-references-user-guide' target='_sc-guide'>User Guide</a> | "
            . "<a href='" . DUPLICATOR_PRO_TECH_FAQ_URL . $faq . "' class='dup-references-faqs' target='_sc-guide'>FAQs</a> | "
            . "<a href='" . DUPLICATOR_PRO_BLOG_URL .
            "knowledge-base-article-categories/quick-start/' class='dup-references-quick-start' target='_sc-guide'>Quick Start</a>";

        $this->screen->add_help_tab(array(
            'id'      => 'dpro_help_package_overview',
            'title'   => __('Overview', 'duplicator-pro'),
            'content' => "<p>{$content}</p>",
        ));

        $this->getSupportTab($guide, $faq);
        $this->screen->set_help_sidebar(self::getPackagesHelpSidebar());
    }

    /**
     * Display columns css
     *
     * @return void
     */
    public static function displayColsCss()
    {
        $uiOpts = UserUIOptions::getInstance();

        $showNote    = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_NOTE);
        $showSize    = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_SIZE);
        $showCreated = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_CREATED);
        $showAge     = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_AGE);
        ?>
        <style>
            <?php if (!$showNote) { ?>
                .dup-packtbl .dup-note-column {
                    display: none;
                }
            <?php } ?>

            <?php if (!$showSize) { ?>
                .dup-packtbl .dup-size-column {
                    display: none;
                }
            <?php } ?>

            <?php if (!$showCreated) { ?>
                .dup-packtbl .dup-created-column {
                    display: none;
                }
            <?php } ?>

            <?php if (!$showAge) { ?>
                .dup-packtbl .dup-age-column {
                    display: none;
                }
            <?php } ?>
        </style>
        <?php
    }

    /**
     * Return HELP sidebar
     *
     * @return string
     */
    public static function getPackagesHelpSidebar()
    {
        $settingsPackageUrl = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);
        ob_start();
        ?>
        <div class="dpro-screen-hlp-info"><b><?php esc_html_e('Resources', 'duplicator-pro'); ?>:</b> 
            <ul>
                <?php echo self::getHelpSidebarBaseItems(); ?>
                <li>
                    <i class='fas fa-cog'></i> 
                    <a href="<?php echo esc_url($settingsPackageUrl); ?>" class='dup-package-settings'>
                        <?php esc_html_e('Package Settings', 'duplicator-pro'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * Return list HELP
     *
     * @return string
     */
    protected function getListHelp()
    {
        return TplMng::getInstance()->render('admin_pages/packages/list_help', [], false);
    }

    /**
     * Return step1 HELP
     *
     * @return string
     */
    protected function getStep1Help()
    {
        ob_start();
        ?>
        <b><?php esc_html_e('Packages New » 1 Setup', 'duplicator-pro'); ?></b><br/>
        <?php
        esc_html_e(
            "The setup screen allows users to choose where they would like to store thier package, 
            such as Google Drive, Dropbox, on the local server or a combination of both. 
            Setup also allow users to setup optional filtered directory paths, files and database tables to change what is included in the archive file.
            The optional option to also have the installer pre-filled can be used. To expedited the workflow consider using a Template.",
            'duplicator-pro'
        ); ?>
        <br/><br/>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * Return step2 HELP
     *
     * @return string
     */
    protected function getStep2Help()
    {
        ob_start();
        ?>
        <b><?php esc_html_e('Packages New » 2 Build', 'duplicator-pro'); ?></b><br/>
        <?php
        esc_html_e(
            "The plugin will scan your system, files and database to let you know if there are any concerns or issues that may be present.
            All items in green mean the checks looked good. All items in red indicate a warning.
            Warnings will not prevent the build from running, 
            however if you do run into issues with the build then checking the warnings should be considered.",
            'duplicator-pro'
        ); ?>
        <br/><br/>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * Return details HELP
     *
     * @return string
     */
    public function getDetailsHelp()
    {
        return __(
            "<b>Packages » Details</b> <br/>
            The details view will give you a full break-down of the package including any errors that may have occured during the install.",
            'duplicator-pro'
        ) . "<br/><br/>";
    }

    /**
     * Packages List: Screen Options Tab
     *
     * @param string    $screen_settings Screen settings
     * @param WP_Screen $args            Screen args
     *
     * @return string
     */
    public function showOptions($screen_settings, WP_Screen $args)
    {


        // Only display on packages screen and not build screens
        if (
            !PackagesPageController::getInstance()->isCurrentPage() ||
            PackagesPageController::getCurrentInnerPage(PackagesPageController::LIST_INNER_PAGE_LIST) !== PackagesPageController::LIST_INNER_PAGE_LIST
        ) {
            return $screen_settings;
        }

        return TplMng::getInstance()->render('admin_pages/packages/screen_options', [], false);
    }

    /**
     * Set duplicator screen option
     *
     * @param mixed  $screen_option The value to save instead of the option value. Default false (to skip saving the current option).
     * @param string $option        The option name.
     * @param int    $value         The option value.
     *
     * @return bool
     */
    public static function setScreenOptions($screen_option, $option, $value)
    {
        $uiOpts = UserUIOptions::getInstance();

        $perPage = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'duplicator_pro_opts_per_page', 10);
        $uiOpts->set(UserUIOptions::VAL_PACKAGES_PER_PAGE, $perPage);

        $dateFormat = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'duplicator_pro_created_format', 1);
        $uiOpts->set(UserUIOptions::VAL_CREATED_DATE_FORMAT, $dateFormat);

        $showNote = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dup-note-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_NOTE, $showNote);

        $showSize = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dup-size-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_SIZE, $showSize);

        $showCreated = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dup-created-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_CREATED, $showCreated);

        $showAge = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dup-age-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_AGE, $showAge);

        $uiOpts->save();

        // Returning false from the filter will skip saving the current option
        return false;
    }
}