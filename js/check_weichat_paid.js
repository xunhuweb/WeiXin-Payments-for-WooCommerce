(function ($) {
    var loopCnt = 50;
    var looptime = 300; //ms

    function queryOrderStatus() {
        var orderId = $('#WxQRCode').attr('OId');
        $.ajax({
            type: "GET",
            url: wc_checkout_params.ajax_url,
            data: {
                orderId: orderId,
                action: "WXLoopOrderStatus"
            }
        }).done(function (data) {
            data = JSON.parse(data);
            if (data && data.status === "Paid") {
                location.href = data.url;
            } else {
            	setTimeout(queryOrderStatus, looptime);
            }
        });
    }

    $(function () {
        queryOrderStatus();
    });

})(jQuery);