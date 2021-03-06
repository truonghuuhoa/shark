<?php

/**
 * Template hooks.
 *
 * @since 4.6.12
 */
class FCMS_WooThumbs_Template_Hooks {
	/**
	 * Init.
	 */
	public static function run() {
		add_action( 'fcms_woothumbs_image', array( __CLASS__, 'slide_content' ), 10, 3 );
	}

	/**
	 * Slide content.
	 *
	 * @param $image
	 * @param $i
	 * @param $images
	 */
	public static function slide_content( $image, $i, $images ) {
		if ( ! empty( $image['media_embed'] ) ) {
			echo $image['media_embed'];
		} else { ?>
			<img <?php echo FCMS_WooThumbs::array_to_html_atts( FCMS_WooThumbs::get_image_loop_data( $image, $i ) ); ?>>
		<?php }
	}
}