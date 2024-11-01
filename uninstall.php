<?php
/**
 * Cleanup process while removing plugin.
 *
 * @since 1.0.0
 * @package SpeedBoosterforElementor
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wppoolsbe_setup_complete' );

if ( file_exists( WPMU_PLUGIN_DIR . '/SpeedBoosterForElementor.class.php' ) ) {
	@unlink( WPMU_PLUGIN_DIR . '/SpeedBoosterForElementor.class.php' );
}