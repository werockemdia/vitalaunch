jQuery( function( $ ) {

    var wc_multistore_master_stock = {
        master_products: wc_multistore_master_stock_data.master_products,
        init: function() {
        },
        sendRequestToChildren: function(){
            $.each( this.master_products, function ( key, value ) {
                $.each( value, function ( key2, value2 ) {
                    // console.log(value2);
                    $.ajax({
                        type:		'POST',
                        url:		value2.ajax_url,
                        data:		value2,
                        dataType:   'json',
                        success:	function( result ) {
                            // console.log(result)
                        },
                        error:	function( jqXHR, textStatus, errorThrown ) {
                            // console.log(textStatus)
                        }
                    });
                } );
            } );
        },
    };

    wc_multistore_master_stock.init();
    wc_multistore_master_stock.sendRequestToChildren();

});
