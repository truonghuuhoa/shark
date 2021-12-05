<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Fees
 *
 * This class is for attribute fees.
 *
 * @class          FCMS_WAS_Fees
 * @version        1.0.0
 * @category       Class
 * @author         FCMS
 */
class FCMS_WAS_Fees {
	/**
	 * DB version.
	 *
	 * @var string
	 */
	protected static $db_version = '1.0.0';

	/**
	 * DB name.
	 *
	 * @var string
	 */
	public static $db_name = 'fcms_was_fees';

	/**
	 * Install/update the DB table.
	 */
	public static function install() {
		if ( version_compare( get_site_option( 'fcms_was_db_version' ), self::$db_version, '>=' ) ) {
			return;
		}

		$table_name = self::get_table_name();

		$sql = "CREATE TABLE $table_name (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`product_id` bigint(20) DEFAULT NULL,
		`attribute` varchar(200) DEFAULT NULL,
		`fees` longtext,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( "fcms_was_db_version", self::$db_version );
	}

	/**
	 * Run actions/filters for this class.
	 */
	public static function run() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}

	/**
	 * Run on init.
	 */
	public static function init() {
		if ( apply_filters( 'fcms_was_disable_fees', false ) ) {
			return;
		}

		add_action( 'woocommerce_after_product_attribute_settings', array( __CLASS__, 'add_fees_meta_row' ), 10, 2 );
		add_action( 'woocommerce_update_product', array( __CLASS__, 'on_update_product' ), 10 );
		add_action( 'woocommerce_before_calculate_totals', array( __CLASS__, 'calculate_totals' ), 10 );
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price' ), 10, 3 );
		add_action( 'woocommerce_before_variations_form', array( __CLASS__, 'output_fees_in_form' ), 10 );
		add_filter( 'woocommerce_variation_option_name', array( __CLASS__, 'variation_option_name' ), 10, 4 );
		add_filter( 'woocommerce_variable_price_html', array( __CLASS__, 'variable_price_html' ), 10, 2 );
		add_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'show_variation_price' ), 10, 3 );
		add_filter( 'fcms_was_attribute_fields', array( __CLASS__, 'add_fee_field_to_attribute_term' ), 10, 4 );
	}

	/**
	 * Add fees meta row.
	 *
	 * @param WC_Product_Attribute $attribute
	 * @param int                  $i
	 */
	public static function add_fees_meta_row( $attribute, $i ) {
		$attribute_data = self::get_attribute_data( $attribute );

		if ( ! $attribute->get_variation() ) {
			return;
		}
		?>
		<tr class="fcms-was-fees">
			<td colspan="4">
				<h4><?php _e( 'Fees', 'fcms-was' ); ?></h4>
				<table class="fcms-was-table widefat fixed striped">
					<thead>
					<th><?php _e( 'Value', 'fcms-was' ); ?></th>
					<th><?php _e( 'Fee', 'fcms-was' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</th>
					</thead>
					<tbody>
					<?php foreach ( $attribute_data['values'] as $slug => $value ) { ?>
						<tr>
							<td><?php echo $value['label']; ?></td>
							<td>
								<input name="fcms-was-fees[<?php echo esc_attr( $attribute_data['slug'] ); ?>][<?php echo esc_attr( $slug ); ?>]" class="short wc_input_price" type="number" min="0" step="0.01" onkeypress="return event.charCode >= 48 || 46 === event.charCode" value="<?php echo esc_attr( $value['value'] ); ?>" placeholder="<?php echo ! empty( $value['default'] ) ? esc_attr( sprintf( '%s: %.2f', __( 'Default', 'fcms-was' ), $value['default'] ) ) : ''; ?>">
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		return;
	}

	/**
	 * Get attribute data.
	 *
	 * @param WC_Product_Attribute $attribute
	 * @param int                  $product_id
	 *
	 * @return array
	 */
	public static function get_attribute_data( $attribute, $product_id = null ) {
		if ( ! $product_id ) {
			if ( isset( $_GET['post'] ) ) {
				$product_id = absint( $_GET['post'] );
			} elseif ( isset( $_POST['post_id'] ) ) {
				$product_id = absint( $_POST['post_id'] );
			}
		}

		$return = array(
			'slug'   => sanitize_title( $attribute->get_name() ),
			'values' => array(),
		);

		if ( ! $product_id ) {
			return $return;
		}

		$return['slugs']   = $attribute->get_slugs();
		$return['options'] = $attribute->get_options();

		foreach ( $return['options'] as $index => $option ) {
			$label                = $option;
			$attribute_value_slug = $return['slugs'][ $index ];
			$default              = false;

			if ( $attribute->get_taxonomy() ) {
				$term    = get_term_by( 'id', $option, $attribute->get_taxonomy() );
				$label   = $term->name;
				$default = floatval( FCMS_WAS_Swatches::get_swatch_value( 'taxonomy', 'fee', $term ) );
			}

			$fee = self::get_fees_by_attribute( $product_id, $return['slug'], $attribute_value_slug );

			$return['values'][ $attribute_value_slug ] = array(
				'label'   => $label,
				'value'   => is_numeric( $fee ) ? $fee : '',
				'default' => $default,
			);
		}

		return $return;
	}

	/**
	 * Update product.
	 *
	 * @param $product_id
	 */
	public static function on_update_product( $product_id ) {
		$posted_fees = filter_input( INPUT_POST, 'fcms-was-fees', FILTER_DEFAULT, FILTER_FORCE_ARRAY );

		if ( is_null( $posted_fees ) ) {
			$posted_data = filter_input( INPUT_POST, 'data' );

			if ( ! $posted_data ) {
				return;
			}

			parse_str( $posted_data, $data );

			$posted_fees = isset( $data['fcms-was-fees'] ) ? $data['fcms-was-fees'] : null;
		}

		if ( is_null( $posted_fees ) ) {
			return;
		}

		foreach ( $posted_fees as $attribute => $fees ) {
			self::set_fees( $product_id, $attribute, $fees );
		}
	}

	/**
	 * Set fees.
	 *
	 * @param int    $product_id
	 * @param string $attribute
	 * @param array  $fees
	 */
	public static function set_fees( $product_id, $attribute, $fees ) {
		global $wpdb;

		$fees       = array_filter( $fees, 'is_numeric' );
		$table_name = self::get_table_name();

		$data = array(
			'product_id' => absint( $product_id ),
			'attribute'  => $attribute,
		);

		if ( empty( $fees ) ) {
			$wpdb->delete(
				$table_name,
				array(
					'product_id' => $data['product_id'],
					'attribute'  => $data['attribute'],
				)
			);

			return;
		}

		$data['fees'] = $fees;

		$format = array(
			'%d',
			'%s',
			'%s',
		);

		$current_fees = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE product_id = %d AND attribute = %s",
				$product_id,
				$attribute
			),
			ARRAY_A
		);

		$data['fees'] = serialize( $data['fees'] );

		if ( $current_fees && ! is_wp_error( $current_fees ) ) {
			// Update existing records.
			$where = array(
				'product_id' => $data['product_id'],
				'attribute'  => $data['attribute'],
			);

			$where_format = array( '%d', '%s' );
			$wpdb->update( $table_name, $data, $where, $format, $where_format );
		} else {
			$wpdb->insert(
				$table_name,
				$data,
				$format
			);
		}

	}

	/**
	 * Get all fees for product.
	 *
	 * @param int|WC_Product $product Product or product ID.
	 *
	 * @return bool|array
	 */
	public static function get_fees( $product ) {
		static $fees = array();

		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return false;
		}

		$product_id = $product->get_id();

		if ( ! wp_doing_ajax() && isset( $fees[ $product_id ] ) ) {
			return apply_filters( 'fcms_was_fees', $fees[ $product_id ], $product );
		}

		$fees[ $product_id ] = array();
		$attributes          = FCMS_WAS_Attributes::get_product_attributes( $product_id );

		if ( empty( $attributes ) ) {
			return apply_filters( 'fcms_was_fees', $fees[ $product_id ], $product );
		}

		$product_fees = self::get_product_specific_fees( $product_id );

		// Loop all variable attributes.
		foreach ( $attributes as $attribute_key => $attribute ) {
			$fees[ $product_id ][ $attribute_key ] = array();
			$options = $attribute->get_options();

			// Loop through terms and assign fee.
			foreach ( $options as $option ) {
				if ( $attribute->is_taxonomy() ) {
					$term   = get_term_by( 'id', absint( $option ), $attribute->get_name() );

					if ( empty( $term ) ) {
						continue;
					}

					$option = $term->slug;
				}

				// Use product specific fee if it exists.
				if ( isset( $product_fees[ $attribute_key ] ) && isset( $product_fees[ $attribute_key ][ $option ] ) ) {
					$fees[ $product_id ][ $attribute_key ][ $option ] = $product_fees[ $attribute_key ][ $option ];
					continue;
				}

				// Don't use global fees on the admin side (product edit screen).
				if ( is_admin() ) {
					$fees[ $product_id ][ $attribute_key ][ $option ] = '';
					continue;
				}

				// If it's not a taxonomy, set to 0 and exit.
				if ( ! $attribute->is_taxonomy() ) {
					$fees[ $product_id ][ $attribute_key ][ $option ] = 0;
					continue;
				}

				// Otherwise, check the global term for a fee.
				$fees[ $product_id ][ $attribute_key ][ $option ] = floatval( FCMS_WAS_Swatches::get_swatch_value( 'taxonomy', 'fee', $term ) );
			}
		}

		return apply_filters( 'fcms_was_fees', $fees[ $product_id ], $product );
	}

	/**
	 * Get fees assigned to a product.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool|mixed
	 */
	public static function get_product_specific_fees( $product_id ) {
		global $wpdb;

		static $fees = array();

		if ( ! wp_doing_ajax() && isset( $fees[ $product_id ] ) ) {
			return $fees[ $product_id ];
		}

		$table_name = self::get_table_name();

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE product_id = %d",
				$product_id
			),
			ARRAY_A
		);

		if ( empty( $results ) || is_wp_error( $results ) ) {
			return false;
		}

		$fees[ $product_id ] = array();

		foreach ( $results as $result ) {
			$fees[ $product_id ][ $result['attribute'] ] = array_map( 'floatval', (array) maybe_unserialize( $result['fees'] ) );
		}

		return $fees[ $product_id ];
	}

	/**
	 * Get fees by attribute.
	 *
	 * Static response to reduce DB queries.
	 *
	 * @param int    $product_id
	 * @param string $attribute The attribute name.
	 * @param bool   $value     Return the fee for a specific attribute value.
	 *
	 * @return array|bool
	 */
	public static function get_fees_by_attribute( $product_id, $attribute, $value = false ) {
		if ( ! $product_id || ! $attribute ) {
			return false;
		}

		static $fees = array();

		if ( ! wp_doing_ajax() && isset( $fees[ $product_id ] ) && isset( $fees[ $product_id ][ $attribute ] ) ) {
			if ( $value ) {
				return isset( $fees[ $product_id ][ $attribute ][ $value ] ) ? $fees[ $product_id ][ $attribute ][ $value ] : false;
			}

			return $fees[ $product_id ][ $attribute ];
		}

		$all_fees  = self::get_fees( $product_id );
		$attribute = str_replace( 'attribute_', '', $attribute );

		// If no fees are set, return false.
		if ( empty( $all_fees ) || ! isset( $all_fees[ $attribute ] ) ) {
			$fees[ $product_id ][ $attribute ] = false;

			return $fees[ $product_id ][ $attribute ];
		}

		// Otherwise, assign the fees to the attirbute.
		$fees[ $product_id ][ $attribute ] = $all_fees[ $attribute ];

		// If we fetching all fees for this attribute, return.
		if ( false === $value ) {
			return $fees[ $product_id ][ $attribute ];
		}

		// If the value/term we want is not set, return false.
		if ( ! isset( $all_fees[ $attribute ][ $value ] ) ) {
			$fees[ $product_id ][ $attribute ][ $value ] = false;

			return $fees[ $product_id ][ $attribute ][ $value ];
		}

		// Otherwise, assign the value and return it.
		$fees[ $product_id ][ $attribute ][ $value ] = is_numeric( $all_fees[ $attribute ][ $value ] ) ? floatval( $all_fees[ $attribute ][ $value ] ) : '';

		return $fees[ $product_id ][ $attribute ][ $value ];
	}

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;

		return $wpdb->prefix . self::$db_name;
	}

	/**
	 * Modify cart item prices.
	 *
	 * @param WC_Cart $cart
	 */
	public static function calculate_totals( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Avoiding hook repetition (when using price calculations for example)
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		$cart_items = $cart->get_cart();

		if ( empty( $cart_items ) ) {
			return;
		}

		foreach ( $cart_items as $key => $cart_item ) {
			if ( empty( $cart_item['variation'] ) ) {
				continue;
			}

			// If the price has already been set, use it.
			if ( ! empty( $cart_item['fcms_was_fee'] ) ) {
				$cart_items[ $key ]['data']->set_price( $cart_item['fcms_was_fee'] );
				continue;
			}

			// Context is 'edit' because we want the real price without filters applied.
			$base_price = floatval( $cart_item['data']->get_price( 'edit' ) );
			$base_price = apply_filters( 'fcms_was_calculate_totals_base_price', $base_price );

			foreach ( $cart_item['variation'] as $attribute => $attribute_value ) {
				if ( empty( $attribute_value ) ) {
					continue;
				}

				$attribute = str_replace( 'attribute_', '', $attribute );
				$fees      = self::get_fees_by_attribute( $cart_item['product_id'], $attribute, $attribute_value );

				if ( $fees ) {
					$base_price += $fees;
				}
			}

			$cart_items[ $key ]['data']->set_price( $base_price );
			$cart_items[ $key ]['fcms_was_fee'] = $base_price;
		}

		$cart->set_cart_contents( $cart_items );
	}

	/**
	 * Modify cart item price for mini cart, mainly.
	 *
	 * @param string $price_html
	 * @param array  $cart_item
	 * @param string $cart_item_key
	 *
	 * @return string
	 */
	public static function cart_item_price( $price_html, $cart_item, $cart_item_key ) {
		if ( empty( $cart_item['fcms_was_fee'] ) ) {
			return $price_html;
		}

		return wc_price( $cart_item['fcms_was_fee'] );
	}

	/**
	 * Add fee to product terms (variation dropdowns).
	 *
	 * @param array  $terms
	 * @param int    $product_id
	 * @param string $taxonomy
	 * @param array  $args
	 *
	 * @return array
	 */
	public static function get_product_terms( $terms, $product_id, $taxonomy, $args ) {
		if ( is_admin() || strpos( $taxonomy, 'pa_' ) === false ) {
			return $terms;
		}

		if ( empty( $terms ) ) {
			return $terms;
		}

		foreach ( $terms as $index => $term ) {
			$fee = self::get_fees_by_attribute( $product_id, $taxonomy, $term->slug );

			if ( ! $fee ) {
				continue;
			}

			$terms[ $index ]->name = self::add_fee_to_label( $term->name, $fee );
		}

		return $terms;
	}

	/**
	 * Add fee to swatch label (taxonomy).
	 *
	 * @param string       $term_name
	 * @param WP_Term|null $term
	 * @param string       $attribute_slug
	 * @param WC_Product   $product
	 *
	 * @return string
	 */
	public static function variation_option_name( $term_name, $term = null, $attribute_slug = null, $product = null ) {
		if ( empty( $product ) ) {
			global $product;
		}

		// Backwards compatibility check (as term, attribute_slug and product are all optional).
		if ( ( ! $product instanceof WC_Product ) ||
		     ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], array(
				     'woocommerce_load_variations',
				     'woocommerce_add_variation'
			     ) ) ) ||
		     ( is_admin() && ! wp_doing_ajax() )
		) {
			return $term_name;
		}

		$product_id = $product->get_id();

		if ( is_a( $term, 'WP_Term' ) ) {
			$term_slug = $term->slug;
		} else {
			$term_slug      = $term_name;
			$attribute_slug = sanitize_title( $attribute_slug );
		}

		$fee = self::get_fees_by_attribute( $product_id, sanitize_title( $attribute_slug ), $term_slug );

		if ( ! $fee ) {
			return $term_name;
		}

		return self::add_fee_to_label( $term_name, $fee );
	}

	/**
	 * Add fee to label.
	 *
	 * @param string $label
	 * @param float  $fee
	 *
	 * @return string
	 */
	public static function add_fee_to_label( $label, $fee ) {
		$prefix = $fee > 0 ? '+' : '';

		return strip_tags( sprintf( '%s (%s%s)', $label, $prefix, wc_price( $fee ) ) );
	}

	/**
	 * Modify variable price.
	 *
	 * @param string              $price
	 * @param WC_Product_Variable $product
	 *
	 * @return string
	 */
	public static function variable_price_html( $price, $product ) {
		if ( ! self::has_fees( $product->get_id() ) ) {
			return $price;
		}

		$min_price = FCMS_WAS_Helpers::get_min_price( $product );

		if ( ! $min_price ) {
			return $price;
		}

		return apply_filters( 'fcms_was_price_from', sprintf( '%s: %s', __( 'From', 'fcms-was' ), wc_price( $min_price ) ), $product );
	}

	/**
	 * Does this product have fees associated to it?
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public static function has_fees( $product_id ) {
		static $fees = array();

		if ( isset( $fees[ $product_id ] ) ) {
			return $fees[ $product_id ];
		}

		$product_fees = self::get_fees( $product_id );

		if ( empty( $product_fees ) ) {
			return false;
		}

		foreach( $product_fees as $attribute => $fees ) {
			$fees = array_filter( $fees );

			if ( ! empty( $fees ) ) {
				continue;
			}

			unset( $product_fees[ $attribute ] );
		}

		$product_fees = array_filter( $product_fees );

		return ! empty( $product_fees ) ? $product_fees : false;
	}

	/**
	 * Output fees in form.
	 */
	public static function output_fees_in_form() {
		global $product;

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return;
		}

		$fees = self::has_fees( $product->get_id() );

		if ( ! $fees ) {
			return;
		}

		$fees_processed = array();

		foreach ( $fees as $attribute_key => $attribute_values ) {
			foreach ( $attribute_values as $attribute_value => $fee ) {
				$fees_processed[ $attribute_key ][ $attribute_value ] = array(
					'default'             => wc_get_price_to_display( $product, array( 'price' => $fee ) ),
					'price_including_tax' => wc_get_price_including_tax( $product, array( 'price' => $fee ) ),
					'price_excluding_tax' => wc_get_price_excluding_tax( $product, array( 'price' => $fee ) ),
				);
			}
		} ?>
		<script class="fcms-was-fees" type="application/json"><?php echo json_encode( $fees_processed ); ?></script>
		<?php
	}

	/**
	 * Show variation price on product page.
	 *
	 * @param bool                 $show
	 * @param WC_Product_Variable  $product
	 * @param WC_Product_Variation $variation
	 *
	 * @return bool
	 */
	public static function show_variation_price( $show, $product, $variation ) {
		if ( ! self::has_fees( $product->get_id() ) ) {
			return $show;
		}

		return true;
	}

	/**
	 * Add fee field to gloabl attribute.
	 *
	 * @param $fields
	 * @param $is_edit_page
	 * @param $term
	 * @param $swatch_type
	 *
	 * @return mixed
	 */
	public static function add_fee_field_to_attribute_term( $fields, $is_edit_page, $term, $swatch_type ) {
		$value = $is_edit_page ? FCMS_WAS_Swatches::get_swatch_value( 'taxonomy', 'fee', $term ) : "";

		$fields[] = array(
			'label'       => sprintf( '<label for="fcms-was-fee-field">%s (%s)</label>', __( 'Fee', 'fcms-was' ), get_woocommerce_currency_symbol() ),
			'field'       => sprintf( '<input type="number" name="fcms_was_term_meta[fee]" value="%s" class="short wc_input_price" type="number" min="0" step="0.01" onkeypress="return event.charCode >= 48 || 46 === event.charCode">', esc_attr( $value ) ),
			'description' => '',
		);

		return $fields;
	}
}