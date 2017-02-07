define(['jquery'], function ($) {
    $(document).ready(function(){

        /* are there already values to handle in hidden field? */
        if ($('input[type="hidden"][name*="tx_cfsc_responsive"]').length > 0) {
            $.each($('input[type="hidden"][name*="tx_cfsc_responsive"]'), function(k,v) {
                if ($(this).val() != '') {
                    var _json = $.parseJSON($(this).val());
                    $that = $(this);
                    $.each(_json, function (k, object) {
                        $that.parent().find('input[name="' + object.name + '"]').val(object.value);
                    });
                }
            });
        }

        /* fill hidden field with responsive values */
        $('form#EditDocumentController').on('submit', function(){
            if ($('input[name*="tx_cfsc_responsive"]').length > 0) {
                $.each($('input[name*="tx_cfsc_responsive"]'), function(k,v) {
                    $(this).val(JSON.stringify($(this).parent().find('input[type="text"]').serializeArray()));
                });
            }

            if ($('select[name*="breakpoint"]').length > 0) {
                $.each($('input[name*="tx_cfsc_responsive"]'), function(k,v){
                    console.log("bla");
                    console.log($(this).parent().find('select'));

                });
            }
        });
    });
});