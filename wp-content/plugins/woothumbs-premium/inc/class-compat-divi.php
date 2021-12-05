<?php
/**
 * Divi theme compatibility Class.
 *
 * @package  FCMS_WooThumbs
 */

/**
 * Divi theme compatibility Class
 * https://www.elegantthemes.com/
 *
 * @since 4.7.2
 */
class FCMS_WooThumbs_Compat_Divi {

	/**
	 * Init.
	 */
	public static function run() {
		add_action( 'wp', array( __CLASS__, 'hooks' ) );
	}

	/**
	 * After theme setup.
	 *
	 * @return void
	 */
	public static function hooks() {
		global $themename, $fcms_woothumbs_class, $post;

		if ( ! isset( $themename ) || 'Divi' !== $themename ) {
			return;
		}

		$is_theme_builder_enabled  = self::is_divi_theme_builder_enabled();
		$is_visual_builder_enabled = 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true );

		// Only disable WooThumbs default action when either
		// Divi Visual Builder or Divi Theme Builder is enabled for the product.
		if ( is_product() && ( $is_theme_builder_enabled || $is_visual_builder_enabled ) ) {
			remove_action( 'woocommerce_before_single_product_summary', array( $fcms_woothumbs_class, 'show_product_images' ), 20 );
		}

		// Replace the output of Divi's image module with WooThumbs.
		add_filter( 'et_module_shortcode_output', array( __CLASS__, 'change_divi_woo_images_module_output' ), 10, 3 );
	}

	/**
	 * Replace the output of Divi's WooCommerce Images module with WooThumbs.
	 *
	 * @param string $output      The HTML output of the module.
	 * @param string $render_slug The slug/shortcode of module.
	 * @param Object $element     The module object.
	 *
	 * @return string $output
	 */
	public static function change_divi_woo_images_module_output( $output, $render_slug, $element ) {
		global $fcms_woothumbs_class;

		if ( 'et_pb_wc_images' !== $render_slug || isset( $_REQUEST['et_fb'] ) ) {
			return $output;
		}

		$shortcode = '[woothumbs-gallery]';

		if ( ! empty( $element->props['product'] ) && 'current' !== $element->props['product'] ) {
			$shortcode = sprintf( '[woothumbs-gallery id="%d"]', $element->props['product'] );
		}

		ob_start();
		woocommerce_show_product_sale_flash();
		$sale_flash = ob_get_clean();

		return do_shortcode( $shortcode . $sale_flash );
	}

	/**
	 * Is Divi builder enabled for current product?
	 * https://www.elegantthemes.com/documentation/divi/the-divi-theme-builder/
	 *
	 * @return bool
	 */
	public static function is_divi_theme_builder_enabled() {
		$layouts = et_theme_builder_get_template_layouts();

		if ( empty( $layouts ) ) {
			return false;
		}

		return $layouts[ ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ]['override'];
	}

}
