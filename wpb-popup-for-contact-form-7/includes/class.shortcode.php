<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shortcode
 */
class WPB_PCF_Shortcode_Handler {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_shortcode( 'wpb-pcf-button', array( $this, 'contact_form_button_shortcode' ) );
	}

	/**
	 * Shortcode handler
	 *
	 * @param  array  $atts An array of attributes.
	 * @param  string $content The shortcode content.
	 *
	 * @return string
	 */
	public function contact_form_button_shortcode( $atts, $content = '' ) {
		ob_start();
		wpb_pcf_contact_form_button( $atts );
		$content .= ob_get_clean();
		return $content;
	}
}
