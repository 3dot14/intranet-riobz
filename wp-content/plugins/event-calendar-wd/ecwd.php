<?php
/**
 * Plugin Name:     Event Calendar WD
 * Plugin URI:		https://web-dorado.com/products/wordpress-event-calendar-wd.html 
 * Description:     Event Calendar WD is an easy event management and planning tool with advanced features.
 * Version:         1.0.8
 * Author:          WebDorado
 * Author URI:      http://web-dorado.com
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

require_once( 'ecwd_class.php' );

if( ! defined( 'ECWD_MAIN_FILE' ) ) {
	define( 'ECWD_MAIN_FILE', plugin_basename(__FILE__));

}if( ! defined( 'ECWD_DIR' ) ) {
	define( 'ECWD_DIR', dirname(__FILE__));

}

add_action( 'plugins_loaded', array( 'ECWD', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( 'ecwd_admin_class.php' );
	register_activation_hook( __FILE__, array( 'ECWD_Admin', 'activate' ) );
	register_uninstall_hook(__FILE__, array('ECWD_Admin', 'uninstall'));
	add_action( 'plugins_loaded', array( 'ECWD_Admin', 'get_instance' ) );
}



