<?php

defined( 'ABSPATH' ) || exit;

add_action('WOO_MSTORE_admin_product/process_product', 'deprecate_process_product_hook');
function deprecate_process_product_hook(){
	wc_deprecated_hook('WOO_MSTORE_admin_product/process_product', '', '', 'WOO_MSTORE_admin_product/process_product hook is deprecated');
}


add_action('WOO_MSTORE_admin_product/master_product_meta_to_exclude', 'deprecate_master_product_meta_to_exclude_hook');
function deprecate_master_product_meta_to_exclude_hook(){
	wc_deprecated_hook('WOO_MSTORE_admin_product/master_product_meta_to_exclude', '', 'wc_multistore_whitelisted_meta_keys', 'WOO_MSTORE_admin_product/master_product_meta_to_exclude hook is deprecated');
}

add_action('WOO_MSTORE_admin_product/is_product_inherit_updates', 'deprecate_is_product_inherit_updates_hook');
function deprecate_is_product_inherit_updates_hook(){
	wc_deprecated_hook('WOO_MSTORE_admin_product/is_product_inherit_updates', '', '', 'WOO_MSTORE_admin_product/is_product_inherit_updates hook is deprecated');
}


add_action('WOO_MSTORE_admin_product/master_slave_products_data_diff', 'deprecate_master_slave_products_data_diff_hook');
function deprecate_master_slave_products_data_diff_hook(){
	wc_deprecated_hook('WOO_MSTORE_admin_product/master_slave_products_data_diff', '', 'wc_multistore_child_product_data', 'WOO_MSTORE_admin_product/master_slave_products_data_diff hook is deprecated');
}

add_action('WOO_MSTORE_admin_product/slave_product_updated', 'deprecate_slave_product_updated_hook');
function deprecate_slave_product_updated_hook(){
	wc_deprecated_hook('WOO_MSTORE_admin_product/slave_product_updated', '', 'wc_multistore_child_product_saved', 'WOO_MSTORE_admin_product/slave_product_updated hook is deprecated');
}

add_action('WOO_MSTORE_SYNC/sync_child/complete', 'deprecate_slave_product_updated_single_hook');
function deprecate_slave_product_updated_single_hook(){
	wc_deprecated_hook('WOO_MSTORE_SYNC/sync_child/complete', '', 'wc_multistore_child_product_saved', 'WOO_MSTORE_SYNC/sync_child/complete hook is deprecated');
}