<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WechatPaymentException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
