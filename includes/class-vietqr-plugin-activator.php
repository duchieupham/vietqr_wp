<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/includes
 * @author     Your Name <email@example.com>
 */
class VietQR_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
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
			// Deactivate the plugin
			deactivate_plugins(plugin_basename(__FILE__));

			// Display an error notice
			$message = 'VietQR VN: Plugin cần có những extension sau để hoạt động: ' . implode(', ', $missing_extensions) . '. Xin hãy cài đặt rồi kích hoạt lại.';
			wp_die(esc_html($message), 'Plugin Activation Error', array('response' => 400));
		}
	}

}
