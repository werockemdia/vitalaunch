jQuery( function( $ ) {

    var wc_multistore_child_stock = {
        childProducts: wc_multistore_child_stock_data.child_products,
        init: function() {
        },
        sendRequestToMaster: function(){
            $.each( this.childProducts, function ( key, value ) {
                $.ajax({
                    type:		'POST',
                    url:		value.ajax_url,
                    data:		value,
                    dataType:   'json',
                    success:	function( result ) {
                        // console.log(result)
                    },
                    error:	function( jqXHR, textStatus, errorThrown ) {
                        // console.log(textStatus)
                    }
                });
            } );
        },
    };

    wc_multistore_child_stock.init();
    wc_multistore_child_stock.sendRequestToMaster();

});
