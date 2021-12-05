<?php
add_filter( 'wpsf_register_settings_fcms_woothumbs', 'fcms_woothumbs_settings' );

/**
 * WooThumbs Settings
 *
 * @param array $wpsf_settings
 *
 * @return array
 */
function fcms_woothumbs_settings( $wpsf_settings ) {
	$wpsf_settings['tabs']     = isset( $wpsf_settings['tabs'] ) ? $wpsf_settings['tabs'] : array();
	$wpsf_settings['sections'] = isset( $wpsf_settings['sections'] ) ? $wpsf_settings['sections'] : array();

	// Tabs.

	$wpsf_settings['tabs'][] = array(
		'id'    => 'display',
		'title' => __( 'Display', 'fcms-woothumbs' ),
	);

	$wpsf_settings['tabs'][] = array(
		'id'    => 'carousel',
		'title' => __( 'Carousel', 'fcms-woothumbs' ),
	);

	$wpsf_settings['tabs'][] = array(
		'id'    => 'media',
		'title' => __( 'Media', 'fcms-woothumbs' ),
	);

	$wpsf_settings['tabs'][] = array(
		'id'    => 'navigation',
		'title' => __( 'Navigation', 'fcms-woothumbs' ),
	);

	$wpsf_settings['tabs'][] = array(
		'id'    => 'zoom',
		'title' => __( 'Zoom', 'fcms-woothumbs' ),
	);

	$wpsf_settings['tabs'][] = array(
		'id'    => 'fullscreen',
		'title' => __( 'Fullscreen', 'fcms-woothumbs' ),
	);

	$wpsf_settings['tabs'][] = array(
		'id'    => 'responsive',
		'title' => __( 'Responsive', 'fcms-woothumbs' ),
	);

	// Sections.

	$default_width = FCMS_WooThumbs_Settings::get_default_width();

	$wpsf_settings['sections']['display'] = array(
		'tab_id'              => 'display',
		'section_id'          => 'general',
		'section_title'       => __( 'Display Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			array(
				'id'       => 'width',
				'title'    => __( 'Gallery Width (%)', 'fcms-woothumbs' ),
				'subtitle' => sprintf( __( 'Enter a percentage for the width of the image gallery. The default for your theme is %d%%', 'fcms-woothumbs' ), $default_width ),
				'type'     => 'number',
				'default'  => $default_width,
			),
			array(
				'id'       => 'position',
				'title'    => __( 'Position', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose a position for the images. Go to the Responsive tab to change the position at a certain breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'left',
				'choices'  => array(
					'left'  => __( 'Left', 'fcms-woothumbs' ),
					'right' => __( 'Right', 'fcms-woothumbs' ),
					'none'  => __( 'None', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'icon_colours',
				'title'    => __( 'Icon Colours', 'fcms-woothumbs' ),
				'subtitle' => '',
				'type'     => 'color',
				'default'  => '#7c7c7c',
			),
			array(
				'id'       => 'icons_hover',
				'title'    => __( 'Show Icons on Hover?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, icons will only be visible when the image is hovered.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'icons_tooltips',
				'title'    => __( 'Show Icon Tooltips?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When icons are hovered, a tooltip will be displayed.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections']['images'] = array(
		'tab_id'              => 'display',
		'section_id'          => 'images',
		'section_title'       => __( 'Image Sizes', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			'single_image_width'            => array(
				'id'       => 'single_image_width',
				'title'    => __( 'Single Image Width (px)', 'fcms-woothumbs' ),
				'subtitle' => __( 'For a responsive site, set this to the largest size your single image will be. Images will be regenerated automatically, but may take time to appear on the frontend.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => FCMS_WooThumbs_Images::get_image_width( 'single' ),
			),
			'single_image_crop'             => array(
				'id'       => 'single_image_crop',
				'title'    => __( 'Single Image Crop Ratio', 'fcms-woothumbs' ),
				'subtitle' => __( 'Common examples: 1:1, 4:6, 16:9. If empty, no cropping will occur. Once changed, product gallery images will be regenerated automatically, but may take time to appear on the frontend.', 'fcms-woothumbs' ),
				'type'     => 'custom',
				'default'  => FCMS_WooThumbs_Settings::ratio_fields( array(
					'name'   => 'display_images_single_image_crop',
					'width'  => FCMS_WooThumbs_Images::get_image_crop( 'single', 'width' ),
					'height' => FCMS_WooThumbs_Images::get_image_crop( 'single', 'height' ),
				) ),
			),
			'gallery_thumbnail_image_width' => array(
				'id'       => 'gallery_thumbnail_image_width',
				'title'    => __( 'Thumbnail Image Width (px)', 'fcms-woothumbs' ),
				'subtitle' => __( 'Images will be regenerated automatically, but may take time to appear on the frontend.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => FCMS_WooThumbs_Images::get_image_width( 'gallery_thumbnail' ),
			),
			'gallery_thumbnail_image_crop'  => array(
				'id'       => 'gallery_thumbnail_image_crop',
				'title'    => __( 'Thumbnail Image Crop Ratio', 'fcms-woothumbs' ),
				'subtitle' => __( 'Common examples: 1:1, 4:6, 16:9. If empty, no cropping will occur. Once changed, thumbnail images will be regenerated automatically, but may take time to appear on the frontend.', 'fcms-woothumbs' ),
				'type'     => 'custom',
				'default'  => FCMS_WooThumbs_Settings::ratio_fields( array(
					'name'   => 'display_images_gallery_thumbnail_image_crop',
					'width'  => FCMS_WooThumbs_Images::get_image_crop( 'gallery_thumbnail', 'width' ),
					'height' => FCMS_WooThumbs_Images::get_image_crop( 'gallery_thumbnail', 'height' ),
				) ),
			),
			array(
				'id'       => 'large_image_size',
				'title'    => __( 'Large Image Size', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose a size for large images. Hover zoom and fullscreen will both use the size you select here.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'full',
				'choices'  => FCMS_WooThumbs_Settings::get_image_sizes(),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'media',
		'section_id'          => 'mp4',
		'section_title'       => __( 'MP4 Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			array(
				'id'       => 'controls',
				'title'    => __( 'Show Controls?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, the video controls will be visible.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'loop',
				'title'    => __( 'Loop?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, the video will loop continuously.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'autoplay',
				'title'    => __( 'Autoplay?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, the video will autoplay once the page has loaded. Note: autoplay may not work on all devices. Also, the videos will be muted.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'lazyload',
				'title'    => __( 'Lazyload MP4 videos?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, videos will only be loaded when play button is clicked. Helps reduce pageload time.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'carousel',
		'section_id'          => 'general',
		'section_title'       => __( 'Carousel Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			array(
				'id'       => 'mode',
				'title'    => __( 'Mode', 'fcms-woothumbs' ),
				'subtitle' => __( 'How should the main images transition?', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'horizontal',
				'choices'  => array(
					'horizontal' => __( 'Horizontal', 'fcms-woothumbs' ),
					'vertical'   => __( 'Vertical', 'fcms-woothumbs' ),
					'fade'       => __( 'Fade', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'transition_speed',
				'title'    => __( 'Transition Speed (ms)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The speed at which images slide or fade in milliseconds.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 250,
			),
			array(
				'id'       => 'autoplay',
				'title'    => __( 'Autoplay?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, the slider images will automatically transition.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'duration',
				'title'    => __( 'Slide Duration (ms)', 'fcms-woothumbs' ),
				'subtitle' => __( 'If you have autoplay set to true, then you can set the slide duration for each slide.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 5000,
			),
			array(
				'id'       => 'infinite_loop',
				'title'    => __( 'Enable Infinite Loop?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When you get to the last image, loop back to the first. Horizontal or Vertical modes only.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'main_slider_swipe_threshold',
				'title'    => __( 'Main Slider Touch Threshold', 'fcms-woothumbs' ),
				'subtitle' => __( 'A bigger number means less swipe length is needed to advance the slider.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => '5',
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'navigation',
		'section_id'          => 'general',
		'section_title'       => __( 'General Navigation Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 10,
		'fields'              => array(
			array(
				'id'       => 'controls',
				'title'    => __( 'Enable Prev/Next Arrows?', 'fcms-woothumbs' ),
				'subtitle' => __( 'This will display prev/next arrows over the main slider image.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'navigation',
		'section_id'          => 'thumbnails',
		'section_title'       => __( 'Thumbnails Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 20,
		'fields'              => array(
			array(
				'id'       => 'enable',
				'title'    => __( 'Enable Thumbnails?', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose whether to enable the thumbnail navigation.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'type',
				'title'    => __( 'Thumbnails Type', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose either sliding or stacked thumbnails.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'sliding',
				'choices'  => array(
					'sliding' => __( 'Sliding thumbnails', 'fcms-woothumbs' ),
					'stacked' => __( 'Stacked Thumbnails', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'controls',
				'title'    => __( 'Enable Thumbnail Controls?', 'fcms-woothumbs' ),
				'subtitle' => __( 'If you are using sliding thumbnails, enable or disable the prev/next controls.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'position',
				'title'    => __( 'Thumbnails Position', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose where the thumbnails are positioned in relation to the main images.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'below',
				'choices'  => array(
					'above' => __( 'Above', 'fcms-woothumbs' ),
					'below' => __( 'Below', 'fcms-woothumbs' ),
					'left'  => __( 'Left', 'fcms-woothumbs' ),
					'right' => __( 'Right', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'width',
				'title'    => __( 'Width (%)', 'fcms-woothumbs' ),
				'subtitle' => __( 'If you chose to position your thumbanils on the left or right, enter a percentage width for them.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 20,
			),
			array(
				'id'       => 'count',
				'title'    => __( 'Thumbnails Count', 'fcms-woothumbs' ),
				'subtitle' => __( 'The number of thumbnails to display in a row.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 4,
			),
			array(
				'id'       => 'transition_speed',
				'title'    => __( 'Thumbnails Transition Speed (ms)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The speed at which the sliding thumbnail navigation moves in milliseconds.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 250,
			),
			array(
				'id'       => 'spacing',
				'title'    => __( 'Thumbnails Spacing (px)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The space between each thumbnail.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 10,
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'navigation',
		'section_id'          => 'bullets',
		'section_title'       => __( 'Bullets Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 30,
		'fields'              => array(
			array(
				'id'       => 'enable',
				'title'    => __( 'Enable Bullets?', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose whether to enable the bullet navigation.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'zoom',
		'section_id'          => 'general',
		'section_title'       => __( 'General Zoom Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 10,
		'fields'              => array(
			array(
				'id'       => 'enable',
				'title'    => __( 'Enable Hover Zoom?', 'fcms-woothumbs' ),
				'subtitle' => '',
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'zoom_type',
				'title'    => __( 'Zoom Type', 'fcms-woothumbs' ),
				'subtitle' => '',
				'type'     => 'select',
				'default'  => 'inner',
				'choices'  => array(
					'inner'    => __( 'Inner', 'fcms-woothumbs' ),
					'standard' => __( 'Outside', 'fcms-woothumbs' ),
					'follow'   => __( 'Follow', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'zoom',
		'section_id'          => 'outside_follow_zoom',
		'section_title'       => __( 'Outside and Follow Zoom Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 20,
		'fields'              => array(
			array(
				'id'       => 'lens_width',
				'title'    => __( 'Lens Width (px)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The width of your zoom lens.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 200,
			),
			array(
				'id'       => 'lens_height',
				'title'    => __( 'Lens Height (px)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The height of your zoom lens.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 200,
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'zoom',
		'section_id'          => 'outside_zoom',
		'section_title'       => __( 'Outside Zoom Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 30,
		'fields'              => array(
			array(
				'id'       => 'zoom_position',
				'title'    => __( 'Zoom Position', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose the position of your zoomed image in relation to the main image.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'right',
				'choices'  => array(
					'right' => __( 'Right', 'fcms-woothumbs' ),
					'left'  => __( 'Left', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'lens_colour',
				'title'    => __( 'Lens Colour', 'fcms-woothumbs' ),
				'subtitle' => '',
				'type'     => 'color',
				'default'  => '#000000',
			),
			array(
				'id'       => 'lens_opacity',
				'title'    => __( 'Lens opacity', 'fcms-woothumbs' ),
				'subtitle' => __( 'Set an opacity between 0 and 1 for the lens.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 0.8,
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'zoom',
		'section_id'          => 'follow_zoom',
		'section_title'       => __( 'Follow Zoom Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 40,
		'fields'              => array(
			array(
				'id'       => 'zoom_shape',
				'title'    => __( 'Zoom Shape', 'fcms-woothumbs' ),
				'subtitle' => '',
				'type'     => 'select',
				'default'  => 'circular',
				'choices'  => array(
					'circular' => __( 'Circular', 'fcms-woothumbs' ),
					'square'   => __( 'Square', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'fullscreen',
		'section_id'          => 'general',
		'section_title'       => __( 'Fullscreen Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			array(
				'id'       => 'enable',
				'title'    => __( 'Enable Fullscreen?', 'fcms-woothumbs' ),
				'subtitle' => '',
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'click_anywhere',
				'title'    => __( 'Enable Click Anywhere?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, click anywhere on the main image to trigger fullscreen.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '0',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'image_title',
				'title'    => __( 'Enable Image Title?', 'fcms-woothumbs' ),
				'subtitle' => __( 'When enabled, the image title will be visible when viewing fullscreen.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
		),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'              => 'responsive',
		'section_id'          => 'general',
		'section_title'       => __( 'Responsive Settings', 'fcms-woothumbs' ),
		'section_description' => '',
		'section_order'       => 0,
		'fields'              => array(
			array(
				'id'       => 'breakpoint_enable',
				'title'    => __( 'Enable Breakpoint?', 'fcms-woothumbs' ),
				'subtitle' => __( 'If your website is responsive, you can change the width of the slider after a certain breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'breakpoint',
				'title'    => __( 'Breakpoint (px)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The slider width will be affected after the breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 768,
			),
			array(
				'id'       => 'width',
				'title'    => __( 'Width After Breakpoint (%)', 'fcms-woothumbs' ),
				'subtitle' => __( 'The width of the images display after the breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 100,
			),
			array(
				'id'       => 'position',
				'title'    => __( 'Position After Breakpoint', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose a position for the images after the breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => 'none',
				'choices'  => array(
					'left'  => __( 'Left', 'fcms-woothumbs' ),
					'right' => __( 'Right', 'fcms-woothumbs' ),
					'none'  => __( 'None', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'thumbnails_below',
				'title'    => __( 'Move Thumbnails Below After Breakpoint?', 'fcms-woothumbs' ),
				'subtitle' => __( 'Choose whether to move the thumbnail navigation below the main image display after the breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'select',
				'default'  => '1',
				'choices'  => array(
					'1' => __( 'Yes', 'fcms-woothumbs' ),
					'0' => __( 'No', 'fcms-woothumbs' ),
				),
			),
			array(
				'id'       => 'thumbnails_count',
				'title'    => __( 'Thumbnails Count After Breakpoint', 'fcms-woothumbs' ),
				'subtitle' => __( 'The number of thumbnails to display in a row after the breakpoint.', 'fcms-woothumbs' ),
				'type'     => 'number',
				'default'  => 3,
			),
		),
	);

	// Conditionals.

	if ( FCMS_WooThumbs_Core_Helpers::woo_version_compare( '3.3', '<' ) ) {
		unset(
			$wpsf_settings['sections']['images']['fields']['single_image_width'],
			$wpsf_settings['sections']['images']['fields']['single_image_crop'],
			$wpsf_settings['sections']['images']['fields']['gallery_thumbnail_image_width'],
			$wpsf_settings['sections']['images']['fields']['gallery_thumbnail_image_crop']
		);
	}

	if ( FCMS_WooThumbs_Core_Settings::is_settings_page() ) {
		$wpsf_settings['sections']['tools'] = array(
			'tab_id'              => 'dashboard',
			'section_id'          => 'tools',
			'section_title'       => __( 'Tools', 'fcms-woothumbs' ),
			'section_description' => '',
			'section_order'       => 20,
			'fields'              => array(
				array(
					'id'       => 'clear-cache',
					'title'    => __( 'Clear Image Cache', 'fcms-woothumbs' ),
					'subtitle' => __( 'Clear the image cache to refresh all product imagery.', 'fcms-woothumbs' ),
					'type'     => 'custom',
					'default'  => FCMS_WooThumbs_Settings::clear_image_cache_link(),
				),
			),

		);
	}

	return $wpsf_settings;
}
