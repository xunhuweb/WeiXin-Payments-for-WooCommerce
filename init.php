<?php
/*
 * Plugin Name: WeiXin Payments for WooCommerce
 * Plugin URI: http://www.wpweixin.net/xh/product/103 
 * Description:给Woocommerce系统添加微信支付功能,支持H5支付和扫码支付。若需要企业版本，请联系QQ:<a href="http://wpa.qq.com/msgrd?v=3&uin=6347007&site=qq&menu=yes" target="_blank">6347007</a> 
 * Version: 1.7.0
 * Author: 迅虎网络 
 * Author URI:http://www.wpweixin.net 
 * Text Domain: WeiXin Payments for WooCommerce
 */
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly

if (! defined ( 'XH_WC_WEIXINPAY' )) {
	define ( 'XH_WC_WEIXINPAY', 'XH_WC_WEIXINPAY' );
} else {
	return;
}

function wc_wechatpay_gateway_init() {

    if( !class_exists('WC_Payment_Gateway') )  return;
    include_once( plugin_dir_path(__FILE__) .'/xh-weixinpay-for-wc-core.php');
    load_plugin_textdomain( 'wechatpay', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/'  );

    add_filter('woocommerce_payment_gateways', 'woocommerce_wechatpay_add_gateway' );

    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_wechatpay_plugin_edit_link' );

    $WX = new xh_weixinpay_for_wc_core();
    add_action( 'wp_ajax_WXLoopOrderStatus', array($WX, "WX_Loop_Order_Status" ) );
    add_action( 'wp_ajax_nopriv_WXLoopOrderStatus', array($WX, "WX_Loop_Order_Status") );
    add_action('woocommerce_receipt_wechatpay', array($WX, 'receipt_page'));
}
add_action( 'plugins_loaded', 'wc_wechatpay_gateway_init' );

/**
 * Add the gateway to WooCommerce
 *
 * @access  public
 * @param   array $methods
 * @package WooCommerce/Classes/Payment
 * @return  array
 */
function woocommerce_wechatpay_add_gateway( $methods ) {

    $methods[] = 'xh_weixinpay_for_wc_core';
    return $methods;
}

/**
 * Display WeChatPay Trade No. for customer
 * @param array $total_rows
 * @param mixed $order
 * @return array
 */
function wc_wechatpay_display_order_meta_for_customer( $total_rows, $order ){
    $trade_no = get_post_meta( $order->id, 'WeChatPay Trade No.', true );

    if( !empty( $trade_no ) ){
        $new_row['wechatpay_trade_no'] = array(
            'label' => __( 'WeChatPay Trade No.:', 'wechatpay' ),
            'value' => $trade_no
        );
        // Insert $new_row after shipping field
        $total_rows = array_merge( array_splice( $total_rows,0,2), $new_row, $total_rows );
    }
    return $total_rows;
}
add_filter( 'woocommerce_get_order_item_totals', 'wc_wechatpay_display_order_meta_for_customer', 10, 2 );

function wc_wechatpay_plugin_edit_link( $links ){
    return array_merge(
        array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=xh_weixinpay_for_wc_core') . '">'.__( 'Settings', 'wechatpay' ).'</a>'
        ),
        $links
    );
}
?>