<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://stefanocotterli.it
 * @since             1.0.0
 * @package           Showcta
 *
 * @wordpress-plugin
 * Plugin Name:       show CTA
 * Plugin URI:        https://stefanocotterli.it/wpplugin/showcta
 * Description:       Visualizza una call to action settabile dall'utente
 * Version:           1.0.0
 * Author:            Stefano Cotterli
 * Author URI:        https://stefanocotterli.it
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       showcta
 * Domain Path:       /languages (non implementato)
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version and name.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SHOWCTA_VERSION', '1.0.0' );
define( 'SHOWCTA_NAME', 'Show a Call to Action' );
define( 'SHOWCTA_CODE', 'showcta' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-showcta.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_showcta() {
	return new Showcta();
}
run_showcta();
