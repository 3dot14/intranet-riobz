<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package tdMacro
 */

get_header(); ?>

<div class="container">
	<div class="row">
		<section id="primary" class="content-area <?php echo esc_attr( tdmacro_get_blog_primary_class() ); ?>">
			<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h2 class="page-title">
						<?php
							if ( is_category() ) :
								single_cat_title();

							elseif ( is_tag() ) :
								single_tag_title();

							elseif ( is_author() ) :
								printf( __( 'Author: %s', 'tdmacro' ), '<span class="vcard">' . get_the_author() . '</span>' );

							elseif ( is_day() ) :
								printf( __( 'Day: %s', 'tdmacro' ), '<span>' . get_the_date() . '</span>' );

							elseif ( is_month() ) :
								printf( __( 'Month: %s', 'tdmacro' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'tdmacro' ) ) . '</span>' );

							elseif ( is_year() ) :
								printf( __( 'Year: %s', 'tdmacro' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'tdmacro' ) ) . '</span>' );

							elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
								_e( 'Asides', 'tdmacro' );

							elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
								_e( 'Galleries', 'tdmacro');

							elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
								_e( 'Images', 'tdmacro');

							elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
								_e( 'Videos', 'tdmacro' );

							elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
								_e( 'Quotes', 'tdmacro' );

							elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
								_e( 'Links', 'tdmacro' );

							elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
								_e( 'Statuses', 'tdmacro' );

							elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
								_e( 'Audios', 'tdmacro' );

							elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
								_e( 'Chats', 'tdmacro' );

							else :
								_e( 'Archives', 'tdmacro' );

							endif;
						?>
					</h2>
					<?php
						// Show an optional term description.
						$term_description = term_description();
						if ( ! empty( $term_description ) ) :
							printf( '<div class="taxonomy-description">%s</div>', $term_description );
						endif;
					?>
				</header><!-- .page-header -->

				<div id="blog-grid" class="row content-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="<?php echo esc_attr( tdmacro_column_class() ); ?> post-box">
					<?php get_template_part( 'content', get_post_format() ); ?>
					</div><!-- .col -->
				<?php endwhile; ?>
				</div><!-- .row -->

				<?php tdmacro_paging_nav(); ?>

			<?php else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</section><!-- #primary -->
	</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>