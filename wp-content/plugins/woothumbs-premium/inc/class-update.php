<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FCMS_WooThumbs_Update.
 *
 * @class    FCMS_WooThumbs_Update
 * @version  1.0.0
 * @package  FCMS_WooThumbs
 * @category Class
 * @author   FCMS
 */
class FCMS_WooThumbs_Update {
	/**
	 * Run update.
	 */
	public static function run() {
		add_action( 'admin_init', array( __CLASS__, 'update' ) );
	}

	/**
	 * Update WooThumbs.
	 */
	public static function update() {
		global $fcms_woothumbs_class;

		$option_name     = 'fcms_woothumbs_version';
		$current_version = get_option( $option_name );

		if ( version_compare( $current_version, $fcms_woothumbs_class->version, '<' ) ) {
			$fcms_woothumbs_class->delete_transients( true );
			update_option( $option_name, $fcms_woothumbs_class->version );
		}
	}
}