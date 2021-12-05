<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Attibutes
 *
 * This class is for anything attribute related
 *
 * @class          FCMS_WAS_Attributes
 * @version        1.0.0
 * @category       Class
 * @author         FCMS
 */
class FCMS_WAS_Attributes {
	/**
	 * Attribute meta name for fields
	 *
	 * @var string $attribute_meta_name
	 */
	public $attribute_meta_name = 'fcms_was_attribute_meta';

	/**
	 * Attribute term meta name for fields
	 *
	 * @var string $attribute_term_meta_name
	 */
	public $attribute_term_meta_name = 'fcms_was_term_meta';

	/**
	 * Run actions/filters for this class
	 */
	public function run() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'add_attribute_term_fields' ), 10 );

			add_action( 'woocommerce_attribute_added', array( $this, 'add_attribute_fields' ), 10, 2 );
			add_action( 'woocommerce_attribute_updated', array( $this, 'update_attribute_fields' ), 10, 3 );
			add_action( 'fcms_was_attribute_updated', array( $this, 'update_term_groups' ), 10, 2 );

			add_action( 'wp_ajax_fcms_was_get_attribute_fields', array( $this, 'ajax_get_attribute_fields' ) );
		}

		add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'enable_modify_attribute_label' ) );
		add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'disable_modify_attribute_label' ) );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array(
			$this,
			'modify_attribute_html',
		), 10, 2 );
		add_filter( 'wc_dropdown_variation_attribute_options_html', array( $this, 'modify_attribute_html' ), 10, 2 );
		add_filter( 'woocommerce_layered_nav_term_html', array( $this, 'modify_layered_nav_term_html' ), 10, 4 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'add_widget_class' ), 10, 1 );
	}

	/**
	 * Admin: Add attribute term fields
	 */
	public function add_attribute_term_fields() {
		$attributes = wc_get_attribute_taxonomies();

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $attribute ) {
			add_action( sprintf( 'pa_%s_add_form_fields', $attribute->attribute_name ), array(
				$this,
				'output_attribute_term_fields',
			), 100, 2 );
			add_action( sprintf( 'pa_%s_edit_form', $attribute->attribute_name ), array(
				$this,
				'output_attribute_term_fields',
			), 100, 2 );

			add_action( sprintf( 'create_pa_%s', $attribute->attribute_name ), array(
				$this,
				'save_attribute_term_fields',
			) );
			add_action( sprintf( 'edited_pa_%s', $attribute->attribute_name ), array(
				$this,
				'save_attribute_term_fields',
			) );

			add_filter( sprintf( 'manage_edit-pa_%s_columns', $attribute->attribute_name ), array(
				$this,
				'add_attribute_columns',
			) );
			add_filter( sprintf( 'manage_pa_%s_custom_column', $attribute->attribute_name ), array(
				$this,
				'add_attribute_column_content',
			), 10, 3 );
		}
	}

	/**
	 * Admin: Add attribute term fields
	 *
	 * @param int $term the concrete term
	 */
	public function output_attribute_term_fields( $term = false ) {
		global $fcms_was;

		if ( empty( $_GET['taxonomy'] ) ) {
			return;
		}

		$swatch_type = $fcms_was->swatches_class()->get_swatch_option( 'swatch_type', $_GET['taxonomy'] );

		if ( empty( $swatch_type ) ) {
			return;
		}

		$field_data_method_name = sprintf( 'get_%s_fields', str_replace( '-', '_', $swatch_type ) );

		if ( ! method_exists( $fcms_was->swatches_class(), $field_data_method_name ) ) {
			return;
		}

		$fields = $fcms_was->swatches_class()->$field_data_method_name( array(
			'term'       => $term,
			'field_name' => sprintf( '%s[%s]', $this->attribute_term_meta_name, $swatch_type ),
		) );

		$is_edit_page = is_object( $term );

		$fields = apply_filters( 'fcms_was_attribute_fields', $fields, $is_edit_page, $term, $swatch_type );

		if ( $fields ) {
			if ( $is_edit_page ) {
				printf( '<h3>%s</h3>', __( 'Swatch Options', 'fcms-was' ) );

				echo "<table class='form-table'>";
				echo "<tbody>";

				foreach ( $fields as $field ) {
					echo "<tr class='form-field'>";
					echo sprintf( '<th scope="row">%s</th>', $field['label'] );
					echo "<td>";
					echo $field['field'];
					echo $field['description'];
					echo "</td>";
					echo "</tr>";
				}

				echo "</tbody>";
				echo "</table>";
			} else {
				foreach ( $fields as $field ) {
					echo "<div class='form-field'>";
					echo $field['label'];
					echo $field['field'];
					echo $field['description'];
					echo "</div>";
				}
			}
		}
	}

	/**
	 * Admin: Save fields for product categories
	 *
	 * @param int $term_id ID of the term we are saving
	 */
	public function save_attribute_term_fields( $term_id ) {
		if ( isset( $_POST[ $this->attribute_term_meta_name ] ) ) {
			$previous_termmeta = get_term_meta( $term_id, $this->attribute_term_meta_name, true );
			$previous_termmeta = $previous_termmeta ? $previous_termmeta : array();

			// get value, sanitise, and save it into the database
			$new_termmeta = isset( $_POST[ $this->attribute_term_meta_name ] ) ? $_POST[ $this->attribute_term_meta_name ] : '';

			$termmeta = array_replace( $previous_termmeta, $new_termmeta );

			update_term_meta( $term_id, $this->attribute_term_meta_name, $termmeta );
		}
	}

	/**
	 * Admin: Add attribute fields
	 *
	 * @param int   $attribute_id
	 * @param array $attribute
	 */
	public function add_attribute_fields( $attribute_id, $attribute ) {
		if ( isset( $_POST[ $this->attribute_meta_name ] ) ) {
			$this->update_attribute_option( $attribute_id, $_POST[ $this->attribute_meta_name ] );
		}
	}

	/**
	 * Admin: Update attribute fields
	 *
	 * @param $attribute_id
	 * @param $attribute
	 * @param $old_attribute_name
	 */
	public function update_attribute_fields( $attribute_id, $attribute, $old_attribute_name ) {
		$value = isset( $_POST[ $this->attribute_meta_name ] ) ? $_POST[ $this->attribute_meta_name ] : false;

		if ( ! $value ) {
			return;
		}

		$update = $this->update_attribute_option( $attribute_id, $value );

		if ( $update ) {
			// Flush Object Cache to prevent issue with the sites who use persistent object caching plugin.
			wp_cache_flush();

			do_action( 'fcms_was_attribute_updated', $attribute_id, $value );
		}
	}

	/**
	 * Update term groups when attribute is updated.
	 *
	 * @param $attribute_id
	 * @param $value
	 */
	public function update_term_groups( $attribute_id, $value ) {
		if ( ! isset( $value['groups'] ) ) {
			return;
		}

		$attribute = wc_get_attribute( $attribute_id );
		$terms     = get_terms( array(
				'taxonomy' => $attribute->slug,
			)
		);

		if ( empty( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			$meta = $this->get_term_meta( $term );

			if ( empty ( $meta['group'] ) ) {
				continue;
			}

			if ( ! in_array( $meta['group'], $value['groups'] ) ) {
				$meta['group'] = '';
				update_term_meta( $term->term_id, $this->attribute_term_meta_name, $meta );
			}
		}
	}

	/**
	 * Helper: Update attribute option
	 *
	 * @param int   $attribute_id
	 * @param array $value
	 *
	 * @return bool
	 */
	public function update_attribute_option( $attribute_id, $value ) {
		$option_name = $this->get_attribute_option_name( $attribute_id );

		if ( get_option( $option_name ) !== false ) {
			return update_option( $option_name, $value );
		}

		$deprecated = null;
		$autoload   = 'no';

		return add_option( $option_name, $value, $deprecated, $autoload );
	}

	/**
	 * Ajax: Get attribute fields
	 */
	public function ajax_get_attribute_fields() {
		$return = array(
			'success' => true,
			'fields'  => false,
		);

		$attribute_id = ( ! empty( $_POST['attribute_id'] ) && $_POST['attribute_id'] > 0 ) ? (int) $_POST['attribute_id'] : false;

		// swatch type
		$return['fields'] = $this->get_attribute_fields( array(
			'attribute_id' => $attribute_id,
		) );

		wp_send_json( $return );
	}

	/**
	 * Get attribute fields.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_attribute_fields( $args ) {
		$defaults = array(
			'attribute_id'   => false,
			'attribute_slug' => false,
			'product_id'     => false,
		);

		$args = wp_parse_args( $args, $defaults );

		global $fcms_was;

		$fields = array();

		$is_product                    = $args['product_id'];
		$field_name_prefix             = $is_product ? sprintf( 'fcms-was[%s]', $args['attribute_slug'] ) : $this->attribute_meta_name;
		$saved_values                  = $is_product ? $fcms_was->swatches_class()
		                                                          ->get_product_swatch_data( $args['product_id'], $args['attribute_slug'] ) : $this->get_attribute_option_value( $args['attribute_id'] );
		$swatch_type_blank_option_name = $is_product ? __( 'Default', 'fcms-was' ) : __( 'None', 'fcms-was' );

		// swatch type
		$swatch_type_id        = FCMS_WAS_Helpers::strip_brackets( sprintf( '%s-swatch-type', $field_name_prefix ) );
		$saved_swatch_type     = $saved_values && isset( $saved_values['swatch_type'] ) ? $saved_values['swatch_type'] : "";
		$fields['swatch_type'] = array(
			'label'       => __( 'Swatch Type', 'fcms-was' ),
			'description' => __( 'Choose the type of swatches to use for this attribute.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'        => 'select',
				'id'          => $swatch_type_id,
				'name'        => sprintf( '%s[swatch_type]', $field_name_prefix ),
				'options'     => $fcms_was->swatches_class()->get_swatch_types( $swatch_type_blank_option_name ),
				'value'       => $saved_swatch_type,
				'conditional' => $swatch_type_id,
			) ),
			'class'       => array(),
			'condition'   => false,
			'match'       => array(),
		);

		/**
		 * Fields for visual swatches
		 */

		$is_visible = $fcms_was->swatches_class()->is_swatch_visual( $saved_swatch_type );

		// swatch shape
		$fields['swatch_shape'] = array(
			'label'       => __( 'Swatch Shape', 'fcms-was' ),
			'description' => __( 'The shape of your swatches on the frontend.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'    => 'select',
				'name'    => sprintf( '%s[swatch_shape]', $field_name_prefix ),
				'options' => array( 'round' => __( 'Round', 'fcms-was' ), 'square' => __( 'Square', 'fcms-was' ) ),
				'value'   => $saved_values && isset( $saved_values['swatch_shape'] ) ? $saved_values['swatch_shape'] : "",
			) ),
			'class'       => array( 'fcms-was-visual-swatch' ),
			'condition'   => $swatch_type_id,
			'match'       => array( 'image-swatch', 'colour-swatch' ),
		);

		// swatch size
		$fields['swatch_size'] = array(
			'label'       => __( 'Swatch Size (px)', 'fcms-was' ),
			'description' => __( 'The size of your swatches on the frontend.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'  => 'dimensions',
				'name'  => sprintf( '%s[swatch_size]', $field_name_prefix ),
				'value' => ! empty( $saved_values['swatch_size'] )
					? $saved_values['swatch_size']
					: apply_filters( 'fcms_was_default_swatch_size', array( 'width' => 30, 'height' => 30 ) ),
			) ),
			'class'       => array( 'fcms-was-visual-swatch' ),
			'condition'   => $swatch_type_id,
			'match'       => array( 'image-swatch', 'colour-swatch' ),
		);

		// swatch tooltips
		$fields['tooltips'] = array(
			'label'       => __( 'Enable Tooltips?', 'fcms-was' ),
			'description' => __( 'When enabled, a tooltip style text description will accompany your swatches.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'    => 'select',
				'name'    => sprintf( '%s[tooltips]', $field_name_prefix ),
				'options' => array( '1' => __( 'Yes', 'fcms-was' ), '0' => __( 'No', 'fcms-was' ) ),
				'value'   => $saved_values && isset( $saved_values['tooltips'] ) ? $saved_values['tooltips'] : "",
			) ),
			'class'       => array( 'fcms-was-visual-swatch' ),
			'condition'   => $swatch_type_id,
			'match'       => array( 'image-swatch', 'colour-swatch' ),
		);

		// swatch tooltips
		$fields['large_preview'] = array(
			'label'       => __( 'Show Large Preview?', 'fcms-was' ),
			'description' => __( 'Display a larger preview of the image swatch within a tooltip.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'    => 'select',
				'name'    => sprintf( '%s[large_preview]', $field_name_prefix ),
				'options' => array( '0' => __( 'No', 'fcms-was' ), '1' => __( 'Yes', 'fcms-was' ) ),
				'value'   => $saved_values && isset( $saved_values['large_preview'] ) ? $saved_values['large_preview'] : "",
			) ),
			'class'       => array(),
			'condition'   => $swatch_type_id,
			'match'       => array( 'image-swatch' ),
		);

		// swatch loop
		$swatch_loop_id = FCMS_WAS_Helpers::strip_brackets( sprintf( '%s-loop', $field_name_prefix ) );
		$fields['loop'] = array(
			'label'       => __( 'Show Swatch in Catalog?', 'fcms-was' ),
			'description' => __( 'When enabled, available swatches will be displayed in the catalog listing for each product.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'        => 'select',
				'id'          => $swatch_loop_id,
				'name'        => sprintf( '%s[loop]', $field_name_prefix ),
				'options'     => array( '0' => __( 'No', 'fcms-was' ), '1' => __( 'Yes', 'fcms-was' ) ),
				'value'       => $saved_values && isset( $saved_values['loop'] ) ? $saved_values['loop'] : "",
				'conditional' => $swatch_loop_id,
			) ),
			'class'       => array( 'fcms-was-u-hide' ),
			'condition'   => $swatch_type_id,
			'match'       => array( 'image-swatch', 'colour-swatch', 'text-swatch', 'radio-buttons' ),
		);

		$fields['loop-method'] = array(
			'label'       => __( 'Catalog Swatch Method', 'fcms-was' ),
			'description' => __( 'What should happen when a user clicks on the swatch in the catalog.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'    => 'select',
				'name'    => sprintf( '%s[loop-method]', $field_name_prefix ),
				'options' => array(
					'link'  => __( 'Link to Product', 'fcms-was' ),
					'image' => __( 'Change Product Image', 'fcms-was' ),
				),
				'value'   => $saved_values && isset( $saved_values['loop-method'] ) ? $saved_values['loop-method'] : "link",
			) ),
			'class'       => array( 'fcms-was-u-hide' ),
			'condition'   => array( $swatch_type_id, $swatch_loop_id ),
			'match'       => array( array( 'image-swatch', 'colour-swatch', 'text-swatch', 'radio-buttons' ), '1' ),
		);
		
		$fields['handle-overflow'] = array(
			'label'       => __( 'Overflow', 'fcms-was' ),
			'description' => __( 'How to display swatches with a large number of attributes. For radio buttons, this applies to the catalog view only.', 'fcms-was' ),
			'field'       => $fcms_was->helpers_class()->get_field( array(
				'type'    => 'select',
				'name'    => sprintf( '%s[handle-overflow]', $field_name_prefix ),
				'value'   => $saved_values && isset( $saved_values['handle-overflow'] ) ? $saved_values['handle-overflow'] : "no",
				'options' => array(
					'stacked'  => __( 'Stacked', 'fcms-was' ),
					'single-line' => __( 'Single Line', 'fcms-was' ),
					'slider' => __( 'Slider', 'fcms-was' ),
				),
			) ),
			'class'       => array( 'fcms-was-u-hide' ),
			'condition'   => $swatch_type_id,
			'match'       => array( 'image-swatch', 'colour-swatch', 'text-swatch', 'radio-buttons' ),
		);

		if ( ! $is_product ) {
			// swatch filters
			$fields['filters'] = array(
				'label'       => __( 'Show Swatch in Filters?', 'fcms-was' ),
				'description' => __( 'When enabled, swatches will be displayed in the WooCommerce layered nav filter widgets.', 'fcms-was' ),
				'field'       => $fcms_was->helpers_class()->get_field( array(
					'type'    => 'select',
					'name'    => sprintf( '%s[filters]', $field_name_prefix ),
					'options' => array( '0' => __( 'No', 'fcms-was' ), '1' => __( 'Yes', 'fcms-was' ) ),
					'value'   => $saved_values && isset( $saved_values['filters'] ) ? $saved_values['filters'] : "",
				) ),
				'class'       => array( 'fcms-was-u-hide' ),
				'condition'   => $swatch_type_id,
				'match'       => array( 'image-swatch', 'colour-swatch', 'text-swatch', 'radio-buttons' ),
			);

			$fields['groups'] = array(
				'label'       => __( 'Groups', 'fcms-was' ),
				'description' => __( 'Enter group labels into the field and press enter or select from the dropdown.', 'fcms-was' ),
				'field'       => $fcms_was->helpers_class()->get_field( array(
					'type'         => 'groups',
					'attribute_id' => $args['attribute_id'],
					'value'        => $saved_values && isset( $saved_values['groups'] ) ? $saved_values['groups'] : array(),
				) ),
				'class'       => array( 'fcms-was-u-hide' ),
				'condition'   => array( $swatch_type_id ),
				'match'       => array( array( 'image-swatch', 'colour-swatch', 'text-swatch', 'radio-buttons' ) ),
			);
		}

		if ( ! empty( $saved_swatch_type ) ) {
			$fields['loop']['class']        = $fcms_was->helpers_class()
			                                             ->unset_item( 'fcms-was-u-hide', $fields['loop']['class'] );
			$fields['loop-method']['class'] = $fcms_was->helpers_class()
			                                             ->unset_item( 'fcms-was-u-hide', $fields['loop-method']['class'] );
		}

		if ( ! $is_visible ) {
			$fields['swatch_shape']['class'][] = 'fcms-was-u-hide';
			$fields['swatch_size']['class'][]  = 'fcms-was-u-hide';
			$fields['tooltips']['class'][]     = 'fcms-was-u-hide';
		}

		if ( $saved_swatch_type !== "image-swatch" ) {
			$fields['large_preview']['class'][] = 'fcms-was-u-hide';
		}

		return $fields;
	}

	/**
	 * Helper: Get attribute option name
	 *
	 * @param int $attribute_id
	 *
	 * @return string
	 */
	public function get_attribute_option_name( $attribute_id ) {
		return sprintf( '%s_%d', $this->attribute_meta_name, $attribute_id );
	}

	/**
	 * Helper: Get attribute option values
	 *
	 * @param int $attribute_id
	 *
	 * @return array|bool
	 */
	public function get_attribute_option_value( $attribute_id ) {
		if ( empty( $attribute_id ) ) {
			return false;
		}

		static $attribute_option_values = array();

		if ( isset( $attribute_option_values[ $attribute_id ] ) ) {
			return $attribute_option_values[ $attribute_id ];
		}

		if ( $attribute_id ) {
			$attribute_option_values[ $attribute_id ] = get_option( $this->get_attribute_option_name( $attribute_id ) );
		}

		return $attribute_option_values[ $attribute_id ];
	}

	/**
	 * Helper: Get option name by term ID
	 *
	 * @param string $attribute
	 * @param int    $term_id
	 *
	 * @return string
	 */

	public function get_attribute_term_option_name( $attribute, $term_id ) {
		return sprintf( "pa_%s_%s", $attribute, $term_id );
	}

	/**
	 * Modify attribute HTML on frontend
	 *
	 * @param string $html
	 * @param array  $args
	 *
	 * @return string
	 */
	public function modify_attribute_html( $html, $args ) {
		global $product, $fcms_was;

		if ( empty( $args['options'] ) ) {
			return $html;
		}

		$_product                = isset( $args['product'] ) ? $args['product'] : $product;
		$product_id              = $_product->get_id();
		$attribute_raw           = $args['attribute'];
		$args['attribute']       = $this->format_attribute_slug( $args['attribute'] );
		$attribute_name          = $this->format_attribute_slug( $args['attribute'], true );
		$swatch_type             = $fcms_was->swatches_class()
		                                      ->get_swatch_option( 'swatch_type', $args['attribute'] );
		$swatch_shape            = $fcms_was->swatches_class()
		                                      ->get_swatch_option( 'swatch_shape', $args['attribute'] );
		$tooltips                = (bool) $fcms_was->swatches_class()
		                                             ->get_swatch_option( 'tooltips', $args['attribute'] );
		$large_preview           = (bool) $fcms_was->swatches_class()
		                                             ->get_swatch_option( 'large_preview', $args['attribute'] );
		$has_product_swatch_meta = $fcms_was->products_class()->has_swatch_meta( $product_id, $args['attribute'] );

		if ( empty( $swatch_type ) ) {
			return $html;
		}

		$swatch_data = $fcms_was->swatches_class()->get_swatch_data(
			array(
				'product_id'     => $_product->get_id(),
				'attribute_slug' => $args['attribute'],
			)
		);

		$swatch_data['handle-overflow'] = ! empty( $swatch_data['handle-overflow'] ) && 'radio-buttons' !== $swatch_type ? $swatch_data['handle-overflow'] : 'stacked';

		$visual         = $fcms_was->swatches_class()->is_swatch_visual( $swatch_type ) ? 'fcms-was-swatches--visual' : false;
		$tooltips       = $visual && ( $tooltips || $large_preview ) ? "fcms-was-swatches--tooltips" : false;
		$shape          = $visual && $swatch_shape == "round" ? "fcms-was-swatches--round" : "fcms-was-swatches--square";
		$style          = $fcms_was->settings['style_general_selected'];
		$overflow_class = sprintf( 'fcms-was-swatches--%s', $swatch_data['handle-overflow'] );
		$loading_class  = 'stacked' !== $swatch_data['handle-overflow'] ? 'fcms-was-swatches--loading' : '';

		$swatches_list_html = sprintf( '<ul class="fcms-was-swatches fcms-was-swatches--%s fcms-was-swatches--%s %s %s %s %s %s" data-attribute="%s" data-overflow="%s">', $style, $swatch_type, $visual, $tooltips, $shape, esc_attr( $overflow_class ), $loading_class, $attribute_name, esc_attr( $swatch_data['handle-overflow'] ) );

		$available_terms   = $fcms_was->products_class()->get_available_terms_for_product( $_product->get_id(), $args['attribute'] );
		$has_any_variation = true === $available_terms || in_array( null, $available_terms );
		$oos_terms         = $has_any_variation ? array() : $fcms_was->products_class()->get_out_of_stock_terms( $_product->get_id(), $attribute_raw );

		$args['options'] = $this->sort_attribute_terms( $_product->get_id(), $args['attribute'], $args['options'] );

		foreach ( $args['options'] as $label => $options ) {

			if ( 'fcms-was-default' !== $label && ! $has_product_swatch_meta ) {
				$intersect = $has_any_variation || array_intersect( $available_terms, $options );

				// Hide group label where no terms are available.
				if ( ! empty( $intersect ) ) {
					$label_item          = sprintf( '<li class="fcms-was-swatches__label">%s</li>', $label );
					$swatches_list_html .= apply_filters( 'fcms_was_swatch_group_label', $label_item, $args, $_product, $label );
				}
			}

			foreach ( $options as $option ) {
				$out_of_stock = in_array( $option, $oos_terms );
				$disabled_swatch_class = $out_of_stock ? 'fcms-was-swatch--disabled' : '';
				$oos_item_class = $out_of_stock ? 'fcms-was-swatches__item--out-of-stock' : '';

				// Skip if not available, or not set to "any".
				if ( ! $has_any_variation && ! in_array( $option, $available_terms ) ) {
					continue;
				}

				$option_sanitized = sanitize_title( $option );
				$option_data      = isset( $swatch_data['values'][ $option_sanitized ] ) ? $swatch_data['values'][ $option_sanitized ] : false;

				if ( ! $option_data ) {
					continue;
				}

				$swatch_html = $fcms_was->swatches_class()->get_swatch_html( $swatch_data, $option );
				$selected    = $args['selected'] == $option ? "fcms-was-swatch--selected" : "";

				$swatch_item_html = sprintf( '<li class="fcms-was-swatches__item %s"><a href="javascript: void(0);" data-attribute-value="%s" data-attribute-value-name="%s" class="fcms-was-swatch fcms-was-swatch--%s %s %s">%s</a></li>', $oos_item_class, esc_attr( $option ), esc_attr( $option_data['label'] ), esc_attr( $swatch_data['swatch_type'] ), $selected, $disabled_swatch_class, $swatch_html );

				$swatches_list_html .= apply_filters( 'fcms_was_swatch_item_html', $swatch_item_html, $args, $swatch_data, $swatch_html, $option );
			}
		}

		if ( 'single-line' === $swatch_data['handle-overflow'] ) {
			$swatches_list_html .= sprintf( '<li class="fcms-was-swatches__item fcms-was-swatches__item--dummy"><a href="#" class="fcms-was-swatch fcms-was-swatch--dummy">+%s</a></li>', 0 );
		}

		$swatches_list_html .= '</ul>';

		$swatches_list_html = apply_filters( 'fcms_was_swatches_html', $swatches_list_html, $args, $swatch_data );
		$swatches_list_html .= sprintf( '<div style="display: none;">%s</div>', $html );

		return apply_filters( 'fcms_was_variation_attribute_options_html', $swatches_list_html, $args, $swatch_data );
	}

	/**
	 * Helper: Sort attribute terms
	 *
	 * @since 1.0.1
	 *
	 * @param int    $product_id
	 * @param string $attribute
	 * @param array  $options
	 *
	 * @return array
	 */
	public function sort_attribute_terms( $product_id, $attribute, $options ) {
		static $options_sorted = array();

		$key = sprintf( '%s_%s', $product_id, $attribute );

		if ( isset( $options_sorted[ $key ] ) ) {
			return $options_sorted[ $key ];
		}

		$default_key = 'fcms-was-default';
		$terms       = wc_get_product_terms( $product_id, $attribute, array( 'fields' => 'all' ) );

		$options_sorted[ $key ]                 = array();
		$options_sorted[ $key ][ $default_key ] = $options;

		if ( ! $terms ) {
			return $options_sorted[ $key ];
		}

		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, $options ) ) {
				$group = FCMS_WAS_Swatches::get_swatch_value( 'taxonomy', 'group', $term );
				$group = $group ? $group : $default_key;

				$options_sorted[ $key ][ $group ][]     = $term->slug;
				$options_sorted[ $key ][ $default_key ] = FCMS_WAS_Helpers::remove_array_item_by_value( $options_sorted[ $key ][ $default_key ], $term->slug );
			}
		}

		return array_filter( $options_sorted[ $key ] );
	}

	/**
	 * Enable attribute label modification.
	 */
	public static function enable_modify_attribute_label() {
		global $product;

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return;
		}

		add_filter( 'woocommerce_attribute_label', array( __CLASS__, 'modify_attribute_label' ), 100, 3 );
	}

	/**
	 * Disable attribute label modification.
	 */
	public static function disable_modify_attribute_label() {
		remove_filter( 'woocommerce_attribute_label', array( __CLASS__, 'modify_attribute_label' ), 100 );
	}

	/**
	 * Modify attribute label on frontend
	 *
	 * @param string     $label
	 * @param string     $name
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public static function modify_attribute_label( $label, $name, $product ) {
		return sprintf( '<strong>%s</strong>: <span class="fcms-was-chosen-attribute"><span class="fcms-was-chosen-attribute__no-selection">%s</span></span>', $label, __( 'No selection', 'fcms-was' ) );
	}

	/**
	 * Helper: Get an attribute ID by name.
	 *
	 * @param string $attribute_slug
	 *
	 * @return string|bool
	 */
	public function get_attribute_id_by_slug( $attribute_slug ) {
		global $wpdb;

		static $ids = array();

		$attribute_slug = str_replace( 'pa_', '', $attribute_slug );

		if ( isset( $ids[ $attribute_slug ] ) ) {
			return $ids[ $attribute_slug ];
		}

		$attribute_id = $wpdb->get_var( $wpdb->prepare( "
            SELECT attribute_id
            FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
            WHERE attribute_name = %s
        ", $attribute_slug ) );

		if ( ! $attribute_id || is_wp_error( $attribute_id ) ) {
			$attribute_id = false;
		}

		$ids[ $attribute_slug ] = $attribute_id;

		return $ids[ $attribute_slug ];
	}

	/**
	 * Admin: Add column to attribute list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_attribute_columns( $columns ) {
		global $fcms_was;

		if ( ! isset( $_GET['taxonomy'] ) ) {
			return $columns;
		}

		$swatch_type = $fcms_was->swatches_class()->get_swatch_option( 'swatch_type', $_GET['taxonomy'] );

		if ( $fcms_was->swatches_class()->is_swatch_visual( $swatch_type ) ) {
			$columns['fcms-was-swatch'] = __( 'Swatch', 'fcms-was' );
		}

		$groups = $this->get_groups( $_GET['taxonomy'] );

		if ( $groups ) {
			$columns['fcms-was-group'] = __( 'Group', 'fcms-was' );
		}

		return $columns;
	}

	/**
	 * Admin: Add content to attribute columns
	 *
	 * @param string $content
	 * @param string $column_name
	 * @param int    $term_id
	 *
	 * @return string
	 */
	public function add_attribute_column_content( $content, $column_name, $term_id ) {
		global $fcms_was;

		if ( ! isset( $_GET['taxonomy'] ) ) {
			return $content;
		}

		$swatch_data    = $fcms_was->swatches_class()->get_attribute_swatch_data( $_GET['taxonomy'] );
		$attribute_term = get_term( $term_id, $_GET['taxonomy'] );
		$swatch_html    = $attribute_term ? $fcms_was->swatches_class()
		                                               ->get_swatch_html( $swatch_data, $attribute_term->slug ) : "";

		switch ( $column_name ) {
			case 'fcms-was-swatch':
				$content = $swatch_html;
				break;

			case 'fcms-was-group':
				$term    = get_term( $term_id );
				$content = FCMS_WAS_Swatches::get_swatch_value( 'taxonomy', 'group', $term );
				break;

			default:
				break;
		}

		return $content;
	}

	/**
	 * Helper: Get variation attributes for product
	 *
	 * @param int $product_id
	 *
	 * @return bool|array
	 */
	public function get_variation_attributes_for_product( $product_id ) {
		if ( ! $product_id ) {
			return false;
		}

		$product              = wc_get_product( $product_id );
		$attributes           = $product->get_attributes();
		$variation_attributes = array();

		if ( ! $attributes ) {
			return false;
		}

		foreach ( $attributes as $attribute ) {
			/* @var WC_Product_Attribute $attribute */

			if ( ! $attribute->get_variation() ) {
				continue;
			}

			$variation_attribute = array(
				'options' => array(),
			);

			if ( $attribute->is_taxonomy() ) {
				$variation_attribute['slug'] = $attribute->get_name();

				$options          = wp_get_post_terms( $product_id, $attribute->get_name() );
				$attribute_object = get_taxonomy( $attribute->get_name() );

				$variation_attribute['label'] = $attribute_object->label;

				if ( $options ) {
					foreach ( $options as $option ) {
						$variation_attribute['options'][] = array(
							'id'   => $option->term_id,
							'slug' => $option->slug,
							'name' => $option->name,
							'term' => $option,
						);
					}
				}
			} else {
				$variation_attribute['slug']  = $this->format_attribute_slug( $attribute->get_name() );
				$variation_attribute['label'] = $attribute->get_name();

				if ( empty( $attribute->get_options() ) ) {
					continue;
				}

				foreach ( $attribute->get_options() as $index => $option ) {
					$variation_attribute['options'][] = array(
						'id'   => $index,
						'slug' => sanitize_title( $option ),
						'name' => $option,
						'term' => false,
					);
				}
			}

			$variation_attributes[ $variation_attribute['slug'] ] = $variation_attribute;
		}

		return $variation_attributes;
	}

	/**
	 * Helper: Get non taxonomy attribute value slug
	 *
	 * @param int    $index
	 * @param string $value
	 *
	 * @return string
	 */
	public function get_variation_attribute_value_slug( $index, $value ) {
		$attribute_slug = $this->format_attribute_slug( $value );

		return sprintf( '%s_%d', $attribute_slug, $index );
	}

	/**
	 * Helper: format attribute slug
	 *
	 * @param string $attribute_slug
	 * @param bool   $prefix
	 *
	 * @return string
	 */
	public function format_attribute_slug( $attribute_slug, $prefix = false ) {
		if ( ( ! $this->is_taxonomy( $attribute_slug ) || $prefix ) && strpos( $attribute_slug, 'attribute_' ) === false ) {
			$attribute_slug = 'attribute_' . sanitize_title( $attribute_slug );
		}

		return $attribute_slug;
	}

	/**
	 * Helper: Is attribute a taxonomy?
	 *
	 * @param string $attribute_slug
	 *
	 * @return bool
	 */
	public function is_taxonomy( $attribute_slug ) {
		return substr( $attribute_slug, 0, 3 ) === "pa_";
	}

	/**
	 * Get term meta.
	 *
	 * This method allows plugins to hook in before
	 * the get_term_meta call. Useful for WPML.
	 *
	 * @param WP_Term $term
	 *
	 * @return mixed
	 */
	public function get_term_meta( $term ) {
		$term_meta = apply_filters( 'fcms_was_get_term_meta', false, $term );

		if ( ! empty( $term_meta ) ) {
			return $term_meta;
		}

		return get_term_meta( $term->term_id, $this->attribute_term_meta_name, true );
	}

	/**
	 * Get terms.
	 *
	 * This method allows plugins to hook in before
	 * the get_terms call. Useful for WPML.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function get_terms( $args ) {
		$terms = apply_filters( 'fcms_was_get_terms', false, $args );

		if ( ! empty( $terms ) ) {
			return $terms;
		}

		return get_terms( $args );
	}

	/**
	 * Modify layered nav term HTML.
	 *
	 * @param $term_html
	 * @param $term
	 * @param $link
	 * @param $count
	 *
	 * @return mixed
	 */
	public function modify_layered_nav_term_html( $term_html, $term, $link, $count ) {
		global $fcms_was;

		$swatch_data = $fcms_was->swatches_class()->get_attribute_swatch_data( $term->taxonomy );

		if ( empty( $swatch_data ) || ! $swatch_data['filters'] ) {
			return $term_html;
		}

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$current_values     = isset( $_chosen_attributes[ $term->taxonomy ]['terms'] ) ? $_chosen_attributes[ $term->taxonomy ]['terms'] : array();
		$option_is_set      = in_array( $term->slug, $current_values, true );
		$visual             = $fcms_was->swatches_class()->is_swatch_visual( $swatch_data['swatch_type'] );
		$append             = sprintf( '(%d)', $count );
		$swatch             = $fcms_was->swatches_class()->get_swatch_html( $swatch_data, $term->slug, $append );

		$class = array(
			'fcms-was-swatch',
			sprintf( 'fcms-was-swatch--%s', esc_attr( $swatch_data['swatch_type'] ) ),
		);

		if ( $option_is_set ) {
			$class[] = 'fcms-was-swatch--selected';
		}

		if ( $visual ) {
			$class[] = sprintf( 'fcms-was-swatch--%s', esc_attr( $swatch_data['swatch_shape'] ) );
		}

		if ( $link ) {
			$group_name = isset( $this->get_term_meta( $term )['group'] ) ? $this->get_term_meta( $term )['group'] : false;
			$term_html = sprintf( '<a href="%s" class="%s" data-group="%s">%s</a>', esc_url( $link ), esc_attr( implode( ' ', $class ) ), $group_name, $swatch );
		} else {
			$term_html = sprintf( '<div class="%s">%s</a>', esc_url( $link ), esc_attr( implode( ' ', $class ) ), $swatch );
		}

		return $term_html;
	}

	/**
	 * Add class to layered nav widgets for swatches.
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	public function add_widget_class( $params ) {
		global $wp_registered_widgets;

		$classes     = array();
		$instance_id = $params[1]['number'];
		$widget_id   = $params[0]['widget_id'];

		if ( strpos( $widget_id, 'woocommerce_layered_nav' ) === false ) {
			return $params;
		}

		$settings = $wp_registered_widgets[ $widget_id ]['callback'][0]->get_settings();

		if ( empty ( $settings[ $instance_id ] ) ) {
			return $params;
		}

		$attribute = ! empty ( $settings[ $instance_id ]['attribute'] ) ? 'pa_' . $settings[ $instance_id ]['attribute'] : false;

		if ( ! $attribute ) {
			return $params;
		}

		global $fcms_was;

		$swatch_data = $fcms_was->swatches_class()->get_attribute_swatch_data( $attribute );

		if ( empty( $swatch_data ) || ! $swatch_data['filters'] ) {
			return $params;
		}

		$visual = $fcms_was->swatches_class()->is_swatch_visual( $swatch_data['swatch_type'] );
		$style  = $fcms_was->settings['style_general_selected'];

		$classes[] = 'fcms-was-swatches';
		$classes[] = 'fcms-was-swatches--widget';
		$classes[] = sprintf( 'fcms-was-swatches--%s', esc_attr( $swatch_data['swatch_type'] ) );

		if ( $swatch_data['swatch_shape'] ) {
			$classes[] = sprintf( 'fcms-was-swatches--%s', esc_attr( $swatch_data['swatch_shape'] ) );
		}

		if ( $visual ) {
			$classes[] = 'fcms-was-swatches--tooltips';
			$classes[] = 'fcms-was-swatches--visual';
			$classes[] = sprintf( 'fcms-was-swatches--%s', $style );
		}

		if ( empty ( $classes ) ) {
			return $params;
		}

		$params[0]['before_widget'] = str_replace(
			'class="',
			'class="' . join( ' ', $classes ) . ' ',
			$params[0]['before_widget']
		);

		return $params;
	}

	/**
	 * Get terms from attribute ID.
	 *
	 * @param int $attribute_id
	 *
	 * @return array
	 */
	public static function get_terms_from_attribute_id( $attribute_id ) {
		static $terms = array();

		if ( isset( $terms[ $attribute_id ] ) ) {
			return $terms[ $attribute_id ];
		}

		$terms[ $attribute_id ] = array();
		$taxonomy               = wc_attribute_taxonomy_name_by_id( $attribute_id );

		if ( ! $taxonomy ) {
			return $terms[ $attribute_id ];
		}

		$get_terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );

		if ( ! $get_terms ) {
			return $terms[ $attribute_id ];
		}

		foreach ( $get_terms as $term ) {
			$terms[ $attribute_id ][ $term->term_id ] = $term->name;
		}

		return $terms[ $attribute_id ];
	}

	/**
	 * Get groups.
	 *
	 * @param int|string $attribute
	 *
	 * @return bool|array
	 */
	public function get_groups( $attribute ) {
		if ( ! is_numeric( $attribute ) ) {
			$attribute = wc_attribute_taxonomy_id_by_name( $attribute );
		}

		if ( ! $attribute ) {
			return false;
		}

		$attribute_data = $this->get_attribute_option_value( $attribute );

		return ! empty( $attribute_data['groups'] ) ? $attribute_data['groups'] : false;
	}
	
	/**
	 * Get all terms for the given Attribute, handles both taxonomy and custom attribute.
	 *
	 * @param int $poduct_id 
	 * @param string $attribute
	 * 
	 * @return array | false
	 */
	public function get_attribute_terms( $attribute, $product_id ) {
		$product            = wc_get_product( $product_id );
		
		if( ! $product ) {
			return false; 
		}
		
		$all_attributes     = $product->get_variation_attributes();
		
		foreach( $all_attributes as $attribute_key => $terms ) {
			if( $attribute_key === $attribute ) {
				return $terms;
			}
		}
		
		return false;
	}

	/**
	 * Get product attributes.
	 *
	 * @param $product
	 *
	 * @return bool|mixed
	 */
	public static function get_product_attributes( $product ) {
		static $formatted_attributes = array();

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		$product_id = $product->get_id();

		if ( isset( $formatted_attributes[ $product_id ] ) ) {
			return $formatted_attributes[ $product_id ];
		}

		$formatted_attributes[ $product_id ] = array();
		$attributes = $product->get_attributes();

		if ( empty( $attributes ) ) {
			return false;
		}

		foreach ( $attributes as $attribute_key => $attribute ) {
			if ( ! (bool) $attribute->get_variation() ) {
				continue;
			}

			$formatted_attributes[ $product_id ][ $attribute_key ] = $attribute;
		}

		return $formatted_attributes[ $product_id ];
	}
}