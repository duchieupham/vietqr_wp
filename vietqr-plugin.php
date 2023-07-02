<?php

/**
 * VietQR VN
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           VietQR_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       VIETQR VN
 * Description:       Plugin for connect to VIETQR VN payment
 * Version:           1.0.0
 * Author:            Khoi Tran
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vietqr-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VIETQR_PLUGIN_VERSION', '1.0.0' );
define( 'VIETQR_PLUGIN_ADMIN_TEMPLATE_DIR',  plugin_dir_path(__FILE__) . "/admin/partials");
define( 'VIETQR_PLUGIN_DIR',  plugin_dir_path(__FILE__));
define( 'VIETQR_PLUGIN_TEMP_DIR',  plugin_dir_path(__FILE__) . "temp");
define( 'VIETQR_PLUGIN_TEMP_URL',  plugin_dir_url(__FILE__) . "temp" );
define( 'VIETQR_PLUGIN_PUBLIC_TEMPLATE_DIR',  plugin_dir_path(__FILE__) . "/public/partials");
define( 'VIETQR_PLUGIN_ADMIN_IMG_URL',  plugin_dir_url(__FILE__) . "/admin/img");
define( 'VIETQR_PLUGIN_PUBLIC_IMG_URL',  plugin_dir_url(__FILE__) . "/public/img");

// Require helpers functions
require_once plugin_dir_path( __FILE__ ) . "helper-functions.php";
require_once plugin_dir_path( __FILE__ ) . "vendor/autoload.php";

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vietqr-plugin-activator.php
 */
function activate_vietqr_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vietqr-plugin-activator.php';
	VietQR_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vietqr-plugin-deactivator.php
 */
function deactivate_vietqr_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vietqr-plugin-deactivator.php';
	VietQR_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vietqr_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_vietqr_plugin' );


/**
 * PRS4 Autoloader
 *
 * @param $class
 * @return void
 */
function vietqr_autoloader($class) {
	$base_dir = __DIR__ . '/includes/classes/';
	$file = $base_dir . str_replace('\\', '/', $class) . '.php';

	if (file_exists($file)) {
		require $file;
	}
}

spl_autoload_register('vietqr_autoloader');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vietqr-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vietqr_plugin() {

	$plugin = new VietQR_Plugin();
	$plugin->run();

}
run_vietqr_plugin();
