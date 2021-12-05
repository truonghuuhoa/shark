<?php
/**
 * Setting related functions.
 *
 * @package fcms-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'FCMS_WooThumbs_Core_Settings' ) ) {
	return;
}

/**
 * FCMS_WooThumbs_Core_Settings.
 *
 * @class    FCMS_WooThumbs_Core_Settings
 * @version  1.0.6
 */
class FCMS_WooThumbs_Core_Settings {
	/**
	 * Single instance of the FCMS_WooThumbs_Core_Settings object.
	 *
	 * @var FCMS_WooThumbs_Core_Settings
	 */
	public static $single_instance = null;

	/**
	 * Class args.
	 *
	 * @var array
	 */
	public static $args = array();

	/**
	 * Settings framework instance.
	 *
	 * @var FCMS_WooThumbs_Settings_Framework
	 */
	public static $settings_framework = null;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Docs base url.
	 *
	 * @var string
	 */
	public static $docs_base = 'https://docs.fcmswp.com';

	/**
	 * FCMS svg src.
	 *
	 * @var string
	 */
	public static $fcms_svg = 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjMwcHgiIGhlaWdodD0iMzUuNDU1cHgiIHZpZXdCb3g9IjAgMCAzMCAzNS40NTUiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDMwIDM1LjQ1NSIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8Zz4NCgkJPHBvbHlnb24gcG9pbnRzPSIxMC45MSwzMy44MTggMTMuNjM2LDM1LjQ1NSAxMy42MzYsMTkuMDkxIDEwLjkxLDE3LjQ1NSAJCSIvPg0KCQk8cG9seWdvbiBwb2ludHM9IjE2LjM2MywzNS40NTUgMzAsMjcuMTY4IDMwLDIzLjk3NiAxNi4zNjMsMzIuMjYzIAkJIi8+DQoJCTxnPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIxMi4zNSwxLjU5IDI1Ljk4Niw5Ljc3MiAyOC42MzcsOC4xODIgMTUsMCAJCQkiLz4NCgkJCTxwb2x5Z29uIHBvaW50cz0iNS40NTUsMzAuNTQ1IDguMTgyLDMyLjE4MiA4LjE4MiwxNS44MTggNS40NTUsMTQuMTgyIAkJCSIvPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIxNi4zNjMsMjguOTIxIDMwLDIwLjYzNCAzMCwxNy40NDIgMTYuMzYzLDI1LjcyOSAJCQkiLz4NCgkJCTxwb2x5Z29uIHBvaW50cz0iNi44NzEsNC45ODQgMjAuNTA4LDEzLjE2NyAyMy4xNTgsMTEuNTc2IDkuNTIxLDMuMzk1IAkJCSIvPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIyLjcyNywxMi41NDUgMCwxMC45MDkgMCwyNy4yNzMgMi43MjcsMjguOTA5IAkJCSIvPg0KCQkJPHBvbHlnb24gcG9pbnRzPSIxNi4zNjMsMjIuMzg4IDMwLDE0LjEgMzAsMTAuOTA5IDE2LjM2MywxOS4xOTYgCQkJIi8+DQoJCQk8cG9seWdvbiBwb2ludHM9IjEuMzkyLDguMTY1IDE1LjAyOCwxNi4zNDcgMTcuNjc4LDE0Ljc1NiA0LjA0Miw2LjU3NSAJCQkiLz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K';

	/**
	 * Creates/returns the single instance FCMS_WooThumbs_Core_Settings object.
	 *
	 * @param array $args Arguments.
	 *
	 * @return FCMS_WooThumbs_Core_Settings
	 */
	public static function run( $args = array() ) {
		if ( null === self::$single_instance ) {
			self::$args                            = $args;
			self::$args['option_group_underscore'] = str_replace( '-', '_', self::$args['option_group'] );
			self::$single_instance                 = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 20 );
		add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Init.
	 */
	public static function init() {
		require_once self::$args['vendor_path'] . 'wp-settings-framework/wp-settings-framework.php';

		add_filter( 'wpsf_register_settings_' . self::$args['option_group'], array( __CLASS__, 'setup_dashboard' ) );

		self::$settings_framework = new FCMS_WooThumbs_Settings_Framework( self::$args['settings_path'], self::$args['option_group'] );
		self::$settings           = self::$settings_framework->get_settings();
	}

	/**
	 * Get setting.
	 *
	 * @param string $setting Setting.
	 *
	 * @return mixed
	 */
	public static function get_setting( $setting ) {
		if ( empty( self::$settings ) ) {
			return null;
		}

		if ( ! isset( self::$settings[ $setting ] ) ) {
			return null;
		}

		return self::$settings[ $setting ];
	}

	/**
	 * Get a setting directly from the database.
	 *
	 * @param string $section_id May also be prefixed with tab ID.
	 * @param string $field_id   The id of the specific field.
	 * @param mixed  $default    Default field value.
	 *
	 * @return mixed
	 */
	public static function get_setting_from_db( $section_id, $field_id, $default = false ) {
		$options = get_option( self::$args['option_group'] . '_settings' );

		// If no settings saved, return default.
		if ( false === $options ) {
			return $default;
		}

		if ( isset( $options[ $section_id . '_' . $field_id ] ) ) {
			return $options[ $section_id . '_' . $field_id ];
		}

		return false;
	}

	/**
	 * Add settings page.
	 */
	public static function add_settings_page() {
		$default_title = 'WooThumbs';

		self::$settings_framework->add_settings_page(
			array(
				'parent_slug' => isset( self::$args['parent_slug'] ) ? self::$args['parent_slug'] : 'woocommerce',
				'page_title'  => isset( self::$args['page_title'] ) ? self::$args['page_title'] : $default_title,
				'menu_title'  => self::$args['menu_title'],
				'capability'  => self::get_settings_page_capability(),
			)
		);

		do_action( 'admin_menu_' . self::$args['option_group'] );
	}

	/**
	 * Get settings page capability.
	 *
	 * @return mixed
	 */
	public static function get_settings_page_capability() {
		$capability = isset( self::$args['capability'] ) ? self::$args['capability'] : 'manage_woocommerce';

		return apply_filters( self::$args['option_group'] . '_settings_page_capability', $capability );
	}

	/**
	 * Is settings page?
	 *
	 * @param string $suffix Suffix.
	 *
	 * @return bool
	 */
	public static function is_settings_page( $suffix = '' ) {
		if ( ! is_admin() ) {
			return false;
		}

		$path = str_replace( '_', '-', self::$args['option_group'] ) . '-settings' . $suffix;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['page'] ) || $_GET['page'] !== $path ) {
			return false;
		}

		return true;
	}


	/**
	 * Get doc links.
	 *
	 * @return array
	 */
	public static function get_doc_links() {
		$transient_name = self::$args['option_group'] . '_getting_started_links';
		$saved_return   = get_transient( $transient_name );

		if ( false !== $saved_return ) {
			return $saved_return;
		}

		$return   = array();
		$url      = self::get_docs_url( 'getting-started' );
		$response = wp_remote_get( $url );
		$html     = wp_remote_retrieve_body( $response );

		if ( ! $html ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		$dom = new DOMDocument();

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@$dom->loadHTML( $html );

		$lists = $dom->getElementsByTagName( 'ul' );

		if ( empty( $lists ) ) {
			set_transient( $transient_name, $return, 12 * HOUR_IN_SECONDS );

			return $return;
		}

		foreach ( $lists as $list ) {
			$classes = $list->getAttribute( 'class' );

			if ( strpos( $classes, 'articleList' ) === false ) {
				continue;
			}

			$links = $list->getElementsByTagName( 'a' );

			foreach ( $links as $link ) {
				$return[] = array(
					'href'  => $link->getAttribute( 'href' ),
					'title' => $link->nodeValue, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				);
			}
		}

		set_transient( $transient_name, $return, 30 * DAY_IN_SECONDS );

		return $return;
	}

	/**
	 * Output getting started links.
	 */
	public static function output_getting_started_links() {
		$links = self::get_doc_links();

		if ( empty( $links ) ) {
			return;
		}
		?>
		<h3><?php esc_html_e( 'Getting Started', 'fcms-woothumbs' ); ?></h3>

		<ol>
			<?php foreach ( $links as $link ) { ?>
				<li>
					<a href="<?php echo esc_url( self::get_docs_url() . $link['href'] ); ?>?utm_source=FCMS&utm_medium=Plugin&utm_campaign=fcms-woothumbs&utm_content=getting-started-links" target="_blank"><?php echo esc_html( $link['title'] ); ?></a>
				</li>
			<?php } ?>
		</ol>
		<?php
	}

	/**
	 * Get docs URL.
	 *
	 * @param bool $type Type.
	 *
	 * @return mixed|string
	 */
	public static function get_docs_url( $type = false ) {
		if ( ! $type || 'base' === $type || ! isset( self::$args['docs'][ $type ] ) ) {
			return self::$docs_base;
		}

		return self::$docs_base . self::$args['docs'][ $type ];
	}

	/**
	 * Configure settings dashboard.
	 *
	 * @param array $settings Settings.
	 *
	 * @return mixed
	 */
	public static function setup_dashboard( $settings ) {
		if ( ! self::is_settings_page() ) {
			return $settings;
		}

		$settings['tabs']     = isset( $settings['tabs'] ) ? $settings['tabs'] : array();
		$settings['sections'] = isset( $settings['sections'] ) ? $settings['sections'] : array();

		$settings['tabs'][] = array(
			'id'    => 'dashboard',
			'title' => __( 'Dashboard', 'fcms-woothumbs' ),
		);

	}

	/**
	 * Enqueue scripts.
	 */
	public static function enqueue_scripts() {
		if ( ! self::is_settings_page() && ! self::is_settings_page( '-account' ) ) {
			return;
		}

		wp_enqueue_script( 'freemius-checkout', 'https://checkout.freemius.com/checkout.min.js', array(), '1', true );
	}

}
