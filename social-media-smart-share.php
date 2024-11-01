<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://thedotstore.com/
 * @since             1.0.0
 * @package           Smartshare
 *
 * @wordpress-plugin
 * Plugin Name:       Social Media Smart Share
 * Plugin URI:        https://www.multidots.com/
 * Description:       Social Media Smart Share help to you shares your old posts automatically in your social media profile and social groups.
 * Version:           1.0.2
 * Author:            Thedotstore
 * Author URI:        https://thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       social-media-smart-share
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if access directly
}

/**
 * Define all constants for the plugin
 */
if ( ! defined( 'SMART_SHARE_PLUGIN_FILE' ) ) {
	define( 'SMART_SHARE_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'SMART_SHARE_PLUGIN_DIR' ) ) {
	define( 'SMART_SHARE_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'SMART_SHARE_PLUGIN_URL' ) ) {
	define( 'SMART_SHARE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SMART_SHARE_BASENAME' ) ) {
	define( 'SMART_SHARE_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'SMART_SHARE_VERSION' ) ) {
	define( 'SMART_SHARE_VERSION', '1.0.2' );
}
if ( ! defined( 'SMART_SHARE_NAME' ) ) {
	define( 'SMART_SHARE_NAME', 'SOCIAL MEDIA SMART SHARE' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smartshare-activator.php
 */
function ss_activate_smart_share() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smartshare-activator.php';
	Smart_Share_Activator::ss_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smartshare-deactivator.php
 */
function ss_deactivate_smart_share() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smartshare-deactivator.php';
	Smart_Share_Deactivator::ss_deactivate();
}

register_activation_hook( __FILE__, 'ss_activate_smart_share' );
register_deactivation_hook( __FILE__, 'ss_deactivate_smart_share' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smartshare.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function ss_run_smart_share() {

	$plugin = new Smart_Share();
	$plugin->run();

}

ss_run_smart_share();

function register_session() {
	if ( ! session_id() ) { //phpcs:ignore
		session_start(); //phpcs:ignore
	}
}

add_action( 'init', 'register_session' );
