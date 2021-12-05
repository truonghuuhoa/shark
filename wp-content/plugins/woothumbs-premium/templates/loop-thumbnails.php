<?php
/**
 * Loop thumbnail slider images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$mode = ( $this->settings['navigation_thumbnails_position'] == "above" || $this->settings['navigation_thumbnails_position'] == "below" ) ? "horizontal" : "vertical";

?>

<?php if ( ! empty( $images ) ) { ?>

	<?php do_action( 'fcms_woothumbs_before_thumbnails_wrap' ); ?>

	<div class="fcms-woothumbs-thumbnails-wrap fcms-woothumbs-thumbnails-wrap--<?php echo $this->settings['navigation_thumbnails_type']; ?> fcms-woothumbs-thumbnails-wrap--<?php echo $mode; ?> fcms-woothumbs-thumbnails-wrap--hidden" style="height: 0;">

		<?php do_action( 'fcms_woothumbs_before_thumbnails' ); ?>

		<div class="fcms-woothumbs-thumbnails">

			<?php $image_count = count( $images ); ?>

			<?php if ( $image_count > 1 ) { ?>

				<?php $i = 0;
				foreach ( $images as $image ): ?>

					<div class="fcms-woothumbs-thumbnails__slide <?php if ( $i == 0 ) { ?>fcms-woothumbs-thumbnails__slide--active<?php } ?>" data-index="<?php echo $i; ?>">

						<div class="fcms-woothumbs-thumbnails__image-wrapper">

							<?php do_action( 'fcms_woothumbs_before_thumbnail', $image, $i ); ?>

							<img class="fcms-woothumbs-thumbnails__image no-lazyload skip-lazy" src="<?php echo $image['gallery_thumbnail_src']; ?>" srcset="<?php echo $image['gallery_thumbnail_srcset']; ?>" sizes="<?php echo $image['gallery_thumbnail_sizes']; ?>" title="<?php echo esc_attr( $image['title'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo $image['gallery_thumbnail_src_w']; ?>" height="<?php echo $image['gallery_thumbnail_src_h']; ?>" nopin="nopin">

							<?php do_action( 'fcms_woothumbs_after_thumbnail', $image, $i ); ?>

						</div>

					</div>

					<?php $i ++; endforeach; ?>

				<?php

				// pad out thumbnails if there are less than the number
				// which are meant to be shown.

				if ( $image_count < $this->settings['navigation_thumbnails_count'] ) {
					$empty_count = $this->settings['navigation_thumbnails_count'] - $image_count;
					$i           = 0;

					while ( $i < $empty_count ) {
						echo "<div></div>";
						$i ++;
					}
				}

				?>

			<?php } ?>

		</div>

		<?php if ( $this->settings['navigation_thumbnails_type'] == "sliding" && $this->settings['navigation_general_controls'] ) { ?>

			<a href="javascript: void(0);" class="fcms-woothumbs-thumbnails__control fcms-woothumbs-thumbnails__control--<?php echo ( $mode == "horizontal" ) ? "left" : "up"; ?>" data-direction="<?php echo ( is_rtl() && $mode == "horizontal" ) ? "next" : "prev"; ?>"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-<?php echo ( $mode == "horizontal" ) ? "left" : "up"; ?>-open-mini"></i></a>
			<a href="javascript: void(0);" class="fcms-woothumbs-thumbnails__control fcms-woothumbs-thumbnails__control--<?php echo ( $mode == "horizontal" ) ? "right" : "down"; ?>" data-direction="<?php echo ( is_rtl() && $mode == "horizontal" ) ? "prev" : "next"; ?>"><i class="fcms-woothumbs-icon fcms-woothumbs-icon-<?php echo ( $mode == "horizontal" ) ? "right" : "down"; ?>-open-mini"></i></a>

		<?php } ?>

		<?php do_action( 'fcms_woothumbs_after_thumbnails' ); ?>

	</div>

	<?php do_action( 'fcms_woothumbs_after_thumbnails_wrap' ); ?>

<?php } ?>