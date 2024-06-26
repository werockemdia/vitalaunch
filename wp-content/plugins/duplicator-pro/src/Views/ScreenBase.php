<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Views;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Libs\Snap\SnapUtil;
use WP_Screen;

/**
 * Screen base class
 */
class ScreenBase
{
    /** @var ?WP_Screen Used as a placeholder for the current screen object */
    public $screen = null;

    /**
     *  Init this object when created
     */
    public function __construct()
    {
    }

    /**
     * Print custom CSS for the current color scheme
     *
     * @return void
     */
    public static function getCustomCss()
    {
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        if (($colorScheme = self::getCurrentColorScheme()) === null) {
            return;
        }

        $primaryButtonColor = self::getPrimaryButtonColorByScheme();
        ?>
        <style>
            .dup-pro-meter.blue>span {
                background-color: <?php echo $colorScheme->colors[2]; ?>;
                background-image: none;
            }

            .dup-pro-recovery-point-actions>.copy-link {
                border-color: <?php echo $primaryButtonColor; ?>;
            }

            .dup-pro-recovery-point-actions>.copy-link .copy-icon {
                background-color: <?php echo $primaryButtonColor; ?>;
            }


            .tippy-box[data-theme~='duplicator'],
            .tippy-box[data-theme~='duplicator-filled'] {
                border-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'] h3,
            .tippy-box[data-theme~='duplicato-filled'] h3 {
                background-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator-filled'] .tippy-content {
                background-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='top']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='top']>.tippy-arrow::before {
                border-top-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='bottom']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='bottom']>.tippy-arrow::before {
                border-bottom-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='left']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='left']>.tippy-arrow::before {
                border-left-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='right']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='right']>.tippy-arrow::before {
                border-right-color: <?php echo $primaryButtonColor; ?>;
            }

            nav.dup-dnload-menu-items button:hover {
                background-color: <?php echo $primaryButtonColor; ?>;
            }

            .button-primary.dup-base-color,
            .button-primary .dup-base-color,
            .button-primary i[data-tooltip].fa-question-circle.dup-base-color,
            .button-primary i[data-tooltip].fa-question-circle.dup-base-color {
                color: <?php echo $colorScheme->colors[1]; ?>;
            }

            .dup-radio-button-group-wrapper input[type="radio"] + label {
                color: <?php echo $primaryButtonColor; ?>;
                border-color: <?php echo $primaryButtonColor; ?>;
            }

            .dup-radio-button-group-wrapper input[type="radio"] + label:hover,
            .dup-radio-button-group-wrapper input[type="radio"]:focus + label, 
            .dup-radio-button-group-wrapper input[type="radio"]:checked + label {
                background: <?php echo $primaryButtonColor; ?>;
                border-color: <?php echo $primaryButtonColor; ?>;
            }
        </style>
        <?php
    }

    /**
     * Unfortunately not all color schemes take the same color as the buttons so you need to make a custom switch/
     *
     * @return string
     */
    public static function getPrimaryButtonColorByScheme()
    {
        $colorScheme = self::getCurrentColorScheme();
        $name        = strtolower($colorScheme->name);
        switch ($name) {
            case 'blue':
                return '#e3af55';
            case 'light':
            case 'midnight':
                return $colorScheme->colors[3];
            case 'ocean':
            case 'ectoplasm':
            case 'coffee':
            case 'sunrise':
            case 'default':
            default:
                return $colorScheme->colors[2];
        }
    }

    /**
     *
     * @global object[] $_wp_admin_css_colors
     * @return null|object return the current color scheme object or null if not found
     */
    public static function getCurrentColorScheme()
    {
        global $_wp_admin_css_colors;
        $colorScheme = get_user_option('admin_color');

        if (isset($_wp_admin_css_colors[$colorScheme])) {
            return $_wp_admin_css_colors[$colorScheme];
        } else {
            if (is_array($_wp_admin_css_colors) && count($_wp_admin_css_colors) > 0) {
                return $_wp_admin_css_colors[SnapUtil::arrayKeyFirst($_wp_admin_css_colors)];
            } else {
                return null;
            }
        }
    }

    /**
     * Get the help support tab view content shown in the help system
     *
     * @param string $guide The target URL to navigate to on the online user guide
     * @param string $faq   The target URL to navigate to on the online user tech FAQ
     *
     * @return void
     */
    public function getSupportTab($guide, $faq)
    {
        ob_start();
        ?>
        <ul class="dup-help-support">
            <li>
                <a href="<?php echo DUPLICATOR_PRO_DUPLICATOR_DOCS_URL . $guide; ?>" class="dup-user-guide" target="_sc-faq">
                    <?php esc_html_e('Full Online User Guide', 'duplicator-pro'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo DUPLICATOR_PRO_TECH_FAQ_URL . $faq; ?>" class="dup-faq" target="_sc-faq">
                    <?php esc_html_e('Frequently Asked Questions', 'duplicator-pro'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo DUPLICATOR_PRO_BLOG_URL . 'knowledge-base-article-categories/quick-start/'; ?>" class="dup-quick-start" target="_sc-faq">
                    <?php esc_html_e('Quick Start Guide', 'duplicator-pro'); ?>
                </a>
            </li>
        </ul>
        <?php
        $content = (string) ob_get_clean();

        $this->screen->add_help_tab(array(
            'id'      => 'dpro_help_tab_callback',
            'title'   => esc_html__('Support', 'duplicator-pro'),
            'content' => "<p>{$content}</p>",
        ));
    }

    /**
     * Get the help sidebar content shown in the help system
     *
     * @return string
     */
    public static function getHelpSidebarBaseItems()
    {
        ob_start();
        ?>
        <li>
            <i class='fa fa-home'></i> <a href='<?php echo DUPLICATOR_PRO_DUPLICATOR_DOCS_URL; ?>' class='dup-knowledge-base' target='_sc-home'>
                <?php esc_html_e('Knowledge Base', 'duplicator-pro'); ?>
            </a>
        </li>
        <li>
            <i class='fa fa-book'></i> <a href='<?php echo DUPLICATOR_PRO_USER_GUIDE_URL; ?>' class='dup-full-guide' target='_sc-guide'>
                <?php esc_html_e('Full User Guide', 'duplicator-pro'); ?>
            </a>
        </li>
        <li>
            <i class='far fa-file-code'></i> <a href='<?php echo DUPLICATOR_PRO_TECH_FAQ_URL; ?>' class='dup-faqs' target='_sc-faq'>
                <?php esc_html_e('Technical FAQs', 'duplicator-pro'); ?>
            </a>
        </li>
        <?php
        return ob_get_clean();
    }
}
