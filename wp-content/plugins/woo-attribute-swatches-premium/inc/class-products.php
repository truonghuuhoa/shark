<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Products
 *
 * This class is for anything product related
 *
 * @class          FCMS_WAS_Products
 * @version        1.0.0
 * @category       Class
 * @author         FCMS
 */
class FCMS_WAS_Products {
	/**
	 * Swatch data for a single (current) product
	 *
	 * @var array $swatch_data
	 */
	public $swatch_data = array();

	/**
	 * Run actions/filters for this class
	 */
	public function run() {
		if ( is_admin() ) {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'product_tab_fields' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_fields' ) );
			add_action( 'wp_ajax_fcms_was_get_product_attribute_fields', array( $this, 'get_product_attribute_fields' ) );
		} else {
			add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'dropdown_variation_attribute_options_args' ), 10, 1 );
			add_filter( 'post_class', array( $this, 'add_was_class' ), 10, 3 );
		}

		add_action( 'init', array( $this, 'position_swatches_in_loop' ) );
	}

	/**
	 * Admin: Add tab to product edit page
	 */
	public function product_tab() {
		global $post, $fcms_was;

		printf( '<li class="%1$s-options-tab show_if_variable"><a href="#%1$s-options"><span>%2$s</span></a></li>', $fcms_was->slug, __( 'Swatches', 'fcms-was' ) );
	}

	/**
	 * Admin: Add custom product fields
	 */
	public function product_tab_fields() {
		global $woocommerce, $post, $fcms_was;

		include_once( ICONIC_WAS_PATH . 'inc/admin/product-tab.php' );
	}

	/**
	 * Admin: Save custom product fields
	 *
	 * @param int $product_id
	 */
	public function save_product_fields( $product_id ) {
		$product_settings = array();

		if ( isset( $_POST['fcms-was'] ) ) {
			if ( empty( $_POST['fcms-was'] ) ) {
				return;
			}

			$product = wc_get_product( $product_id );

			foreach ( $_POST['fcms-was'] as $attribute_slug => $value ) {
				if ( ! empty( $value['swatch_type'] ) ) {
					$product_settings[ $attribute_slug ] = $value;
				} else {
					$product_settings[ $attribute_slug ] = array( 'swatch_type' => '' );
				}
			}

			$product->update_meta_data( '_fcms-was', $product_settings );
			$product->save();
		}
	}

	/**
	 * Admin: Get product swatch data for attribute
	 *
	 * @param int    $product_id
	 * @param string $attribute_slug
	 *
	 * @return array
	 */
	public function get_product_swatch_data_for_attribute( $product_id, $attribute_slug ) {
		if ( ! isset( $this->swatch_data[ $product_id ] ) ) {
			$product                          = wc_get_product( $product_id );
			$this->swatch_data[ $product_id ] = $product->get_meta( '_fcms-was', true );
		}

		if ( isset( $this->swatch_data[ $product_id ][ $attribute_slug ] ) ) {
			return $this->swatch_data[ $product_id ][ $attribute_slug ];
		}

		return array(
			'swatch_type' => "",
			'values'      => false,
		);
	}

	/**
	 * Ajax: Get product attribute fields
	 */
	public function get_product_attribute_fields() {
		global $fcms_was;

		$return = array(
			'success' => true,
			'fields'  => false,
		);

		$attributes = $fcms_was->attributes_class()->get_variation_attributes_for_product( $_POST['product_id'] );
		$attribute  = isset( $attributes[ $_POST['attribute_slug'] ] ) ? $attributes[ $_POST['attribute_slug'] ] : false;

		$saved_values = $fcms_was->swatches_class()
		                           ->get_product_swatch_data( $_POST['product_id'], $_POST['attribute_slug'] );
		$swatch_type  = $_POST['swatch_type'];

		if ( $saved_values && ! empty( $saved_values ) ) {
			// Remove values if we're loading a different swatch type.
			$saved_values['values'] = $swatch_type === $saved_values['swatch_type'] ? $saved_values['values'] : array();
		}

		ob_start();
		include( ICONIC_WAS_PATH . 'inc/admin/product-attribute-options.php' );
		$return['fields'] = ob_get_clean();

		wp_send_json( $return );
	}

	/**
	 * Position swatches in loop
	 */
	public function position_swatches_in_loop() {
		$loop_position = apply_filters( 'fcms_was_loop_position', 'woocommerce_after_shop_loop_item' );
		$loop_priority = apply_filters( 'fcms_was_loop_priority', 8 );
		add_action( $loop_position, array( $this, 'add_swatches_to_loop' ), $loop_priority );
	}

	/**
	 * Add swatches to loop based on settings for attribute
	 */
	public function add_swatches_to_loop() {
		global $product, $fcms_was;

		if ( apply_filters( 'fcms_was_hide_loop_swatches', false, $product ) ) {
			return;
		}

		$original_product = $product;

		// SSV integration: Use parent product temporarily.
		if ( is_a( $product, 'WC_Product_Variation' ) ) {
			$parent_product = $product->get_parent_id();
			$product        = wc_get_product( $parent_product );
		}

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return;
		}

		$attributes = $product->get_variation_attributes();

		if ( ! $attributes ) {
			// SSV integration: Revert to original product variation.
			if ( ! empty( $original_product ) ) {
				$product = $original_product;
			}

			return;
		}

		foreach ( $attributes as $attribute_slug => $attribute_terms ) {
			if ( empty( $attribute_terms ) ) {
				continue;
			}

			if ( $product && taxonomy_exists( $attribute_slug ) ) {
				// Get ordered terms if this is a taxonomy.
				$attribute_terms_raw = wc_get_product_terms(
					$product->get_id(),
					$attribute_slug,
					array(
						'fields' => 'all', // We use "all" instead of "slug" because it causes a php warning, bug on WC's side.
					)
				);
				$attribute_terms     = wp_list_pluck( $attribute_terms_raw, 'slug' );
			}

			$attribute_slug_raw  = $attribute_slug;
			$attribute_slug      = $fcms_was->attributes_class()->format_attribute_slug( $attribute_slug );
			$product_id          = $product->get_id();
			$swatch_data         = $fcms_was->swatches_class()->get_swatch_data(
				array(
					'product_id'     => $product_id,
					'attribute_slug' => $attribute_slug,
				)
			);

			if ( empty( $swatch_data['loop'] ) ) {
				continue;
			}

			$product_url       = $product->get_permalink();
			$swatch_type       = 'radio-buttons' === $swatch_data['swatch_type'] ? 'text-swatch' : $swatch_data['swatch_type'];
			$swatch_shape      = $fcms_was->swatches_class()->get_swatch_option( 'swatch_shape', $attribute_slug );
			$tooltips          = (bool) $fcms_was->swatches_class()->get_swatch_option( 'tooltips', $attribute_slug );
			$large_preview     = (bool) $fcms_was->swatches_class()->get_swatch_option( 'large_preview', $attribute_slug );
			$visual            = $fcms_was->swatches_class()->is_swatch_visual( $swatch_type ) ? 'fcms-was-swatches--visual' : false;
			$tooltips          = $visual && ( $tooltips || $large_preview ) ? 'fcms-was-swatches--tooltips' : false;
			$shape             = $visual && 'round' === $swatch_shape ? 'fcms-was-swatches--round' : 'fcms-was-swatches--square';
			$loop_method       = empty( $swatch_data['loop-method'] ) ? 'link' : $swatch_data['loop-method'];
			$available_terms   = $this->get_available_terms_for_product( $product->get_id(), $attribute_slug );
			$has_any_variation = true === $available_terms || in_array( null, $available_terms );
			$oos_terms         = $has_any_variation ? array() : $this->get_out_of_stock_terms( $product->get_id(), $attribute_slug_raw );
			$style             = $fcms_was->settings['style_general_selected'];
			$handle_overflow   = isset( $swatch_data['handle-overflow'] ) ? $swatch_data['handle-overflow'] : 'stacked';
			$overflow_class    = sprintf( 'fcms-was-swatches--%s', $handle_overflow );
			$loading_class     = 'stacked' !== $handle_overflow ? 'fcms-was-swatches--loading' : '';
			$height            = 'stacked' !== $handle_overflow ? $swatch_data['swatch_size']['height'] + 8 . 'px' : 'auto';
			$swatches_html     = sprintf( '<ul class="fcms-was-swatches fcms-was-swatches--loop fcms-was-swatches--%s fcms-was-swatches--%s %s %s %s %s %s" data-attribute="%s" data-overflow="%s" style="height: %s;" >', $swatch_type, esc_attr( $style ), $visual, $tooltips, $shape, esc_attr( $overflow_class ), $loading_class, $attribute_slug, esc_attr( $handle_overflow ), $height );

			foreach ( $attribute_terms as $attribute_term ) {
				$out_of_stock          = in_array( $attribute_term, $oos_terms );
				$disabled_swatch_class = $out_of_stock ? 'fcms-was-swatch--disabled' : '';
				$oos_item_class        = $out_of_stock ? 'fcms-was-swatches__item--out-of-stock' : '';

				if ( ! $attribute_term ) {
					continue;
				}

				// If no variation is enabled, skip the swatch.
				if ( ! $has_any_variation && ! in_array( $attribute_term, $available_terms ) ) {
					continue;
				}

				$first_variation_id = $this->get_first_variation_id_for_attribute_value( $product, $attribute_slug, $attribute_term );

				// If there's no available variaiton, skip the swatch.
				if ( ! $has_any_variation && ! $first_variation_id ) {
					continue;
				}

				$variation_image_url     = false;
				$attribute_slug_prefixed = $fcms_was->attributes_class()->format_attribute_slug( $attribute_slug, true );
				$url                     = esc_url( add_query_arg( array( $attribute_slug_prefixed => $attribute_term ), $product_url ) );
				$swatch_html             = $fcms_was->swatches_class()->get_swatch_html( $swatch_data, $attribute_term );

				if ( 'image' === $loop_method ) {
					$variation_image_url = $this->get_variation_image_by_attribute( $product, $attribute_slug, $attribute_term );
					$url                 = $variation_image_url ? $variation_image_url[0] : $url;

					if ( $variation_image_url ) {
						$swatch_item_html = sprintf( '<li class="fcms-was-swatches__item %s"><a href="%s" class="fcms-was-swatch fcms-was-swatch--follow fcms-was-swatch--change-image fcms-was-swatch--%s %s" data-srcset="%s" data-sizes="%s">%s</a></li>', $oos_item_class, $url, $swatch_data['swatch_type'], $disabled_swatch_class, $variation_image_url['srcset'], $variation_image_url['sizes'], $swatch_html );
					}
				}

				if ( 'link' === $loop_method || ! $variation_image_url ) {
					$swatch_item_html = sprintf( '<li class="fcms-was-swatches__item %s"><a href="%s" class="fcms-was-swatch fcms-was-swatch--follow fcms-was-swatch--%s %s">%s</a></li>', $oos_item_class, $url, $swatch_data['swatch_type'], $disabled_swatch_class, $swatch_html );
				}

				$swatches_html .= apply_filters( 'fcms_was_swatch_item_loop_html', $swatch_item_html, $swatch_data, $attribute_term, $product );
			}

			if ( 'single-line' === $handle_overflow ) {
				$swatches_html .= sprintf( '<li class="fcms-was-swatches__item fcms-was-swatches__item--dummy"><a href="%s" class="fcms-was-swatch fcms-was-swatch--dummy">+%s</a></li>', esc_url( $original_product->get_permalink() ), 0 );
			}

			$swatches_html .= '</ul>';

			echo apply_filters( 'fcms_was_swatches_loop_html', $swatches_html, $swatch_data, $product );
		}

		// SSV integration: Revert to original product variation.
		if ( ! empty( $original_product ) ) {
			$product = $original_product;
		}
	}

	/**
	 * Get available variation IDs for product.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return array
	 */
	public static function get_available_variation_ids( $product_id ) {
		$cache_key               = "fcms_was_available_variation_ids_{$product_id}";
		$available_variation_ids = wp_cache_get( $cache_key );

		if ( false !== $available_variation_ids ) {
			return $available_variation_ids;
		}

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT p.ID FROM $wpdb->posts p, $wpdb->postmeta pm,  $wpdb->postmeta pm2
			 WHERE p.post_status = 'publish'
			 AND p.ID in (SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_parent = %d)
			 AND p.ID = pm.post_id
			 AND ( pm.meta_key = '_stock_status' )
			 AND pm2.post_id = p.ID
			 AND pm2.meta_key = '_price'",
			$product_id
		);

		$results = $wpdb->get_results( $query );

		if ( is_wp_error( $results ) || empty( $results ) ) {
			wp_cache_set( $cache_key, array() );

			return array();
		}

		$available_variation_ids = wp_list_pluck( $results, 'ID' );

		wp_cache_set( $cache_key, $available_variation_ids );

		return $available_variation_ids;
	}

	/**
	 * Returns the terms which are active.
	 *
	 * @param int    $product_id Product ID of the variable/parent product.
	 * @param string $taxonomy   The taxonomy whose active terms you want.
	 *
	 * @return bool|array Array of active terms in the given taxonomy for the given Variable product. Or false if no variations available. Or true if all terms are available (variation is set to "any...").
	 */
	public function get_available_terms_for_product( $product_id, $taxonomy ) {
		global $wpdb;

		$cache_key       = "fcms_was_available_terms_{$product_id}_{$taxonomy}";
		$available_terms = wp_cache_get( $cache_key );

		if ( false !== $available_terms ) {
			return $available_terms;
		}

		$variation_ids = self::get_available_variation_ids( $product_id );

		// There's no active variations.
		if ( empty( $variation_ids ) ) {
			wp_cache_set( $cache_key, false );

			return array();
		}

		$taxonomy = strpos( $taxonomy, 'attribute_' ) !== 0 ? 'attribute_' . $taxonomy : $taxonomy;

		$query = $wpdb->prepare(
			"SELECT DISTINCT meta_value, meta_key from $wpdb->postmeta 
			 WHERE meta_key = %s",
			$taxonomy
		);

		$placeholders = array_fill( 0, count( $variation_ids ), '%d' );
		$format       = implode( ',', $placeholders );

		$query .= $wpdb->prepare( " AND post_id IN($format)", $variation_ids );

		$available_terms = $wpdb->get_results( $query );

		// If no terms are found then the variation is probably set to "Any...". This can happen
		// if the attribute is added after the variations are created.
		if ( empty( $available_terms ) ) {
			wp_cache_set( $cache_key, true );

			return true;
		}

		$available_terms = wp_list_pluck( $available_terms, 'meta_value' );

		// If meta value is empty or null, the variation is set to "Any...".
		if ( in_array( '', $available_terms ) || in_array( null, $available_terms ) ) {
			wp_cache_set( $cache_key, true );

			return true;
		}

		// Otherwise, return the terms which *are* set/available.
		wp_cache_set( $cache_key, $available_terms );

		return $available_terms;
	}

	/**
	 * Returns the terms which are not in stock.
	 *
	 * @param int    $product_id Product ID of the variable/parent product.
	 * @param string $attribute  The attribute whose out-of-stock terms you want.
	 *
	 * @return array array of out-of-stock terms
	 */
	public function get_out_of_stock_terms( $product_id, $attribute ) {
		global $wpdb, $fcms_was;
		
		$attribute_meta_key = $fcms_was->attributes_class()->format_attribute_slug( $attribute, true ); 
		$all_terms          = $fcms_was->attributes_class()->get_attribute_terms( $attribute, $product_id ); 
		
		$query = $wpdb->prepare(
			"SELECT pm2.meta_value as terms, count(pm2.meta_value) as c from $wpdb->posts p, $wpdb->postmeta pm1, $wpdb->postmeta pm2, $wpdb->postmeta pm3
					WHERE p.ID = pm1.post_id
					AND  p.ID = pm2.post_id
					AND p.ID = pm3.post_id
					AND (pm1.meta_key = '_stock_status' and pm1.meta_value IN ( 'instock', 'onbackorder' ))
					AND (pm2.meta_key = %s)
					AND (pm3.meta_key = '_price')
					AND p.post_parent = %d
					AND p.post_status = 'publish'

					GROUP BY pm2.meta_value",
			$attribute_meta_key, $product_id );

		$in_stock_terms     = $wpdb->get_col( $query );
		$not_in_stock_terms = array_diff( $all_terms, $in_stock_terms );

		return $not_in_stock_terms;
	}
	
	/**
	 * Get Variation Image by Attribute
	 *
	 * @return bool|array
	 */
	public function get_variation_image_by_attribute( $product, $attribute_name, $attribute_value ) {
		global $fcms_was;

		$attribute_name = $fcms_was->attributes_class()->format_attribute_slug( $attribute_name );

		$first_variation_id = $this->get_first_variation_id_for_attribute_value( $product, $attribute_name, $attribute_value );

		if ( ! $first_variation_id ) {
			return false;
		}

		$post_thumbnail_id = get_post_thumbnail_id( $first_variation_id );

		if ( ! $post_thumbnail_id ) {
			return false;
		}

		$thumbnail_size = FCMS_WAS_Helpers::get_image_size_name( 'shop_thumbnail' );
		$post_thumbnail = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );

		if ( ! $post_thumbnail ) {
			return false;
		}

		$post_thumbnail['srcset'] = wp_get_attachment_image_srcset( $post_thumbnail_id, $thumbnail_size );
		$post_thumbnail['sizes']  = wp_get_attachment_image_sizes( $post_thumbnail_id, $thumbnail_size );

		return $post_thumbnail;
	}

	/**
	 * Get first variation for attribute value.
	 *
	 * @param WC_Product_Variable $product
	 * @param string              $attribute_name
	 * @param string              $attribute_value
	 *
	 * @return bool|int
	 */
	public function get_first_variation_id_for_attribute_value( $product, $attribute_name, $attribute_value ) {
		$product_id = $product->get_id();
		$id         = hash( 'md5', $product_id . $attribute_name . $attribute_value );

		static $variation_ids = array();

		if ( isset( $variation_ids[ $id ] ) ) {
			return $variation_ids[ $id ];
		}

		global $wpdb;

		$variation_ids[ $id ] = false;
		
		if( substr( $attribute_name, 0, 10  ) !== "attribute_") {
			$attribute_name = "attribute_" . $attribute_name;
		}

		$sql = "
		SELECT pm.post_id
		FROM $wpdb->postmeta as pm
		INNER JOIN $wpdb->posts AS p ON ( p.ID = pm.post_id )
		WHERE pm.meta_key = %s
		AND pm.meta_value = %s
		AND p.post_status = 'publish'
		AND p.post_type = 'product_variation'
		AND p.post_parent = %d
		ORDER BY p.menu_order ASC
		LIMIT 1
		";
		$sql = $wpdb->prepare( $sql, $attribute_name, $attribute_value, $product_id ) ;
		$result = $wpdb->get_row( $sql );

		if ( empty( $result ) || is_wp_error( $result ) ) {
			return $variation_ids[ $id ];
		}

		$variation_ids[ $id ] = absint( $result->post_id );

		return $variation_ids[ $id ];
	}

	/**
	 * Modify Dropdown variation attribute options args
	 *
	 * Some themes add the label into the dropdown,
	 * let's remove it!
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function dropdown_variation_attribute_options_args( $args ) {
		$args['show_option_none'] = __( 'Choose an option', 'woocommerce' );

		return $args;
	}

	/**
	 * Get swatch meta for product.
	 *
	 * @param int $product_id
	 *
	 * @return mixed
	 */
	public function get_swatch_meta( $product_id ) {
		$product     = wc_get_product( $product_id );
		$swatch_meta = $product->get_meta( '_fcms-was', true );

		if ( ! empty( $swatch_meta ) ) {
			$product = wc_get_product( $product_id );

			foreach ( $swatch_meta as $attribute_slug => $data ) {
				if ( empty( $data['values'] ) ) {
					continue;
				}

				foreach ( $data['values'] as $attribute_term_slug => $value ) {
					$term                                                                      = get_term_by( 'slug', $attribute_term_slug, $attribute_slug );
					$swatch_meta[ $attribute_slug ]['values'][ $attribute_term_slug ]['label'] = apply_filters( 'woocommerce_variation_option_name', $value['label'], $term ? $term : null, $attribute_slug, $product );
				}
			}
		}

		return apply_filters( 'fcms_was_swatch_meta', $swatch_meta, $product_id );
	}

	/**
	 * Has product-specific swatch meta.
	 *
	 * @param $product_id
	 * @param $attribute
	 *
	 * @return bool
	 */
	public function has_swatch_meta( $product_id, $attribute ) {
		$swatch_meta = $this->get_swatch_meta( $product_id );

		if ( ! $swatch_meta ) {
			return false;
		}

		if ( empty( $swatch_meta[ $attribute ] ) || empty( $swatch_meta[ $attribute ]['swatch_type'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add a flag class to the product suggesting swatches are enabled for the given product.
	 *
	 * @param array $classes    Classes.
	 * @param array $class      Class.
	 * @param int   $product_id Post ID.
	 *
	 * @return array $classes Classes.
	 */
	public static function add_was_class( $classes, $class, $product_id ) {
		if ( ! is_singular( 'product' ) ) {
			return $classes;
		}

		if ( self::is_was_enabled( $product_id ) ) {
			$classes[] = 'fcms-was-has-swatches';
		}

		return $classes;
	}

	/**
	 * Is WAS enabled for given product?
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	public static function is_was_enabled( $product_id ) {
		global $fcms_was;

		$product = wc_get_product( $product_id );

		if ( empty( $product ) ) {
			return false;
		}

		if ( ! $product->is_type( 'variable' ) ) {
			return false;
		}

		$attributes = $product->get_variation_attributes();

		if ( empty( $attributes ) ) {
			return false;
		}

		foreach ( $attributes as $attribute_slug => $data ) {
			$swatch_data = $fcms_was->swatches_class()->get_swatch_data(
				array(
					'product_id'     => $product_id,
					'attribute_slug' => $attribute_slug,
				)
			);

			// Even if one of the attribute's data is available, it means WAS is enabled for the product.
			if ( ! empty( $swatch_data['swatch_type'] ) ) {
				return true;
			}
		}

		return false;
	}
}
