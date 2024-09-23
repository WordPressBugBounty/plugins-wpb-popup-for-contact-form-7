<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'wpb_pcf_get_option' ) ) {

	/**
	 * Get settings option value.
	 *
	 * @param string $option The option key.
	 * @param string $section The section key.
	 * @param string $default_value The default value.
	 * @return array
	 */
	function wpb_pcf_get_option( $option, $section, $default_value = '' ) {

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default_value;
	}
}

/**
 * Adding the Popup Button using action hook.
 */
add_action( 'wpb_pcf_contact_form_button', 'wpb_pcf_contact_form_button', 10 );

/**
 * Add CF7 Shortcodes.
 */
add_action( 'wpcf7_init', 'wpb_pcf_cf7_add_form_tag_for_post_title' );

/**
 * Add CF7 Shortcodes function.
 *
 * @return void
 */
function wpb_pcf_cf7_add_form_tag_for_post_title() {
	wpcf7_add_form_tag( 'post_title', 'wpb_pcf_cf7_post_title_tag_handler' ); // "clock" is the type of the form-tag
}

/**
 * Add post title CF7 Shortcode.
 *
 * @return string
 */
function wpb_pcf_cf7_post_title_tag_handler() {
	check_ajax_referer( 'wpb-pcf-button-ajax', 'wpb_pcf_fire_popup_nonce' ); // Verify the nonce.

	if ( isset( $_POST['wpb_pcf_post_id'] ) ) {
		return '<input type="hidden" name="post_title" value="' . esc_html( get_the_title( (int) $_POST['wpb_pcf_post_id'] ) ) . '">';
	}
}

/**
 * Premium Links
 */
add_action(
	'wpb_pcf_after_settings_page',
	function () {
		?>
	<div class="wpb_pcf_pro_features wrap">
		<h3>Premium Features:</h3>
		<ul>
			<li>Popup buttons for the Contact Form 7 forms.</li>
			<li>Show the buttons using ShortCodes and action hooks.</li>
			<li>Show different popup buttons on different locations with different forms.</li>
			<li>Including a link to the popup in the navigation menu.</li>
			<li>Initiate the popup form automatically on chosen pages based on a variety of conditions. There is no need to push any buttons.</li>
			<li>Automatic pop-ups can be set to appear in response to various events, such as page load, tab close, scroll down, or hovering over an element.</li>
			<li>Specific URLs or page/post type IDs can trigger an automated popup.</li>
			<li>Advanced settings for button and popup style configuration.</li>
			<li>Configurable options for the open and close animations of popups.</li>
			<li>Settings for closing the popup on successfully submitting the form.</li>
			<li>Elementor widget for easy use with the Elementor page builder.</li>
			<li>Use the popup as a standalone widget or integrate it into other Elementor widgets.</li>
			<li>Advanced popup buttons generator, for adding multiple different customized popup buttons.</li>
			<li>The popup buttons can be shown to any action hooks.</li>
			<li>Beautiful design for the forms that show in the popup.</li>
			<li>RTL Support and mobile responsive.</li>
			<li>Easy to use and customize.</li>
			<li>Online documentation and video tutorials.</li>
			<li>Quality support, and free installation if required.</li>
			<li>Tested with hundreds of popular themes and plugins.</li>
			<li>Tested with the Gutenberg Editor.</li>
			<li>Regular updates.</li>
		</ul>
		<div class="wpb-submit-button">
			<a class="button button-primary button-pro" href="https://wpbean.com/downloads/popup-for-contact-form-7-pro/?utm_content=Popup+for+Contact+Form+7+Pro&utm_campaign=adminlink&utm_medium=dash-widget&utm_source=FreeVersion" target="_blank">Buy PRO Version</a>
		</div>
	</div>

	<div class="wpb_pcf_pro_features wrap">
		<h3>Showing the Button:</h3>
		<p>The Popup button can be shown using different methods. Example: Using a ShortCode, Calling a PHP function, Adding our action hook, Add our PHP function to your site’s hook.</p>
		<p>Just this ShortCode where you want to show the button that will show the popup. This ShortCode has some parameters that allow you to customize it. Check the <a target="_blank" href="https://docs.wpbean.com/docs/popup-for-contact-form-7/showing-the-button/shortcode-parameters/">ShortCode Parameters</a> section for more about it.</p>
		<p>Copy this ShortCode and add where you want to show the button.</p>
		<pre class="wp-block-code"><code>[wpb-pcf-button]</code></pre>
		<div class="wpb-submit-button">
			<a class="button button-primary button-pro" href="https://docs.wpbean.com/docs/popup-for-contact-form-7/showing-the-button/" target="_blank">Detail Documentation</a>
		</div>
	</div>

	<div class="wpb_pcf_pro_features wrap">
		<h3>Elementor Use:</h3>
		<p>Those that use the Elementor page builder will find that our Elementor widget makes it simple to display the popup button in any convenient location.</p>
		<div class="wpb-submit-button">
			<a class="button button-primary button-pro" href="https://docs.wpbean.com/docs/popup-for-contact-form-7/showing-the-button/elementor-use/" target="_blank">Elementor Documentation</a>
		</div>
	</div>
	<div class="wpb_pcf_pro_features wrap">
		<h3>Web Development Services:</h3>
		<p>Do you need help creating a WordPress site? Yes, we can assist you with that. We've been working with WordPress and other cutting-edge website builders for more than eight years.</p>
		<p>To discuss and receive a quote, please get in touch with us.</p>
		<div class="wpb-submit-button">
			<a class="button button-primary button-pro" href="https://wpbean.com/web-development-services/?utm_content=WPB+Accordion+Menu+Pro&utm_campaign=adminlink&utm_medium=dash-widget&utm_source=FreeVersion" target="_blank">Let's Talk</a>
		</div>
	</div>
		<?php
	}
);