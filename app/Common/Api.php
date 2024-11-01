<?php
/**
 * Responsible for managing the plugin's REST API endpoints.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */

namespace SpeedBoosterforElementor\Common;

/**
 * For now it'll be handling ajax points but in the future it'll be handling * rest api endpoints.
 *
 * @since   1.0.0
 * @package SpeedBoosterforElementor
 */
class Api {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_wppoolsbe_update_single', [ $this, 'updateSingle' ] );
		add_action( 'wp_ajax_wppoolsbe_get_count', [ $this, 'getBlacklistedItemsCount' ] );
		add_action( 'wp_ajax_wppoolsbe_update_wc_all', [ $this, 'updateWcAll' ] );
		add_action( 'wp_ajax_wppoolsbe_complete_setup', [ $this, 'setCompleteSetupWizard' ] );
		add_action( 'wp_ajax_wppoolsbe_save_all', [ $this, 'saveAllData' ] );
		add_action( 'wp_ajax_wppoolsbe_continue_with_risk', [ $this, 'continueWithRisk' ] );
		add_action( 'wp_ajax_wppoolsbe_deactive_hfBuilders', [ $this, 'deactiveHfBuilder' ] );
		add_action( 'wp_ajax_wppoolsbe_get_risk_status', [ $this, 'getRiskStatus' ] );
	}

	/**
	 * Performs save all operation.
	 *
	 * @since 1.2.2
	 */
	public function saveAllData() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		$payload  = isset( $_POST['payload'] ) && is_array( $_POST['payload'] ) && ! empty( $_POST['payload'] ) ? ( $_POST['payload'] ) : [];
		$postType = sanitize_text_field( $_POST['postType'] );
		$mode     = isset( $_POST['mode'] ) ? sanitize_text_field( $_POST['mode'] ) : 'blacklist';

		foreach ( $payload as $item ) {
			wppoolsbe()->blacklist->add( $item );
		}

		wp_send_json_success([
			'message'             => sprintf( __( '%s updated', SPEED_BOOSTER_FOR_ELEMENTOR_TD ), ucfirst( $mode ) ),
			"updated{$postType}s" => wppoolsbe()->helpers->getPostsByPostType( $postType ),
			'savedPageCount'      => wppoolsbe()->blacklist->getCountByPostType( 'page' ),
			'savedPostCount'      => wppoolsbe()->blacklist->getCountByPostType( 'post' )
		]);
	}

	/**
	 * Update single blacklist item.
	 *
	 * @since 1.0.0
	 */
	public function updateSingle() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		$value    = wp_validate_boolean( $_POST['value'] );
		$payload  = array_map( 'sanitize_text_field', $_POST['payload'] );
		$postType = sanitize_text_field( $_POST['postType'] );

		if ( $value ) {
			if ( wppoolsbe()->blacklist->getCountByPostType( $postType ) >= WPPOOLSBE_FREE_LIMIT ) {
				wp_send_json_error([
					'message' => __( 'Max 10', SPEED_BOOSTER_FOR_ELEMENTOR_TD )
				]);

				die();
			}
			wppoolsbe()->blacklist->add( $payload );
		} else {
			wppoolsbe()->blacklist->remove( $payload['slug'] );
		}

		wp_send_json_success([
			'message'             => __( 'Blacklist updated', SPEED_BOOSTER_FOR_ELEMENTOR_TD ),
			"updated{$postType}s" => wppoolsbe()->helpers->getPostsByPostType( $postType )
		]);
	}

	/**
	 * Retrieves blacklisted items count.
	 *
	 * @since 1.0.0
	 */
	public function getBlacklistedItemsCount() {
		// verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		wp_send_json_success([
			'savedPageCount' => wppoolsbe()->blacklist->getCountByPostType( 'page' ),
			'savedPostCount' => wppoolsbe()->blacklist->getCountByPostType( 'post' )
		]);
	}

	/**
	 * Update woocommerce all status.
	 *
	 * @since 1.0.0
	 */
	public function updateWcAll() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		$value  = wp_validate_boolean( $_POST['value'] );
		$status = wppoolsbe()->blacklist->woocommerce->setAll( $value );

		if ( $status ) {
			wp_send_json_success([
				'message' => __( 'Updated woocommerce blacklist.', SPEED_BOOSTER_FOR_ELEMENTOR_TD )
			]);
		}
	}

	/**
	 * Complete setup wizard status.
	 *
	 * @since 1.0.0
	 */
	public function setCompleteSetupWizard() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		update_option( 'wppoolsbe_setup_complete', true );

		wp_send_json_success([
			'message' => __( 'Setup complete.', SPEED_BOOSTER_FOR_ELEMENTOR_TD )
		]);
	}

	/**
	 * Handle continue with risk.
	 *
	 * @since 1.0.2
	 */
	public function continueWithRisk() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		wp_send_json_success([
			'message' => __( 'Continue with risk.', SPEED_BOOSTER_FOR_ELEMENTOR_TD ),
			'risk'    => wp_validate_boolean( update_option( 'wppoolsbe_continue_with_risk', true ) )
		]);
	}

	/**
	 * Deactivate header footer builders.
	 *
	 * @since 1.0.2
	 */
	public function deactiveHfBuilder() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		deactivate_plugins( 'header-footer-elementor/header-footer-elementor.php' );

		wp_send_json_success([
			'message' => __( 'Header Footer Builder plugin deactivated.', SPEED_BOOSTER_FOR_ELEMENTOR_TD )
		]);
	}

	/**
	 * Retrieves risk status.
	 *
	 * @since 1.0.2
	 */
	public function getRiskStatus() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wppoolsbe_admin_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		wp_send_json_success([
			'risk' => wp_validate_boolean( get_option( 'wppoolsbe_continue_with_risk', false ) )
		]);
	}
}