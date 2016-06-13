<?php
/*
 * Plugin Name: WeiXin Payments for WooCommerce
 * Plugin URI: http://www.wpweixin.net/xh/product/103 
 * Description:给Woocommerce系统添加微信支付功能,支持扫码支付和退款功能。若需要企业版本，请联系QQ:<a href="http://wpa.qq.com/msgrd?v=3&uin=6347007&site=qq&menu=yes" target="_blank">6347007</a> 
 * Version: 1.8.0
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

define('XH_WC_WeChat_DIR',rtrim(plugin_dir_path(__FILE__),'/'));
define('XH_WC_WeChat_URL',rtrim(plugin_dir_url(__FILE__),'/'));


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
    add_action ( 'woocommerce_update_options_payment_gateways_' . $WX->id, array ($WX,'process_admin_options') ); // WC >= 2.0
    add_action ( 'woocommerce_update_options_payment_gateways', array ($WX,'process_admin_options') );
    add_action ( 'wp_enqueue_scripts', array ($WX,'WX_enqueue_script_onCheckout') );
}

 function xh_isWebApp(){
		if(isset($_GET['mobile'])){
			return true;
		}
	
		if(!isset($_SERVER['HTTP_USER_AGENT'])){
			return false;
		}
	
		$u=strtolower($_SERVER['HTTP_USER_AGENT']);
		if($u==null||strlen($u)==0){
			return false;
		}
	
		preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/',$u,$res);
	
		if($res&&count($res)>0){
			return true;
		}
	
		if(strlen($u)<4){
			return false;
		}
	
		preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/',substr($u,0,4),$res);
		if($res&&count($res)>0){
			return true;
		}
	
		$ipadchar = "/(ipad|ipad2)/i";
		preg_match($ipadchar,$u,$res);
		if($res&&count($res)>0){
			return true;
		}
	
		return false;
	}
	
function xh_isWeixinClient(){
	return strripos($_SERVER['HTTP_USER_AGENT'],'micromessenger');
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

// /**
//  * Display WeChatPay Trade No. for customer
//  * @param array $total_rows
//  * @param mixed $order
//  * @return array
//  */
// function wc_wechatpay_display_order_meta_for_customer( $total_rows, $order ){
//     $trade_no = get_post_meta( $order->id, 'WeChatPay Trade No.', true );

//     if( !empty( $trade_no ) ){
//         $new_row['wechatpay_trade_no'] = array(
//             'label' => __( 'WeChatPay Trade No.:', 'wechatpay' ),
//             'value' => $trade_no
//         );
//         // Insert $new_row after shipping field
//         $total_rows = array_merge( array_splice( $total_rows,0,2), $new_row, $total_rows );
//     }
//     return $total_rows;
// }
// add_filter( 'woocommerce_get_order_item_totals', 'wc_wechatpay_display_order_meta_for_customer', 10, 2 );

function wc_wechatpay_plugin_edit_link( $links ){
    return array_merge(
        array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=xh_weixinpay_for_wc_core') . '">'.__( 'Settings', 'wechatpay' ).'</a>'
        ),
        $links
    );
}
?>