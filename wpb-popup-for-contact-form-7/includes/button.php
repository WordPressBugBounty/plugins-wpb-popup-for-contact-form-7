<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'wpb_pcf_contact_form_button' ) ) {

	/**
	 * Generic function for displaying docs
	 *
	 * @param  array $args An array of attributes.
	 *
	 * @return void
	 */
	function wpb_pcf_contact_form_button( $args = array() ) {

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
			'text'                => wpb_pcf_get_option( 'btn_text', 'wpb_pcf_btn_settings', esc_html__( 'Contact Us', 'wpb-popup-for-cf7-lite' ) ),
			'btn_size'            => wpb_pcf_get_option( 'btn_size', 'wpb_pcf_btn_settings', 'large' ),
			'form_style'          => ( 'on' === wpb_pcf_get_option( 'form_style', 'wpb_pcf_popup_settings' ) ? true : false ),
			'allow_outside_click' => ( 'on' === wpb_pcf_get_option( 'allow_outside_click', 'wpb_pcf_popup_settings' ) ? true : false ),
			'width'               => wpb_pcf_get_option( 'popup_width', 'wpb_pcf_popup_settings', 500 ) . wpb_pcf_get_option( 'popup_width_unit', 'wpb_pcf_popup_settings', 'px' ),
		);

		$args = wp_parse_args( $args, $defaults );

		if ( defined( 'WPCF7_PLUGIN' ) ) {
			if ( $args['id'] ) {
				echo wp_kses_post(
					apply_filters(
						'wpb_pcf_button_html',
						sprintf(
							'<button data-id="%1$s" data-post_id="%2$s" data-form_style="%3$s" data-allow_outside_click="%4$s" data-width="%5$s" class="wpb-pcf-form-fire wpb-pcf-btn-%6$s wpb-pcf-btn wpb-pcf-btn-default%7$s">%8$s</button>',
							esc_attr( $args['id'] ),
							esc_attr( $args['post_id'] ),
							esc_attr( $args['form_style'] ),
							esc_attr( $args['allow_outside_click'] ),
							esc_attr( $args['width'] ),
							esc_attr( $args['btn_size'] ),
							( $args['class'] ? esc_attr( ' ' . $args['class'] ) : '' ),
							esc_html( $args['text'] )
						),
						$args
					)
				);
			} else {
				printf( '<div class="wpb-pcf-alert wpb-pcf-alert-inline wpb-pcf-alert-error">%s</div>', esc_html__( 'Form id required.', 'wpb-popup-for-cf7-lite' ) );
			}
		} else {
			printf( '<div class="wpb-pcf-alert wpb-pcf-alert-inline wpb-pcf-alert-error">%s</div>', esc_html__( 'Popup for Contact Form 7 required the Contact Form 7 plugin to work with.', 'wpb-popup-for-cf7-lite' ) );
		}
	}
}
