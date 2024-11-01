<?php
/**
 * Responsible for managing the plugin's blacklist store.
 * When I say store it means a wp_options key 'wppoolsbe_blacklist_store'
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */

namespace SpeedBoosterforElementor\Blacklist;

/**
 * For now it'll be handling ajax points but in the future it'll be handling * rest api endpoints.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */
class WooCommerce {

	/**
	 * WC blacklist all option key.
	 *
	 * @since 1.0.0
	 */
	const WOOCOMMERCE_ALL_KEY = 'wppoolsbe_wc_blacklist_all';

	/**
	 * Set all woocommerce blacklist status
	 *
	 * @since  1.0.0
	 * @param  boolean $value The boolean value to set.
	 * @return boolean
	 */
	public function setAll( $value ) {
		return update_option( self::WOOCOMMERCE_ALL_KEY, $value );
	}

	/**
	 * Get all woocommerce blacklist status
	 *
	 * @since 1.0.0
	 */
	public function getAll() {
		return wp_validate_boolean( get_option( self::WOOCOMMERCE_ALL_KEY ) );
	}

	/**
	 * Performs reset operation on woocommerce blacklist store.
	 *
	 * @since 1.0.0
	 */
	public function reset() {
		return delete_option( self::WOOCOMMERCE_ALL_KEY );
	}
}