<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/public
 * @author     Your Name <email@example.com>
 */
class VietQR_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in VietQR_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The VietQR_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );
		wp_enqueue_style( 'jquery-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css', array(), '1.13.2' );
		wp_enqueue_style( 'print-js', 'https://printjs-4de6.kxcdn.com/print.min.css', array(), '1.13.2' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in VietQR_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The VietQR_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vietqr-plugin-public.js', array( 'jquery' ), $this->version, false );
//		wp_enqueue_script( 'html-to-image', plugin_dir_url( __FILE__ ) . 'vendor/html-to-image/html-to-image.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'dom-to-image', plugin_dir_url( __FILE__ ) . 'vendor/dom-to-image/dom-to-image.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'print-js', 'https://printjs-4de6.kxcdn.com/print.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js', array(), '3.6.0', true );
		wp_enqueue_script( 'jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array( 'jquery' ), '1.13.2', true );
	}

	/**
	 * Register rest endpoints
	 *
	 * @return void
	 */
	public function register_rest_endpoint() {
		register_rest_route('vietqr', 'sync', array(
			'methods'  => 'POST',
			'callback' => array($this, 'transaction_sync'),
			'permission_callback' => '__return_true'
		));

		register_rest_route('vietqr', 'recheck', array(
			'methods'  => 'POST',
			'callback' => array($this, 'transaction_recheck'),
			'permission_callback' => '__return_true'
		));
	}

	/**
	 * VietQR sync endpoint handler
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function transaction_sync(WP_REST_Request $request) {

		// Compare if any transaction meet requirement
		$order_id = $request->get_param("orderId") ?? null;
		$amount = $request->get_param("amount") ?? null;
		$transaction_time = $request->get_param("transactiontime") ?? null;
		$bank_transaction_id = $request->get_param("referencenumber") ?? "";

		// response
		$response = array(
			'error'  => false,
			'errorReason' => 'Không có', // Nguyên nhân lỗi
			'toastMessage' => 'Không có', // Mô tả lỗi
			'object' => [
				'reftransactionid' => $bank_transaction_id
			],
		);

		/* Get woocommerce order by $order_id */
		$order = wc_get_order($order_id);
		if (!empty($order)) {
			if ($order->get_status() === "completed") {
				$response["toastMessage"] = "Order đã thanh toán";
			} else if ( $amount == format_currency_to_number($order->get_total()) ) {
				$order->set_status("completed");
				$order->save();
				update_post_meta($order_id, "vietqr_bank_transaction_id", $bank_transaction_id);
				update_post_meta($order_id, "vietqr_transaction_time", $transaction_time);
				$response["toastMessage"] = "Order thanh toán thành công";
			} else {
				$response["toastMessage"] = "Thông tin order không khớp";
			}
		} else {
			$response["toastMessage"] = "Order id không tồn tại";
		}

		echo json_encode($response);
		die();
	}

	/**
	 * Recheck Woocommerce order status
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function transaction_recheck(WP_REST_Request $request) {

		$order_id = $request->get_param("order_id");

		$order = wc_get_order($order_id);
		$status = false;
		$message = "";

		if (empty($order)) {
			$message = "order không tồn tại";
		}else {
			if ($order->get_status() == "completed") {
				$status = true;
				$message = "Đã thanh toán thành công";
			} else {
				$message = "Chưa thanh toán";
			}
		}

		wp_send_json_success(["status" => $status, "message" => $message]);
		die();
	}

	/**
	 * Woo thank you page custom
	 * @param $order_id
	 *
	 * @return void
	 * @throws Exception
	 */
	function woo_custom_thankyou_page($order_id) {
		set_query_var( 'order_id', $order_id );
		require_once VIETQR_PLUGIN_PUBLIC_TEMPLATE_DIR . "/woo_thank_you_page_vietqr_code.php";
	}

	/**
	 * Rewrite url
	 *
	 * @return void
	 */
	function custom_rewrite_rule () {
		add_rewrite_rule( '^bank/api/transaction-sync$', 'index.php?rest_route=/vietqr/sync', 'top' );
		flush_rewrite_rules();
	}

	/**
	 * Custom woocommerce payment method icon
	 *
	 * @param $icon_html
	 * @param $gateway_id
	 *
	 * @return mixed|string
	 */
	function custom_payment_method_icon($icon_html, $gateway_id) {
		// Define an array of payment methods and their corresponding images
		$payment_methods = array(
			'bacs' => VIETQR_PLUGIN_PUBLIC_IMG_URL . "/vietqr_payment_1x.png",
		);

		// Check if the payment method has a corresponding image
		if (isset($payment_methods[$gateway_id])) {
			$image_url = $payment_methods[$gateway_id];
			$icon_html = '<img width="70px" src="' . esc_url($image_url) . '" alt="' . esc_attr($gateway_id) . '">';
		}

		return $icon_html;
	}

	/**
	 * Custom woocommerce payment method title
	 *
	 * @param $title
	 * @param $gateway_id
	 *
	 * @return string
	 */
	function custom_payment_method_title($title, $gateway_id) {
		// Define an array of payment methods and their corresponding images
		$payment_methods = array(
			'bacs' => "Chuyển khoản ngân hàng VietQR VN",
		);

		// Get text of correspond methods
		if (isset($payment_methods[$gateway_id])) {
			$title = $payment_methods[$gateway_id];
		}

		return $title;
	}

	/**
	 * Custom woocommerce payment method description
	 *
	 * @param $title
	 * @param $gateway_id
	 *
	 * @return string
	 */
	function custom_payment_method_description ($desc, $gateway_id) {
		// Define an array of payment methods and their corresponding images
		$payment_methods = array(
			'bacs' => "Thanh toán bằng chuyển khoản Ngân hàng qua phương pháp Quét mã VietQR, được cung cấp bởi Dịch vụ <a style='color: blue; font-size: .92em;' href='https://vietqr.vn/'>VietQR.vn</a>",
		);

		// Get text of correspond methods
		if (isset($payment_methods[$gateway_id])) {
			$desc = $payment_methods[$gateway_id];
		}

		return $desc;
	}
}
