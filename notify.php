<?php
define('XH_LOGIN_IGNORE', true);
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');
$core =new xh_weixinpay_for_wc_core();
$core->check_wechatpay_response();