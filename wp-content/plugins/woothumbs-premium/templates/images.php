<?php
/**
 * Images template
 *
 * This template displays both the main images, and the thumbnails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

$default_variation_id = $this->get_default_variation_id();
$initial_product_id   = ( $default_variation_id ) ? $default_variation_id : $product->get_id();
$initial_product_id   = $this->get_selected_variation( $initial_product_id );
$images               = $this->get_all_image_sizes( $initial_product_id );
$default_images       = $this->get_all_image_sizes( $product->get_id() );
$maintain_slide_index = FCMS_WooThumbs_Product::get_setting( $post->ID, 'maintain_slide_index' );
$maintain_slide_index = ! empty( $maintain_slide_index ) ? 'yes' : "no";
$video_url            = FCMS_WooThumbs_Product::get_setting( $post->ID, 'video_url' );
$has_video            = ! empty( $video_url ) ? "yes" : "no";

/**
 * Setup classes for all images wrap
 */

$classes = array(
	'fcms-woothumbs-all-images-wrap',
	sprintf( 'fcms-woothumbs-all-images-wrap--thumbnails-%s', $this->settings['navigation_thumbnails_position'] ),
);

if ( $initial_product_id == $product->get_id() ) {
	$classes[] = 'fcms-woothumbs-reset';
}

if ( $this->settings['display_general_icons_hover'] ) {
	$classes[] = 'fcms-woothumbs-hover-icons';
}

if ( $this->settings['display_general_icons_tooltips'] ) {
	$classes[] = 'fcms-woothumbs-tooltips-enabled';
}

if ( $this->settings['zoom_general_enable'] ) {
	$classes[] = 'fcms-woothumbs-zoom-enabled';
}

if ( is_rtl() ) {
	$classes[] = 'fcms-woothumbs-all-images-wrap--rtl';
}
?>

<?php do_action( 'fcms_woothumbs_before_all_images_wrap' ); ?>

	<div
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
		data-showing="<?php echo esc_attr( $initial_product_id ); ?>" 
		data-parentid="<?php echo esc_attr( $product->get_id() ); ?>" 
		data-default="<?php echo filter_var( wp_json_encode( $default_images ), FILTER_SANITIZE_SPECIAL_CHARS ); ?>"
		data-slide-count="<?php echo count( $images ); ?>" data-maintain-slide-index="<?php echo esc_attr( $maintain_slide_index ); ?>"
		data-has-video="<?php echo esc_attr( $has_video ); ?>" data-product-type="<?php echo esc_attr( $product->get_type() ); ?>" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">

		<?php /* ?><div class="fcms-woothumbs-caption"></div><?php */ ?>

		<?php if ( $this->settings['navigation_thumbnails_enable'] && ( $this->settings['navigation_thumbnails_position'] === "above" || $this->settings['navigation_thumbnails_position'] === "left" ) ) {
			include( 'loop-thumbnails.php' );
		} ?>

		<?php include( 'loop-images.php' ); ?>

		<?php if ( $this->settings['navigation_thumbnails_enable'] && ( $this->settings['navigation_thumbnails_position'] === "below" || $this->settings['navigation_thumbnails_position'] === "right" ) ) {
			include( 'loop-thumbnails.php' );
		} ?>

	</div>

<?php do_action( 'fcms_woothumbs_after_all_images_wrap' ); ?>