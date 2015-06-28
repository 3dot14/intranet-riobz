<?php

/**
 * ECWD
 *
 */
class ECWD {

	protected $version = '1.0.8';
	protected $plugin_name = 'event-calendar-wd';
	protected $prefix = 'ecwd';
	protected $old_version = '1.4.11';
	protected static $instance = null;

	private function __construct() {
		$this->setup_constants();
		include_once( 'includes/ecwd-shortcodes.php' );
		$this->includes();
		$cpt_instance = ECWD_Cpt::get_instance();
		$this->user_info();
		add_action( 'init', array( $this, 'add_localization' ) );
		add_filter( 'body_class', array( $this, 'theme_body_class' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}


	/**
	 * Setup constants
	 */
	public function setup_constants() {
		if ( ! defined( 'ECWD_PLUGIN_DIR' ) ) {
			define( 'ECWD_PLUGIN_DIR', dirname( __FILE__ ) );
		}

		if ( ! defined( 'ECWD_PLUGIN_PREFIX' ) ) {
			define( 'ECWD_PLUGIN_PREFIX', $this->prefix );
		}
		if ( ! defined( 'ECWD_PLUGIN_NAME' ) ) {
			define( 'ECWD_PLUGIN_NAME', $this->plugin_name );
		}

	}




	public function add_localization() {
		$path   = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$loaded = load_plugin_textdomain( 'ecwd', false, $path );
		if ( isset( $_GET['page'] ) && $_GET['page'] == basename( __FILE__ ) && ! $loaded ) {
			echo '<div class="error">Event calendar WD ' . __( 'Could not load the localization file: ' . $path, 'ecwd' ) . '</div>';

			return;
		}
	}

	public function user_info() {
		//detect timezone

	}


	public static function theme_body_class(
		$classes
	) {
		$child_theme  = get_option( 'stylesheet' );
		$parent_theme = get_option( 'template' );

		if ( $child_theme == $parent_theme ) {
			$child_theme = false;
		}

		if ( $child_theme ) {
			$theme_classes = "ecwd-theme-parent-$parent_theme ecwd-theme-child-$child_theme";
		} else {
			$theme_classes = "ecwd-theme-$parent_theme";
		}
		$classes[] = $theme_classes;

		return $classes;
	}

	/**
	 * Include all necessary files
	 */
	public static function includes() {
		global $ecwd_options;

		include_once( 'includes/ecwd-cpt-class.php' );
		include_once( 'includes/register-settings.php' );
		$ecwd_options     = ecwd_get_settings();
		$default_timezone = self::isValidTimezone( @ini_get( 'date.timezone' ) ) ? ini_get( 'date.timezone' ) : 'Europe/Berlin';
		$timezone         = ( isset( $ecwd_options['time_zone'] ) && self::isValidTimezone( $ecwd_options['time_zone'] ) ) ? $ecwd_options['time_zone'] : $default_timezone;
		date_default_timezone_set( $timezone );
		include_once( 'includes/ecwd-functions.php' );
		include_once( 'includes/ecwd-event-class.php' );
		include_once( 'includes/ecwd-display-class.php' );
		include_once( 'views/widgets.php' );
	}

	/**
	 * Load public facing scripts
	 */
	public function enqueue_scripts() {
		global $wp_scripts;
		$map_included = false;
		if ( isset( $wp_scripts->registered ) && $wp_scripts->registered ) {
			foreach ( $wp_scripts->registered as $wp_script ) {
				if ( $wp_script->src && ( strpos( $wp_script->src, 'maps.googleapis.com' ) || strpos( $wp_script->src, 'maps.google.com' ) ) !== false ) {
					$map_included = true;
					break;
				}
			}
		}
		if ( ! $map_included ) {
			wp_enqueue_script( $this->prefix . '-maps-public', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ), $this->version, true );
		}
		wp_enqueue_script( $this->prefix . '-gmap-public', plugins_url( 'js/gmap/gmap3.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->prefix . '-public', plugins_url( 'js/scripts.js', __FILE__ ), array(
			'jquery',
			'jquery-ui-draggable',
			'masonry'
		), $this->version, true );

	}

	/*
	 * Load public facing styles
	 */

	public function enqueue_styles() {
		wp_enqueue_style( $this->prefix . '_font-awesome', plugins_url( '/css/font-awesome/font-awesome.css', __FILE__ ), '', $this->version, 'all' );
		wp_enqueue_style( $this->prefix . '-public', plugins_url( 'css/style.css', __FILE__ ), '', $this->version, 'all' );
	}


	public static function isValidTimezone( $timezone ) {
		return in_array( $timezone, timezone_identifiers_list() );
	}

	/**
	 * Return the plugin name.
	 */
	public function get_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the plugin prefix.
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Return the plugin version.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return the plugin old version.
	 */
	public function get_old_version() {
		return $this->old_version;
	}


	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
