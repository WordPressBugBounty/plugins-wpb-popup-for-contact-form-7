<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'WPB_PCF_Plugin_Settings' ) ) :
	/**
	 * Plugin Settings Class
	 */
	class WPB_PCF_Plugin_Settings {

		/**
		 * Plugin Settings API.
		 *
		 * @var mix
		 */
		private $settings_api;

		/**
		 * Plugin Settings Page URL.
		 *
		 * @var string
		 */
		private $settings_name = 'wpb-popup-for-cf7';

		/**
		 * Class Constructor.
		 */
		public function __construct() {
			$this->settings_api = new WPB_PCF_WeDevs_Settings_API();

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Admin Init.
		 */
		public function admin_init() {

			// set the settings.
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// initialize settings.
			$this->settings_api->admin_init();
		}

		/**
		 * Settings page scripts.
		 */
		public function admin_enqueue_scripts() {
			$screen = get_current_screen();

			$preg_match = preg_match( '/_wpb-popup-for-cf7/i', $screen->id );

			if ( 1 === $preg_match ) {
				$this->settings_api->admin_enqueue_scripts();
			}
		}

		/**
		 * Admin Menu.
		 */
		public function admin_menu() {
			add_submenu_page(
				'wpcf7',
				esc_html__( 'Popup for Contact Form 7', 'wpb-popup-for-cf7-lite' ),
				esc_html__( 'Popup', 'wpb-popup-for-cf7-lite' ),
				apply_filters( 'wpcf7_admin_management_page', 'delete_posts' ),
				$this->settings_name,
				array( $this, 'plugin_page' )
			);
		}

		/**
		 * Settings Sections.
		 */
		public function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'wpb_pcf_form_settings',
					'title' => esc_html__( 'Form Settings', 'wpb-popup-for-cf7-lite' ),
				),
				array(
					'id'    => 'wpb_pcf_btn_settings',
					'title' => esc_html__( 'Button Settings', 'wpb-popup-for-cf7-lite' ),
				),
				array(
					'id'    => 'wpb_pcf_popup_settings',
					'title' => esc_html__( 'Popup Settings', 'wpb-popup-for-cf7-lite' ),
				),
			);

			return apply_filters( 'wpb_pcf_settings_sections', $sections );
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		public function get_settings_fields() {

			$forms = wp_list_pluck(
				get_posts(
					array(
						'post_type'   => 'wpcf7_contact_form',
						'numberposts' => -1,
					)
				),
				'post_title',
				'ID'
			);

			$settings_fields = array(
				'wpb_pcf_form_settings'  => array(
					array(
						'name'    => 'cf7_form_id',
						'label'   => esc_html__( 'Select a CF7 Form', 'wpb-popup-for-cf7-lite' ),
						'desc'    => ( ! empty( $forms ) ? esc_html__( 'Select a Contact Form 7 form for popup.', 'wpb-popup-for-cf7-lite' ) : sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wpcf7-new' ), esc_html__( 'Create a Form', 'wpb-popup-for-cf7-lite' ) ) ),
						'type'    => 'select',
						'options' => $forms,
					),
				),
				'wpb_pcf_btn_settings'   => array(
					array(
						'name'              => 'btn_text',
						'label'             => esc_html__( 'Button Text', 'wpb-popup-for-cf7-lite' ),
						'desc'              => esc_html__( 'You can add your own text for the button.', 'wpb-popup-for-cf7-lite' ),
						'placeholder'       => esc_html__( 'Contact Us', 'wpb-popup-for-cf7-lite' ),
						'type'              => 'text',
						'default'           => esc_html__( 'Contact Us', 'wpb-popup-for-cf7-lite' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'    => 'btn_size',
						'label'   => esc_html__( 'Button Size', 'wpb-popup-for-cf7-lite' ),
						'desc'    => esc_html__( 'Select button size. Default: Medium.', 'wpb-popup-for-cf7-lite' ),
						'type'    => 'select',
						'size'    => 'wpb-select-buttons',
						'default' => 'large',
						'options' => array(
							'small'  => esc_html__( 'Small', 'wpb-popup-for-cf7-lite' ),
							'medium' => esc_html__( 'Medium', 'wpb-popup-for-cf7-lite' ),
							'large'  => esc_html__( 'Large', 'wpb-popup-for-cf7-lite' ),
						),
					),
					array(
						'name'    => 'btn_color',
						'label'   => esc_html__( 'Button Color', 'wpb-popup-for-cf7-lite' ),
						'desc'    => esc_html__( 'Choose button color.', 'wpb-popup-for-cf7-lite' ),
						'type'    => 'color',
						'default' => '#ffffff',
					),
					array(
						'name'    => 'btn_bg_color',
						'label'   => esc_html__( 'Button Background', 'wpb-popup-for-cf7-lite' ),
						'desc'    => esc_html__( 'Choose button background color.', 'wpb-popup-for-cf7-lite' ),
						'type'    => 'color',
						'default' => '#17a2b8',
					),
					array(
						'name'    => 'btn_hover_color',
						'label'   => esc_html__( 'Button Hover Color', 'wpb-popup-for-cf7-lite' ),
						'desc'    => esc_html__( 'Choose button hover color.', 'wpb-popup-for-cf7-lite' ),
						'type'    => 'color',
						'default' => '#ffffff',
					),
					array(
						'name'    => 'btn_bg_hover_color',
						'label'   => esc_html__( 'Button Hover Background', 'wpb-popup-for-cf7-lite' ),
						'desc'    => esc_html__( 'Choose button hover background color.', 'wpb-popup-for-cf7-lite' ),
						'type'    => 'color',
						'default' => '#138496',
					),
				),
				'wpb_pcf_popup_settings' => array(
					array(
						'name'    => 'form_style',
						'label'   => esc_html__( 'Enable Form Style', 'wpb-popup-for-cf7-lite' ),
						'desc'    => esc_html__( 'Check this to enable the form style.', 'wpb-popup-for-cf7-lite' ),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'  => 'allow_outside_click',
						'label' => esc_html__( 'Close Popup on Outside Click', 'wpb-popup-for-cf7-lite' ),
						'desc'  => esc_html__( 'If checked, the user can dismiss the popup by clicking outside it.', 'wpb-popup-for-cf7-lite' ),
						'type'  => 'checkbox',
					),
					array(
						'name'              => 'popup_width',
						'label'             => esc_html__( 'Popup Width', 'wpb-popup-for-cf7-lite' ),
						'desc'              => esc_html__( 'Popup window width, Can be in px or %. The default width is 500px.', 'wpb-popup-for-cf7-lite' ),
						'type'              => 'numberunit',
						'default'           => 500,
						'default_unit'      => 'px',
						'sanitize_callback' => 'floatval',
						'options'           => array(
							'px' => esc_html__( 'Px', 'wpb-popup-for-cf7-lite' ),
							'%'  => esc_html__( '%', 'wpb-popup-for-cf7-lite' ),
						),
					),
				),
			);

			return apply_filters( 'wpb_pcf_settings_fields', $settings_fields );
		}

		/**
		 * The plugin page.
		 */
		public function plugin_page() {
			echo '<div id="wpb-pcf-settings" class="wrap wpb-plugin-settings-wrap">';

			settings_errors();

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';

			do_action( 'wpb_pcf_after_settings_page' );
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		public function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}
	}
endif;
