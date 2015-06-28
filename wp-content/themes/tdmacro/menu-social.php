<?php
/**
 * The default template for displaying social menu content
 * @package tdMacro
 */

if ( has_nav_menu( 'social' ) ) {

	wp_nav_menu(
		array(
			'theme_location'  => 'social',
			'container'       => 'div',
			'container_id'    => '',
			'container_class' => 'social-list',
			'menu_id'         => '',
			'menu_class'      => 'menu-items list-unstyled',
			'depth'           => 1,
			'link_before'     => '<span class="screen-reader-text">',
			'link_after'      => '</span>',
			'fallback_cb'     => '',
		)
	);

} ?>