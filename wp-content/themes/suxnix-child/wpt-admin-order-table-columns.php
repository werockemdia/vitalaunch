<?php

/**
 * Headwall WP Tutorials Admin Order Table Columns (WPTAOTC)
 *
 * https://wp-tutorials.tech/add-functionality/add-custom-column-to-the-woocommerce-orders-table/
 *
 */

defined('WPINC') || die();

const WPTAOTC_SHIPPING_COUNTRY_COLUMN_NAME = 'shipping_country';
const WPTAOTC_BILLING_COUNTRY_COLUMN_NAME = 'billing_country';
// Add more custom column name constants in here...
// ...

/**
 * Enqueue admin styles to support our custom columns.
 */
function wptaotc_admin_enqueue_scripts() {
   $base_uri = get_stylesheet_directory_uri();
   $theme_version = wp_get_theme()->get('Version');

   wp_enqueue_style(
      'wptaotc',
      $base_uri . '/wpt-admin-order-table-columns.css',
      null,
      $theme_version
   );
}
add_action('admin_enqueue_scripts', 'wptaotc_admin_enqueue_scripts');

/**
 * A utility function to inject an associative array into an existing
 * associative array, after a specific index.
 */
function wptaotc_insert_into_array_after_key(array $source_array, string $key, array $new_element) {
   if (array_key_exists($key, $source_array)) {
      $position = array_search($key, array_keys($source_array)) + 1;
   } else {
      $position = count($source_array);
   }

   $before = array_slice($source_array, 0, $position, true);
   $after = array_slice($source_array, $position, null, true);
   return array_merge($before, $new_element, $after);
}

/**
 * Convert two-character country codes like "gb", "us", "de", "in" into an
 * HTML snippet for the country's flag. Cache snippets in $wptaotc_flag_htmls
 * so if we repeatedly request snippets for the same country, it should make
 * things a bit faster.
 */
function wptaotc_get_flag_html(string $country_code) {
   global $wptaotc_flag_htmls;

   if (is_null($wptaotc_flag_htmls)) {
      $wptaotc_flag_htmls = array();
   }

   if (empty($country_code)) {
      // $country_code cannot be blank.
   } elseif (array_key_exists($country_code, $wptaotc_flag_htmls)) {
      // We've already created the HTML for this country_code
      // and stored it in $wptaotc_flag_htmls
   } else {
      $wptaotc_flag_htmls[$country_code] = sprintf(
         '<span class="country-flag"><img src="%s/country-flags/%s.svg" /></span>',
         get_stylesheet_directory_uri(),
         strtolower($country_code)
      );
   }

   $html = null;
   if (array_key_exists($country_code, $wptaotc_flag_htmls)) {
      $html = $wptaotc_flag_htmls[$country_code];
   }

   return $html;
}