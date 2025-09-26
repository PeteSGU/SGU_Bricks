jQuery(document).ready(function($) {
    $(document).on('click', '#tnc-pvfw-lite-black-friday-notice .notice-dismiss', function() {
        $.post(tncPvfwLiteAdminNotice.ajax_url, {
            action: 'tnc_pvfw_lite_dismiss_black_friday_notice'
        });
    });
});