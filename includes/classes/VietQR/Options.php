<?php
/**
 * This class use for get and set specific VietQR options
 */

namespace VietQR;

class Options {
	private static $instance;

	// Private constructor to prevent direct instantiation
	private function __construct() {}

	// Static method to retrieve the instance of the class
	public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new Options();
		}

		return self::$instance;
	}

	//	Get set authorization code
	public function get_authorization_code () {
		return get_option("vietqr_authorization_code") ?? "";
	}

	public function set_authorization_code ($data) {
		return update_option("vietqr_authorization_code", $data);
	}

	//	Get set bank_transfer_enabled
	public function get_bank_transfer_enabled () {
		return get_option("vietqr_bank_transfer_enabled") ?? false;
	}

	public function set_bank_transfer_enabled ($data) {
		return update_option("vietqr_bank_transfer_enabled", $data);
	}

	//	Get set vietqr_qr_code_enabled
	public function get_qr_code_enabled () {
		return get_option("vietqr_qr_code_enabled") ?? false;
	}

	public function set_qr_code_enabled ($data) {
		return update_option("vietqr_qr_code_enabled", $data);
	}

	//	Get set vietqr_transaction_prefix
	public function get_transaction_prefix () {
		return get_option("vietqr_transaction_prefix") ?? "";
	}

	public function set_transaction_prefix ($data) {
		return update_option("vietqr_transaction_prefix", $data);
	}

	//	Get set selected bank account
	public function get_selected_bank_account () {
		return unserialize(get_option("vietqr_selected_bank_account")) ?? [];
	}

	public function set_selected_bank_account (array $data) {
		$serialize_data = serialize($data);
		return update_option("vietqr_selected_bank_account", $serialize_data);
	}

	//	Get set account list
	public function get_bank_account_list () {
		return unserialize(get_option("vietqr_bank_account_list")) ?? [];
	}

	public function set_bank_account_list (array $data) {
		$serialize_data = serialize($data);
		return update_option("vietqr_bank_account_list", $serialize_data);
	}

	//	Get set account current
	public function get_account_current () {
		$current = get_option("vietqr_account_current");
		return (!empty($current)) ? $current : 0;
	}

	public function set_account_current ($data) {
		return update_option("vietqr_account_current", $data);
	}

}