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
		check_ajax_referer( 'wpb-pcf-button-ajax', 'wpb_pcf_fire_popup_nonce' ); // Verify the nonce.

		$form        = '';
		$pcf_form_id = isset( $_POST['pcf_form_id'] ) ? sanitize_text_field( $_POST['pcf_form_id'] ) : '';

		if ( $pcf_form_id ) {

			$shortcode = apply_filters( 'wpb_pcf_form_shortcode', '[contact-form-7 id="' . esc_attr( $pcf_form_id ) . '"]', $pcf_form_id );

			$form .= '<div class="wpb-pcf-wpcf7-form">';
			$form .= do_shortcode( $shortcode );
			$form .= '</div>';
		}

		if ( $form ) {
			wp_send_json_success( $form );
		}
	}
}
