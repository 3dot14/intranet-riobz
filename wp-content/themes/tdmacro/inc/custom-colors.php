<?php
/**
 * Custom colors
 *
 * @package tdMacro
 */

function tdmacro_custom_colors() {
	$custom_colors = '';

	//Accent Color
	if( get_theme_mod( 'tdmacro_accent_color', '#2980b9' ) != '#2980b9' ) {
		$custom_colors .= "input[type='submit'], a.button { background: ".esc_attr( get_theme_mod( 'tdmacro_accent_color', '#2980b9' ) )."; } \n";
		$custom_colors .= " #colophon { border-color: ".esc_attr( get_theme_mod( 'tdmacro_accent_color', '#2980b9' ) )."; } \n";
		$custom_colors .= " a:hover { color:".esc_attr( get_theme_mod( 'tdmacro_accent_color', '#2980b9' ) )."; } \n";
	}

	//Header Background Color
	if( get_theme_mod( 'tdmacro_header_bg', '#2c3e50' ) != '#2c3e50' ) {
		$custom_colors .= "#masthead { background: ".esc_attr( get_theme_mod( 'tdmacro_header_bg', '#2c3e50' ) )."; } \n";
	}

	//Menu Background Color
	if( get_theme_mod( 'tdmacro_menu_bg', '#2980b9' ) != '#2980b9' ) {
		$custom_colors .= ".main-navigation, .main-navigation ul ul { background: ".esc_attr( get_theme_mod( 'tdmacro_menu_bg', '#2980b9' ) )."; } \n";
	}

	//Menu Text Color
	if( get_theme_mod( 'tdmacro_menu_textcolor', '#ffffff' ) != '#ffffff' ) {
		$custom_colors .= ".main-navigation a, .main-navigation .nav-bar li.menu-item-has-children > a:after, .main-navigation .nav-bar li.page_item_has_children > a:after { color: ".esc_attr( get_theme_mod( 'tdmacro_menu_textcolor', '#ffffff' ) )."; } \n";
	}

	//Footer Background Color
	if( get_theme_mod( 'tdmacro_footer_bg', '#1a2530' ) != '#1a2530' ) {
		$custom_colors .= "#colophon { background: ".esc_attr( get_theme_mod( 'tdmacro_footer_bg', '#1a2530' ) )."; } \n";
	}

	//Footer Primary Text Color
	if( get_theme_mod( 'tdmacro_footer_primary_textcolor', '#e2e4e4' ) != '#e2e4e4' ) {
		$custom_colors .= "#footer-widgets .widget a, #colophon .footer-bottom a { color: ".esc_attr( get_theme_mod( 'tdmacro_footer_primary_textcolor', '#e2e4e4' ) )."; } \n";
	}

	//Footer Secondary Text Color
	if( get_theme_mod( 'tdmacro_footer_secondary_textcolor', '#c9ced4' ) != '#c9ced4' ) {
		$custom_colors .= "#footer-widgets .widget,#footer-widgets .widget .widget-title,#footer-widgets .widget a:hover, #colophon .footer-bottom { color: ".esc_attr( get_theme_mod( 'tdmacro_footer_secondary_textcolor', '#c9ced4' ) )."; } \n";
	}

	if( $custom_colors ):
	?>
		<style type='text/css'>
		<?php echo $custom_colors; ?>
		</style>
	<?php
	endif;
}
add_action('wp_head', 'tdmacro_custom_colors');