(function ($) {
    function queryOrderStatus() {
        $.ajax({
            type: "POST",
            url: wc_checkout_params.ajax_url,
            data: {
                orderId: $('#xh-wechat-payment-pay-img').attr('data-oid'),
                action: "XH_WECHAT_PAYMENT_GET_ORDER"
            }
        }).done(function (data) {
            data = JSON.parse(data);
            if (data && data.status === "paid") {
                location.href = data.url;
            } else {
            	setTimeout(queryOrderStatus, 2000);
            }
        });
    }

   $(function(){
	   var qrcode = new QRCode(document.getElementById('xh-wechat-payment-pay-img'), {
	        width : 282,
	        height : 282
	    });
	    
	    qrcode.makeCode($('#xh-wechat-payment-pay-url').val());
	    queryOrderStatus();
   });
})(jQuery);