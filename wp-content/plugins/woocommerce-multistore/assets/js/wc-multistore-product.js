jQuery( function( $ ) {

    var wc_multistore_product = {
        init: function() {
            let self = this;

            if ($('#woonet_toggle_all_sites').is(":checked")) {
                $('#woonet_data input[type="checkbox"]._woonet_publish_to').each(function () {
                    if ($(this).prop('disabled') == false) {
                        $(this).prop('checked', true);
                        self.set_value(this);
                        self.display_fields($(this));
                    }
                })
            }

            if ($('#woonet_toggle_child_product_inherit_updates').is(":checked")) {
                $('#woonet_data input[type="checkbox"]._woonet_publish_to_child_inheir').each(function () {
                    if ($(this).prop('disabled') == false) {
                        $(this).prop('checked', true);
                        self.set_value(this)
                    }
                })
            }

            if ($('#woonet_toggle_stock_updates').is(":checked")) {
                $('#woonet_data input[type="checkbox"]._woonet_sync_stock').each(function () {
                    if ($(this).prop('disabled') == false) {
                        $(this).prop('checked', true);
                        self.set_value(this)
                    }
                })
            }

            $('#woonet_data input[type="checkbox"]').change(function () {
                self.set_value(this);
            });

            $('#woonet_data input[type="checkbox"]._woonet_publish_to').change(function () {
                self.display_fields($(this));
            });

            $('#woonet_data input[type="checkbox"]._woonet_publish_to').each(function (index, element) {
                self.display_fields(element);
            });

            $('#woonet_data input[type="checkbox"]').each(function (index, element) {
                self.set_value(element)
            });



            $('#woonet_toggle_all_sites').change(function () {
                if ($(this).is(":checked")) {
                    $('#woonet_data input[type="checkbox"]._woonet_publish_to').each(function () {
                        if ($(this).prop('disabled') == false) {
                            $(this).prop('checked', true);
                            self.set_value(this);
                            self.display_fields($(this));
                        }
                    })
                } else {
                    $('#woonet_data input[type="checkbox"]._woonet_publish_to').each(function () {
                        if ($(this).prop('disabled') == false) {
                            $(this).prop('checked', false);
                            self.set_value(this);
                            self.display_fields($(this));
                        }
                    })
                }
            });

            $('#woonet_toggle_child_product_inherit_updates').change(function () {
                if ($(this).is(":checked")) {
                    $('#woonet_data input[type="checkbox"]._woonet_publish_to_child_inheir').each(function () {
                        if ($(this).prop('disabled') == false) {
                            $(this).prop('checked', true);
                            self.set_value(this);
                        }
                    })
                } else {
                    $('#woonet_data input[type="checkbox"]._woonet_publish_to_child_inheir').each(function () {
                        if ($(this).prop('disabled') == false) {
                            $(this).prop('checked', false);
                            self.set_value(this);
                        }
                    })
                }
            });

            $('#woonet_toggle_stock_updates').change(function () {
                if ($(this).is(":checked")) {
                    $('#woonet_data input[type="checkbox"]._woonet_sync_stock').each(function () {
                        if ($(this).prop('disabled') == false) {
                            $(this).prop('checked', true);
                            self.set_value(this);
                        }
                    })
                } else {
                    $('#woonet_data input[type="checkbox"]._woonet_sync_stock').each(function () {
                        if ($(this).prop('disabled') == false) {
                            $(this).prop('checked', false);
                            self.set_value(this);
                        }
                    })
                }
            });


        },
        set_value: function (element){
            $(element).prev('input[type="hidden"]').val(
            $(element).is(":checked") ? 'yes' : 'no'
            );
        },
        display_fields(element){
            var group_id = $(element).closest('p.form-field').attr('data-group-id');

            if ($(element).is(":checked")) {
                $('#woonet_data').find('.form-field.group_' + group_id).slideDown();
                $(element).closest('p.form-field').find('.description .warning').slideUp();
            } else {
                $('#woonet_data').find('.form-field.group_' + group_id).slideUp();
                if ($(element).attr('data-default-value') != '')
                    $(element).closest('p.form-field').find('.description .warning').slideDown();
            }
        }
    };

    $(document).ready(function(){
        wc_multistore_product.init();
    });

});