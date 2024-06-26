jQuery( function( $ ) {

    var wc_multistore_bulk_edit = {
        init: function() {
            $('._woonet_global_publish_to').change(function () {
                $('._woonet_publish_to').val(this.value).change();
            });

            $('._woonet_global_inherit').change(function () {
                $('._woonet_inherit').val(this.value).change();
            });

            $('._woonet_global_stock').change(function () {
                $('._woonet_stock').val(this.value).change();
            });
        },
    };

    $(document).ready(function(){
        wc_multistore_bulk_edit.init();
    });

});