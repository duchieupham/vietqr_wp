<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/admin
 */

use VietQR\Api;
use VietQR\Options;
use VietQR\Ultility;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/admin
 * @author     Your Name <email@example.com>
 */
class VietQR_Plugin_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vietqr-plugin-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register VietQR Menu pages
	 *
	 * @return void
	 */
	function admin_menu() {
		add_menu_page(
			'VietQR VN', // Page title
			'VietQR VN', // Menu title
			'manage_options', // Capability required to access the menu
			'vietqr-admin', // Menu slug
			[$this , 'vietqr_admin_page_config'], // Callback function to display the page content
			'dashicons-admin-generic', // Icon slug (optional)
			99 // Menu position (optional)
		);
	}

	/**
	 * Render VietQR config pages
	 *
	 * @return void
	 */
	function vietqr_admin_page_config() {
		require_once VIETQR_PLUGIN_ADMIN_TEMPLATE_DIR . "/vietqr-config.php";
	}

	/**
	 * Hanle option page submit data
	 *
	 * @return void
	 */
	function handle_option_page_submit() {
		// Check if the form is submitted to options.php
		if (isset($_POST['option_page']) && $_POST['option_page'] === 'vietqr_options') {
			// check data submit
			$selected_bank_account = "";

			if (isset($_POST["vietqr_selected_bank_account"])) {
				// Use the trim function to remove whitespace from the beginning and end of the string
				$selected_bank_account_raw = trim($_POST["vietqr_selected_bank_account"]);

				// Use the sanitize_text_field function to sanitize the string
				$selected_bank_account = sanitize_text_field($selected_bank_account_raw);
			}

			// enable sync on this bank account
			if (!empty($selected_bank_account)) {
				Api::get_instance()->sync_check($selected_bank_account, "True");
			}

			// save selected bank to db
			$bank_accounts_list = Api::get_instance()->get_bank_accounts();
			Options::get_instance()->set_bank_account_list($bank_accounts_list);

			foreach ($bank_accounts_list as $account) {
				if ($account->syncAccount)
					Options::get_instance()->set_selected_bank_account(get_object_vars($account));
			}

		}
	}


	/**
	 * Register VietQR config options
	 *
	 * @return void
	 */
	function vietqr_register_settings() {

		$prefix = "vietqr_";

//		VietQR option
		register_setting('vietqr_options', $prefix.'bank_transfer_enabled');
		register_setting('vietqr_options', $prefix.'qr_code_enabled');
//		register_setting('vietqr_options', $prefix.'authorization_code');
		register_setting('vietqr_options', $prefix.'transaction_prefix');
		register_setting('vietqr_options', $prefix.'account_current');
	}

	/**
	 * Woo add custom display field
	 *
	 * @param $order
	 *
	 * @return void
	 */
	function woo_order_detail_custom_field($order) {
		echo '<div class="order_data_column">';
		$transaction_id = get_post_meta($order->get_id(), 'vietqr_bank_transaction_id', true) ?? "";
		$vietqr_tran_content = get_post_meta($order->get_id(), 'vietqr_transaction_content', true) ?? "";
		$vietqr_tran_time = get_post_meta($order->get_id(), 'vietqr_transaction_time', true) ?? "";

		// Output the custom field value
		echo '<p><strong>' . __('Bank transaction ID: ') . '</strong> ' . $transaction_id . '</p>';
		echo '<p><strong>' . __('VietQR transaction content: ') . '</strong> ' . $vietqr_tran_content . '</p>';
		echo '<p><strong>' . __('VietQR transaction time: ') . '</strong> ' . ($vietqr_tran_time ?? convert_timestamp_to_date($vietqr_tran_time, "d-m-Y h:i:s")) . '</p>';
		echo '</div>';
	}


	/**
	 * @return void
	 */
	function ajax_save_authorization_code() {
		// Perform necessary checks and validations here

		// Process the AJAX request
		$response = array();

		// Access the data sent via AJAX
		$data = trim($_POST['authorization_code']); // Modify 'data' to match the parameter name sent in your AJAX request

		// Perform your logic and generate a response
		$result = Options::get_instance()->set_authorization_code($data);

		// save selected bank to db
		$bank_accounts_list = Api::get_instance()->get_bank_accounts();

		write_wp_log_to_file($bank_accounts_list);

		if (!empty($bank_accounts_list->error)) {
			$response['success'] = false;
			$response['message'] = 'Authorization code không hợp lệ';
			wp_send_json_success($response);
			die();
		}

		Options::get_instance()->set_bank_account_list($bank_accounts_list);

		foreach ($bank_accounts_list as $account) {
			if ($account->syncAccount)
				Options::get_instance()->set_selected_bank_account(get_object_vars($account));
		}

		if ($result) {
			$response['success'] = true;
			$response['message'] = 'Update code thành công';
		} else {
			$response['success'] = false;
			$response['message'] = 'Update code failed';
		}

		// Send the JSON response
		wp_send_json_success($response);
		die();
	}

	/**
     * Custom wordpress admin order detail page
     *
	 * @return void
	 */
	function prepend_html_to_order_detail_page() {
		$current_screen = get_current_screen();

		// Check if the current screen is the WooCommerce order detail page in the admin
		if ( $current_screen && $current_screen->post_type === 'shop_order' && $current_screen->base === 'post' ) {
			global $post;
			$order = wc_get_order($post->ID);

			if (empty($order))
				return;

			ob_start(); ?>
			<div style="text-align: center; <?php echo $order->get_status() != "completed" ? "color: #fc9f31" : "color: green"; ?>">
				<p style="margin-block: 5px; font-size: 28px;"><?php echo Ultility::get_instance()->get_woo_order_status_text($order->get_status()); ?></p>
				<p style="margin-block: 5px; font-size: 16px;"><?php echo __("Đơn hàng", "vietqr-plugin") . " #{$order->get_id()}"; ?></p>
				<p style="margin-block: 5px; font-size: 20px;"><?php echo number_format($order->get_total()) , " VNĐ"; ?></p>
			</div>
			<?php
			echo ob_get_clean();
		}
	}

	/**
     * Check dependencies and warning
     *
	 * @return void
	 */
	function dependencies_check() {
		$missing_extensions = [];

		if (!extension_loaded('iconv')) {
			$missing_extensions[] = 'ext-iconv';
		}

		if (!extension_loaded('mbstring')) {
			$missing_extensions[] = 'ext-mbstring';
		}

		if (!extension_loaded('gd')) {
			$missing_extensions[] = 'ext-gd';
		}

		if (!empty($missing_extensions)) {
			$message = 'VietQR VN: The following extensions are missing or not enabled: ' . implode(', ', $missing_extensions) . '. Please make sure they are installed and enabled.';
			echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
		}
	}

}
