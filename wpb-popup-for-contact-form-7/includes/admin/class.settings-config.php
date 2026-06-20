<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


if (! class_exists('WPB_PCF_Plugin_Settings')) :
	/**
	 * Plugin Settings Class
	 */
	class WPB_PCF_Plugin_Settings
	{

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
		public function __construct()
		{
			$this->settings_api = new WPB_PCF_WeDevs_Settings_API();

			add_action('admin_init', array($this, 'admin_init'));
			add_action('admin_menu', array($this, 'admin_menu'));
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		}

		/**
		 * Admin Init.
		 */
		public function admin_init()
		{

			// set the settings.
			$this->settings_api->set_sections($this->get_settings_sections());
			$this->settings_api->set_fields($this->get_settings_fields());

			// initialize settings.
			$this->settings_api->admin_init();
		}

		/**
		 * Settings page scripts.
		 */
		public function admin_enqueue_scripts()
		{
			$screen = get_current_screen();

			$preg_match = preg_match('/_wpb-popup-for-cf7/i', $screen->id);

			if (1 === $preg_match) {
				$this->settings_api->admin_enqueue_scripts();
			}
		}

		/**
		 * Admin Menu.
		 */
		public function admin_menu()
		{
			add_submenu_page(
				'wpcf7',
				esc_html__('Popup for Contact Form 7', 'wpb-popup-for-contact-form-7'),
				esc_html__('Popup', 'wpb-popup-for-contact-form-7'),
				apply_filters('wpcf7_admin_management_page', 'delete_posts'),
				$this->settings_name,
				array($this, 'plugin_page')
			);
		}

		/**
		 * Settings Sections.
		 */
		public function get_settings_sections()
		{
			$sections = array(
				array(
					'id'    => 'wpb_pcf_form_settings',
					'title' => esc_html__('Form Settings', 'wpb-popup-for-contact-form-7'),
				),
				array(
					'id'    => 'wpb_pcf_btn_settings',
					'title' => esc_html__('Button Settings', 'wpb-popup-for-contact-form-7'),
				),
				array(
					'id'    => 'wpb_pcf_popup_settings',
					'title' => esc_html__('Popup Settings', 'wpb-popup-for-contact-form-7'),
				),
			);

			return apply_filters('wpb_pcf_settings_sections', $sections);
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		public function get_settings_fields()
		{

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
						'label'   => esc_html__('Select a CF7 Form', 'wpb-popup-for-contact-form-7'),
						'desc'    => (! empty($forms) ? esc_html__('Select a Contact Form 7 form for popup.', 'wpb-popup-for-contact-form-7') : sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wpcf7-new'), esc_html__('Create a Form', 'wpb-popup-for-contact-form-7'))),
						'type'    => 'select',
						'options' => $forms,
					),
				),
				'wpb_pcf_btn_settings'   => array(
					array(
						'name'              => 'btn_text',
						'label'             => esc_html__('Button Text', 'wpb-popup-for-contact-form-7'),
						'desc'              => esc_html__('You can add your own text for the button.', 'wpb-popup-for-contact-form-7'),
						'placeholder'       => esc_html__('Contact Us', 'wpb-popup-for-contact-form-7'),
						'type'              => 'text',
						'default'           => esc_html__('Contact Us', 'wpb-popup-for-contact-form-7'),
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'    => 'btn_size',
						'label'   => esc_html__('Button Size', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('Select button size. Default: Medium.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'select',
						'size'    => 'wpb-select-buttons',
						'default' => 'large',
						'options' => array(
							'small'  => esc_html__('Small', 'wpb-popup-for-contact-form-7'),
							'medium' => esc_html__('Medium', 'wpb-popup-for-contact-form-7'),
							'large'  => esc_html__('Large', 'wpb-popup-for-contact-form-7'),
						),
					),
					array(
						'name'    => 'btn_color',
						'label'   => esc_html__('Button Color', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('Choose button color.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'color',
						'default' => '#ffffff',
					),
					array(
						'name'    => 'btn_bg_color',
						'label'   => esc_html__('Button Background', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('Choose button background color.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'color',
						'default' => '#17a2b8',
					),
					array(
						'name'    => 'btn_hover_color',
						'label'   => esc_html__('Button Hover Color', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('Choose button hover color.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'color',
						'default' => '#ffffff',
					),
					array(
						'name'    => 'btn_bg_hover_color',
						'label'   => esc_html__('Button Hover Background', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('Choose button hover background color.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'color',
						'default' => '#138496',
					),
				),
				'wpb_pcf_popup_settings' => array(
					array(
						'name'    => 'popup_type',
						'label'   => esc_html__('Load Popup Content', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('The pop-up form may load when a button is clicked or when the page loads for immediate opening.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'select',
						'size'    => 'wpb-select-buttons',
						'default' => 'on_button_click',
						'options' => array(
							'on_page_load'  => esc_html__('On Page Load', 'wpb-popup-for-contact-form-7'),
							'on_button_click'  => esc_html__('On Button Click', 'wpb-popup-for-contact-form-7'),
						),
					),
					array(
						'name'    => 'form_style',
						'label'   => esc_html__('Enable Form Style', 'wpb-popup-for-contact-form-7'),
						'desc'    => esc_html__('Check this to enable the form style.', 'wpb-popup-for-contact-form-7'),
						'type'    => 'checkbox',
						'default' => 'on',
					),
					array(
						'name'  => 'allow_outside_click',
						'label' => esc_html__('Close Popup on Outside Click', 'wpb-popup-for-contact-form-7'),
						'desc'  => esc_html__('If checked, the user can dismiss the popup by clicking outside it.', 'wpb-popup-for-contact-form-7'),
						'type'  => 'checkbox',
					),
					array(
						'name'              => 'popup_width',
						'label'             => esc_html__('Popup Width', 'wpb-popup-for-contact-form-7'),
						'desc'              => esc_html__('Popup window width, Can be in px or %. The default width is 500px.', 'wpb-popup-for-contact-form-7'),
						'type'              => 'numberunit',
						'default'           => 500,
						'default_unit'      => 'px',
						'sanitize_callback' => 'floatval',
						'options'           => array(
							'px' => esc_html__('Px', 'wpb-popup-for-contact-form-7'),
							'%'  => esc_html__('%', 'wpb-popup-for-contact-form-7'),
						),
					),
				),
			);

			return apply_filters('wpb_pcf_settings_fields', $settings_fields);
		}

		/**
		 * The plugin page.
		 */
		public function plugin_page()
		{
			$pro_url = 'https://wpbean.com/downloads/popup-for-contact-form-7-pro/?utm_content=Popup+for+Contact+Form+7+Pro&utm_campaign=adminlink&utm_medium=settings-header&utm_source=FreeVersion';

			echo '<div id="wpb-pcf-settings" class="wrap wpb-plugin-settings-wrap">';
?>
			<div class="wpb-pcf-page-header">
				<div class="wpb-pcf-header-brand">
					<div class="wpb-pcf-header-icon">
						<span class="dashicons dashicons-format-chat"></span>
					</div>
					<div class="wpb-pcf-header-info">
						<h1><?php esc_html_e('WPB Popup for Contact Form 7', 'wpb-popup-for-contact-form-7'); ?></h1>
						<div class="wpb-pcf-header-meta">
							<span class="wpb-pcf-plan-badge"><?php esc_html_e('FREE PLAN', 'wpb-popup-for-contact-form-7'); ?></span>
							<span class="wpb-pcf-version">v<?php echo esc_html(WPB_PCF_FREE_VERSION); ?></span>
							<a href="https://docs.wpbean.com/docs/popup-for-contact-form-7/installing/" target="_blank" class="wpb-pcf-header-docs-link"><?php esc_html_e('Documentation', 'wpb-popup-for-contact-form-7'); ?></a>
							<a href="https://wpbean.com/support" target="_blank" class="wpb-pcf-header-docs-link"><?php esc_html_e('Support', 'wpb-popup-for-contact-form-7'); ?></a>
						</div>
					</div>
				</div>
				<a href="<?php echo esc_url($pro_url); ?>" target="_blank" class="wpb-pcf-header-upgrade-btn">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e('Upgrade to Pro', 'wpb-popup-for-contact-form-7'); ?>
				</a>
			</div>
			<hr class="wp-header-end">
<?php

			settings_errors();

			echo '<div class="wpb-pcf-layout-row">';

			echo '<div class="wpb-pcf-col-main">';
			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();
			echo '</div>';

			// Sticky sidebar.
			echo '<div class="wpb-pcf-col-sidebar">';
			echo '<div class="wpb-pcf-pro-sidebar-card">';

			echo '<div class="wpb-pcf-pro-card-header">';
			echo '<span class="dashicons dashicons-star-filled wpb-pcf-pro-card-crown"></span>';
			echo '<h3>' . esc_html__('Upgrade to Pro', 'wpb-popup-for-contact-form-7') . '</h3>';
			echo '<p>' . esc_html__('Unlock powerful features for your site', 'wpb-popup-for-contact-form-7') . '</p>';
			echo '</div>';

			echo '<div class="wpb-pcf-pro-card-body">';

			$features = array(
				esc_html__('Show different popup forms in different locations.', 'wpb-popup-for-contact-form-7'),
				esc_html__('Add popup links directly to navigation menus.', 'wpb-popup-for-contact-form-7'),
				esc_html__('Auto-trigger popups on selected pages with flexible conditions.', 'wpb-popup-for-contact-form-7'),
				esc_html__('Trigger popups on events like page load, exit intent, scroll, or hover.', 'wpb-popup-for-contact-form-7'),
				esc_html__('Display popups based on specific URLs or post/page IDs.', 'wpb-popup-for-contact-form-7'),
				esc_html__('Customize button and popup styles with advanced settings.', 'wpb-popup-for-contact-form-7'),
				esc_html__('Generate multiple customized popup buttons easily.', 'wpb-popup-for-contact-form-7'),
			);

			echo '<ul class="wpb-pcf-pro-features-list">';
			foreach ($features as $feature) {
				echo '<li>' . $feature . '</li>';
			}
			echo '</ul>';

			echo '<a href="' . esc_url($pro_url) . '" target="_blank" class="wpb-pcf-pro-card-cta">';
			echo '<span class="dashicons dashicons-cart"></span>';
			echo esc_html__('Upgrade to Pro', 'wpb-popup-for-contact-form-7');
			echo '</a>';

			echo '</div>'; // .wpb-pcf-pro-card-body
			echo '</div>'; // .wpb-pcf-pro-sidebar-card
			echo '</div>'; // .wpb-pcf-col-sidebar

			echo '</div>'; // .wpb-pcf-layout-row
			echo '</div>'; // #wpb-pcf-settings

			do_action('wpb_pcf_after_settings_page');
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		public function get_pages()
		{
			$pages         = get_pages();
			$pages_options = array();
			if ($pages) {
				foreach ($pages as $page) {
					$pages_options[$page->ID] = $page->post_title;
				}
			}

			return $pages_options;
		}
	}
endif;
