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
class Store {

	/**
	 * Pages blacklist store option key.
	 *
	 * @since 1.0.0
	 */
	const STORE_KEY = 'wppoolsbe_blacklist_store';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->woocommerce = new \SpeedBoosterforElementor\Blacklist\WooCommerce();
	}

	/**
	 * Returns the saved blacklisted pages.
	 *
	 * @since 1.0.0
	 * @return array Saved blacklisted pages slug.
	 */
	public function getStore() {
		return get_option( self::STORE_KEY, [] );
	}

	/**
	 * Performs add operation to the store.
	 *
	 * @since 1.0.0
	 * @param  array $data The post data to save.
	 * @return bool        Saved status.
	 */
	public function add( $data ) {
		$store = $this->getStore();

		$store[ $data['slug'] ] = $data;

		return update_option( self::STORE_KEY, $store );
	}

	/**
	 * Performs remove operation to the store.
	 *
	 * @since  1.0.0
	 * @param  string $slug The post data to remove.
	 * @return bool         Saved status.
	 */
	public function remove( $slug ) {
		$store = $this->getStore();

		if ( isset( $store[ $slug ] ) ) {
			unset( $store[ $slug ] );
		}

		return update_option( self::STORE_KEY, $store );
	}

	/**
	 * Override the full store with provided data.
	 *
	 * @since  1.0.0
	 * @param  array $data Data to update the store.
	 * @return boolean
	 */
	public function override( $data ) {
		return update_option( self::STORE_KEY, $data );
	}

	/**
	 * Reset store.
	 *
	 * @since 1.0.3
	 */
	public function reset() {
		return delete_option( self::STORE_KEY );
	}

	/**
	 * Performs remove all operation to the store based on the post type.
	 *
	 * @since 1.0.0
	 * @param  string $postType The post type collection remove.
	 * @return bool             Saved status.
	 */
	public function removeAllByPostType( $postType ) {
		$store = $this->getStore();

		$store = array_filter( $store, function( $item ) use ( $postType ) {
			return $item['postType'] !== $postType;
		});

		return update_option( self::STORE_KEY, $store );
	}

	/**
	 * Returns all blacklisted status based on the post type.
	 *
	 * @since 1.0.0
	 * @param  string $postType The post type to count.
	 * @return bool             Saved status.
	 */
	public function getAllStatusByPostType( $postType ) {
		$pages = wppoolsbe()->helpers->getPostsByPostType( $postType );
		$counter = 0;

		foreach ( $pages as $page ) {
			if ( $counter >= 10 ) {
				break;
			}

			if ( 1 === absint( $page['isBlackListed'] ) ) {
				$counter++;
			}
		}

		return $counter >= 10 ? true : false;
	}

	/**
	 * Returns blacklisted items count based on the post type.
	 *
	 * @since 1.0.0
	 * @param  string $postType The post type to count.
	 * @return bool             Saved status.
	 */
	public function getCountByPostType( $postType = 'page' ) {
		$pages = wppoolsbe()->helpers->getPostsByPostType( $postType );

		$counter = 0;

		foreach ( $pages as $item ) {
			if ( 1 === absint( $item['isBlackListed'] ) ) {
				$counter++;
			}
		}

		return $counter;
	}
}