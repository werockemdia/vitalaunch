jQuery( function( $ ) {

    var wc_multistore_master_order = {
        data: wc_multistore_order_data.data,
        init: function() {
            // console.log(this.data);
            // console.log(wc_checkout_params.ajaxurl);
        },
        sendRequestToMasterSite: function(){
            $.ajax({
                type:		'POST',
                url:		wc_multistore_master_order.data.ajax_url,
                data:		wc_multistore_master_order.data,
                dataType:   'json',
                success:	function( result ) {
                    // console.log(result)
                },
                error:	function( jqXHR, textStatus, errorThrown ) {
                    // console.log(textStatus)
                }
            });
        },
    };

    wc_multistore_master_order.init();
    wc_multistore_master_order.sendRequestToMasterSite();

});
