var woomulti_bulk_run_job = function (data) {
    jQuery.post(ajaxurl, {
        action: "run_woomulti_bulk_update",
        data: data
    }, function (response) {
        window.woomulti_queue_size++;
        response = JSON.parse(response);
        woomulti_bulk_update_progress(response);

        if (response.queue_id && response.queue_id.length > 0) {
            window.location.href = window.location.href + '&queue_id=' + response.queue_id;
            return;
        }

        if (response.status == 'failed') {
            jQuery('#woomulti-restart-update').remove();
            jQuery('.sync-progress').append("<div style='color:red;'>" + response.message + '</div>');
            jQuery('.sync-progress').append("<button type='button' id='woomulti-restart-update' class='button button-primary'> Continue Sync </button>");
        } else if (response.status != 'completed') {
            woomulti_bulk_run_job('');
        } else if (response.status == 'completed') {
            jQuery('#bulk-update-cancel-button').remove();
            jQuery('#bulk-update-reload').show();
        }

    }).fail(function (error) {
        if (error.statusText != 'abort') {
            jQuery('#woomulti-restart-update').remove();
            jQuery('.sync-progress').append("<div style='color:red;'>" + error.statusText + '</div>');
            jQuery('.sync-progress').append("<button type='button' id='woomulti-restart-update' class='button button-primary'> Continue Sync </button>");
        }
    });
};

var woomulti_bulk_update_progress = function (response) {
    if (response.message) {
        jQuery('.sync-progress').append( "<div>" + response.message + "</div>");
    }

    if( response.result !== undefined ){
        for ( i = 0; i < response.result.length; i++ ){
            if( response.result[i] !== undefined ){

                if(response.result[i].status === 'failed'){
                    jQuery('.sync-progress').append("<div style='color:red;'>Failed: " + response.result[i].message + '</div>');
                }

                if(response.result[i].data !== undefined){
                    if( response.result[i].data.variation_errors !== undefined && response.result[i].data.variation_errors !== null ){
                        for ( n = 0; n < response.result[i].data.variation_errors.length; n++ ){
                            jQuery('.sync-progress').append("<div style='color:red;'>Variation: " + response.result[i].data.variation_errors[n].message + '</div>');
                        }
                    }
                }
            }
        }
    }

    if( response.result !== undefined ){
        for ( i = 0; i < response.result.length; i++ ){
            if( response.result[i] !== undefined ){
                if(response.result[i].data !== undefined){
                    if( response.result[i].data.variation_errors !== undefined && response.result[i].data.variation_errors !== null ){
                        for ( n = 0; n < response.result[i].data.variation_errors.length; n++ ){
                            jQuery('.sync-progress').append("<div style='color:red;'>Variation: " + response.result[i].data.variation_errors[n].message + '</div>');
                        }
                    }
                }
            }
        }
    }

    if (response.status == 'completed') {
        jQuery('.sync-progress .wc-multistore-spinner-image').css('display', 'none');
    }
};

function woomulti_sleep(milliseconds) {
    const date = Date.now();
    let currentDate = null;
    do {
        currentDate = Date.now();
    } while (currentDate - date < milliseconds);
}

jQuery(document).ready(function ($) {
    $('.select-all-products').on('change', function () {
        if ($('.select-all-products').is(':checked')) {
            $('.select_categories').attr('disabled', 'disabled');
        } else {
            $('.select_categories').removeAttr('disabled');
        }
    });


    $('#bulk-update-button').on('click', function () {
        /*if (!confirm("The sync may take a long time depending on the number of produts. Do you really want to begin the sync?")) {
            return;
        }*/

        //begin the sync
        $('.sync-progress').show();
        $('#bulk-update-cancel-button').css('visibility', 'visible');
        woomulti_bulk_run_job($('#bulk-update-form').serialize());
    });

    $('#bulk-update-cancel-button').on('click', function () {
        if (confirm("Do you really want to cancel sync?")) {
            // window.woomulti_bulk_sync_request_object.abort();
            // $.post(ajaxurl, {
            //     action: 'cancel_woomulti_bulk_sync'
            // }, function(response) {
            //     window.location.href = $('#bulk-update-cancel-button').attr('data-attr');
            // });
            window.location.href = $('#bulk-update-cancel-button').attr('data-attr');
        }
    });

    $('#bulk-update-reload').on('click', function () {
        window.location.href = $(this).attr('data-attr');
    });

    $('body').on('click', '#woomulti-restart-update', function () {
        console.log('asdasd');
        if (confirm("Do you really want to restart the sync?")) {
            woomulti_bulk_run_job('');
        }
    });

    if ($('#start-update-operation').length) {
        $('.sync-progress').show();
        $('#bulk-update-button').remove();
        $('#bulk-update-cancel-button').css('visibility', 'visible');

        woomulti_bulk_run_job('');
    }

    $('.wc-multistore-bulk-update-categories-select').select2();

    $(document).on("click", '.woonet-checkbox-list .select-all', function (e) {
        var checks = $(this).parents('.woonet-checkbox-list').find(':checkbox').filter(':not(:disabled)');
        checks.prop('checked', $(this).is(':checked') ? 'checked' : '');
    });
});