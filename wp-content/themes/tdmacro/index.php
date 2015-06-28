<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package tdMacro
 */

get_header(); ?>

<div class="container">
	<div class="row">
		<div id="primary" class="content-area <?php echo esc_attr( tdmacro_get_blog_primary_class() ); ?>">
			<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

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
		</div><!-- #primary -->
	</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
