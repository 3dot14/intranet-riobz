<?php
/**
 * The template for displaying Search Results pages.
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
					<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'tdmacro' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
				</header><!-- .page-header -->

				<div id="blog-grid" class="row content-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="<?php echo esc_attr( tdmacro_column_class() ); ?> post-box">
					<?php get_template_part( 'content', 'search' ); ?>
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
