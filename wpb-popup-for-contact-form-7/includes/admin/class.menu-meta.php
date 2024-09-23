<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Menu Meta Class
 */
class WPB_PCF_Menu_Meta {

	/**
	 * The Menu Custom Fields.
	 *
	 * @var array
	 */
	protected static $fields = array();

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_custom_fields' ), 10, 2 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'nav_update' ), 10, 2 );
		add_filter( 'manage_nav-menus_columns', array( $this, 'nav_columns' ), 99 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'link_attributes' ), 10, 2 );

		self::$fields = array(
			'cf7_popup_trigger' => esc_html__( 'CF7 Popup Trigger', 'wpb-popup-for-cf7-lite' ),
		);
	}

	/**
	 * Filters the HTML attributes applied to a menu item’s anchor element.
	 *
	 * @param array  $atts The HTML attributes applied to the menu item’s <a> element, empty strings are ignored.
	 * @param object $menu_item The current menu item object.
	 * @return array
	 */
	public function link_attributes( $atts, $menu_item ) {
		if ( is_object( $menu_item ) && isset( $menu_item->ID ) ) {
			$menu_cf7_popup_trigger = get_post_meta( $menu_item->ID, 'menu-item-cf7_popup_trigger', true );
			if ( ! empty( $menu_cf7_popup_trigger ) && 'on' === $menu_cf7_popup_trigger ) {
				$atts['class']                    = 'wpb-pcf-form-fire';
				$atts['href']                     = '';
				$atts['data-id']                  = wpb_pcf_get_option( 'cf7_form_id', 'wpb_pcf_form_settings' );
				$atts['data-post_id']             = get_the_ID();
				$atts['data-form_style']          = ( 'on' === wpb_pcf_get_option( 'form_style', 'wpb_pcf_popup_settings' ) ? true : false );
				$atts['data-width']               = wpb_pcf_get_option( 'popup_width', 'wpb_pcf_popup_settings', 500 ) . wpb_pcf_get_option( 'popup_width_unit', 'wpb_pcf_popup_settings', 'px' );
				$atts['data-allow_outside_click'] = ( 'on' === wpb_pcf_get_option( 'allow_outside_click', 'wpb_pcf_popup_settings' ) ? true : false );
			}
		}
		return $atts;
	}

	/**
	 * Add custom fields to menu item
	 *
	 * @param string $item_id Menu item ID as a numeric string.
	 * @param object $menu_item Menu item data object.
	 * @return void
	 */
	public function menu_item_custom_fields( $item_id, $menu_item ) {
		foreach ( self::$fields as $_key => $label ) :
			$key   = sprintf( 'menu-item-%s', $_key );
			$id    = sprintf( 'edit-%s-%s', $key, $menu_item->ID );
			$name  = sprintf( '%s[%s]', $key, $menu_item->ID );
			$value = get_post_meta( $menu_item->ID, $key, true );
			$class = sprintf( 'field-%s', $_key );
			?>
				<p class="description description-wide <?php echo esc_attr( $class ); ?>">
					<?php
					printf(
						'<label for="%1$s"><input type="checkbox" id="%1$s" class="widefat %1$s" name="%3$s" value="on" %4$s />%2$s</label>',
						esc_attr( $id ),
						esc_html( $label ),
						esc_attr( $name ),
						esc_attr( checked( $value, 'on', false ) )
					)
					?>
				</p>
			<?php
		endforeach;
	}

	/**
	 * Save the menu item meta
	 *
	 * @param int $menu_id The ID of the menu. If 0, makes the menu item a draft orphan.
	 * @param int $menu_item_db_id The ID of the menu item. If 0, creates a new menu item.
	 * @return void
	 */
	public function nav_update( $menu_id, $menu_item_db_id ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		foreach ( self::$fields as $_key => $label ) {
			$key = sprintf( 'menu-item-%s', $_key );

			if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ][ $menu_item_db_id ] );
			} else {
				$value = null;
			}

			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	/**
	 * Add our fields to the screen options toggle
	 *
	 * @param array $columns Menu item columns.
	 * @return array
	 */
	public static function nav_columns( $columns ) {
		$columns = array_merge( $columns, self::$fields );
		return $columns;
	}
}
