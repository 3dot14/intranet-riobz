<?php
/**
 * The default template for displaying attachment content
 * @package tdMacro
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="single-posted-on">
		<?php
				$metadata = wp_get_attachment_metadata();
				printf( __( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s" pubdate>%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%7$s</a>', 'tdmacro' ),
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() ),
					wp_get_attachment_url(),
					$metadata['width'],
					$metadata['height'],
					get_permalink( $post->post_parent ),
					get_the_title( $post->post_parent )
				);
			?>
		<?php edit_post_link( __( 'Edit', 'tdmacro' ), ' &bull; <span class="edit-link">', '</span>' ); ?>
		</div><!-- .single-posted-on -->

	</header><!-- .entry-header -->

	<div class="entry-content">
		<div class="entry-attachment">
		<?php echo wp_get_attachment_image( $post->ID, 'full' );  ?>
		</div><!-- .entry-attachment -->
	</div><!-- .entry-content -->
</article><!-- #post-## -->