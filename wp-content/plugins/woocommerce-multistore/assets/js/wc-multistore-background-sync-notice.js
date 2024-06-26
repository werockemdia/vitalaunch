jQuery( document ).ready(function($) {
    $('.wc-multistore-background-sync-notice-close-sync-screen a').on( 'click', function() {
        $('.wc-multistore-background-sync-notice-container').slideUp();
    });

    if ( $('.wc-multistore-background-sync-notice-container').length >= 1) {
        if ( $('#wp__notice-list').length ) {
            $('#wp__notice-list').show(); // show sync dialogue hidden by woocommerce admin.
            woomulti_close_counter();
        }
    }
});

var woomulti_close_counter = function (msg='Close (10)') {
    window.woo_multi_close_counter = setInterval(function(){
        var counter = jQuery('.wc-multistore-background-sync-notice-close-sync-screen a').attr('data-attr');

        if (counter <= 0) {
            jQuery('.wc-multistore-background-sync-notice-container').slideUp();
            window.clearInterval( window.woo_multi_close_counter );
        } else {
            jQuery('.wc-multistore-background-sync-notice-close-sync-screen a').text("Close (" + counter + ") ");
            counter = counter - 1;
            jQuery('.wc-multistore-background-sync-notice-close-sync-screen a').attr('data-attr', counter);
        }
    }, 1000);
};