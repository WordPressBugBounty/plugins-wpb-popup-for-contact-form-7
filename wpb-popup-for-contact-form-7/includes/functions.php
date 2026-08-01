<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! function_exists('wpb_pcf_get_option')) {

	/**
	 * Get settings option value.
	 *
	 * @param string $option The option key.
	 * @param string $section The section key.
	 * @param string $default_value The default value.
	 * @return array
	 */
	function wpb_pcf_get_option($option, $section, $default_value = '')
	{

		$options = get_option($section);

		if (isset($options[$option])) {
			return $options[$option];
		}

		return $default_value;
	}
}

/**
 * Searches for a contact form ID by a hash string.
 *
 * @param string $hash Part of a hash string.
 * @return int|null Contact form ID or null if not found.
 */
if (! function_exists('wpb_pcf_wpcf7_get_contact_form_id_by_hash')) {
	function wpb_pcf_wpcf7_get_contact_form_id_by_hash($hash)
	{
		global $wpdb;

		// Trim the hash and ensure it's a string.
		$hash = trim((string) $hash);

		// Check if the hash length is valid.
		if (strlen($hash) < 7) {
			return null;
		}

		// Prepare the like clause safely.
		$like = $wpdb->esc_like($hash) . '%';

		// Use a prepared statement for the entire query.
		$query = $wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value LIKE %s",
			'_hash',
			$like
		);

		// Execute the query and get the result.
		$post_id = $wpdb->get_var($query);

		// Return the post ID or null if not found.
		return $post_id ? (int) $post_id : null;
	}
}


if (! function_exists('wpb_pcf_contact_form_button')) {

	/**
	 * Backward compatible wrapper for displaying the popup button.
	 *
	 * Kept so themes/snippets built against pre-2.x versions of this
	 * plugin (which called this function directly) keep working. The
	 * actual rendering now lives in WPB_PCF_Shortcode_Handler::contact_form_button().
	 *
	 * @param array $args An array of attributes.
	 * @return void
	 */
	function wpb_pcf_contact_form_button($args = array())
	{
		if (! class_exists('WPB_PCF_Shortcode_Handler')) {
			require_once __DIR__ . '/class.shortcode.php';
		}

		static $shortcode_handler = null;

		if (null === $shortcode_handler) {
			$shortcode_handler = new WPB_PCF_Shortcode_Handler();
		}

		$shortcode_handler->contact_form_button($args);
	}
}

/**
 * Add CF7 Shortcodes.
 */
add_action('wpcf7_init', 'wpb_pcf_cf7_add_form_tag_for_post_title');

/**
 * Add CF7 Shortcodes function.
 *
 * @return void
 */
function wpb_pcf_cf7_add_form_tag_for_post_title()
{
	wpcf7_add_form_tag('post_title', 'wpb_pcf_cf7_post_title_tag_handler'); // "clock" is the type of the form-tag
}

/**
 * Add post title CF7 Shortcode.
 *
 * @return string
 */
function wpb_pcf_cf7_post_title_tag_handler()
{
	check_ajax_referer('wpb-pcf-button-ajax', 'wpb_pcf_fire_popup_nonce'); // Verify the nonce.

	if (isset($_POST['wpb_pcf_post_id'])) {
		return '<input type="hidden" name="post_title" value="' . esc_html(get_the_title((int) $_POST['wpb_pcf_post_id'])) . '">';
	}
}
