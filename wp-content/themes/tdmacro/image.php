<?php
/**
 * The Template for displaying a single attachment.
 *
 * @package tdMacro
 */

get_header(); ?>

<div class="container">
	<div class="row">
		<div id="primary" class="content-area <?php echo esc_attr( tdmacro_get_blog_primary_class() ); ?>">
			<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'attachment' ); ?>

				<?php tdmacro_post_nav(); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<div class="col-lg-4 col-md-4">
			<?php get_sidebar(); ?>
		</div><!-- .col -->
	</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>