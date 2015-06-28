<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package tdMacro
 */

if ( ! function_exists( 'tdmacro_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function tdmacro_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'tdmacro' ); ?></h1>

		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'tdmacro' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'tdmacro' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->

	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'tdmacro_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function tdmacro_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'tdmacro' ); ?></h1>
		<div class="nav-links">
			<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-title">'.__( 'Previous Article', 'tdmacro' ).'</span> %title' ); ?>
			<?php next_post_link( '<div class="nav-next">%link</div>', '<span class="meta-title">'.__( 'Next Article', 'tdmacro' ).'</span> %title' ); ?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'tdmacro_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time.
 */
function tdmacro_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$time_human_time_string = '<span class="meta-posted">%1$s</span> <time class="entry-date-human published" datetime="%2$s">%3$s</time> %4$s';
	$time_human_time_string = sprintf( $time_human_time_string,
		__( 'Posted', 'tdmacro' ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( human_time_diff( get_the_time('U'), current_time('timestamp') ) ),
		__( 'ago', 'tdmacro' )
	);

	return sprintf( __( '<span class="human-date">%1$s</span> <span class="regular-date">%2$s</span>', 'tdmacro' ),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_human_time_string
		),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		)
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function tdmacro_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'tdmacro_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'tdmacro_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so tdmacro_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so tdmacro_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in tdmacro_categorized_blog.
 */
function tdmacro_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'tdmacro_categories' );
}
add_action( 'edit_category', 'tdmacro_category_transient_flusher' );
add_action( 'save_post',     'tdmacro_category_transient_flusher' );

/**
 * Return Post Author
 *
 * @since tdmacro 1.0
 */
function tdmacro_get_post_author() {
	$current_user_id = get_the_author_meta( 'ID' );

	$output = '<div class="entry-author clearfix">';
	$output .= '<div class="avatar-container">' . get_avatar( $current_user_id, 132 ) . '</div><!-- .avatar-container -->';

	$output .= '<div class="author-info">';
	$output .= '<div class="posted-on">'.tdmacro_posted_on().'</div><!-- .posted-on -->';
	$output .= '<span class="author-meta">'.__( 'By ', 'tdmacro' ) . '</span>';
	$output .= sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			esc_url( get_author_posts_url( $current_user_id ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'tdmacro' ), get_the_author() ) ),
			esc_html( get_the_author() )
			);

	$output .= '</div><!-- .author-info -->';
	$output .= '</div><!-- .entry-author -->';

	return $output;
}

/**
 * Prints HTML with meta information for the single post-date/time and author.
 *
 * @since tdmacro 1.0
 */
function tdmacro_single_posted_on() {
	$output = '<span class="avatar-container">' . get_avatar( get_the_author_meta( 'ID' ) , 132 ) . '</span><!-- .avatar-container -->';
	$output .= '<span class="author-meta">'.__( 'Posted by ', 'tdmacro' ) . '</span>';
	$output .= sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'tdmacro' ), get_the_author() ) ),
			esc_html( get_the_author() )
			);
	$output .= '<span class="sep">/</span>';
	$output .= tdmacro_posted_on();

	return $output;
}