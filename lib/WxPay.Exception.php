<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WxPayException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
