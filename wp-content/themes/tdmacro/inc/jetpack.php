<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package tdMacro
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function tdmacro_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'blog-grid',
		'footer'    => 'page',
		'render' => 'tdmacro_infinite_scroll_render',
		'wrapper' => false
	) );
}
add_action( 'after_setup_theme', 'tdmacro_jetpack_setup' );
