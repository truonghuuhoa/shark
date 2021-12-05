<?php
add_filter( 'wpsf_register_settings_fcms_was', 'fcms_was_settings' );

/**
 * WooCommerce Attribute Swatches Settings
 *
 * @param array $wpsf_settings
 *
 * @return array
 */
function fcms_was_settings( $wpsf_settings ) {
	$wpsf_settings['tabs'][] = array(
		'id'    => 'style',
		'title' => __( 'Style', 'fcms-was' ),
	);

	$wpsf_settings['sections'][] = array(
		'tab_id'        => 'style',
		'section_id'    => 'general',
		'section_title' => __( 'General', 'fcms-was' ),
		'section_order' => 0,
		'fields'        => array(
			array(
				'id'       => 'selected',
				'title'    => __( 'Selected Style', 'fcms-was' ),
				'subtitle' => __( 'Choose the style for selected image or colour swatches.', 'fcms-was' ),
				'type'     => 'select',
				'default'  => 'border',
				'choices'  => array(
					'tick'   => __( 'Tick', 'fcms-was' ),
					'border' => __( 'Border', 'fcms-was' ),
				),
			),
			array(
				'id'       => 'accordion',
				'title'    => __( 'Enable Accordion', 'fcms-was' ),
				'subtitle' => __( 'Show swatches in accordion?', 'fcms-was' ),
				'type'     => 'select',
				'default'  => 'no',
				'choices'  => array(
					'no'  => __( 'No', 'fcms-was' ),
					'yes' => __( 'Yes', 'fcms-was' ),
				),
			),
		),
	);

	return $wpsf_settings;
}