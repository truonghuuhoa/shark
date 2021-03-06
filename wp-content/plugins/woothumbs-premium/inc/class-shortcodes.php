<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FCMS_WooThumbs_Shortcodes.
 *
 * @class    FCMS_WooThumbs_Shortcodes
 * @version  1.0.0
 * @package  FCMS_WooThumbs
 * @category Class
 * @author   FCMS
 */
class FCMS_WooThumbs_Shortcodes {
	/*
	 * Init shortcodes
	 */
	public static function run() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		add_shortcode( 'woothumbs-gallery', array( __CLASS__, 'gallery' ) );
	}

	public static function gallery( $atts ) {
		global $post, $fcms_woothumbs_class;

		$atts = shortcode_atts( array(
			'id' => false,
		), $atts, 'woothumbs-gallery' );

		$atts['id'] = $atts['id'] ? $atts['id'] : $post->ID;

		if ( ! $atts['id'] ) {
			return;
		}

		ob_start();

		$post_object = get_post( $atts['id'] );

		if ( ! $post_object ) {
			return;
		}

		$GLOBALS['post'] =& $post_object;

		setup_postdata( $GLOBALS['post'] );

		$fcms_woothumbs_class->show_product_images();

		wp_reset_postdata();

		return ob_get_clean();
	}
}