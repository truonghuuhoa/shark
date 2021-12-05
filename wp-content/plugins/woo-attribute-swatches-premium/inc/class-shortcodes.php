<?php
/**
 * Add shortcodes.
 *
 * @class   FCMS_WAS_Shortcodes
 * @package FCMS_WAS
 */

defined( 'ABSPATH' ) || exit;

/**
 * FCMS_WAS_Shortcodes class.
 */
class FCMS_WAS_Shortcodes {
	/**
	 * Run.
	 */
	public static function run() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		add_shortcode( 'fcms_was_catalog_swatches', array( __CLASS__, 'catalog_swatches' ) );
	}

	/**
	 * Display catalog swatches.
	 */
	public static function catalog_swatches() {
		global $fcms_was, $product;

		if ( ! $fcms_was || ! $product ) {
			return;
		}

		ob_start();
		$fcms_was->products_class()->add_swatches_to_loop();

		return ob_get_clean();
	}
}
