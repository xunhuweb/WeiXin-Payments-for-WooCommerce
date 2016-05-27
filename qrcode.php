<?php
define('XH_LOGIN_IGNORE', true);
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

error_reporting ( E_ERROR );
if(!class_exists('QRcode')){
	require_once './lib/phpqrcode/phpqrcode.php';
	
}

$value = urldecode ( $_GET ["data"] ); // 二维码内容
$errorCorrectionLevel = 'L'; // 容错级别
$matrixPointSize = 9; // 生成图片大小
$logopath = false;//XH_WEIXINPAY_FOR_WC_DIR.'/images/qrcode/middle-logo.png';

$QR = QRcode::image ( $value, $errorCorrectionLevel, $matrixPointSize, 1 );

// 生成二维码图片
if ($logopath) {
	$logo = imagecreatefrompng ( $logopath );
	if ($logo) {
		$QR_width = imagesx ( $QR ); // 二维码图片宽度
		$QR_height = imagesy ( $QR ); // 二维码图片高度
		
		$logo_width = imagesx ( $logo ); // logo图片宽度
		$logo_height = imagesy ( $logo ); // logo图片高度
		
		$logo_qr_width = $QR_width / 5;
		$scale = $logo_width / $logo_qr_width;
		
		$logo_qr_height = $logo_height / $scale;
		$from_width = ($QR_width - $logo_qr_width) / 2;
		
		// 重新组合图片并调整大小
		imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
	}
}

header ( "Content-type: image/png" );
imagepng ( $QR );
