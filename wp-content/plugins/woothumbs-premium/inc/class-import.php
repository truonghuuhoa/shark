<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * FCMS_WooThumbs_Import.
 *
 * @class    FCMS_WooThumbs_Import
 * @version  1.0.0
 * @package  FCMS_WooThumbs
 */
class FCMS_WooThumbs_Import {
	/**
	 * Run.
	 */
	public static function run() {
		add_action( 'woocommerce_product_import_inserted_product_object', array( __CLASS__, 'clear_image_cache_after_import' ), 10, 2 );
	}

	/**
	 * Clear image cache after import.
	 *
	 * @param WC_Product $product
	 * @param array      $data
	 */
	public static function clear_image_cache_after_import( $product, $data ) {
		global $fcms_woothumbs_class;

		$fcms_woothumbs_class->delete_transients( true, $product->get_id() );
	}
}
