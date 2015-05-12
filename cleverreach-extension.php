<?php
/**
 * CleverReach WordPress Extension
 *
 * @package     Cleverreach_Extension
 * @author      Sven Hofmann <info@hofmannsven.com>
 * @license     GPLv3
 * @link        https://github.com/hofmannsven/cleverreach-extension
 *
 * @wordpress-plugin
 * Plugin Name: CleverReach Extension
 * Plugin URI:  https://github.com/hofmannsven/cleverreach-extension
 * Description: Simple interface for CleverReach newsletter software using the official CleverReach SOAP API.
 * Version:     0.1.0
 * Author:      CODE64
 * Author URI:  http://code64.de/
 * License:     GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: cleverreachextension
 * Domain Path: /languages
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();

/**
 * Check requirements during plugin activation.
 *
 * @since    0.1.0
 */
register_activation_hook( __FILE__, 'cleverreachextension_check_requirements' );
function cleverreachextension_check_requirements() {

	// Define plugin requirements.
	$required_php_version   = '5.3.0';
	$required_php_extension = 'soap';

	// Check requirements.
	if ( version_compare( PHP_VERSION, $required_php_version, '<=' ) ) {

		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			sprintf( esc_html__( 'CleverReach Extension plugin requires PHP %s or greater.', 'cleverreachextension' ), $required_php_version ),
			esc_html__( 'Plugin Activation Error', 'cleverreachextension' ),
			array( 'back_link' => true )
		);

	} elseif ( ! extension_loaded( $required_php_extension ) ) {

		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			sprintf( esc_html__( 'CleverReach Extension plugin requires PHP %s extension.', 'cleverreachextension' ), strtoupper( $required_php_extension ) ),
			esc_html__( 'Plugin Activation Error', 'cleverreachextension' ),
			array( 'back_link' => true )
		);

	} else {
		return;
	}

}

/**
 * TODO: Cleanup database during plugin deactivation.
 */
// register_deactivation_hook( __FILE__, 'cleverreachextension_cleanup' );

if ( ! class_exists( 'Cleverreach_Extension' ) ) {
	require plugin_dir_path( __FILE__ ) . 'includes/class-cleverreach-extension.php';
}

/**
 * Run plugin if everything is ready.
 *
 * @since    0.1.0
 */
add_action( 'plugins_loaded', 'run_cleverreachextension', 0 );
function run_cleverreachextension() {

	// Define plugin meta.
	$plugin_name     = esc_html__( 'CleverReach Extension', 'cleverreachextension' );
	$plugin_slug     = 'cleverreach-extension';
	$plugin_basename = plugin_basename( __FILE__ );
	$plugin_version  = '0.1.0';

	// Finally we're ready to run the plugin.
	$plugin = new \CleverreachExtension\Core\CleverReach_Extension( $plugin_name, $plugin_slug, $plugin_basename, $plugin_version );
	$plugin->run();

}