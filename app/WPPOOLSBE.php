<?php
/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */

namespace SpeedBoosterforElementor {

	// if direct access than exit the file.
	defined( 'ABSPATH' ) || exit;

	/**
	 * Main plugin class.
	 *
	 * @since 1.0.0
	 */
	final class WPPOOLSBE {

		/**
		 * Holds the instance of the plugin currently in use.
		 *
		 * @since 1.0.0
		 *
		 * @var SpeedBoosterforElementor\WPPOOLSBE
		 */
		private static $instance = null;

		/**
		 * Main Plugin Instance.
		 *
		 * Insures that only one instance of the addon exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since  1.0.0
		 * @return SpeedBoosterforElementor\WPPOOLSBE
		 */
		public static function getInstance() {
			if ( null === self::$instance || ! self::$instance instanceof self ) {
				self::$instance = new self();

				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Initialize SpeedBoosterforElementor.
		 *
		 * @since 1.0.0
		 */
		private function init() {
			$this->constants();
			$this->includes();
			$this->preLoad();
			$this->loader();
		}

		/**
		 * Setup plugin constants.
		 * All the path/URL related constants are defined in main plugin file.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function constants() {
			$defaultHeaders = [
				'name'    => 'Plugin Name',
				'version' => 'Version',
			];

			$pluginData = get_file_data( SPEED_BOOSTER_FOR_ELEMENTOR_FILE, $defaultHeaders );

			$constants = [
				'SPEED_BOOSTER_FOR_ELEMENTOR_BASENAME' => plugin_basename( SPEED_BOOSTER_FOR_ELEMENTOR_FILE ),
				'SPEED_BOOSTER_FOR_ELEMENTOR_NAME'     => esc_attr( $pluginData['name'] ),
				'SPEED_BOOSTER_FOR_ELEMENTOR_URL'      => plugin_dir_url( SPEED_BOOSTER_FOR_ELEMENTOR_FILE ),
				'SPEED_BOOSTER_FOR_ELEMENTOR_VERSION'  => esc_attr( $pluginData['version'] ),
				'WPPOOLSBE_FREE_LIMIT'                 => 10,
				'WPPOOLSBE_BLACKLIST'                  => esc_attr( 'blacklist' ),
				'WPPOOLSBE_WHITELIST'                  => esc_attr( 'whitelist' )
			];

			foreach ( $constants as $constant => $value ) {
				if ( ! defined( $constant ) ) {
					define( $constant, $value );
				}
			}
		}

		/**
		 * Including the new files with PHP 5.3 style.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function includes() {
			$dependencies = [
				'/vendor/autoload.php',
			];

			foreach ( $dependencies as $path ) {
				if ( ! file_exists( SPEED_BOOSTER_FOR_ELEMENTOR_DIR . $path ) ) {
					status_header( 500 );
					wp_die( esc_html__( 'Plugin is missing required dependencies. Please contact support for more information.', SPEED_BOOSTER_FOR_ELEMENTOR_TD ) );
				}

				require SPEED_BOOSTER_FOR_ELEMENTOR_DIR . $path;
			}
		}

		/**
		 * Runs before we load the plugin.
		 *
		 * @since 4.0.0
		 *
		 * @return void
		 */
		private function preLoad() {

		}

		/**
		 * Load plugin classes.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function loader() {
			$this->admin     = new \SpeedBoosterforElementor\Common\Admin();
			$this->helpers   = new \SpeedBoosterforElementor\Common\Helpers();
			$this->api       = $this->isPro() ? new \SpeedBoosterforElementorPro\Api() : new \SpeedBoosterforElementor\Common\Api();
			$this->blacklist = new \SpeedBoosterforElementor\Blacklist\Store();

			add_action( 'admin_init', [ $this, 'redirectOnActivation' ] );
			add_action( 'wp_trash_post', [ $this, 'runOnTrashPost' ] );

			register_activation_hook(
				SPEED_BOOSTER_FOR_ELEMENTOR_FILE,
				[ $this, 'activation' ]
			);

			register_deactivation_hook(
				SPEED_BOOSTER_FOR_ELEMENTOR_FILE,
				[ $this, 'deactivation' ]
			);

			add_filter(
				'plugin_action_links_' . SPEED_BOOSTER_FOR_ELEMENTOR_BASENAME,
				[ $this, 'addLinks' ]
			);
		}

		/**
		 * Run on plugin activation.
		 *
		 * @since 1.0.0
		 */
		public function activation() {
			$this->helpers->setRedirect( 1 );
			$this->helpers->writeMuPlugin();
		}

		/**
		 * Run on the plugin deactivation
		 *
		 * @since 1.2.2
		 */
		public function deactivation() {
			delete_option( 'wppoolsbe_continue_with_risk' );
		}

		/**
		 * Redirect to admin page on plugin activation
		 *
		 * @since 1.0.0
		 */
		public function redirectOnActivation() {
			$redirect_to_admin_page = get_option( 'wppoolsbe_redirect_to_admin_page', 0 );

			if ( $redirect_to_admin_page == 1 ) {
				wppoolsbe()->helpers->setRedirect( 0 );
				wp_safe_redirect( admin_url( 'admin.php?page=speed-booster-for-elementor' ) );
				exit;
			}
		}

		/**
		 * Remove from the store while delete posts.
		 *
		 * @since  1.0.0
		 * @param  int $postId The deleted post id.
		 * @return void
		 */
		public function runOnTrashPost( $postId ) {
			$slug = esc_attr( basename( get_permalink( $postId ) ) );
			$this->blacklist->remove( $slug );
		}

		/**
		 * Check is ultimate activated.
		 *
		 * @since 1.0.0
		 */
		public function isPro() {
			$plugins = get_option( 'active_plugins' );
			$pro     = 'speed-booster-for-elementor-pro/speed-booster-for-elementor-pro.php';

			return in_array( $pro, $plugins );
		}

		/**
		 * Add plugin action link.
		 *
		 * @since  1.0.0
		 * @param  array $links The action links.
		 * @return array
		 */
		public function addLinks( $links ) {
			if ( ! $this->isPro() ) {
				$links[] = sprintf(
					'<a style="font-weight: bold; color: #93003c;" href="%s" target="_blank">%s</a>',
					esc_url( 'https://go.wppool.dev/HyCX' ),
					esc_html__( 'Get Pro', SPEED_BOOSTER_FOR_ELEMENTOR_TD )
				);
			}

			return $links;
		}
	}
}

namespace {
	// if direct access than exit the file.
	defined( 'ABSPATH' ) || exit;

	/**
	 * This function is responsible for running the main plugin.
	 *
	 * @since  1.0.0
	 * @return object SpeedBoosterforElementor\WPPOOLSBE The plugin instance.
	 */
	function wppoolsbe() {
		return \SpeedBoosterforElementor\WPPOOLSBE::getInstance();
	}
}