<?php
/**
 * tdMacro Theme Customizer
 *
 * @package tdMacro
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function tdmacro_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	// Renaming "Header Image" section to "Logo"
	$wp_customize->add_section( 'header_image', array(
		'title' => __( 'Custom Logo', 'tdmacro' ),
		'theme_supports' => 'custom-header',
		'priority' => 60,
	) );

	//Accent Color
	$wp_customize->add_setting( 'tdmacro_accent_color', array(
    	'default'        => '#2980b9',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_accent_color', array(
		'label' => __( 'Accent Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_accent_color',
		'priority' => 1
    ) ) );

	//Header Background Color
	$wp_customize->add_setting( 'tdmacro_header_bg', array(
    	'default'        => '#2c3e50',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_header_bg', array(
		'label' => __( 'Header Background Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_header_bg',
		'priority' => 2
    ) ) );

    //Menu Background Color
	$wp_customize->add_setting( 'tdmacro_menu_bg', array(
    	'default'        => '#2980b9',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_menu_bg', array(
		'label' => __( 'Menu Background Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_menu_bg',
		'priority' => 100
    ) ) );

    //Menu Text Color
	$wp_customize->add_setting( 'tdmacro_menu_textcolor', array(
    	'default'        => '#ffffff',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_menu_textcolor', array(
		'label' => __( 'Menu Text Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_menu_textcolor',
		'priority' => 101
    ) ) );

    //Footer Background Color
	$wp_customize->add_setting( 'tdmacro_footer_bg', array(
    	'default'        => '#1a2530',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_footer_bg', array(
		'label' => __( 'Footer Background Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_footer_bg',
		'priority' => 102
    ) ) );

    //Footer Primary Text Color
	$wp_customize->add_setting( 'tdmacro_footer_primary_textcolor', array(
    	'default'        => '#e2e4e4',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_footer_primary_textcolor', array(
		'label' => __( 'Footer Primary Text Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_footer_primary_textcolor',
		'priority' => 103
    ) ) );

    //Footer Secondary Text Color
	$wp_customize->add_setting( 'tdmacro_footer_secondary_textcolor', array(
    	'default'        => '#c9ced4',
    	'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tdmacro_footer_secondary_textcolor', array(
		'label' => __( 'Footer Secondary Text Color', 'tdmacro' ),
		'section' => 'colors',
		'settings' => 'tdmacro_footer_secondary_textcolor',
		'priority' => 104
    ) ) );

	/**
	* Blog Settings
 	*/
 	$wp_customize->add_section( 'tdmacro_blog_settings', array(
     	'title'    => __( 'Theme Options', 'tdmacro' ),
     	'priority' => 2000,
	) );

	/* Hide/show post content when post has a featured image */
	$wp_customize->add_setting( 'tdmacro_is_featured_image_without_content', array(
        'default' => 'hide',
        'sanitize_callback' => 'sanitize_text_field'
    ) );

    $wp_customize->add_control( 'tdmacro_is_featured_image_without_content', array(
        'type' => 'select',
        'label' => __( 'Post with Featured Image', 'tdmacro' ),
        'section' => 'tdmacro_blog_settings',
        'choices' => array(
            'show' => __( 'Show Post Content', 'tdmacro' ),
            'hide' => __( 'Hide Post Content', 'tdmacro' ),
        ),
        'priority' => 2
    ));

    /* Auto Post Summary */
	$wp_customize->add_setting( 'tdmacro_is_auto_post_summary', array(
        'default' => '1',
        'sanitize_callback' => 'tdmacro_sanitize_numeric_value'
    ) );

	$wp_customize->add_control( 'tdmacro_is_auto_post_summary', array(
        'type' => 'select',
        'label' => __( 'Auto Post Summary', 'tdmacro' ),
        'section' => 'tdmacro_blog_settings',
        'choices' => array(
            '1' => __( 'On', 'tdmacro' ),
            '0' => __( 'Off', 'tdmacro' ),
        ),
        'priority' => 3
    ));
}
add_action( 'customize_register', 'tdmacro_customize_register' );

/**
 * Settings Sanitization
 */

 /** Numeric values only */
function tdmacro_sanitize_numeric_value( $value ) {
	return intval( $value );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function tdmacro_customize_preview_js() {
	wp_enqueue_script( 'tdmacro_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'tdmacro_customize_preview_js' );