jQuery( function( $ ) {

    var wc_multistore_ajax_sync = {
        notice_container: $('.wc-multistore-ajax-sync-notice-container'),
        wp_notice_list: $('#wp__notice-list'),
        cancel_sync_container: $('.wc-multistore-ajax-sync-notice-cancel-sync'),
        progress_bar: $( ".wc-multistore-ajax-notice-progress-bar" ),
        close_button: $('.wc-multistore-ajax-sync-notice-close a'),
        nonce: $('#wc_multistore_ajax_sync_nonce').val(),
        action: false,
        cancel_action: false,
        transient: false,
        init: function() {
            if ( this.notice_container.length < 1 ){
                return;
            }

            let t = this;

            t.action = t.notice_container.data('action');
            t.cancel_action = t.cancel_sync_container.data('action');
            t.transient = t.notice_container.data('transient');

            t.progress_bar.progressbar({ value: 0 });

            t.cancel_sync_container.on('click', function (){
                return t.cancel();
            });

            t.close_button.on( 'click', function (){
                return t.close_container();
            } );

            t.display_container();

            t.request();
        },
        close_container: function (){
            this.notice_container.slideUp()
        },
        display_container: function (){
            this.wp_notice_list.show()
        },
        update_progress: function (product_id, percentage){
            $( "#wc-multistore-ajax-sync-notice-product-id-"+product_id ).find('.wc-multistore-ajax-sync-notice-progress-bar').progressbar({
                value:  percentage
            });
        },
        complete:function(){
            $('.wc-multistore-ajax-sync-notice-completed').show();
            this.cancel_sync_container.hide();
            this.close_counter();
        },
        failed: function(product_id,message){
            let error = '<p class="notice-error">' + message + '</p>'

            if( product_id === null ){
                this.notice_container.find('.wc-multistore-ajax-sync-notice-message').append(error);
            }else{
                $( "#wc-multistore-ajax-sync-notice-product-id-"+product_id ).find('.wc-multistore-ajax-sync-notice-message').append(error);
            }
        },
        cancel:function(){
            if (confirm("Do you really want to cancel sync?") ) {
                window.woomulti_product_sync_request_object.abort();
                let params = {
                    action: wc_multistore_ajax_sync.cancel_action,
                    transient: wc_multistore_ajax_sync.transient,
                    nonce: wc_multistore_ajax_sync.nonce,
                };
                params = $.param(params);

                $.post(ajaxurl, params, function(response) {
                    wc_multistore_ajax_sync.notice_container.slideUp();
                });
            }
        },
        close_counter:function(){
            $('.wc-multistore-ajax-sync-notice-close').show();

            window.woo_multi_close_counter = setInterval(function(){
                var counter = $('.wc-multistore-ajax-sync-notice-close a').attr('data-attr');

                if (counter <= 0) {
                    $('.wc-multistore-ajax-sync-notice-container').slideUp();
                    window.clearInterval( window.woo_multi_close_counter );
                } else {
                    $('.wc-multistore-ajax-sync-notice-close a').text("Close (" + counter + ") ");
                    counter = counter - 1;
                    $('.wc-multistore-ajax-sync-notice-close a').attr('data-attr', counter);
                }
            }, 1000);
        },
        update_column: function(product_id, result, site_id ){
            $( '#post-' + product_id ).find('.woonet-quick-edit-site-id-'+ site_id + ' a').attr('href', result.data.edit_link);
            $( '#post-' + product_id ).find('.woonet-quick-edit-site-id-'+ site_id + ' a').text('edit');
        },
        request: function(){
            let params = {
                action: wc_multistore_ajax_sync.action,
                transient: wc_multistore_ajax_sync.transient,
                nonce: wc_multistore_ajax_sync.nonce,
            };
            params = $.param(params);

            this.post(params);
        },
        variations_errors: function(data){
            if( data.result !== null && data.result !== undefined && data.product_id !== undefined ){
                if( data.result.data !== null && data.result.data !== undefined && data.result.data.variation_errors !== null && data.result.data.variation_errors !== undefined ){
                    for ( i = 0; i < data.result.data.variation_errors.length; i++ ){
                        $( "#wc-multistore-ajax-sync-notice-product-id-"+ data.product_id ).find('.wc-multistore-ajax-sync-notice-message').append('<p class="notice-error">Variation: ' + data.result.data.variation_errors[i].message + '</p>');
                    }
                }
            }
        },
        post: function(params){
            window.woomulti_product_sync_request_object = $.post(ajaxurl, params, function(data){
                data = JSON.parse(data);
                if (data.status === 'failed') {
                    wc_multistore_ajax_sync.failed(data.message);
                } else {
                    if ( data.percentage ) {
                        wc_multistore_ajax_sync.update_progress(data.product_id,data.percentage);
                        wc_multistore_ajax_sync.variations_errors(data);
                    }

                    if ( data.status === 'completed' ) {
                        wc_multistore_ajax_sync.complete();
                        wc_multistore_ajax_sync.variations_errors(data);
                        return true;
                    } else {
                        if( data.result.status === 'failed' ){
                            wc_multistore_ajax_sync.failed(data.product_id,data.result.message);
                        }
                        wc_multistore_ajax_sync.post(params);
                    }
                    // console.log(data);
                    if( data.result !== undefined && data.site_id !== undefined ){
                        wc_multistore_ajax_sync.update_column( data.product_id, data.result, data.site_id);
                    }
                }
            }).fail(function(error) {
                if (error.statusText !== 'abort') {
                    wc_multistore_ajax_sync.failed( null,"Sync failed due to server error. Please try again.");
                }
            });
        }

    };

    $(document).ready(function(){
        wc_multistore_ajax_sync.init();
    });

});