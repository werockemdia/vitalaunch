<?php

defined( 'ABSPATH' ) || exit;

?>

<div class='woomulti-bulk-sync-page'>
	<h1> Update all products in your network. </h1>

	<p> A fast way to update all child products with already existing product settings. If you want to publish products please use bulk sync page or product pages where you can customize the settings</p>

	<form id='bulk-update-form' action='#' method='POST'>
		<label class="wc-multistore-checkbox-label">
			<input class='select-all-products' type='checkbox' name='select-all-products' checked='checked' value='1' />
			<span class="checkmark"></span>
		</label>
		<label> Select All Products </label> <br />
		<h2> Select Categories </h2>
        <?php
            if( is_multisite() ){
	            switch_to_blog( (int) get_site_option('wc_multistore_master_store') );
            }

            $all_categories = get_categories(
                array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                )
            );

            if( is_multisite() ){
                restore_current_blog();
            }
        ?>
        <div>
            <label for="select_categories[]"> If you want to select by category, unselect Select All Products </label>
            <select class="wc-multistore-bulk-update-categories-select select_categories" name="select_categories[]" multiple="multiple" disabled = "disabled">
		        <?php foreach ( $all_categories as $cat ) : ?>
                    <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?> ( <?php echo $cat->count; ?> )</option>
		        <?php endforeach; ?>
            </select>
            <p></p>
        </div>

		<div class='sync-progress' style='display: none;'>
			<img class = 'wc-multistore-spinner-image' src='<?php echo WOO_MSTORE_ASSET_URL . '/assets/images/ajax-loader.gif'; ?>' alt='Loader Image'/>
			<div style='display:block;'> <span style='display:block;'> Sync in progress </span> </div>
		</div>
		<?php if ( ! empty( $_REQUEST['queue_id'] ) ) : ?>
			<input type='hidden' id='start-update-operation' name='start-update-operation' value='1' />
		<?php endif; ?>
		<button type='button' id='bulk-update-button' class='button-primary'> Update Selected Products </button>
		<button type='button' data-attr='<?php echo network_admin_url() . 'admin.php?page=woonet-bulk-update-products'; ?>' style='display:none;' id='bulk-update-reload' class='button-primary'> Complete Sync </button>
		<button type='button' data-attr='<?php echo network_admin_url() . 'admin.php?page=woonet-bulk-update-products'; ?>' id='bulk-update-cancel-button' class='button-primary' style="visibility: hidden;"> Cancel </button>
	</form>
</div>
