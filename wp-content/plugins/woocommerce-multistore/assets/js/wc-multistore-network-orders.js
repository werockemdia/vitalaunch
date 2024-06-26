jQuery(function ($) {
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