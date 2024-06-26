jQuery(function ($) {
    if( wc_multistore_data.sites ){
        for ( let i = 0; i < wc_multistore_data.sites.length; i++ ) {
            $.ajax({
                type:		'POST',
                url:		wc_multistore_data.sites[i].adminUrl,
                data:		{ 'key' : wc_multistore_data.sites[i].key, 'action' : wc_multistore_data.sites[i].action, 'nonce' : wc_multistore_data.sites[i].nonce },
                dataType:   'json',
                success:	function( result ) {
                    if( result.status === 'success' && result.data.status === 'success' ){
                        $('.wc-multistore-connected-site-id-' + wc_multistore_data.sites[i].key ).find('.wc-multistore-site-connection').text('Child site version ' + result.data.version );
                    }else{
                        $('.wc-multistore-connected-site-id-' + wc_multistore_data.sites[i].key ).find('.wc-multistore-site-connection').text('Failed: ' + result.code + ' - ' + result.message );
                    }
                },
                error:	function( jqXHR, textStatus, errorThrown ) {
                    $('.wc-multistore-connected-site-id-' + wc_multistore_data.sites[i].key ).find('.wc-multistore-site-connection').text('Failed: ' + jqXHR.status );
                }
            });
        }
    }
});