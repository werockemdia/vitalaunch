<?php
/**
 * Plugin Name: Slide Anything - Responsive Content / HTML Slider and Carousel
 * Plugin URI: https://wordpress.org/plugins/slide-anything/
 * Description: Slide Anything allows you to create a carousel/slider where the content for each slide can be anything you want - images, text, HTML, and even shortcodes. This plugin uses the Owl Carousel jQuery plugin, and lets you create beautiful, touch enabled, responsive carousels and sliders.
 * Version: 2.4.9
 *
 * @package     WordPress_Slide_Anything
 * @author      Simon Edge
 * @copyright   EdgeWebPages
 * @license     GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // EXIT IF ACCESSED DIRECTLY.
}

// SET CONSTANT FOR PLUGIN PATH.
define( 'SA_PLUGIN_PATH', plugins_url( '/', __FILE__ ) );

require 'php/slide-anything-admin.php';
require 'php/slide-anything-frontend.php';

/* ##### PLUGIN ACTIVATION HOOK ##### */
register_activation_hook( __FILE__, 'cpt_slider_plugin_activation' );

/* ##### ADD ACTION HOOKS & FILTERS FOR PLUGIN ##### */
add_action( 'admin_enqueue_scripts', 'cpt_slider_register_admin_scripts', 999999 );
add_action( 'init', 'cpt_slider_register' );
add_action( 'post_row_actions', 'cpt_slider_row_actions', 10, 2 );
add_action( 'add_meta_boxes', 'cpt_slider_add_meta_boxes' );
add_action( 'save_post', 'cpt_slider_save_postdata' );
add_filter( 'manage_sa_slider_posts_columns', 'cpt_slider_modify_columns' );
add_filter( 'manage_sa_slider_posts_custom_column', 'cpt_slider_custom_column_content' );
if ( ! get_option( 'sa-disable-tinymce-button' ) ) {
	add_action( 'admin_head', 'cpt_slider_add_tinymce_button' );
	add_action( 'admin_footer', 'cpt_slider_get_tinymce_shortcode_array', 9999999 );
}
add_action( 'admin_menu', 'cpt_slider_extra_sa_menu_pages' );
add_filter( 'template_include', 'cpt_slider_sa_preview_page_template' );
add_filter( 'wp_kses_allowed_html', 'cpt_slider_allow_iframes_filter' );

// ADD A CHECKBOX OPTION UNDER 'Settings -> Writing' CALLED 'Disable TinyMCE Button'.
add_action( 'admin_init', 'cpt_slider_disable_tinymce_button_setting' );

