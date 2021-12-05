<style>
/* Default Styles */
.fcms-woothumbs-all-images-wrap {
	float: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_position' ); ?>;
	width: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_width' ); ?>%;
}

/* Icon Styles */
.fcms-woothumbs-icon {
	color: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_icon_colours' ); ?>;
}

/* Bullet Styles */
.fcms-woothumbs-all-images-wrap .slick-dots button,
.fcms-woothumbs-zoom-bullets .slick-dots button {
	border-color: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_icon_colours' ); ?> !important;
}

.fcms-woothumbs-all-images-wrap .slick-dots .slick-active button,
.fcms-woothumbs-zoom-bullets .slick-dots .slick-active button {
	background-color: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_icon_colours' ); ?> !important;
}

/* Thumbnails */
<?php if( FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_enable' ) ) { ?>

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap {
	width: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_width' ); ?>%;
}

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-images-wrap,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-images-wrap {
	width: <?php echo 100-FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_width' ); ?>%;
}

<?php } else { ?>

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-images-wrap,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-images-wrap {
	width: 100%;
}

<?php } ?>

.fcms-woothumbs-thumbnails__image-wrapper:after {
	border-color: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_icon_colours' ); ?>;
}

.fcms-woothumbs-thumbnails__control {
	color: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'display_general_icon_colours' ); ?>;
}

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails__control {
	right: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails__control {
	left: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

<?php
$thumbnail_width = FCMS_WooThumbs_Settings::get_thumbnail_width();
?>

/* Stacked Thumbnails - Left & Right */

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap--stacked,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap--stacked {
	margin: 0;
}

.fcms-woothumbs-thumbnails-wrap--stacked .fcms-woothumbs-thumbnails__slide {
	width: <?php echo esc_html( $thumbnail_width ); ?>%;
}

/* Stacked Thumbnails - Left */

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap--stacked .fcms-woothumbs-thumbnails__slide {
	padding: 0 <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px 0;
}

/* Stacked Thumbnails - Right */

.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap--stacked .fcms-woothumbs-thumbnails__slide {
	padding: 0 0 <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Stacked Thumbnails - Above & Below */

<?php
$thumbnail_gutter_left = floor( FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' )/2 );
$thumbnail_gutter_right = ceil( FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' )/2 );
?>

.fcms-woothumbs-all-images-wrap--thumbnails-above .fcms-woothumbs-thumbnails-wrap--stacked,
.fcms-woothumbs-all-images-wrap--thumbnails-below .fcms-woothumbs-thumbnails-wrap--stacked {
	margin: 0 -<?php echo $thumbnail_gutter_left; ?>px 0 -<?php echo $thumbnail_gutter_right; ?>px;
}

/* Stacked Thumbnails - Above */

.fcms-woothumbs-all-images-wrap--thumbnails-above .fcms-woothumbs-thumbnails-wrap--stacked .fcms-woothumbs-thumbnails__slide {
	padding: 0 <?php echo $thumbnail_gutter_left; ?>px <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px <?php echo $thumbnail_gutter_right; ?>px;
}

/* Stacked Thumbnails - Below */

.fcms-woothumbs-all-images-wrap--thumbnails-below .fcms-woothumbs-thumbnails-wrap--stacked .fcms-woothumbs-thumbnails__slide {
	padding: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px <?php echo $thumbnail_gutter_left; ?>px 0 <?php echo $thumbnail_gutter_right; ?>px;
}

/* Sliding Thumbnails - Left & Right, Above & Below */

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap--sliding,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap--sliding {
	margin: 0;
}

/* Sliding Thumbnails - Left & Right */

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap--sliding .slick-list,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap--sliding .slick-list {
	margin-bottom: -<?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap--sliding .fcms-woothumbs-thumbnails__image-wrapper,
.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap--sliding .fcms-woothumbs-thumbnails__image-wrapper {
	margin-bottom: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Sliding Thumbnails - Left */

.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap--sliding {
	padding-right: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Sliding Thumbnails - Right */

.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap--sliding {
	padding-left: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Sliding Thumbnails - Above & Below */

.fcms-woothumbs-thumbnails-wrap--horizontal.fcms-woothumbs-thumbnails-wrap--sliding .fcms-woothumbs-thumbnails__slide {
	width: <?php echo esc_html( $thumbnail_width ); ?>%;
}

.fcms-woothumbs-all-images-wrap--thumbnails-above .fcms-woothumbs-thumbnails-wrap--sliding .slick-list,
.fcms-woothumbs-all-images-wrap--thumbnails-below .fcms-woothumbs-thumbnails-wrap--sliding .slick-list {
	margin-right: -<?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

.fcms-woothumbs-all-images-wrap--thumbnails-above .fcms-woothumbs-thumbnails-wrap--sliding .fcms-woothumbs-thumbnails__image-wrapper,
.fcms-woothumbs-all-images-wrap--thumbnails-below .fcms-woothumbs-thumbnails-wrap--sliding .fcms-woothumbs-thumbnails__image-wrapper {
	margin-right: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Sliding Thumbnails - Above */

.fcms-woothumbs-all-images-wrap--thumbnails-above .fcms-woothumbs-thumbnails-wrap--sliding {
	margin-bottom: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Sliding Thumbnails - Below */

.fcms-woothumbs-all-images-wrap--thumbnails-below .fcms-woothumbs-thumbnails-wrap--sliding {
	margin-top: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'navigation_thumbnails_spacing' ); ?>px;
}

/* Zoom Styles */

<?php if(FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_general_zoom_type' ) == 'follow'):
$borderRadius = (FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_follow_zoom_lens_width' ) > FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_follow_zoom_lens_height' )) ? FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_follow_zoom_lens_width' ) : FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_follow_zoom_lens_height' ); ?>
.zm-viewer.shapecircular {
	-webkit-border-radius: <?php echo $borderRadius; ?>px;
	-moz-border-radius: <?php echo $borderRadius; ?>px;
	border-radius: <?php echo $borderRadius; ?>px;
}

<?php endif; ?>

.zm-handlerarea {
	background: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_zoom_lens_colour' ); ?>;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_zoom_lens_opacity' )*100; ?>)" !important;
	filter: alpha(opacity=<?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_zoom_lens_opacity' )*100; ?>) !important;
	-moz-opacity: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_zoom_lens_opacity' ); ?> !important;
	-khtml-opacity: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_zoom_lens_opacity' ); ?> !important;
	opacity: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'zoom_outside_zoom_lens_opacity' ); ?> !important;
}

/* Media Queries */

<?php if( FCMS_WooThumbs_Core_Settings::get_setting( 'responsive_general_breakpoint_enable' ) ): ?>

<?php $thumbnail_width = 100/(int)FCMS_WooThumbs_Core_Settings::get_setting( 'responsive_general_thumbnails_count' ); ?>

@media screen and (max-width: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'responsive_general_breakpoint' ); ?>px) {

	.fcms-woothumbs-all-images-wrap {
		float: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'responsive_general_position' ); ?>;
		width: <?php echo FCMS_WooThumbs_Core_Settings::get_setting( 'responsive_general_width' ); ?>%;
	}

	.fcms-woothumbs-hover-icons .fcms-woothumbs-icon {
		opacity: 1;
	}

<?php if( FCMS_WooThumbs_Core_Settings::get_setting( 'responsive_general_thumbnails_below' ) ): ?>

	.fcms-woothumbs-all-images-wrap--thumbnails-above .fcms-woothumbs-images-wrap,
	.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-images-wrap,
	.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-images-wrap {
		width: 100%;
	}

	.fcms-woothumbs-all-images-wrap--thumbnails-left .fcms-woothumbs-thumbnails-wrap,
	.fcms-woothumbs-all-images-wrap--thumbnails-right .fcms-woothumbs-thumbnails-wrap {
		width: 100%;
	}

<?php endif; ?>

	.fcms-woothumbs-thumbnails-wrap--horizontal .fcms-woothumbs-thumbnails__slide {
		width: <?php echo $thumbnail_width; ?>%;
	}

}

<?php endif; ?>

</style>