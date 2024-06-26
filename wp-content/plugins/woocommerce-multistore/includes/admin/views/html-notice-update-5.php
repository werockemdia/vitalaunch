<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php  if(is_multisite()):  ?>
<div id="message" class="error woocommerce-message wc-connect">
    <p><strong>Version 5.0.0 contains a major change for multisite environment : The ability to have multiple main stores has been removed and you will need to choose a main store during setup wizard.</strong></p>
    <p><strong>That means that products can only be published from main store to child store and not viceversa anymore </strong></p>
    <p><strong>This also affects import order and global image as orders will be imported on main store by default and images will be used from main store </strong></p>
    <p><strong>Please do not run the setup wizard and data update if you feel the changes are not suitable for you</strong></p>
    <p><strong>In case you run the setup wizard and database update please make sure you make a backup of the database</strong></p>
    <p><strong>If the data update doesn't finish, please restart the process until it finished</strong></p>
</div>
<?php else: ?>
    <div id="message" class="error woocommerce-message wc-connect">
        <p><strong>Version 5.0.0 is a major release : Please make sure you make a database backup before you run the data update</strong></p>
        <p><strong>If the data update doesn't finish, please restart the process until it finishes</strong></p>
        <p><strong>Please don't forget to update all stores from the network to have the same version</strong></p>
    </div>
<?php endif; ?>