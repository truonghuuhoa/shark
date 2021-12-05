<?php
/**
 * Plugin Name: WooCommerce Attribute Swatches by FCMS
 * Plugin URI: https://fcmswp.com
 * Description: Swatches for your variable products.
 * Version: 1.3.5
 * Author: FCMS <support@fcmswp.com>
 * Author URI: https://fcmswp.com
 * Text Domain: fcms-was
 * WC requires at least: 2.6.14
 * WC tested up to: 5.5.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	wp_die();
}

class FCMS_Woo_Attribute_Swatches {
	/**
	 * Long name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string $name
	 */
	protected $name = "WooCommerce Attribute Swatches";

	/**
	 * Short name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string $shortname
	 */
	protected $shortname = "Attribute Swatches";

	/**
	 * Slug - Hyphen
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string $slug
	 */
	public $slug = "fcms-was";

	/**
	 * Class prefix
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string $class_prefix
	 */
	protected $class_prefix = "FCMS_WAS_";

	/**
	 * Version.
	 *
	 * @var string
	 */
	public static $version = '1.3.5';

	/**
	 * Plugin URL
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string $plugin_url trailing slash
	 */
	protected $plugin_url;

	/**
	 * Attributes
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var FCMS_WAS_Attributes
	 */
	public $attributes;

	/**
	 * Helpers
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var FCMS_WAS_Helpers
	 */
	public $helpers;

	/**
	 * Products
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var FCMS_WAS_Products
	 */
	public $products;

	/**
	 * Swatches
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var FCMS_WAS_Swatches
	 */
	public $swatches;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Construct
	 */
	public function __construct() {
		if ( ! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) && ! $this->is_plugin_active( 'woocommerce-old/woocommerce.php' ) ) {
			return;
		}

		$this->define_constants();
		$this->load_classes();
		$this->install();

		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'init', array( $this, 'init_hook' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded_hook' ) );
	}

	/**
	 * Load textdomain
	 */
	public function textdomain() {
		load_plugin_textdomain( 'fcms-was', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load classes
	 */
	private function load_classes() {
		require_once( ICONIC_WAS_INC_PATH . 'class-core-autoloader.php' );

		FCMS_WAS_Core_Autoloader::run( array(
			'prefix'   => 'FCMS_WAS_',
			'inc_path' => ICONIC_WAS_INC_PATH,
		) );

		FCMS_WAS_Core_Settings::run( array(
			'vendor_path'   => ICONIC_WAS_VENDOR_PATH,
			'title'         => 'WooCommerce Attribute Swatches',
			'version'       => self::$version,
			'menu_title'    => 'Attribute Swatches',
			'settings_path' => ICONIC_WAS_INC_PATH . 'admin/settings.php',
			'option_group'  => 'fcms_was',
			'docs'          => array(
				'collection'      => '/collection/134-woocommerce-attribute-swatches',
				'troubleshooting' => '/category/139-troubleshooting',
				'getting-started' => '/category/137-getting-started',
			),
			'cross_sells'   => array(
				'fcms-woo-show-single-variations',
				'fcms-woothumbs',
			),
		) );

		$this->attributes_class()->run();
		$this->products_class()->run();
		FCMS_WAS_Compat_WPML::run();
		FCMS_WAS_Compat_Flatsome::run();
		FCMS_WAS_Compat_Woo_Variations_Table::run();
		FCMS_WAS_Compat_Oceanwp::run();
		FCMS_WAS_Fees::run();
		FCMS_WAS_Shortcodes::run();
		FCMS_WAS_Compat_WooCS::run();
	}

	/**
	 * Install plugin.
	 */
	private function install() {
		add_action( 'plugins_loaded', array( 'FCMS_WAS_Fees', 'install' ) );
	}

	/**
	 * Class: Swatches
	 *
	 * Access the swatches class without loading multiple times
	 */
	public function swatches_class() {
		if ( ! $this->swatches ) {
			$this->swatches = new FCMS_WAS_Swatches;
		}

		return $this->swatches;
	}

	/**
	 * Class: Products
	 *
	 * Access the products class without loading multiple times
	 */
	public function products_class() {
		if ( ! $this->products ) {
			$this->products = new FCMS_WAS_Products;
		}

		return $this->products;
	}

	/**
	 * Class: Attributes
	 *
	 * Access the attributes class without loading multiple times
	 */
	public function attributes_class() {
		if ( ! $this->attributes ) {
			$this->attributes = new FCMS_WAS_Attributes;
		};

		return $this->attributes;
	}

	/**
	 * Class: Helpers
	 *
	 * Access the helpers class without loading multiple times
	 */
	public function helpers_class() {
		if ( ! $this->helpers ) {
			$this->helpers = new FCMS_WAS_Helpers;
		}

		return $this->helpers;
	}

	/**
	 * Autoloader
	 *
	 * Classes should reside within /inc and follow the format of
	 * FCMS_The_Name ~ class-the-name.php or FCMS_WAS_The_Name ~ class-the-name.php
	 */
	private function autoload( $class_name ) {
		/**
		 * If the class being requested does not start with our prefix,
		 * we know it's not one in our project
		 */
		if ( 0 !== strpos( $class_name, 'FCMS_' ) && 0 !== strpos( $class_name, $this->class_prefix ) ) {
			return;
		}

		$file_name = strtolower( str_replace( array(
			$this->class_prefix,
			'FCMS_',
			'_',
		),      // Prefix | Plugin Prefix | Underscores
			array( '', '', '-' ),                              // Remove | Remove | Replace with hyphens
			$class_name ) );

		// Compile our path from the current location
		$file = dirname( __FILE__ ) . '/inc/class-' . $file_name . '.php';

		// If a file is found
		if ( file_exists( $file ) ) {
			// Then load it up!
			require( $file );
		}
	}

	/**
	 * Set constants
	 */
	public function define_constants() {
		$this->define( 'ICONIC_WAS_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'ICONIC_WAS_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'ICONIC_WAS_INC_PATH', ICONIC_WAS_PATH . 'inc/' );
		$this->define( 'ICONIC_WAS_VENDOR_PATH', ICONIC_WAS_INC_PATH . 'vendor/' );
		$this->define( 'ICONIC_WAS_BASENAME', plugin_basename( __FILE__ ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name
	 * @param string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Init.
	 */
	public function init_hook() {
		$this->settings = FCMS_WAS_Core_Settings::$settings;
	}

	/**
	 * Plugins Loaded.
	 */
	public function plugins_loaded_hook() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );

			add_filter( 'jck_qv_modal_classes', array( $this, 'qv_modal_classes' ), 10, 1 );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ) );
			add_filter( 'post_class', array( $this, 'add_accordion_class' ) );
		}
	}

	/**
	 * Frontend: Styles
	 */
	public function frontend_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'fcms_was_load_slider_assets', true ) ) {
			wp_register_style( 'flickity', ICONIC_WAS_URL . 'assets/vendor/flickity/flickity' . $suffix . '.css', array(), self::$version );
			wp_enqueue_style( 'flickity' );
		}

		wp_register_style( 'fcms-was-styles', ICONIC_WAS_URL . 'assets/frontend/css/main' . $suffix . '.css', array(), self::$version );
		wp_enqueue_style( 'fcms-was-styles' );
	}

	/**
	 * Frontend: Scripts
	 */
	public function frontend_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'fcms_was_load_slider_assets', true ) ) {
			wp_register_script( 'flickity', ICONIC_WAS_URL . 'assets/vendor/flickity/flickity.pkgd' . $suffix . '.js', array( 'jquery' ), self::$version, true );
			wp_enqueue_script( 'flickity' );
		}

		wp_register_script( 'fcms-was-scripts', ICONIC_WAS_URL . 'assets/frontend/js/main' . $suffix . '.js', array( 'jquery', 'accounting' ), self::$version, true );

		wp_enqueue_script( 'accounting' );
		wp_enqueue_script( 'fcms-was-scripts' );

		$vars = apply_filters(
			'fcms_was_script_vars',
			array(
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( $this->slug ),
				'is_mobile' => wp_is_mobile(),
				'currency'  => array(
					'format_num_decimals'  => wc_get_price_decimals(),
					'format_symbol'        => get_woocommerce_currency_symbol(),
					'format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
					'format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
					'format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
					'price_display_suffix' => get_option( 'woocommerce_price_display_suffix' ),
				),
				'i18n'      => array(
					'calculating'  => __( 'Calculating Price...', 'fcms-was' ),
					'no_selection' => __( 'No selection', 'fcms-was' ),
				),
			)
		);

		wp_localize_script( 'fcms-was-scripts', 'fcms_was_vars', $vars );
	}

	/**
	 * Admin: Styles
	 */
	public function admin_styles() {
		global $post, $pagenow;

		wp_register_style( 'fcms-was-admin-styles', ICONIC_WAS_URL . 'assets/admin/css/main.min.css', array(), self::$version );

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'fcms-was-admin-styles' );
	}

	/**
	 * Admin: Scripts
	 */
	public function admin_scripts() {
		global $post;

		$current_screen = get_current_screen();
		$min            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$page           = ! empty( $_GET['page'] ) ? $_GET['page'] : false;
		$taxonomy       = ! empty( $_GET['taxonomy'] ) ? $_GET['taxonomy'] : false;
		$product_edit   = $current_screen->base == "post" && $current_screen->post_type == "product";

		wp_register_script( 'fcms-was-conditional', ICONIC_WAS_URL . 'assets/vendor/js/jquery.conditional.min.js', array(
			'jquery',
		), self::$version, true );

		wp_register_script( 'fcms-was-scripts', ICONIC_WAS_URL . 'assets/admin/js/main' . $min . '.js', array(
			'jquery',
			'wp-color-picker',
			'fcms-was-conditional',
		), self::$version, true );

		if ( $page == "product_attributes" || substr( $taxonomy, 0, 3 ) === "pa_" || $product_edit ) {
			wp_enqueue_media();
			wp_enqueue_script( 'fcms-was-conditional' );
			wp_enqueue_script( 'fcms-was-scripts' );

			$vars = array(
				'url_params' => $_GET,
			);

			wp_localize_script( 'fcms-was-scripts', 'fcms_was_vars', $vars );
		}
	}

	/**
	 * Check whether the plugin is active.
	 *
	 * @since 1.0.1
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return bool True if inactive. False if active.
	 */
	public function is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->is_plugin_active_for_network( $plugin );
	}

	/**
	 * Check whether the plugin is active for the entire network.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * @since 1.0.1
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 *
	 * @return bool True, if active for the network, otherwise false.
	 */
	public function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add classes to quickview modal
	 *
	 * @since 1.0.1
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function qv_modal_classes( $classes ) {
		$classes[] = "jck-qc-has-swatches";

		return $classes;
	}

	public function add_accordion_class( $classess ) {
		if( ! is_product() ) {
			return $classess;
		}

		$enable_accordion = false;
		if( isset( $this->settings[ "style_general_accordion" ] ) ) {
			$enable_accordion = $this->settings[ "style_general_accordion" ] == "yes" ? true : false;
		}

		if( $show_accordion = apply_filters( "fcms_was_show_accordion" , $enable_accordion, get_the_ID() ) ) {
			$classess[] = "fcms-was-accordion";
		}

		return $classess;
	}
}

$fcms_was = new FCMS_Woo_Attribute_Swatches();
