<?php

namespace VietQR;

/**
 * Class for using some VietQR specifics helper functions
 */
class Ultility {
	private static $instance;

	// Private constructor to prevent direct instantiation
	private function __construct() {}

	// Static method to retrieve the instance of the class
	public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new Ultility();
		}

		return self::$instance;
	}

	/**
	 * Generate VQR code
	 *
	 * @return string
	 */
	public function generate_vqr_code() : string {
		$prefix = 'VQR';
		$randomNumber = mt_rand(0, 9999999999);
		$paddedNumber = str_pad($randomNumber, 10, '0', STR_PAD_LEFT);
		$vqrCode = $prefix . $paddedNumber;

		return $vqrCode;
	}

	/**
	 * Get WooCommerce order status text
	 *
	 * @param $status
	 *
	 * @return string
	 */
	public function get_woo_order_status_text($status) : string {
		switch ($status) {
			case 'completed':
				return 'Đơn hàng thành công';

			case 'pending':
				return 'Đơn hàng đang chờ';

			case 'processing':
				return 'Đơn hàng đang xử lý';

			case 'on-hold':
				return 'Đơn hàng đang chờ xử lý';

			case 'refunded':
				return 'Đã hoàn tiền';

			case 'cancelled':
				return 'Đơn hàng đã hủy';

			case 'failed':
				return 'Đơn hàng thất bại';

			// Add more cases for additional WooCommerce order statuses here

			default:
				return '';
		}
	}

}
