<?php
/**
 * Plugin Name:       Elementor Speed Optimizer
 * Plugin URI:        https://wppool.dev/speed-booster-for-elementor/
 * Description:       Elementor Speed Optimizer - optimize the loading of Elementor page builder
 * Version:           1.4.5
 * Requires at least: 5.4
 * Requires PHP:      5.6
 * Author:            WPPOOL
 * Author URI:        https://wppool.dev/
 * Text Domain:       speed-booster-for-elementor
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package SpeedBoosterforElementor
 */

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Defining plugin constans
 *
 * @since 1.0.0
 */
if ( ! defined( 'SPEED_BOOSTER_FOR_ELEMENTOR_FILE' ) ) {
	define( 'SPEED_BOOSTER_FOR_ELEMENTOR_FILE', __FILE__ );
}

if ( ! defined( 'SPEED_BOOSTER_FOR_ELEMENTOR_TD' ) ) {
	define( 'SPEED_BOOSTER_FOR_ELEMENTOR_TD', 'speed-booster-for-elementor' );
}

if ( ! defined( 'SPEED_BOOSTER_FOR_ELEMENTOR_DIR' ) ) {
	define( 'SPEED_BOOSTER_FOR_ELEMENTOR_DIR', __DIR__ );
}

require_once SPEED_BOOSTER_FOR_ELEMENTOR_DIR . '/lib/wppool/Plugin.php';

if ( file_exists( SPEED_BOOSTER_FOR_ELEMENTOR_DIR . '/lib/appsero/Client.php' ) ) {
	require_once SPEED_BOOSTER_FOR_ELEMENTOR_DIR . '/lib/appsero/Client.php';
}

/**
 * Initialize the plugin tracker
 *
 * @since 1.0.0
 * @return void
 */
function appsero_init_tracker_speed_booster_for_elementor() {
	$client = new \SpeedBoosterforElementor\Appsero\Client(
		'a36d7985-d692-4d98-9545-481dcddcd752',
		'Elementor Speed Optimizer',
		__FILE__
	);

	// Active insights.
	$client->insights()->init();
}

appsero_init_tracker_speed_booster_for_elementor();

// Define the class and the function.
require_once dirname( __FILE__ ) . '/app/WPPOOLSBE.php';
wppoolsbe();