<?php
namespace WPB_PCF_Elementor_Addons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class PCF_Button extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'pcf-button';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Contact Form 7 Popup', 'wpb-popup-for-cf7-lite' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-lightbox';
	}

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the reload preview is required.
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'wpb-popup-for-cf7-lite' ),
			)
		);

		$this->add_control(
			'form_id',
			array(
				'label'       => esc_html__( 'Select a CF7 Form', 'wpb-popup-for-cf7-lite' ),
				'description' => esc_html__( 'Select a Contact Form 7 form for popup.', 'wpb-popup-for-cf7-lite' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => wp_list_pluck(
					get_posts(
						array(
							'post_type'   => 'wpcf7_contact_form',
							'numberposts' => -1,
						)
					),
					'post_title',
					'ID'
				),
			)
		);

		$this->add_control(
			'btn_text',
			array(
				'label'       => esc_html__( 'Button Text', 'wpb-popup-for-cf7-lite' ),
				'description' => esc_html__( 'You can add your own text for the button.', 'wpb-popup-for-cf7-lite' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Contact Us', 'wpb-popup-for-cf7-lite' ),
			)
		);

		$this->add_control(
			'btn_size',
			array(
				'label'       => esc_html__( 'Button Size', 'wpb-popup-for-cf7-lite' ),
				'description' => esc_html__( 'Select button size. Default: Medium.', 'wpb-popup-for-cf7-lite' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'medium',
				'options'     => array(
					'small'  => esc_html__( 'Small', 'wpb-popup-for-cf7-lite' ),
					'medium' => esc_html__( 'Medium', 'wpb-popup-for-cf7-lite' ),
					'large'  => esc_html__( 'Large', 'wpb-popup-for-cf7-lite' ),
				),
			)
		);

		$this->add_control(
			'form_style',
			array(
				'label'       => esc_html__( 'Form Style', 'wpb-popup-for-cf7-lite' ),
				'description' => esc_html__( 'Check this to enable the form style.', 'wpb-popup-for-cf7-lite' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'allow_outside_click',
			array(
				'label'       => esc_html__( 'Outside Click', 'wpb-popup-for-cf7-lite' ),
				'description' => esc_html__( 'If checked, the user can dismiss the popup by clicking outside it.', 'wpb-popup-for-cf7-lite' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'width',
			array(
				'label'      => esc_html__( 'Width', 'wpb-popup-for-cf7-lite' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 5000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 500,
				),
			)
		);

		$this->add_control(
			'_btn_css_classes',
			array(
				'label'        => esc_html__( 'Button CSS Classes', 'wpb-popup-for-cf7-lite' ),
				'type'         => Controls_Manager::TEXT,
				'dynamic'      => array(
					'active' => true,
				),
				'prefix_class' => '',
				'title'        => esc_html__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'wpb-popup-for-cf7-lite' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		global $post;
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'pcf_button', 'class', 'wpb-pcf-form-fire' );
		$this->add_render_attribute( 'pcf_button', 'class', 'wpb-pcf-btn' );
		$this->add_render_attribute( 'pcf_button', 'class', 'wpb-pcf-btn-default' );

		if ( ! empty( $settings['btn_size'] ) ) {
			$this->add_render_attribute( 'pcf_button', 'class', 'wpb-pcf-btn-' . $settings['btn_size'] );
		}

		if ( ! empty( $settings['_btn_css_classes'] ) ) {
			$this->add_render_attribute( 'pcf_button', 'class', $settings['_btn_css_classes'] );
		}

		if ( ! empty( $settings['form_id'] ) ) {
			$this->add_render_attribute( 'pcf_button', 'data-id', $settings['form_id'] );
		}

		if ( $post ) {
			$this->add_render_attribute( 'pcf_button', 'data-post_id', $post->ID );
		}

		if ( $settings['form_style'] ) {
			$this->add_render_attribute( 'pcf_button', 'data-form_style', 'yes' === $settings['form_style'] ? '1' : '' );
		}

		if ( $settings['allow_outside_click'] ) {
			$this->add_render_attribute( 'pcf_button', 'data-allow_outside_click', 'yes' === $settings['allow_outside_click'] ? '1' : '' );
		}

		if ( $settings['width']['size'] ) {
			$this->add_render_attribute( 'pcf_button', 'data-width', $settings['width']['size'] . $settings['width']['unit'] );
		}

		if ( defined( 'WPCF7_PLUGIN' ) ) {
			if ( $settings['form_id'] ) {
				echo wp_kses_post(
					apply_filters(
						'wpb_pcf_button_html',
						sprintf(
							'<button %s>%s</button>',
							$this->get_render_attribute_string( 'pcf_button' ),
							esc_html( $settings['btn_text'] )
						),
						$settings
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
