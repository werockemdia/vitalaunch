<?php

defined( 'ABSPATH' ) || exit;
$site_url = get_site_url() ;
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
      #adminmenumain {
	display: none;
}
#wpfooter {
	display: none;
}
#wpadminbar {display:none !important;}
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 20px;
  height: 20px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.btn-close{display:none;}
.modal-header {
	display: block !important; 
}
  </style>
<div class="container mt-3">
 
   <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Please Wait Site Creation In Progress......<img style="width:2%;" src = "https://i.gifer.com/7plX.gif" /></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!---<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">-->
    </div>
  </div>
    
  
  
    
  </button>
</div>

<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><Center>Please Wait Site Creation In Progress. This might take couple of moments......</Center></Center></h4><span class="loader"></span>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      

    </div>
  </div>
</div>
<div class='woomulti-bulk-sync-page' style="display:none;">
	<h1> Sync all products in your network. </h1>

	<p> Normally, you would be using the regular sync page as that offers more control. However, when you are setting up the plugin for the first time, you may have a lot of products that you want to sync with your child sites. Or you may need to sync all product data again if you have added product data.
	</p>

	<form id='bulk-sync-form' action='#' method='POST'>
        <label class="wc-multistore-checkbox-label">
		    <input class='select-all-products' type='checkbox' name='select-all-products'  value='1' checked='checked' />
            <span class="checkmark"></span>
        </label>
		<label> Select All Products </label> <br />
		<h2> Select Categories </h2>
		<p> If you want to select by category, unselect Select All Products </p>

		<?php
        if( is_multisite() ){
	        switch_to_blog( (int) get_site_option('wc_multistore_master_store') );
        }

		$all_categories = get_categories(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'exclude' => 50
			)
		);

		if( is_multisite() ){
            restore_current_blog();
        }

		foreach ( $all_categories as $cat ) {
            //$tm_id = $cat->term_id;
            //if($tm_id!=50){
			?>
            <label class="wc-multistore-checkbox-label">
                <input disabled='disabled' type='checkbox' class='select-categories' name='select_categories[]' value='<?php echo $cat->term_id; ?>'  />
                <span class="checkmark"></span>
            </label>

			<label> <?php echo $cat->name; ?> ( <?php echo $cat->count; ?> ) </label> <br />
			<?php
           // }
		}
		?>

		<h2> Select Child Sites </h2>
		<p> Select all the sites you want to sync with. </p>
		<?php
        $sites = WOO_MULTISTORE()->active_sites;
        ?>

        <div class="woonet-checkbox-list">
            <label class="wc-multistore-checkbox-label">
                <input type='checkbox' class='select-all' value='' checked='checked' />
                <span class="checkmark"></span>
            </label>
            <label> Select/Deselect All </label> <br />
            <?php
            foreach ( $sites as $site ) {
                $list_site_url = $site->get_url();
                if($list_site_url == $site_url){
                ?>
                <label class="wc-multistore-checkbox-label">
                    <input type='checkbox' class='select-child-sites child-sites-id-<?php echo $site->get_id(); ?>' name='select_child_sites[]' value='<?php echo $site->get_id(); ?>' checked />
                    <span class="checkmark"></span>
                </label>
                <label> <?php echo $site->get_url(); ?> </label> <br />
                <?php
            }else{
                ?>
                <label class="wc-multistore-checkbox-label">
                    <input type='checkbox' class='select-child-sites child-sites-id-<?php echo $site->get_id(); ?>' name='select_child_sites[]' value='<?php echo $site->get_id(); ?>' />
                    <span class="checkmark"></span>
                </label>
                <label> <?php echo $site->get_url(); ?> </label> <br />
                <?php
            }
            }
            ?>
        </div>


		<h2> Sync Settings </h2>
		<p> Select stock and sync settings. </p>
		<?php
		$sync_options = array(
			'child-sync' => array(
				'label' => 'Child product inherit Parent products changes',
				'value' => 'yes',
			),

			'stock-sync' => array(
				'label' => 'If checked, any stock change will syncronize across product tree',
				'value' => 'yes',
			),
		);

		foreach ( $sync_options as $key => $value ) {
			?>
            <label class="wc-multistore-checkbox-label">
			    <input checked='checked' type='checkbox' class='select-sync-settings <?php echo $key; ?>' name='<?php echo $key; ?>' value='<?php echo $value['value']; ?>' />
                <span class="checkmark"></span>
            </label>
			<label> <?php echo $value['label']; ?> </label> <br />
			<?php
		}
		?>

        	<input type='hidden' id='up_val' name='up_val' value='' />
		<div class='sync-progress' style='display: none;'>
			<img src='<?php echo WOO_MSTORE_ASSET_URL . '/assets/images/ajax-loader.gif'; ?>' alt='Loader Image'/>
			<p style='display:block;'> <span style='display:block;'> Sync in progress </span> </p>
		</div>
		<?php if ( ! empty( $_REQUEST['queue_id'] ) ) : ?>
			<input type='hidden' id='start-sync-operation' name='start-sync-operation' value='1' />
		<?php endif; ?>
		<button type='button' id='bulk-sync-button' class='button-primary'> Sync Selected Products </button>
		<button type='button' data-attr='<?php echo network_admin_url() . 'admin.php?page=woonet-bulk-sync-products'; ?>' style='display:none;' id='bulk-sync-reload' class='button-primary'> Complete Sync </button>
		<button type='button' data-attr='<?php echo network_admin_url() . 'admin.php?page=woonet-bulk-sync-products'; ?>' id='bulk-sync-cancel-button' class='button-primary' style="visibility: hidden;"> Cancel </button>
	</form>
</div>
<?php $check = $_POST['select-all-products'];
if(empty($check)){
    ?>
<script>
            jQuery(document).ready(function () {
               jQuery('.container.mt-3 .btn').click(); 
               jQuery('#bulk-sync-button').click();
                var realConfirm=window.confirm;
                window.confirm=function(){
                  window.confirm=realConfirm;
                  return true;
                };
               window.setTimeout(function() {
                    window.location.href = 'https://vitalaunch.io/my-account/mystore/';
                }, 60000);
               
            });
        </script>
<?php } ?>