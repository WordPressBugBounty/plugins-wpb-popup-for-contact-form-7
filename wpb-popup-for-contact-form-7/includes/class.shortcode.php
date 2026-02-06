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
		add_action( 'wpb_pcf_contact_form_button', array($this, 'contact_form_button'), 10 ); // Adding the Popup Button using action hook.
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
		$this->contact_form_button($atts);
		$content .= ob_get_clean();
		return $content;
	}

	/**
	 * Generic function for displaying docs
	 *
	 * @param  array $args An array of attributes.
	 *
	 * @return void
	 */
	public function contact_form_button( $args = array() ) {

		global $post;

		if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
		}

		if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
			wpcf7_enqueue_styles();
		}

		$defaults = array(
			'id'                  => wpb_pcf_get_option( 'cf7_form_id', 'wpb_pcf_form_settings' ),
			'post_id'             => ( $post ? $post->ID : '' ),
			'class'               => '',
			'text'                => wpb_pcf_get_option( 'btn_text', 'wpb_pcf_btn_settings', esc_html__( 'Contact Us', 'wpb-popup-for-contact-form-7' ) ),
			'btn_size'            => wpb_pcf_get_option( 'btn_size', 'wpb_pcf_btn_settings', 'large' ),
			'form_style'          => ( 'on' === wpb_pcf_get_option( 'form_style', 'wpb_pcf_popup_settings' ) ? true : false ),
			'allow_outside_click' => ( 'on' === wpb_pcf_get_option( 'allow_outside_click', 'wpb_pcf_popup_settings' ) ? true : false ),
			'width'               => wpb_pcf_get_option( 'popup_width', 'wpb_pcf_popup_settings', 500 ) . wpb_pcf_get_option( 'popup_width_unit', 'wpb_pcf_popup_settings', 'px' ),
			'popup_type'     	  => wpb_pcf_get_option('popup_type', 'wpb_pcf_popup_settings', 'on_button_click'),
		);

		$args = wp_parse_args( $args, $defaults );

		// Adding the popup content in the footer for instat popup
		$this->add_popup_content($args);

		if ( defined( 'WPCF7_PLUGIN' ) ) {
			if ( $args['id'] ) {
				echo wp_kses_post(
					apply_filters(
						'wpb_pcf_button_html',
						sprintf(
							'<button data-id="%1$s" data-post_id="%2$s" data-form_style="%3$s" data-allow_outside_click="%4$s" data-width="%5$s" data-popup_type="%6$s" class="wpb-pcf-form-fire wpb-pcf-btn-%7$s wpb-pcf-btn wpb-pcf-btn-default%8$s">%9$s</button>',
							esc_attr( $args['id'] ),
							esc_attr( $args['post_id'] ),
							esc_attr( $args['form_style'] ),
							esc_attr( $args['allow_outside_click'] ),
							esc_attr( $args['width'] ),
							esc_attr( $args['popup_type'] ),
							esc_attr( $args['btn_size'] ),
							( $args['class'] ? esc_attr( ' ' . $args['class'] ) : '' ),
							esc_html( $args['text'] )
						),
						$args
					)
				);
			} else {
				printf( '<div class="wpb-pcf-alert wpb-pcf-alert-inline wpb-pcf-alert-error">%s</div>', esc_html__( 'Form id required.', 'wpb-popup-for-contact-form-7' ) );
			}
		} else {
			printf( '<div class="wpb-pcf-alert wpb-pcf-alert-inline wpb-pcf-alert-error">%s</div>', esc_html__( 'Popup for Contact Form 7 required the Contact Form 7 plugin to work with.', 'wpb-popup-for-contact-form-7' ) );
		}
	}

	/**
	 * Add popup content to the footer on page load for instat popup
	 *
	 * @param  array  $args The shortcode attributes.
	 *
	 * @return string
	 */
	public function add_popup_content($args)
	{
		if ('on_page_load' !== $args['popup_type']) {
			return;
		}
		$pcf_form_id 		= isset($args['id']) ? sanitize_key($args['id']) : 0;

		// Getting the CF7 form ID form the hash.
		if (get_post_type($pcf_form_id) !== 'wpcf7_contact_form') {
			$pcf_form_id = wpb_pcf_wpcf7_get_contact_form_id_by_hash($pcf_form_id);
		} else {
			$pcf_form_id = intval($pcf_form_id);
		}

		if ($pcf_form_id > 0 && get_post_type($pcf_form_id) === 'wpcf7_contact_form') {

			$shortcode = sprintf('[contact-form-7 id="%d"]', esc_attr($pcf_form_id));
			$response = '<div class="wpb-pcf-wpcf7-form wpb-pcf-popup-content" data-id="' . esc_attr($pcf_form_id) . '" style="display: none!important; visibility: hidden!important; opacity: 0!important;">';
			$response .= do_shortcode($shortcode);
			$response .= '</div>';

			add_action('wp_footer', function () use ($response) {
				echo $response;
			});
		}
	}
}
