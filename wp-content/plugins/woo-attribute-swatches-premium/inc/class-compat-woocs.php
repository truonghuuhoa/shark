<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCS compatibility.
 */
class FCMS_WAS_Compat_WooCS {

	/**
	 * Run.
	 */
	public static function run() {
		add_action( 'plugins_loaded', array( __CLASS__, 'hooks' ) );
	}

	/**
	 * Hooks.
	 */
	public static function hooks() {
		// PHPCS:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		global $WOOCS;

		if ( empty( $WOOCS ) || is_admin() ) {
			return;
		}

		add_filter( 'fcms_was_fees', array( __CLASS__, 'currency_convert_fees' ), 2, 10 );
		add_filter( 'fcms_was_calculate_totals_base_price', array( __CLASS__, 'currency_convert_price' ), 1, 10 );
	}

	/**
	 * Convert fees.
	 *
	 * @param array      $fees    Fees.
	 * @param WC_Product $product Product.
	 *
	 * @return array $fees Updated fees.
	 */
	public static function currency_convert_fees( $fees, $product ) {
		global $WOOCS;

		foreach ( $fees as $attribute_key => &$attribute_fees ) {
			foreach ( $attribute_fees as $term => $term_fee ) {
				$attribute_fees[ $term ] = $WOOCS->woocs_exchange_value( $term_fee );
			}
		}

		return $fees;
	}


	/**
	 * Currency Convert Fees.
	 *
	 * @param float $base_price Base price of product.
	 *
	 * @return float currency converted base price of product.
	 */
	public static function currency_convert_price( $base_price ) {
		global $WOOCS;

		if ( ! $WOOCS ) {
			return $base_price;
		}

		return $WOOCS->woocs_exchange_value( $base_price );
	}
}
