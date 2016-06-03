/**
 * Created by setler on 3.6.16.
 */
(function($){
    $(function(){
        if(typeof customer_login_link != 'undefined' && typeof customer_login != 'undefine') {
            $('#container-customer .panel-heading:first').append(
                '<div style="line-height: 0; position: absolute; top: 0; right: 65px;">' +
                    '<a class="btn btn-default" href="' + customer_login_link + '" style="color: #00aff0;">' +
                        customer_login +
                    '</a>' +
                '</div>'
            );
        }
    });
})(jQuery);