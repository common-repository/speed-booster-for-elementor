<?php
/**
 * Responsible for managing the plugin's helpers method.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */

namespace SpeedBoosterforElementor\Common;

/**
 * Contains plugin helper methods.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */
class Helpers {

	/**
	 * Retrieves posts list by given post type.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type  Given post type.
	 * @param  int    $limit Posts limit.
	 * @return array         Posts list with filtered fields.
	 */
	public function getPostsByPostType( $type = 'page', $limit = false ) {
		$blacklist = wppoolsbe()->blacklist->getStore();
		$result    = $this->query( $type, $limit );
		$whitelist = [];

		if ( wppoolsbe()->isPro() && wppoolsbepro()->client->license()->is_valid() ) {
			$whitelist = wppoolsbepro()->whitelist->getStore();
		}

		$postsArray = array_map(function( $post ) use ( $blacklist, $whitelist ) {
			$post['isBlackListed'] = isset( $blacklist[ $post['slug'] ] ) ? wp_validate_boolean( $blacklist[ $post['slug'] ]['isBlackListed'] ) : false;

			if ( wp_validate_boolean( did_action( 'elementor/loaded' ) ) ) {
				$post['isBuiltWithElementor'] = \Elementor\Plugin::$instance->documents->get( $post['ID'] )->is_built_with_elementor();
			}

			if ( wppoolsbe()->isPro() && wppoolsbepro()->client->license()->is_valid() ) {
				$post['isWhiteListed'] = isset( $whitelist[ $post['slug'] ] ) ? wp_validate_boolean( $whitelist[ $post['slug'] ]['isWhiteListed'] ) : false;
			}

			return $post;
		}, $result );

		return $postsArray;
	}


	/**
	 * Get post type count that are build with elementor.
	 *
	 * @param string $postType The post type.
	 *
	 * @since 1.0.3
	 * @return integer The count.
	 */
	public function getElementoredPostType( $postType ) {
		$items = $this->getPostsByPostType( $postType );
		$store = [];

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				if ( isset( $item['isBuiltWithElementor'] ) && wp_validate_boolean( $item['isBuiltWithElementor'] ) ) {
					$store[] = $item;
				}
			}
		}

		return $store;
	}

	/**
	 * Filter listed posts by post type.
	 *
	 * @since  1.0.0
	 * @param  string $type The Post Type.
	 * @param  string $mode Current mode.
	 * @return array
	 */
	public function filterListedPostsByPostType( $type, $mode = WPPOOLSBE_BLACKLIST ) {
		$store = ( WPPOOLSBE_WHITELIST === $mode && wppoolsbe()->isPro() ) ? wppoolsbepro()->whitelist->getStore() : wppoolsbe()->blacklist->getStore();

		$filtered = array_filter($store, function( $item ) use ( $type ) {
			return $type === $item['postType'];
		});

		return $filtered;
	}

	/**
	 * Reset mode.
	 *
	 * @since 1.0.0
	 */
	public function resetMode() {
		return update_option( 'wppoolsbe_mode', WPPOOLSBE_BLACKLIST );
	}

	/**
	 * Restore store with the limited quantity.
	 *
	 * @since 1.0.0
	 */
	public function resetStore() {
		$pages = array_slice( $this->filterListedPostsByPostType( 'page' ), 0, 10 );
		$posts = array_slice( $this->filterListedPostsByPostType( 'post' ), 0, 10 );
		$store = array_merge( $pages, $posts );
		return wppoolsbe()->blacklist->override( $store );
	}

	/**
	 * Performs the sql query based on the post type and limit.
	 *
	 * @since 1.0.0
	 *
	 * @param  string   $postType The post type.
	 * @param  bool|int $limit    The limt.
	 * @return array              The retrieved collection.
	 */
	public function query( $postType = 'page', $limit = false ) {
		global $wpdb;

		if ( $limit ) {
			$query = $wpdb->prepare(
				"SELECT ID, post_title as title, post_name as slug, post_type as postType from {$wpdb->prefix}posts WHERE post_type = %s AND post_name NOT IN ('cart', 'my-account', 'shop', 'checkout') AND post_status = 'publish' ORDER BY ID DESC LIMIT = %d",
				$postType,
				absint( $limit )
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT ID, post_title as title, post_name as slug, post_type as postType from {$wpdb->prefix}posts WHERE post_type = %s AND post_name NOT IN ('cart', 'my-account', 'shop', 'checkout') AND post_status = 'publish' ORDER BY ID DESC",
				$postType
			);
		}

		$posts = $wpdb->get_results( $query, ARRAY_A );

		return $posts;
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
	 * Admin notice for elementor if missing
	 *
	 * @since  1.0.0
	 * @return mixed
	 */
	public function elementorMissingNotice() {
		if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
			$notice_title = __( 'Activate Elementor', SPEED_BOOSTER_FOR_ELEMENTOR_TD );
			$notice_url = wp_nonce_url( 'plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1', 'activate-plugin_elementor/elementor.php' );
		} else {
			$notice_title = __( 'Install Elementor', SPEED_BOOSTER_FOR_ELEMENTOR_TD );
			$notice_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		}

		$notice = wp_kses_post(sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Elementor installation link */
			__( '%1$s requires %2$s to be installed and activated to function properly. %3$s', SPEED_BOOSTER_FOR_ELEMENTOR_TD ),
			'<strong>' . SPEED_BOOSTER_FOR_ELEMENTOR_NAME . '</strong>',
			'<strong>' . __( 'Elementor', SPEED_BOOSTER_FOR_ELEMENTOR_TD ) . '</strong>',
			'<a href="' . esc_url( $notice_url ) . '">' . $notice_title . '</a>'
		));

		return sprintf( '<p>%1$s</p>', $notice );
	}

	/**
	 * Returns woocommerce installation button.
	 * 
	 * @since 1.0.0
	 */
	public function getWooCommerceInstallationButton() {
		$woocommerce = 'woocommerce/woocommerce.php';

		if ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $woocommerce . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $woocommerce );

			$button_text = __( 'Activate WooCommerce', 'pqfw' );
		} else {
			$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
			$button_text    = __( 'Install WooCommerce', 'pqfw' );
		}

		$button = '<a href="' . $activation_url . '" class="install-wocommerce-button">' . $button_text . '</a>';

		return sprintf( '<p>%1$s</p>', $button );
	}

	/**
	 * Returns current mode.
	 *
	 * @since  1.0.0
	 * @return string The saved mode.
	 */
	public function getMode() {
		return esc_attr( get_option( 'wppoolsbe_mode' ) );
	}

	/**
	 * Set redirect.
	 *
	 * @since 1.0.0
	 * @param  number $value The absint to set.
	 * @return boolean       The updated status.
	 */
	public function setRedirect( $value ) {
		return update_option( 'wppoolsbe_redirect_to_admin_page', absint( $value ) );
	}

	/**
	 * Writes the MU plugin for the plugin free version.
	 *
	 * @since 1.0.0
	 */
	public function writeMuPlugin() {
		if ( ! file_exists( WPMU_PLUGIN_DIR ) ) {
			@mkdir( WPMU_PLUGIN_DIR );
		}

		if ( file_exists( WPMU_PLUGIN_DIR . '/SpeedBoosterForElementor.class.php' ) ) {
			@unlink( WPMU_PLUGIN_DIR . '/SpeedBoosterForElementor.class.php' );
		}

		if ( file_exists( SPEED_BOOSTER_FOR_ELEMENTOR_DIR . '/lib' ) ) {
			@copy( SPEED_BOOSTER_FOR_ELEMENTOR_DIR . '/lib/SpeedBoosterForElementor.class.php', WPMU_PLUGIN_DIR . '/SpeedBoosterForElementor.class.php' );
		}
	}

	/**
	 * Check if the header footer builder plugin is active
	 *
	 * @since 1.2.2
	 */
	public function isHfPluginActive() {
		return is_plugin_active( 'header-footer-elementor/header-footer-elementor.php' );
	}
}