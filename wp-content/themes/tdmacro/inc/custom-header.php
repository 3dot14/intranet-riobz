<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package tdmacro
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses tdmacro_header_style()
 * @uses tdmacro_admin_header_style()
 * @uses tdmacro_admin_header_image()
 *
 * @package tdmacro
 */
function tdmacro_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'tdmacro_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => 'ffffff',
		'width'                  => 300,
		'height'                 => 60,
		'flex-height'            => true,
		'uploads'                => true,
		'wp-head-callback'       => 'tdmacro_header_style',
		'admin-head-callback'    => 'tdmacro_admin_header_style',
		'admin-preview-callback' => 'tdmacro_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'tdmacro_custom_header_setup' );

if ( ! function_exists( 'tdmacro_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see tdmacro_custom_header_setup().
 */
function tdmacro_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == $header_text_color ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		#masthead .site-title,
		#masthead .site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#masthead .site-title a,
		#masthead .site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // tdmacro_header_style

if ( ! function_exists( 'tdmacro_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see tdmacro_custom_header_setup().
 */
function tdmacro_admin_header_style() {
?>
	<style type="text/css">
		.appearance_page_custom-header #headimg {
			border: none;
		}

		#headimg {
			background:#2c3e50;
			padding: 20px;
		}

		#headimg h1,
		#desc {
			display: inline-block;
		}

		#headimg h1 {
			font-size: 22px;
			line-height: 1;
			margin-top: 0;
			margin-bottom: 0;
			text-transform: uppercase;
			letter-spacing: 0.04em;
		}

		#headimg h1 a {
			text-decoration: none;
		}

		#headimg h1 a,
		#desc {
			color: #ffffff;
		}

		#desc {
			position: relative;
			top: -3px;
			font-size: 13px;
			line-height: 22px;
			margin-top: 0;
			margin-bottom: 0;
			font-weight: normal;
			letter-spacing: 0.1em;
			opacity: 0.8;
		}

		#desc:before {
			content: "|";
			padding: 0 8px;
		}
	</style>
<?php
}
endif; // tdmacro_admin_header_style

if ( ! function_exists( 'tdmacro_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see tdmacro_custom_header_setup().
 */
function tdmacro_admin_header_image() {
?>
	<div id="headimg">
	<?php if ( get_header_image() ) : ?>
		<div><img src="<?php echo esc_url( get_header_image() ); ?>" alt=""></div>
	<?php endif; ?>
		<h1 class="displaying-header-text"><a id="name"<?php echo sprintf( ' style="color:#%s;"', get_header_textcolor() ); ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div class="displaying-header-text" id="desc"<?php echo sprintf( ' style="color:#%s;"', get_header_textcolor() ); ?>><?php bloginfo( 'description' ); ?></div>
	</div><!-- #headimg -->
<?php
}
endif; // tdmacro_admin_header_image