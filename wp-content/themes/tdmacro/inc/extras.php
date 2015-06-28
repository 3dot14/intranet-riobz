<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package tdMacro
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function tdmacro_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'tdmacro_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function tdmacro_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// adds a custom class if user wants to hide post content on a blog page
	// when post has a featured image
	if( tdmacro_show_featured_image_only() ) {
		$classes[] = 'featured-image-only';
	}

	return $classes;
}
add_filter( 'body_class', 'tdmacro_body_classes' );

/**
 * Adds custom classes to the array of post classes.
 */
function tdmacro_post_classes( $classes ) {
	// adds a custom class when post has a featured image
	if( has_post_thumbnail() ) {
		$classes[] = 'has-featured-img';
	}

	return $classes;
}
add_filter('post_class', 'tdmacro_post_classes');

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function tdmacro_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}

	global $page, $paged;

	// Add the blog name
	$title .= get_bloginfo( 'name', 'display' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	// Add a page number if necessary:
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title .= " $sep " . sprintf( __( 'Page %s', 'tdmacro' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'tdmacro_wp_title', 10, 2 );

/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function tdmacro_setup_author() {
	global $wp_query;

	if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
		$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
	}
}
add_action( 'wp', 'tdmacro_setup_author' );

/**
 * Blog Layout
 *
 * @since tdmacro 1.0
 */
function tdmacro_column_class() {
	return 'col-lg-4 col-md-4 three-columns';
}

/**
 * Return a column class for primary blog container
 *
 * @since tdmacro 1.0
 */
function tdmacro_get_blog_primary_class() {

	if( is_singular() ) {
		return 'col-lg-8 col-md-8';
	} else {
		return 'col-lg-12';
	}
}

/**
 * Custom render fucntion for the Jetpack Infinite Scroll
 *
 * @since tdmacro 1.0
 */
function tdmacro_infinite_scroll_render() {
	echo '<div class="infinite-wrap">';
	while( have_posts() ) {
		the_post();
		echo '<div class="'.esc_attr( tdmacro_column_class() ).' post-box infinite-scroll-item">';
		get_template_part( 'content', get_post_format() );
		echo '</div><!-- .col -->';
	}
	echo '</div><!-- .infinite-wrap -->';
}

/**
 * Author Section / Single Post Pages
 *
 * @since tdmacro 1.0
 */
function tdmacro_author_section() {
	global $post;

	$author_archive_link = get_author_posts_url( $post->post_author );

	$author_section = '<div class="author-section">';
	$author_section .= '<div class="about clearfix"><div class="gravatar">'. get_avatar( $post->post_author, 192 ) . '</div><!-- .gravatar -->';

	$author_section .= '<div class="info">';
	$author_section .= '<h4 class="author-section-header"><span>'.__( 'About', 'tdmacro' ).'</span><a class="author-name" href="'.esc_url( $author_archive_link ).'">'.get_the_author().'</a></h4>';
	$author_section .= '<div class="about-description">'.nl2br( get_the_author_meta( 'description', $post->post_author ) ).'</div>';
	$author_section .= '</div><!-- .info -->';
	$author_section .= '</div> <!-- .about -->';

	$author_section .= '</div><!-- .author-section -->';

	return $author_section;
}

/**
 * Checks if user wants to hide/show post content when post has a featured image
 *
 * @since tdmacro 1.0
 */
function tdmacro_show_featured_image_only() {
	$selected_option = get_theme_mod( 'tdmacro_is_featured_image_without_content', 'hide' );

	if( $selected_option === 'hide' ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Auto Post Summary
 *
 * @since tdmacro 1.0
 */
function tdmacro_is_auto_post_summary() {
	return get_theme_mod( 'tdmacro_is_auto_post_summary', '1' );
}