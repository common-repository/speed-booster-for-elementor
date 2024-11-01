<?php
/**
 * Register as a must use plugin to manage our plugin functionality.
 *
 * @since 1.0.0
 * @version Free
 */
class SpeedBoosterForElementor {

	/**
	 * Pages blacklist store option key.
	 *
	 * @since 1.0.0
	 */
	const STORE_KEY = 'wppoolsbe_blacklist_store';

	/**
	 * WC blacklist all option key.
	 *
	 * @since 1.0.0
	 */
	const WOOCOMMERCE_ALL_KEY = 'wppoolsbe_wc_blacklist_all';

	/**
	 * Returns the saved blacklisted pages.
	 *
	 * @since 1.0.0
	 * @return array Saved blacklisted pages slug.
	 */
	public function getStore() {
		return get_option( self::STORE_KEY, false );
	}

	/**
	 * Returns blocked plugins.
	 *
	 * @since 1.0.0
	 */
	public function getBlockedPlugins() {
		$blockedPlugins = [
			'elementor/elementor.php',
			'essential-addons-for-elementor-lite/essential_adons_elementor.php',
			'happy-elementor-addons/plugin.php',
			'powerpack-lite-for-elementor/powerpack-lite-elementor.php',
			'premium-addons-for-elementor/premium-addons-for-elementor.php',
			'elementskit-lite/elementskit-lite.php'
		];

		return $blockedPlugins;
	}

	/**
	 * Returns formatted url slugs.
	 *
	 * @since 1.0.0
	 */
	public function getUriKey() {
		return basename( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) );
	}

	/**
	 * Check if a page is blacklisted.
	 *
	 * @since  1.0.0
	 * @param  string $key The page slug.
	 * @return boolean
	 */
	private function isBlackListed( $key ) {
		$store = $this->getStore( 'blacklist' );
		return isset( $store[ $key ]['isBlackListed'] ) ? wp_validate_boolean( $store[ $key ]['isBlackListed'] ) : false;
	}

	/**
	 * Get woocommerce blacklist status
	 *
	 * @since 1.0.0
	 */
	public function getWcStatus() {
		return wp_validate_boolean( get_option( self::WOOCOMMERCE_ALL_KEY ) );
	}

	/**
	 * Check current page is product page.
	 *
	 * @since  1.0.0
	 * @param  string $slug The current page slug.
	 * @return boolean
	 */
	public function isProduct( $slug ) {
		global $wpdb;
		$result = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->prefix}posts WHERE post_name = %s", $slug ) );
		return 'product' === $result;
	}

	/**
	 * Retrieve front-page slug.
	 *
	 * @since  1.0.0
	 * @param  integer $id The front page id.
	 * @return boolean
	 */
	public function getFrontPageSlug( $id ) {
		global $wpdb;

		$result = $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM {$wpdb->prefix}posts WHERE ID = %d", $id ) );
		return ! is_null( $result ) ? $result : false;
	}

	/**
	 * Consider its front page if it dosen't have any slug in the URL.
	 *
	 * @since 1.0.0
	 */
	public function isFrontPage() {
		return '/' === $_SERVER['REQUEST_URI'];
	}

	/**
	 * Disable our listed plugins.
	 *
	 * @since 1.0.0
	 */
	public function disablePlugins() {
		add_filter( 'option_active_plugins', function( $plugins ) {
			$plugins = array_diff( $plugins, $this->getBlockedPlugins() );
			return $plugins;
		});
	}

	/**
	 * Check if user set a invalid permalink structure.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public function checkForInvalidPermalinkStructure() {
		$structure = basename( get_option( 'permalink_structure' ) );
		return false === strpos( $structure, '%postname%' );
	}

	/**
	 * Check if it's a woocommerce page.
	 *
	 * @since 1.0.0
	 */
	public function isWooCommercePage( $key ) {
		return in_array( $key, [ 'cart', 'shop', 'my-account', 'checkout' ], true );
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( is_admin() || $this->checkForInvalidPermalinkStructure() ) {
			return;
		}

		if ( ! $this->isFrontPage() ) {
			$key = $this->getUriKey();

			if ( $this->isBlackListed( $key ) ) {
				$this->disablePlugins();
			} else {
				if ( $this->getWcStatus() && ( $this->isProduct( $key ) || $this->isWooCommercePage( $key ) ) ) {
					$this->disablePlugins();
				}
			}
		} else {
			$id  = absint( get_option( 'page_on_front' ) );
			$key = $this->getFrontPageSlug( $id );

			if ( $this->isBlackListed( $key ) ) {
				$this->disablePlugins();
			}
		}
	}
}

new SpeedBoosterForElementor();