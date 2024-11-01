<?php
/**
 * Responsible for managing the plugin's admin functionality.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */

namespace SpeedBoosterforElementor\Common;

/**
 * Abstract class that Pro and Lite both extend.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */
class Admin {

	/**
	 * The page slug for the sidebar.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $pageSlug = 'speed-booster-for-elementor';

	/**
	 * Sidebar menu name.
	 *
	 * @since 4.0.0
	 * @var   string
	 */
	public $menuName = 'Elementor Speed Optimizer';

	/**
	 * An array of pages for the admin.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $pages = [];

	/**
	 * An array of items to add to the admin bar.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $adminBarMenuItems = [];

	/**
	 * Construct method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'addMenu' ] );
		add_action( 'admin_head', [ $this, 'printIconCss' ] );
	}

	/**
	 * Add the menu inside of WordPress.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function addMenu() {
		$this->addMainMenu();
	}

	/**
	 * Add the main menu.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $slug which slug to use.
	 * @return void
	 */
	private function addMainMenu( $slug = 'speed-booster-for-elementor' ) {
		$hook = add_menu_page(
			sprintf( __( '%s', SPEED_BOOSTER_FOR_ELEMENTOR_TD ), $this->menuName ),
			sprintf( __( '%s', SPEED_BOOSTER_FOR_ELEMENTOR_TD ), $this->menuName ),
			'manage_options',
			$slug,
			[ $this, 'page' ],
			SPEED_BOOSTER_FOR_ELEMENTOR_URL . 'assets/admin/images/logo-b&w.svg',
			'58.6'
		);

		add_action( "load-{$hook}", [ $this, 'hooks' ] );
	}

	/**
	 * Hooks for loading our pages.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		if ( ! is_object( $screen ) || 'toplevel_page_speed-booster-for-elementor' !== $screen->id ) {
			return;
		}

		// We don't want any plugin adding notices to our screens. Let's clear them out here.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		$dependencies = require_once SPEED_BOOSTER_FOR_ELEMENTOR_DIR . '/build/index.asset.php';
		$dependencies['dependencies'][] = 'wp-util';

		wp_enqueue_style(
			'wppoolsbe-app',
			SPEED_BOOSTER_FOR_ELEMENTOR_URL . 'build/index.css',
			[],
			SPEED_BOOSTER_FOR_ELEMENTOR_VERSION,
			'all'
		);

		wp_enqueue_style(
			'wppoolsbe-admin',
			SPEED_BOOSTER_FOR_ELEMENTOR_URL . 'assets/admin/css/admin.css',
			'',
			SPEED_BOOSTER_FOR_ELEMENTOR_VERSION,
			'all'
		);

		// Scripts.
		wp_enqueue_script(
			'wppoolsbe-app',
			SPEED_BOOSTER_FOR_ELEMENTOR_URL . 'build/index.js',
			$dependencies['dependencies'],
			SPEED_BOOSTER_FOR_ELEMENTOR_VERSION,
			true
		);

		wp_enqueue_script(
			'wppoolsbe-admin',
			SPEED_BOOSTER_FOR_ELEMENTOR_URL . 'assets/admin/js/admin.js',
			[ 'jquery' ],
			SPEED_BOOSTER_FOR_ELEMENTOR_VERSION,
			true
		);

		$pagesCount = count( wppoolsbe()->helpers->getPostsByPostType() );
		$postsCount = count( wppoolsbe()->helpers->getPostsByPostType( 'post' ) );

		wp_localize_script(
			'wppoolsbe-app',
			'WPPOOLSBE',
			apply_filters(
				'wppoolsbe_app_data',
				[
					'urls'        => [
						'ajax'      => esc_url( admin_url( 'admin-ajax.php' ) ),
						'domain'    => esc_url( get_site_url() ),
						'dashboard' => esc_url( admin_url( 'admin.php?page=speed-booster-for-elementor' ) ),
					],
					'nonce'       => wp_create_nonce( 'wppoolsbe_admin_nonce' ),
					'posts'       => wppoolsbe()->helpers->getPostsByPostType( 'post' ),
					'pages'       => wppoolsbe()->helpers->getPostsByPostType( 'page' ),
					'pagesLimit'  => wppoolsbe()->isPro() ? $pagesCount : ( $pagesCount > WPPOOLSBE_FREE_LIMIT ? WPPOOLSBE_FREE_LIMIT : $pagesCount ),
					'postsLimit'  => wppoolsbe()->isPro() ? $postsCount : ( $postsCount > WPPOOLSBE_FREE_LIMIT ? WPPOOLSBE_FREE_LIMIT : $postsCount ),
					'maxLimit'    => WPPOOLSBE_FREE_LIMIT,
					'allFlags'    => [
						'wc'    => wppoolsbe()->blacklist->woocommerce->getAll(),
						'post'  => wppoolsbe()->blacklist->getAllStatusByPostType( 'post' ),
						'page'  => wppoolsbe()->blacklist->getAllStatusByPostType( 'page' ),
						'setup' => wp_validate_boolean( get_option( 'wppoolsbe_setup_complete' ) )
					],
					'permalink'   => [
						'wrong' => absint( wppoolsbe()->helpers->checkForInvalidPermalinkStructure() ),
						'link'  => esc_url( admin_url( 'options-permalink.php' ) )
					],
					'elementor'   => [
						'loaded'     => wp_validate_boolean( did_action( 'elementor/loaded' ) ),
						'notice'     => wp_kses_post( wppoolsbe()->helpers->elementorMissingNotice() ),
						'pagesCount' => count( wppoolsbe()->helpers->getElementoredPostType( 'page' ) ),
						'postsCount' => count( wppoolsbe()->helpers->getElementoredPostType( 'post' ) )
					],
					'woocommerce' => [
						'loaded' => class_exists( 'WooCommerce' ),
						'notice' => wp_kses_post( wppoolsbe()->helpers->getWooCommerceInstallationButton() )
					],
					'pro'         => [
						'loaded' => wppoolsbe()->isPro()
					],
					'currentMode' => wppoolsbe()->helpers->getMode(),
					'hfBuilder'   => wppoolsbe()->helpers->isHfPluginActive()
				]
			)
		);

		add_action( 'admin_footer_text', [ $this, 'addFooterText' ] );
	}

	/**
	 * Add footer text to the WordPress admin screens.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function addFooterText() {
		$linkText = esc_html__( 'Give us a 5-star rating!', SPEED_BOOSTER_FOR_ELEMENTOR_TD );
		$href     = 'https://wordpress.org/support/plugin/speed-booster-for-elementor/reviews/?filter=5#new-post';

		$link1 = sprintf(
			'<a href="%1$s" target="_blank" title="%2$s">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
			$href,
			$linkText
		);

		$link2 = sprintf(
			'<a href="%1$s" target="_blank" title="%2$s">WordPress.org</a>',
			$href,
			$linkText
		);

		printf(
			// Translators: 1 - The plugin name ("Speed Booster for Elementor"), - 2 - This placeholder will be replaced with star icons, - 3 - "WordPress.org" - 4 - The plugin name ("Speed Booster for Elementor").
			esc_html__( 'Please rate %1$s %2$s on %3$s to help us spread the word. Thank you!', SPEED_BOOSTER_FOR_ELEMENTOR_TD ),
			sprintf( '<strong>%1$s</strong>', esc_html( SPEED_BOOSTER_FOR_ELEMENTOR_NAME ) ),
			wp_kses_post( $link1 ),
			wp_kses_post( $link2 )
		);
	}

	/**
	 * Output the HTML for the page.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function page() {
		echo '<div id="wppoolsbe-app"></div>';
		echo '<div id="wppoolsbe-portal"></div>';
	}

	/**
	 * Print icon css
	 *
	 * @since 1.0.0
	 */
	public function printIconCss() {
		echo '<style>
			#adminmenu .toplevel_page_speed-booster-for-elementor div.wp-menu-image img {
				width: 20px;
				height: 20px;
			}
		</style>';
	}
}