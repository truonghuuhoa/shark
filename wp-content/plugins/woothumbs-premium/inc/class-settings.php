<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * FCMS_WooThumbs_Settings.
 *
 * @class    FCMS_WooThumbs_Settings
 * @version  1.0.0
 * @package  FCMS_WooThumbs
 */
class FCMS_WooThumbs_Settings {
	/**
	 * Run.
	 */
	public static function run() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'update_option_fcms_woothumbs_settings', array( __CLASS__, 'on_save' ), 10, 3 );
		add_filter( 'fcms_woothumbs_settings_validate', array( __CLASS__, 'validate_settings' ), 10, 1 );
	}

	/**
	 * Init.
	 */
	public static function init() {
		global $fcms_woothumbs_class;

		if ( empty( $fcms_woothumbs_class ) ) {
			return;
		}

		$fcms_woothumbs_class->set_settings();
	}

	/**
	 * On save settings.
	 *
	 * @param mixed  $old_value
	 * @param mixed  $value
	 * @param string $option
	 */
	public static function on_save( $old_value, $value, $option ) {
		if ( class_exists( 'WC_Regenerate_Images' ) && apply_filters( 'woocommerce_background_image_regeneration', true ) ) {
			WC_Regenerate_Images::maybe_regenerate_images();
		}
	}

	/**
	 * Admin: Validate Settings
	 *
	 * @param array $settings Un-validated settings
	 *
	 * @return array $validated_settings
	 */
	public static function validate_settings( $settings ) {
		self::maybe_clear_image_cache( $settings );

		if ( isset( $_POST['fcms-woothumbs-delete-image-cache'] ) ) {
			add_settings_error( 'fcms-woothumbs-delete-image-cache', 'fcms-woothumbs', __( 'The image cache has been cleared.', 'fcms-woothumbs' ), 'updated' );
		}

		if ( 0 >= (int) $settings['carousel_general_main_slider_swipe_threshold'] ) {
			$settings['carousel_general_main_slider_swipe_threshold'] = 5;
			add_settings_error( 'fcms-woothumbs-swipe-threshold-invalid-value', 'fcms-woothumbs', __( 'Touch threshold cannot be less then less than 1, the value has been reset to 5.', 'fcms-woothumbs' ), 'error' );
		}

		return $settings;
	}

	/**
	 * Maybe clear image cache.
	 *
	 * @param $settings
	 */
	public static function maybe_clear_image_cache( $settings ) {
		global $fcms_woothumbs_class;

		$controls = $fcms_woothumbs_class->settings['media_mp4_controls'];
		$loop     = $fcms_woothumbs_class->settings['media_mp4_loop'];
		$autoplay = $fcms_woothumbs_class->settings['media_mp4_autoplay'];

		if (
			( isset( $settings['media_mp4_controls'] ) && $controls !== $settings['media_mp4_controls'] ) ||
			( isset( $settings['media_mp4_loop'] ) && $loop !== $settings['media_mp4_loop'] ) ||
			( isset( $settings['media_mp4_autoplay'] ) && $autoplay !== $settings['media_mp4_autoplay'] )
		) {
			$fcms_woothumbs_class->delete_transients( true );
		}
	}

	/**
	 * Get a list of image sizes for the site
	 *
	 * @return array
	 */
	public static function get_image_sizes() {
		$image_sizes = array_merge( get_intermediate_image_sizes(), array( 'full' ) );

		return array_combine( $image_sizes, $image_sizes );
	}

	/**
	 * Clear image cache link.
	 *
	 * @return string
	 */
	public static function clear_image_cache_link() {
		ob_start();

		?>
		<button name="fcms-woothumbs-delete-image-cache" class="button button-secondary"><?php _e( 'Clear Image Cache', 'fcms-woothumbs' ); ?></button>
		<?php

		return ob_get_clean();
	}

	/**
	 * Add ratio fields.
	 *
	 * @param $args
	 *
	 * @return string
	 */
	public static function ratio_fields( $args ) {
		$defaults = array(
			'width'  => '',
			'height' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$width_name  = sprintf( '%s_width', $args['name'] );
		$height_name = sprintf( '%s_height', $args['name'] );
		$input       = '<input id="%s" name="fcms_woothumbs_settings[%s]" type="number" style="width: 50px;" value="%s">';
		$width       = sprintf( $input, $width_name, $width_name, $args['width'] );
		$height      = sprintf( $input, $height_name, $height_name, $args['height'] );

		return sprintf( '%s : %s', $width, $height );
	}

	/**
	 * Get default gallery width based on theme.
	 *
	 * @return int
	 */
	public static function get_default_width() {
		$default = 42;
		$theme   = wp_get_theme();

		if ( empty( $theme ) ) {
			return $default;
		}

		switch ( $theme->template ) {
			case 'twentytwenty':
			case 'Divi':
				$default = 48;
				break;
			case 'astra':
			case 'savoy':
				$default = 50;
				break;
			case 'atelier':
				$default = 60;
				break;
			case 'flatsome':
			case 'Avada':
			case 'enfold':
			case 'porto':
			case 'shopkeeper':
			case 'woodmart':
				$default = 100;
				break;
		}

		return $default;
	}

	/**
	 * Get thumbnail width based on thumbnail count.
	 *
	 * @return int
	 */
	public static function get_thumbnail_width() {
		$navigation_thumbnails_count = FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_count' );

		return empty( $navigation_thumbnails_count ) ? 0 : ( 100 / (int) $navigation_thumbnails_count );
	}
}
