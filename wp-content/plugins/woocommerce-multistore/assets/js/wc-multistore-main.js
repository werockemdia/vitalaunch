jQuery(function ($) {
    $('.wc-multistore-select-switch').on('click', function () {

        $(this).toggleClass('selected');
        var select_option = $(this).next('select').val();
        var next_option = '';

        if( select_option === 'yes' ){
            next_option = 'no';
        }else{
            next_option = 'yes';
        }

        $(this).next('select').val(next_option).change();
        $(this).next('select option[value=' + next_option + ']').attr('selected', 'selected');
    });

    $('.wc-multistore-checkbox-switch').on('click', function (el) {

        $(this).toggleClass('selected');
        var checked = $(el).hasClass("selected");
        // console.log(checked);
        // console.log($(el).next('.inline'));
        // if( checked ){
            $(el).next('.inline').checked(checked);
        // }else{
        //     $(el).next('.inline').checked(checked);
        // }

        // $(this).next('input').val(next_option).change();
        // $(this).next('input').attr('checked', 'checked');
    });


    $('.woonet-network-type-whats-difference-btn').on('click', function () {
        $('.woonet-network-type-whats-difference').toggle();
    });

    $('.woonet-wizard-option').on('change', function () {
        window.location.href = $(this).attr('data-target-url');
    });

    $('#woonet-add-child-site button').on('click', function () {
        if ($('#woonet-add-child-site input').val() == "") {
            $('.error').html("<p> URL can not be empty. </p>");
            $('.error').css('display', 'block');
            return;
        }

        var data = {
            'action': 'wc_multistore_connect_child_site',
            'url': $('#woonet-add-child-site input').val(),
            'nonce': $('#wc_multistore_connect_child_site_nonce').val()
        };

        $.post(ajaxurl, data, function (response) {
            if (response.error) {
                $('.error').html("<p>" + response.message + "</p>");
                $('.error').css('display', 'block');
            }

            if (response.success) {
                $('.error').hide();
                $('#woonet-add-child-site').hide();
                $('#woonet-copy-code').val(response.copy_url);
                $('#woonet-copy-code-form').show();
            }
        });
    });

    $('.wc_multistore_save_child_site').on('click', function () {
        var url = $(this).parents('tr').find('.wc_multistore_child_site_name').val();
        var site_id = $(this).val();
        var data = {
            'action': 'wc_multistore_save_child_site',
            'url': url,
            'id': site_id,
            'nonce': $('#_wpnonce').val()
        };

        $.post(ajaxurl, data, function (response) {
            var data = response;
            if (data.status === 'failed') {
                window.location.href = window.location.href;
                $('.error').html("<p>" + data.message + "</p>");
                $('.error').css('display', 'block');
            }

            if (data.status === 'success') {
                window.location.href = window.location.href;
                $('.notice-success').html("<p>" + data.message + "</p>");
                $('.notice-success').css('display', 'block');
            }
        });
    });

    $('#woonet-add-master-site button').on('click', function () {
        var data = {
            'action': 'wc_multistore_connect_master_site',
            'url': $('#wc_multistore_connect_master_code').val(),
            'nonce': $('#wc_multistore_connect_master_site_nonce').val(),
        };

        $.post(ajaxurl, data, function (response) {
            var data = $.parseJSON(response);

            if (data.error) {
                $('.error').html("<p>" + data.message + "</p>");
                $('.error').css('display', 'block');
            }

            if (data.success) {
                window.location.href = window.location.href;
                $('.notice-success').html("<p>" + data.message + "</p>");
                $('.notice-success').css('display', 'block');
                $('#woonet-add-child-site input').val(data.copy_url);
            }
        });
    });

    $('#woonet-delete-master-site button').on('click', function () {
        let nonce = $('#wc_multistore_delete_master_site_nonce').val();
        var data = {
            'action': 'wc_multistore_delete_master_site',
            'nonce': nonce
        };

        $.post(ajaxurl, data, function (response) {
            var data = $.parseJSON(response);

            if (data.status === 'failed') {
                $('.error').html("<p>" + data.message + "</p>");
                $('.error').css('display', 'block');
            }

            if (data.status === 'success') {
                window.location.href = window.location.href;
            }
        });
    });

    $('.woonet-taxonomy-select-all').on('click', function (event) {
        $(this).toggleClass('active');
        event.preventDefault();
        if( $(this).hasClass('active') ){
            $('input[type=checkbox]').prop('checked', false);
        }else{
            $('input[type=checkbox]').prop('checked', true);
        }

    })

    $('.woonet-taxonomy-select-all-sites').on('click', function (event) {
        $(this).toggleClass('active');
        event.preventDefault();
        if( $(this).hasClass('active') ){
            $('input[type=checkbox]', $(this).parent()).prop('checked', true);
        }else{
            $('input[type=checkbox]', $(this).parent()).prop('checked', false);
        }

    })

    $('.woonet_site_filter').on('change', function (event) {
        if ($(this).val() === 'all') {
            window.location.href = $(this).attr('data-attr');
        } else {
            window.location.href = $(this).attr('data-attr') + '&woonet_site_filter=' + $(this).val();
        }
    })

    $('.woo-network-order-actions .wc-action-button-processing, .woo-network-order-actions .wc-action-button-complete, .woo-network-order-actions .wc-action-button-cancel').on('click', function (event) {
        if (!confirm("Order status will be updated immediately. Do you want to proceed?")) {
            event.preventDefault();
        }
    })

    $('.woo_multistore_alert_nag').on('click', function () {
        if (confirm("Never ask to install addon?")) {
            $.post(ajaxurl, {
                'action': $(this).attr('dismiss-action')
            });
        }
    });

    $('.wc-action-button-refund').on( 'click', function( event ) {
       if ( ! confirm( "Are you sure you wish to process this refund? This action cannot be undone." ) ) {
           event.preventDefault();
       }
    });

    $('.bulkactions input[type=submit]').on( 'click', function( event ) {
        if ( $('.bulkactions select[name=action]' ).val() != 'refund' ) {
            return;
        }

        if ( ! confirm( "Are you sure you wish to process this refund? This action cannot be undone." ) ) {
            event.preventDefault();
        }
    });
});