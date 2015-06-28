<?php
/**
 * @package tdMacro
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() ): ?>
	<div class="post-thumb">
		<a href="<?php echo esc_url( get_permalink() ); ?>" class="thumb-link">
			<?php the_post_thumbnail( 'banner-small-image' ); ?>
		</a><!-- .thumb-link -->
		<header class="entry-header">
			<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta top">
					<span class="entry-cats"><?php the_category('<span class="comma"> / </span>'); ?></span><!-- .entry-cats -->
				</div><!-- .entry-meta -->
			<?php endif; ?>
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header><!-- .entry-header -->
	</div><!-- .post-thumb -->
	<?php else: ?>
	<header class="entry-header">
		<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta top">
				<span class="entry-cats"><?php the_category('<span class="comma"> / </span>'); ?></span><!-- .entry-cats -->
			</div><!-- .entry-meta -->
		<?php endif; ?>
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<?php if( tdmacro_is_auto_post_summary() ): ?>
			<?php the_excerpt(); ?>
		<?php else: ?>
			<?php the_content( __( 'Read More', 'tdmacro' ) ); ?>
		<?php endif; ?>

		<?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'tdmacro') . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>'
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer clearfix">
		<div class="pull-left">
			<?php echo tdmacro_get_post_author(); ?>
		</div><!-- .pull-left -->

		<div class="pull-right">
			<span class="entry-comments">
			<?php comments_popup_link( __( '0', 'tdmacro' ), __( '1', 'tdmacro' ), __( '%', 'tdmacro' ) ); ?>
			</span><!-- .entry-comments -->
		</div><!-- .pull-right -->
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
