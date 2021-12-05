<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Swatches
 *
 * This class is for anything swatch related
 *
 * @class          FCMS_WAS_Swatches
 * @version        1.0.0
 * @category       Class
 * @author         FCMS
 */
class FCMS_WAS_Swatches {
	/**
	 * Field: Get colour swatch field data
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_colour_swatch_fields( $args ) {
		$defaults = array(
			'term'           => false,
			'field_name'     => '',
			'attribute_type' => 'taxonomy',
			'field_value'    => false,
			'field_label'    => __( 'Colour Swatch', 'fcms-was' ),
		);

		$args = wp_parse_args( $args, $defaults );

		$fields   = array();
		$field_id = 'colour-swatch';
		$value    = $args['field_value'] ? $args['field_value'] : self::get_swatch_value( $args['attribute_type'], 'colour-swatch', $args['term'] );

		$fields[] = array(
			'label'       => sprintf( '<label for="%s">%s</label>', $field_id, $args['field_label'] ),
			'field'       => sprintf( '<input id="%s" type="text" name="%s" value="%s" class="colour-swatch-picker">', $field_id, $args['field_name'], $value ),
			'description' => '',
		);

		if ( $args['attribute_type'] !== 'product' ) {
			$fields[] = self::get_group_field( $args );
		}

		return array_filter( $fields );
	}

	/**
	 * Field: Get image swatch field data
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_image_swatch_fields( $args ) {
		$defaults = array(
			'term'           => false,
			'field_name'     => '',
			'attribute_type' => 'taxonomy',
			'field_value'    => false,
			'field_label'    => __( 'Image Swatch', 'fcms-was' ),
		);

		$args = wp_parse_args( $args, $defaults );

		$fields       = array();
		$field_id     = 'fcms-was-image-picker';
		$value        = $args['field_value'] ? $args['field_value'] : self::get_swatch_value( $args['attribute_type'], 'image-swatch', $args['term'] );
		$img          = $value ? wp_get_attachment_image( $value, 'thumbnail' ) : false;
		$upload_class = $value ? sprintf( '%1$s__upload %1$s__upload--edit', $field_id ) : sprintf( '%s__upload', $field_id );

		$fields[] = array(
			'label'       => sprintf( '<label for="%s-field">%s</label>', $field_id, $args['field_label'] ),
			'field'       => sprintf( '<div class="%1$s">
                    <div class="%1$s__preview">%2$s</div>
                    <input id="%1$s-field" type="hidden" name="%3$s" value="%4$s" class="%1$s__field regular-text">

                    <a href="javascript: void(0);" class="%1$s__button %9$s" title="%5$s" id="upload-%1$s" data-title="%5$s" data-button-text="%6$s"><span class="dashicons dashicons-edit"></span><span class="dashicons dashicons-plus"></span></a>

                    <a href="javascript: void(0);" class="%1$s__button %1$s__remove" title="%7$s" %8$s><span class="dashicons dashicons-no"></span></a>
                </div>', $field_id, $img, $args['field_name'], $value, __( 'Upload/Add Image', 'fcms-was' ), __( 'Insert Image', 'fcms-was' ), __( 'Remove Image', 'fcms-was' ), $img ? false : 'style="display: none;"', $upload_class ),
			'description' => '',
		);

		if ( $args['attribute_type'] !== 'product' ) {
			$fields[] = self::get_group_field( $args );
		}

		return array_filter( $fields );
	}

	/**
	 * Field: Get text swatch field data
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_text_swatch_fields( $args ) {
		$fields = array(
			self::get_group_field( $args ),
		);

		return array_filter( $fields );
	}

	/**
	 * Field: Get radio buttons field data
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_radio_buttons_fields( $args ) {
		$fields = array(
			self::get_group_field( $args ),
		);

		return array_filter( $fields );
	}

	/**
	 * Get group field.
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public static function get_group_field( $args ) {
		global $fcms_was;

		$taxonomy = is_object( $args['term'] ) ? $args['term']->taxonomy : $args['term'];

		$groups = $fcms_was->attributes_class()->get_groups( $taxonomy );

		if ( empty( $groups ) ) {
			return array();
		}

		$name_parts = explode( '[', $args['field_name'] );
		$name       = sprintf( '%s[group]', $name_parts[0] );
		$value      = self::get_swatch_value( 'taxonomy', 'group', $args['term'] );

		ob_start();
		?>
		<select name="<?php esc_attr_e( $name ); ?>" id="fcms-was-group-field">
			<option value=""><?php _e( 'Select a group...', 'fcms-was' ); ?></option>
			<?php foreach ( $groups as $group ) { ?>
				<option value="<?php esc_attr_e( $group ); ?>" <?php selected( $group, $value ); ?>><?php echo $group; ?></option>
			<?php } ?>
		</select>
		<?php

		return array(
			'label'       => sprintf( '<label for="fcms-was-group-field">%s</label>', __( 'Group', 'fcms-was' ) ),
			'field'       => ob_get_clean(),
			'description' => '',
		);
	}

	/**
	 * Helper: Get swatch value
	 *
	 * @param string  $attribute_type
	 * @param string  $swatch_type
	 * @param WP_Term $term
	 *
	 * @return string|bool
	 */
	public static function get_swatch_value( $attribute_type, $swatch_type, $term ) {
		global $fcms_was;

		if ( $attribute_type === "taxonomy" ) {
			if ( ! is_object( $term ) ) {
				return false;
			}

			$value = get_term_meta( $term->term_id, $fcms_was->attributes_class()->attribute_term_meta_name, true );

			return isset( $value[ $swatch_type ] ) ? $value[ $swatch_type ] : false;
		}

		return false;
	}

	/**
	 * Helper: Get swatch data for attribute term
	 *
	 * @param string|bool $args_attribute_slug
	 * @param WC_Product  $product
	 *
	 * @return array
	 */
	public function get_attribute_swatch_data( $args_attribute_slug = false, $product = null ) {
		global $fcms_was;

		/* checking if required data is already present in the cache, if yes, let's return it to avoid heavy computation again. */
		$argument_slug_cache = $args_attribute_slug ? $args_attribute_slug : "all";
		$product_id          = $product ? $product->get_id() : 0;
		$cache_key           = "fcms_was_{$argument_slug_cache}_{$product_id}";
		$swatch_data         = wp_cache_get( $cache_key );

		if ( false !== $swatch_data ) {
			return $swatch_data; /* cache hit */
		}

		/* Cache miss */
		$swatch_data = array();
		$attributes  = wc_get_attribute_taxonomies();

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				$attribute_slug = wc_attribute_taxonomy_name( $attribute->attribute_name );
				$swatch_type    = $this->get_swatch_option( 'swatch_type', $attribute_slug );

				if ( empty( $swatch_type ) ) {
					$swatch_data[ $attribute_slug ] = array();
					continue;
				}

				$swatch_size          = $this->get_swatch_option( 'swatch_size', $attribute_slug );
				$swatch_loop          = $this->get_swatch_option( 'loop', $attribute_slug );
				$swatch_loop_method   = $this->get_swatch_option( 'loop-method', $attribute_slug );
				$swatch_large_preview = $this->get_swatch_option( 'large_preview', $attribute_slug );
				$swatch_shape         = $this->is_swatch_visual( $swatch_type ) ? $this->get_swatch_option( 'swatch_shape', $attribute_slug ) : false;
				$swatch_filters       = $this->get_swatch_option( 'filters', $attribute_slug );
				$handle_overflow      = $this->get_swatch_option( 'handle-overflow', $attribute_slug );

				$swatch_data[ $attribute_slug ] = array(
					'swatch_type'     => $swatch_type,
					'swatch_size'     => $swatch_size,
					'swatch_shape'    => $swatch_shape,
					'loop'            => ! empty( $swatch_loop ),
					'loop-method'     => $swatch_loop_method,
					'large_preview'   => $swatch_large_preview,
					'values'          => array(),
					'attribute'       => $attribute,
					'filters'         => ! empty( $swatch_filters ),
					'handle-overflow' => $handle_overflow ? $handle_overflow : 'stacked',
				);

				$attribute_terms = get_terms( array(
					'taxonomy'   => $attribute_slug,
					'hide_empty' => false,
				) );

				if ( $attribute_terms && ! empty( $attribute_terms ) ) {
					foreach ( $attribute_terms as $attribute_term ) {
						$swatch_value = $fcms_was->attributes_class()->get_term_meta( $attribute_term );

						// If swatch is visual, assign no value when not set.
						// Otherwise, use the attribute name.
						if ( $this->is_swatch_visual( $swatch_type ) ) {
							$value = isset( $swatch_value[ $swatch_type ] ) ? $swatch_value[ $swatch_type ] : '';
						} else {
							$value = $attribute_term->name;
						}

						$swatch_data[ $attribute_slug ]['values'][ $attribute_term->slug ] = array(
							'label' => apply_filters( 'woocommerce_variation_option_name', $attribute_term->name, $attribute_term, $attribute_slug, $product ),
							'value' => $value,
						);
					}
				}
			}
		}

		$swatch_data = $args_attribute_slug && isset( $swatch_data[ $args_attribute_slug ] ) ? $swatch_data[ $args_attribute_slug ] : $swatch_data;

		/* saving the output data in cache so we can use it in the future */
		if ( $swatch_data ) {
			wp_cache_set( $cache_key, $swatch_data );
		}

		return $swatch_data;
	}

	/**
	 * Helper: Get swatch data for product
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	public function get_product_swatch_data( $product_id, $args_attribute_slug = false ) {
		global $fcms_was, $product;

		$swatch_data = array();
		$swatch_meta = $fcms_was->products_class()->get_swatch_meta( $product_id );

		if ( $swatch_meta ) {
			$product            = is_null( $product ) ? wc_get_product( $product_id ) : $product;
			$product_attributes = $product->get_attributes();

			foreach ( $swatch_meta as $attribute_slug => $attribute_swatch_data ) {
				if ( ! empty( $attribute_swatch_data['swatch_type'] ) ) {
					// if we passed an attribute slug to the function
					// and this attribute does not match, skip to the
					// next one
					if ( $args_attribute_slug && $attribute_slug !== $args_attribute_slug ) {
						continue;
					}

					$is_tax = $fcms_was->attributes_class()->is_taxonomy( $attribute_slug );

					// if the swatch type is a visual one
					if ( ! $this->is_swatch_visual( $attribute_swatch_data['swatch_type'] ) ) {
						$attribute_swatch_data['values'] = array();

						// if this attribute is a taxonomy
						if ( $is_tax ) {
							$attribute_terms = $fcms_was->attributes_class()->get_terms( array(
								'taxonomy'   => $attribute_slug,
								'hide_empty' => true,
							) );

							if ( $attribute_terms && ! empty( $attribute_terms ) ) {
								foreach ( $attribute_terms as $attribute_term ) {
									$attribute_swatch_data['values'][ $attribute_term->slug ] = array(
										'label' => apply_filters( 'woocommerce_variation_option_name', $attribute_term->name, $attribute_term, $attribute_slug, $product ),
										'value' => $attribute_term->name,
									);
								}
							}

							// if this attribute is a product attribute, not
							// a taxonomy
						} else {
							if ( isset( $product_attributes[ str_replace( 'attribute_', '', $attribute_slug ) ] ) ) {
								$attribute       = $product_attributes[ str_replace( 'attribute_', '', $attribute_slug ) ];
								$attribute_terms = isset( $attribute['value'] ) && ! empty( $attribute['value'] ) ? explode( ' | ', $attribute['value'] ) : false;

								if ( $attribute_terms ) {
									foreach ( $attribute_terms as $attribute_term ) {
										$attribute_swatch_data['values'][ sanitize_title( $attribute_term ) ] = array(
											'label' => apply_filters( 'woocommerce_variation_option_name', $attribute_term, null, $attribute_slug, $product ),
											'value' => $attribute_term,
										);
									}
								}
							}
						}
					}

					$swatch_data[ $attribute_slug ] = $attribute_swatch_data;
				}
			}
		}

		return $args_attribute_slug && isset( $swatch_data[ $args_attribute_slug ] ) ? $swatch_data[ $args_attribute_slug ] : $swatch_data;
	}

	/**
	 * Get swatch data
	 *
	 * Gets default swatch data, and overrides with any product
	 * specific data - for a specific attribute slug
	 *
	 * @param array $args
	 *
	 * @return array|false
	 */
	public function get_swatch_data( $args ) {
		$product               = wc_get_product( $args['product_id'] );
		$args                  = apply_filters( 'fcms_was_swatch_data_args', $args );
		$attribute_swatch_data = $this->get_attribute_swatch_data( false, $product );
		$product_swatch_data   = $this->get_product_swatch_data( $args['product_id'] );
		$combined_swatch_data  = array_replace( $attribute_swatch_data, $product_swatch_data );

		if ( $combined_swatch_data && ! empty( $combined_swatch_data ) ) {
			foreach ( $combined_swatch_data as $attribute_slug => $swatch_data ) {
				if ( $args['attribute_slug'] == $attribute_slug ) {
					$swatch_data = array_filter( $swatch_data );

					$default_swatch_data = array(
						'swatch_type'     => '',
						'swatch_size'     => apply_filters( 'fcms_was_default_swatch_size', array(
							'width'  => 30,
							'height' => 30,
						) ),
						'loop'            => false,
						'loop-method'     => 'image',
						'large_preview'   => false,
						'values'          => array(),
						'attribute'       => null,
						'handle-overflow' => 'stacked',
					);

					$swatch_data = wp_parse_args( $swatch_data, $default_swatch_data );

					return $swatch_data;
				}
			}
		}

		return false;
	}

	/**
	 * Get swatch option
	 *
	 * @param string      $option_name
	 * @param string|bool $attribute_slug
	 * @param int|bool    $attribute_id
	 *
	 * @return string
	 */
	public function get_swatch_option( $option_name, $attribute_slug = false, $attribute_id = false ) {
		global $product, $fcms_was;

		if ( ! is_admin() && ! is_object( $product ) ) {
			global $post;
			if ( empty( $post ) || 'product' !== $post->post_type ) {
				return false;
			}
			$product = wc_get_product( $post->ID );
		}

		$attribute_id = $attribute_id ? $attribute_id : $fcms_was->attributes_class()
		                                                           ->get_attribute_id_by_slug( $attribute_slug );

		$default_swatch_data = $fcms_was->attributes_class()->get_attribute_option_value( $attribute_id );
		$swatch_option       = is_array( $default_swatch_data ) && ! empty( $default_swatch_data[ $option_name ] ) ? $default_swatch_data[ $option_name ] : "";
		
		// Ensure product filters show the default swatch option.
		if ( ( ! $attribute_id || ! is_archive() ) && $product ) {
			$swatch_data   = $fcms_was->products_class()
			                            ->get_product_swatch_data_for_attribute( $product->get_id(), $attribute_slug );
			$swatch_option = is_array( $swatch_data ) && ( isset( $swatch_data[ $option_name ] ) && $swatch_data[ $option_name ] !== "" ) ? $swatch_data[ $option_name ] : $swatch_option;
		}

		return $swatch_option;
	}

	/**
	 * Helper: Get swatch types
	 *
	 * @param string $default_text
	 *
	 * @return string
	 */
	public function get_swatch_types( $default_text ) {
		return apply_filters( 'fcms-was-swatch-types', array(
			''              => $default_text,
			'image-swatch'  => __( 'Image Swatch', 'fcms-was' ),
			'colour-swatch' => __( 'Colour Swatch', 'fcms-was' ),
			'text-swatch'   => __( 'Text Swatch', 'fcms-was' ),
			'radio-buttons' => __( 'Radio Buttons', 'fcms-was' ),
		) );
	}

	/**
	 * Get swatch html
	 *
	 * @param        $swatch_data
	 * @param        $attribute_value_slug
	 * @param string $append
	 *
	 * @return string
	 */
	public function get_swatch_html( $swatch_data, $attribute_value_slug, $append = '' ) {
		global $fcms_was;

		$attribute_value_slug = sanitize_title( $attribute_value_slug );

		if ( ! isset( $swatch_data['values'][ $attribute_value_slug ] ) ) {
			return $attribute_value_slug;
		}

		if ( ! empty( $append ) ) {
			$append = sprintf( ' <span class="fcms-was-swatch__append">%s</span>', trim( $append ) );
		}

		if ( $swatch_data['swatch_type'] == "colour-swatch" ) {
			$hex        = $swatch_data['values'][ $attribute_value_slug ]['value'];
			$luma_class = $fcms_was->helpers_class()
			                         ->luma( $hex ) > 0.8 ? "fcms-was-swatch__graphic--colour-lighter" : "fcms-was-swatch__graphic--colour-darker";

			return sprintf( '<div class="fcms-was-swatch__container" style="width: %dpx; height: %dpx;"><span class="fcms-was-swatch__graphic fcms-was-swatch__graphic--colour %s" style="background-color:%s" data-fcms-was-tooltip="%s"></span><span class="fcms-was-swatch__text">%s %s</span></div>', $swatch_data['swatch_size']['width'], $swatch_data['swatch_size']['height'], $luma_class, $hex, $swatch_data['values'][ $attribute_value_slug ]['label'], $swatch_data['values'][ $attribute_value_slug ]['label'], $append );
		} elseif ( $swatch_data['swatch_type'] == "image-swatch" ) {
			$image_size         = FCMS_WAS_Helpers::get_image_size_name( 'shop_catalog' );
			$large_preview_size = apply_filters( 'fcms-was-large-preview-size', $image_size );

			$image_thumbnail     = wp_get_attachment_image( $swatch_data['values'][ $attribute_value_slug ]['value'], 'thumbnail', false, array( 'class' => 'fcms-was-swatch__graphic fcms-was-swatch__graphic--image' ) );
			$image_large_preview = isset( $swatch_data['large_preview'] ) && $swatch_data['large_preview'] ? wp_get_attachment_image( $swatch_data['values'][ $attribute_value_slug ]['value'], $large_preview_size, false, array( 'class' => 'fcms-was-swatch__large-preview' ) ) : "";

			return sprintf( '<div class="fcms-was-swatch__container" style="width: %dpx; height: %dpx;">%s <span class="fcms-was-swatch__text">%s %s %s</span></div>', $swatch_data['swatch_size']['width'], $swatch_data['swatch_size']['height'], $image_thumbnail, $image_large_preview, $swatch_data['values'][ $attribute_value_slug ]['label'], $append );
		}

		return $swatch_data['values'][ $attribute_value_slug ]['label'] . $append;
	}

	/**
	 * Helper: Is this swatch type "visual"
	 *
	 * @param string $swatch_type
	 *
	 * @return bool
	 */
	public function is_swatch_visual( $swatch_type ) {
		return $swatch_type == "colour-swatch" || $swatch_type == "image-swatch" ? true : false;
	}

	/**
	 * Helper: get swatch label
	 *
	 * @param string $swatch_type
	 * @param string $empty
	 *
	 * @return string
	 */
	public function get_swatch_label( $swatch_type, $empty ) {
		if ( empty( $swatch_type ) ) {
			return $empty;
		}

		return ucwords( str_replace( '-', ' ', $swatch_type ) );
	}
}