<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Ajax Class
 */
class WPB_PCF_Ajax {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wpb_pcf_fire_contact_form', array( $this, 'wpb_pcf_fire_contact_form' ) );
		add_action( 'wp_ajax_nopriv_wpb_pcf_fire_contact_form', array( $this, 'wpb_pcf_fire_contact_form' ) );
	}

	/**
	 * Form Content
	 */
	public function wpb_pcf_fire_contact_form() {
		//check_ajax_referer( 'wpb-pcf-button-ajax', 'wpb_pcf_fire_popup_nonce' ); // Verify the nonce.

		$pcf_form_id = isset( $_POST['pcf_form_id'] ) ? sanitize_key( $_POST['pcf_form_id'] ) : 0;

		// Getting the CF7 form ID form the hash.
		if( get_post_type( $pcf_form_id ) !== 'wpcf7_contact_form' ) {
			$pcf_form_id = wpb_pcf_wpcf7_get_contact_form_id_by_hash( $pcf_form_id );
		}else{
			$pcf_form_id = intval( $pcf_form_id );
		}
		
		if ( $pcf_form_id > 0 && get_post_type( $pcf_form_id ) === 'wpcf7_contact_form' ) {

			$shortcode = sprintf( '[contact-form-7 id="%d"]', esc_attr( $pcf_form_id ) );

			$response = '<div class="wpb-pcf-wpcf7-form">';
			$response .= do_shortcode( $shortcode );
			$response .= '</div>';

			wp_send_json_success( $response );
		}else{
			wp_send_json_error( esc_html__( 'Invalid CF7 Form ID', 'wpb-popup-for-cf7-lite' ) );
		}
	}
}
