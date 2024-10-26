<?php
/**
 * Plugin Name:       WPB Popup for Contact Form 7
 * Plugin URI:        https://wpbean.com/plugins/
 * Description:       Shows a nice popup of the Contact Form 7 form.
 * Version:           1.7.4
 * Author:            wpbean
 * Author URI:        https://wpbean.com
 * Text Domain:       wpb-popup-for-cf7-lite
 * Domain Path:       /languages
 *
 * @package WPB Popup for Contact Form 7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin main class
 */
final class WPB_PCF_Get_Popup_Button {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.7.4';

	/**
	 * The plugin url.
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * The plugin path.
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Theme dir path.
	 *
	 * @var string
	 */
	public $theme_dir_path;

	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class Constructor.
	 */
	private function __construct() {
		$this->define_constants();
		if ( ! defined( 'WPB_PCF_PREMIUM' ) ) {
			add_action( 'plugins_loaded', array( $this, 'plugin_init' ) );
			add_action( 'activated_plugin', array( $this, 'activation_redirect' ) );
		}
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( plugin_basename( __FILE__ ), array( $this, 'plugin_deactivation' ) );
	}

	/**
	 * Define plugin Constants.
	 */
	public function define_constants() {
		define( 'WPB_PCF_FREE_VERSION', $this->version );
		define( 'WPB_PCF_FREE_INIT', plugin_basename( __FILE__ ) );
		define( 'WPB_PCF_CPT_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'WPB_PCF_CPT_PLUGIN_EL_TEMPLATE_PATH', WPB_PCF_CPT_PLUGIN_PATH . '/elementor/' );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function plugin_init() {
		$this->file_includes();
		$this->init_classes();

		add_action( 'init', array( $this, 'localization_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'dependency_admin_notices' ) );
		//add_action( 'admin_notices', array( $this, 'suggeations_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'admin_notice_dismissed' ) );
	}

	/**
	 * Pro version discount admin notice.
	 *
	 * @return void
	 */
	public function suggeations_admin_notice() {
		$user_id              = get_current_user_id();
		$discount_dismiss_url = wp_nonce_url(
			add_query_arg( 'wpb-pcf-pro-discount-admin-notice-dismissed', 'true', admin_url() ),
			'wpbean_pcf_discount_admin_notice_dismissed',
		);

		$form_popup_suggestion_dismiss_url = wp_nonce_url(
			add_query_arg( 'wpb-pcf-form-popup-suggestion-admin-notice-dismissed', 'true', admin_url() ),
			'wpbean_pcf_form_popup_suggestion_admin_notice_dismissed',
		);

		if ( ! get_user_meta( $user_id, 'wpb_pcf_pro_discount_dismissed' ) ) {
			printf(
				'<div class="wpb-pcf-discount-notice updated" style="padding: 30px 20px;border-left-color: #27ae60;border-left-width: 5px;margin-top: 20px;"><p style="font-size: 18px;line-height: 32px">%s <a target="_blank" href="%s">%s</a>! %s <b>%s</b></p><a href="%s">%s</a></div>',
				esc_html__( 'Get a 10% exclusive discount on the premium version of the', 'wpb-popup-for-cf7-lite' ),
				'https://wpbean.com/downloads/popup-for-contact-form-7-pro/?utm_content=Popup+for+Contact+Form+7+Pro&utm_campaign=adminlink&utm_medium=discount-notie&utm_source=FreeVersion',
				esc_html__( 'Popup for Contact Form 7', 'wpb-popup-for-cf7-lite' ),
				esc_html__( 'Use discount code - ', 'wpb-popup-for-cf7-lite' ),
				'10PERCENTOFF',
				esc_url( $discount_dismiss_url ),
				esc_html__( 'Dismiss', 'wpb-popup-for-cf7-lite' )
			);
		}

		if ( ! get_user_meta( $user_id, 'wpb_pcf_form_popup_suggestion' ) ) {
			printf(
				'<div class="wpb-pcf-form-popup-suggestion updated" style="padding: 30px 20px;border-left-color: #27ae60;border-left-width: 5px;margin-top: 20px;"><p style="font-size: 18px;line-height: 32px">%s <a target="_blank" href="%s">%s</a> %s <b>%s</b>%s </p><a href="%s">%s</a></div>',
				esc_html__( 'Try our new ', 'wpb-popup-for-cf7-lite' ),
				'https://wordpress.org/plugins/wpb-form-popup/?utm_content=WPB+Form+Popup&utm_campaign=adminlink&utm_medium=suggestion-notie&utm_source=PCFFreeVersion',
				esc_html__( 'WPB Form Popup', 'wpb-popup-for-cf7-lite' ),
				esc_html__( 'plugin if you prefer to use a different form plugin than', 'wpb-popup-for-cf7-lite' ),
				esc_html__( 'Contact Form 7', 'wpb-popup-for-cf7-lite' ),
				esc_html__( '. All of the form plugins are supported.', 'wpb-popup-for-cf7-lite' ),
				esc_url( $form_popup_suggestion_dismiss_url ),
				esc_html__( 'Dismiss', 'wpb-popup-for-cf7-lite' )
			);
		}
	}

	/**
	 * Initialize the dismissed function
	 *
	 * @return void
	 */
	public function admin_notice_dismissed() {
		$user_id = get_current_user_id();
		if ( isset( $_GET['wpb-pcf-pro-discount-admin-notice-dismissed'] ) ) { // WPCS: input var ok.
			check_admin_referer( 'wpbean_pcf_discount_admin_notice_dismissed' );
			add_user_meta( $user_id, 'wpb_pcf_pro_discount_dismissed', 'true', true );
		}

		if ( isset( $_GET['wpb-pcf-form-popup-suggestion-admin-notice-dismissed'] ) ) { // WPCS: input var ok.
			check_admin_referer( 'wpbean_pcf_form_popup_suggestion_admin_notice_dismissed' );
			add_user_meta( $user_id, 'wpb_pcf_form_popup_suggestion', 'true', true );
		}
	}

	/**
	 * Plugin Deactivation
	 *
	 * @return void
	 */
	public function plugin_deactivation() {
		$user_id = get_current_user_id();
		if ( get_user_meta( $user_id, 'wpb_pcf_pro_discount_dismissed' ) ) {
			delete_user_meta( $user_id, 'wpb_pcf_pro_discount_dismissed' );
		}

		flush_rewrite_rules();
	}

	/**
	 * Do stuff upon plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		update_option( 'wpb_pcf_installed', time() );
		update_option( 'wpb_pcf_lite_version', $this->version );
	}

	/**
	 * Plugin activation redirect.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return void
	 */
	public function activation_redirect( $plugin ) {
		if ( plugin_basename( __FILE__ ) === $plugin && defined( 'WPCF7_PLUGIN' ) ) {
			wp_safe_redirect( esc_url( admin_url( 'admin.php?page=wpb-popup-for-cf7' ) ) );
			exit;
		}
	}

	/**
	 * Plugin action links.
	 *
	 * @param array $links Plugin action links.
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		if ( ! defined( 'WPB_PCF_PREMIUM' ) ) {
			if ( defined( 'WPCF7_PLUGIN' ) ) {
				$links[] = '<a href="' . admin_url( 'admin.php?page=wpb-popup-for-cf7' ) . '">' . esc_html__( 'Settings', 'wpb-popup-for-cf7-lite' ) . '</a>';
			}
			$links[] = '<a target="_blank" href="https://docs.wpbean.com/?p=1192">' . esc_html__( 'Documentation', 'wpb-popup-for-cf7-lite' ) . '</a>';
		
			$links[] = '<a target="_blank" style="    color: #93003c;text-shadow: 1px 1px 1px #eee;font-weight: 700;" href="https://wpbean.com/downloads/popup-for-contact-form-7-pro/?utm_content=Popup+for+Contact+Form+7+Pro&utm_campaign=adminlink&utm_medium=action-link&utm_source=FreeVersion">' . esc_html__( 'Get Pro', 'wpb-popup-for-cf7-lite' ) . '</a>';
		}
		return $links;
	}

	/**
	 *  Load the required files.
	 *
	 * @return void
	 */
	public function file_includes() {
		include_once __DIR__ . '/includes/functions.php';
		include_once __DIR__ . '/includes/button.php';
		include_once __DIR__ . '/includes/admin/class.menu-meta.php';

		if ( is_admin() ) {
			include_once __DIR__ . '/includes/admin/class.settings-api.php';
			include_once __DIR__ . '/includes/admin/class.settings-config.php';

			include_once __DIR__ . '/includes/DiscountPage/DiscountPage.php';
		} else {
			include_once __DIR__ . '/includes/class.shortcode.php';
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			include_once __DIR__ . '/includes/class.ajax.php';
		}

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			include_once __DIR__ . '/includes/elementor.php';
		}
	}

	/**
	 * Initialize the classes.
	 *
	 * @return void
	 */
	public function init_classes() {

		new WPB_PCF_Menu_Meta();

		if ( is_admin() ) {
			new WPB_PCF_Plugin_Settings();

			new WPBean_CF7_Popup_DiscountPage();
		} else {
			new WPB_PCF_Shortcode_Handler();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new WPB_PCF_Ajax();
		}
	}

	/**
	 * Initialize plugin for localization.
	 *
	 * @return void
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wpb-popup-for-cf7-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		do_action( 'cfturnstile_enqueue_scripts' );

		wp_enqueue_style( 'wpb-pcf-sweetalert2', plugins_url( 'assets/css/sweetalert2.min.css', __FILE__ ), array(), '11.4.8' );
		wp_enqueue_style( 'wpb-pcf-styles', plugins_url( 'assets/css/frontend.css', __FILE__ ), array(), '1.0' );
		wp_enqueue_script( 'wpb-pcf-sweetalert2', plugins_url( 'assets/js/sweetalert2.all.min.js', __FILE__ ), array( 'jquery' ), '11.4.8', true );
		wp_enqueue_script( 'wpb-pcf-scripts', plugins_url( 'assets/js/frontend.js', __FILE__ ), array( 'jquery', 'wp-util' ), '1.0', true );
		wp_localize_script(
			'wpb-pcf-scripts',
			'WPB_PCF_Vars',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wpb-pcf-button-ajax' ),
			)
		);

		$btn_color          = wpb_pcf_get_option( 'btn_color', 'wpb_pcf_btn_settings', '#ffffff' );
		$bg_color           = wpb_pcf_get_option( 'btn_bg_color', 'wpb_pcf_btn_settings', '#17a2b8' );
		$btn_hover_color    = wpb_pcf_get_option( 'btn_hover_color', 'wpb_pcf_btn_settings', '#ffffff' );
		$btn_bg_hover_color = wpb_pcf_get_option( 'btn_bg_hover_color', 'wpb_pcf_btn_settings', '#138496' );
		$custom_css         = "
		.wpb-pcf-btn-default,
		.wpb-pcf-form-style-true input[type=submit],
		.wpb-pcf-form-style-true input[type=button],
		.wpb-pcf-form-style-true input[type=submit],
		.wpb-pcf-form-style-true input[type=button]{
			color: {$btn_color}!important;
			background: {$bg_color}!important;
		}
		.wpb-pcf-btn-default:hover, .wpb-pcf-btn-default:focus,
		.wpb-pcf-form-style-true input[type=submit]:hover, .wpb-pcf-form-style-true input[type=submit]:focus,
		.wpb-pcf-form-style-true input[type=button]:hover, .wpb-pcf-form-style-true input[type=button]:focus,
		.wpb-pcf-form-style-true input[type=submit]:hover,
		.wpb-pcf-form-style-true input[type=button]:hover,
		.wpb-pcf-form-style-true input[type=submit]:focus,
		.wpb-pcf-form-style-true input[type=button]:focus {
			color: {$btn_hover_color}!important;
			background: {$btn_bg_hover_color}!important;
		}";

		wp_add_inline_style( 'wpb-pcf-styles', $custom_css );
	}

	/**
	 * Admin notices for dependency.
	 */
	public function dependency_admin_notices() {

		$cf7_form_id = wpb_pcf_get_option( 'cf7_form_id', 'wpb_pcf_form_settings' );

		$action      = 'install-plugin';
		$slug        = 'contact-form-7';
		$install_cf7 = wp_nonce_url(
			add_query_arg(
				array(
					'action' => $action,
					'plugin' => $slug,
				),
				admin_url( 'update.php' )
			),
			$action . '_' . $slug
		);

		if ( ! defined( 'WPCF7_PLUGIN' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><b><?php esc_html_e( 'Popup for Contact Form 7', 'wpb-popup-for-cf7-lite' ); ?></b><?php esc_html_e( ' required ', 'wpb-popup-for-cf7-lite' ); ?><b><a href="https://wordpress.org/plugins/contact-form-7" target="_blank"><?php esc_html_e( 'Contact Form 7', 'wpb-popup-for-cf7-lite' ); ?></a></b><?php esc_html_e( ' plugin to work with.', 'wpb-popup-for-cf7-lite' ); ?> <b><a href="<?php echo esc_url( $install_cf7 ); ?>">Click here</a></b> to install the <b><?php esc_html_e( 'Contact Form 7', 'wpb-popup-for-cf7-lite' ); ?></b> Plugin.</p>
			</div>
			<?php
		}

		if ( ! $cf7_form_id && defined( 'WPCF7_PLUGIN' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'The Popup for Contact Form 7 needs a form to show. Please select a form', 'wpb-popup-for-cf7-lite' ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpb-popup-for-cf7' ) ); ?>"><?php esc_html_e( 'here', 'wpb-popup-for-cf7-lite' ); ?></a>.</p>
			</div>
			<?php
		}
	}
}

/**
 * Initialize the main plugin.
 *
 * @return \WPB_PCF_Get_Popup_Button
 */

WPB_PCF_Get_Popup_Button::instance();