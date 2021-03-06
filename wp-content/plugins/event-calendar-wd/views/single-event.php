<?php
/**
 * Display for Event Custom Post Types
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $post;
global $wp;
global $ecwd_options;

$post_id = $post->ID;
$meta    = get_post_meta( $post_id );

$date_format  = 'Y-m-d';
$time_format  = 'H:i';
$social_icons = false;
if ( isset( $ecwd_options['date_format'] ) && $ecwd_options['date_format'] != '' ) {
	$date_format = $ecwd_options['date_format'];
}
if ( isset( $ecwd_options['time_format'] ) && $ecwd_options['time_format'] != '' ) {
	$time_format = $ecwd_options['time_format'];
}
if ( isset( $ecwd_options['social_icons'] ) && $ecwd_options['social_icons'] != '' ) {
	$social_icons = $ecwd_options['social_icons'];
}
// Load up all post meta data
$ecwd_event_location = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_location', true );
$ecwd_event_latlong  = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_lat_long', true );
$ecwd_event_zoom     = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_map_zoom', true );
$ecwd_event_show_map = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_show_map', true );
if ( $ecwd_event_show_map == '' ) {
	$ecwd_event_show_map = 1;
}
if ( ! $ecwd_event_zoom ) {
	$ecwd_event_zoom = 17;
}

$ecwd_event_organizers = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_organizers', true );
$ecwd_event_date_from  = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_date_from', true );
$ecwd_event_date_to    = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_date_to', true );
$ecwd_event_url        = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_url', true );
$ecwd_event_video      = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_video', true );
$ecwd_all_day_event    = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_all_day_event', true );
$venue                 = '';
$venue_permalink       = '';
$venue_post_id         = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_event_venue', true );
if ( $venue_post_id ) {
	$venue_post = get_post( $venue_post_id );
	if ( $venue_post ) {
		$venue           = $venue_post->post_title;
		$venue_permalink = get_permalink( $venue_post->ID );
	}
}

$this_event_url = get_permalink( $post->ID );
$organizers     = array();
if ( is_array( $ecwd_event_organizers ) || is_object( $ecwd_event_organizers ) ) {
	foreach ( $ecwd_event_organizers as $ecwd_event_organizer ) {
		$organizers[] = get_post( $ecwd_event_organizer, ARRAY_A );
	}
}
$featured_image = '';
if ( has_post_thumbnail() ) {
	$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID, 'full', false ) );
}

get_header();
?>
<div id="ecwd-events-content" class="ecwd-events-single hentry">


	<?php while ( have_posts() ) :
		the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="ecwd-event" itemscope itemtype="http://schema.org/Event">
				<header class="entry-header">
					<?php the_title( '<h1 class="ecwd-events-single-event-title summary entry-title">', '</h1>' ); ?>
				</header>
				<div class="event-detalis">

					<?php ?>
					<?php if ( $featured_image && $featured_image !== '' ) { ?>
						<div class="event-featured-image">
							<img src="<?php echo $featured_image; ?>"/>
						</div>
					<?php } ?>
					<div class="ecwd-event-details">
						<div class="event-detalis-date">
							<label class="ecwd-event-date-info"
							       title="<?php _e( 'Date', 'ecwd' ); ?>"></label>
			 <span class="ecwd-event-date" itemprop="startDate"
			       content="<?php echo date( 'Y-m-d', strtotime( $ecwd_event_date_from ) ) . 'T' . date( 'H:i', strtotime( $ecwd_event_date_from ) ) ?>">
                 <?php if ( $ecwd_all_day_event == 1 ) {
	                 echo date( $date_format, strtotime( $ecwd_event_date_from ) );
	                 if ( $ecwd_all_day_event == 1 ) {
		                 if ( $ecwd_event_date_to && date( $date_format, strtotime( $ecwd_event_date_from ) ) !== date( $date_format, strtotime( $ecwd_event_date_to ) ) ) {
			                 echo ' - ' . date( $date_format, strtotime( $ecwd_event_date_to ) );
		                 }
		                 echo ' ' . __( 'All day', 'ecwd' );
	                 }
                 } else {
	                 echo date( $date_format, strtotime( $ecwd_event_date_from ) ) . ' ' . date( $time_format, strtotime( $ecwd_event_date_from ) );

	                 if ( $ecwd_event_date_to ) {
		                 echo ' - ' . date( $date_format, strtotime( $ecwd_event_date_to ) ) . ' ' . date( $time_format, strtotime( $ecwd_event_date_to ) );
	                 }
                 } ?>
			 </span>
						</div>
						<?php if ( $ecwd_event_url ) { ?>
							<div class="ecwd-url">

								<a href="<?php echo $ecwd_event_url; ?>" target="_blank"><label
										class="ecwd-event-url-info"
										title="<?php _e( 'Url', 'ecwd' ); ?>"></label>    <?php echo $ecwd_event_url; ?>
								</a>
							</div>
						<?php } ?>
						<?php if ( count( $organizers ) > 0 ) { ?>
							<div class="event-detalis-org">
								<label class="ecwd-event-org-info"
								       title="<?php _e( 'Organizers', 'ecwd' ); ?>"></label>
								<?php if ( $organizers ) {
									foreach ( $organizers as $organizer ) { ?>
										<span itemprop="organizer">
						<a href="<?php echo get_permalink( $organizer['ID'] ) ?>"><?php echo $organizer['post_title'] ?></a>
					</span>
									<?php }
								} ?>
							</div>
						<?php } ?>
						<div class="event-venue" itemprop="location" itemscope
						     itemtype="http://schema.org/Place">
							<?php if ( $venue_post_id ) { ?>
								<span itemprop="name"><a
										href="<?php echo $venue_permalink ?>"><?php echo $venue; ?></a></span>
								<div class="address" itemprop="address" itemscope
								     itemtype="http://schema.org/PostalAddress">
									<?php echo $ecwd_event_location; ?>
									<?php
									if ( $ecwd_event_latlong ) {
										?>
									<?php } ?>
								</div>

							<?php } elseif ( $ecwd_event_location ) { ?>
								<div class="address" itemprop="address" itemscope
								     itemtype="http://schema.org/PostalAddress">
									<?php echo $ecwd_event_location; ?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php if ( $social_icons ) {
					?>

					<div class="ecwd-social">
        <span class="share-links">
			<a href="http://twitter.com/home?status=<?php echo get_permalink( $post_id ) ?>" class="ecwd-twitter"
			   target="_blank" data-original-title="Tweet It">
				<span class="visuallyhidden">Twitter</span></a>
			<a href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink( $post_id ) ?>" class="ecwd-facebook"
			   target="_blank" data-original-title="Share on Facebook">
				<span class="visuallyhidden">Facebook</span></a>
			<a href="http://plus.google.com/share?url=<?php echo get_permalink( $post_id ) ?>" class="ecwd-google-plus"
			   target="_blank" data-original-title="Share on Google+">
				<span class="visuallyhidden">Google+</span></a>
		</span>
					</div>
				<?php } ?>
				<?php
				//if($ecwd_event_show_map==1){
				if ( $ecwd_event_show_map == 1 && $ecwd_event_latlong ) {
					$map_events               = array();
					$map_events[0]['latlong'] = explode( ',', $ecwd_event_latlong );
					if ( $ecwd_event_location != '' ) {
						$map_events[0]['location'] = $ecwd_event_location;
					}
					$map_events[0]['zoom']  = $ecwd_event_zoom;
					$map_events[0]['infow'] = '<div class="ecwd_map_event">';
					$map_events[0]['infow'] .= '<span class="location">' . $ecwd_event_location . '</span>';
					$map_events[0]['infow'] .= '</div>';
					$map_events[0]['infow'] .= '<div class="event-detalis-date">
			 <label class="ecwd-event-date-info" title="' . __( 'Date', 'ecwd' ) . '"></label>
			 <span class="ecwd-event-date" itemprop="startDate" content="' . date( 'Y-m-d', strtotime( $ecwd_event_date_from ) ) . 'T' . date( 'H:i', strtotime( $ecwd_event_date_from ) ) . '">';
					if ( $ecwd_all_day_event == 1 ) {
						$map_events[0]['infow'] .= date( $date_format, strtotime( $ecwd_event_date_from ) );
						if ( $ecwd_event_date_to ) {
							$map_events[0]['infow'] .= ' - ' . date( $date_format, strtotime( $ecwd_event_date_to ) ) . '  ' . __( 'All day', 'ecwd' );
						}
					} else {
						$map_events[0]['infow'] .= date( $date_format, strtotime( $ecwd_event_date_from ) ) . ' ' . date( $time_format, strtotime( $ecwd_event_date_from ) );

						if ( $ecwd_event_date_to ) {
							$map_events[0]['infow'] .= date( $date_format, strtotime( $ecwd_event_date_to ) ) . ' ' . date( $time_format, strtotime( $ecwd_event_date_to ) );
						}
					}
					$map_events[0]['infow'] .= ' </span>
		 </div>';

					$markers = json_encode( $map_events );
					?>
					<div class="ecwd-show-map">
						<div class="ecwd_map_div">
						</div>
								<textarea class="hidden ecwd_markers"
								          style="display: none;"><?php echo $markers; ?></textarea>
					</div>
				<?php } ?>
				<div class="clear"></div>


				<div class="ecwd-event-video">
					<?php
					if ( strpos( $ecwd_event_video, 'youtube' ) > 0 ) {
						parse_str( parse_url( $ecwd_event_video, PHP_URL_QUERY ), $video_array_of_vars );
						if ( isset( $video_array_of_vars['v'] ) && $video_array_of_vars['v'] ) {
							?>
							<object data="http://www.youtube.com/v/<?php echo $video_array_of_vars['v'] ?>"
							        type="application/x-shockwave-flash" width="400" height="300">
								<param name="src"
								       value="http://www.youtube.com/v/<?php echo $video_array_of_vars['v'] ?>"/>
							</object>
						<?php }
					} elseif ( strpos( $ecwd_event_video, 'vimeo' ) > 0 ) {
						$videoID = explode( '/', $ecwd_event_video );
						$videoID = $videoID[ count( $videoID ) - 1 ];
						if ( $videoID ) {

							?>
							<iframe
								src="http://player.vimeo.com/video/<?php echo $videoID; ?>?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff"
								width="" height="" frameborder="0" webkitAllowFullScreen mozallowfullscreen
								allowFullScreen></iframe>
						<?php }


					}

					?>
				</div>
				<div>
					<?php the_content(); ?>
				</div>


				<?php
				if ( ! isset( $ecwd_options['related_events'] ) || $ecwd_options['related_events'] == 1 ) {
					$post_cats = wp_get_post_terms( $post_id, ECWD_PLUGIN_PREFIX . '_event_category' );
					$cat_ids   = wp_list_pluck( $post_cats, 'term_id' );
					$post_tags = wp_get_post_terms( $post_id, ECWD_PLUGIN_PREFIX . '_event_tag' );
					$tag_ids   = wp_list_pluck( $post_tags, 'term_id' );
					$events    = array();
					$today     = date( 'Y-m-d' );

					$args                = array(
						'numberposts' => - 1,
						'post_type'   => ECWD_PLUGIN_PREFIX . '_event',
						'tax_query'   => array(
							array(
								'taxonomy' => ECWD_PLUGIN_PREFIX . '_event_category',
								'terms'    => $cat_ids,
								'field'    => 'term_id',
							)
						),
						'orderby'     => 'meta_value',
						'order'       => 'ASC'
					);
					$ecwd_events_by_cats = get_posts( $args );
					$args                = array(
						'numberposts' => - 1,
						'post_type'   => ECWD_PLUGIN_PREFIX . '_event',
						'tax_query'   => array(
							array(
								'taxonomy' => ECWD_PLUGIN_PREFIX . '_event_tag',
								'terms'    => $tag_ids,
								'field'    => 'term_id',
							),
						),
						'orderby'     => 'meta_value',
						'order'       => 'ASC'
					);
					$ecwd_events_by_tags = get_posts( $args );
					$ecwd_events         = array_merge( $ecwd_events_by_tags, $ecwd_events_by_cats );
					$ecwd_events         = array_map( "unserialize", array_unique( array_map( "serialize", $ecwd_events ) ) );
					wp_reset_postdata();
					wp_reset_query();

					foreach ( $ecwd_events as $ecwd_event ) {
						if ( $ecwd_event->ID != $post_id ) {
							$term_metas = '';
							$categories = get_the_terms( $ecwd_event->ID, ECWD_PLUGIN_PREFIX . '_event_category' );
							if ( is_array( $categories ) ) {
								foreach ( $categories as $category ) {
									$term_metas         = get_option( "ecwd_event_category_$category->term_id" );
									$term_metas['id']   = $category->term_id;
									$term_metas['name'] = $category->name;
									$term_metas['slug'] = $category->slug;
								}
							}
							$ecwd_event_metas                                      = get_post_meta( $ecwd_event->ID, '', true );
							$ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_url' ] = array( 0 => '' );
							if ( ! isset( $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_location' ] ) ) {
								$ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_location' ] = array( 0 => '' );
							}
							if ( ! isset( $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_lat_long' ] ) ) {
								$ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_lat_long' ] = array( 0 => '' );
							}
							if ( ! isset( $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_to' ] ) ) {
								$ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_to' ] = array( 0 => '' );
							}
							if ( ! isset( $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_from' ] ) ) {
								$ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_from' ] = array( 0 => '' );
							}

							$permalink                 = get_permalink( $ecwd_event->ID );
							$events[ $ecwd_event->ID ] = new ECWD_Event( $ecwd_event->ID, 0, $ecwd_event->post_title, $ecwd_event->post_content, $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_location' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_from' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_to' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_url' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_lat_long' ][0], $permalink, $ecwd_event, $term_metas, $ecwd_event_metas );
						}
					}

					$d      = new ECWD_Display( 0, '', '', $today );
					$events = $d->get_event_days( $events );



					?>

					<?php if ( count( $events ) > 0 ) {
						$events = $d->events_unique( $events );
						?>

						<div class="ecwd-venue-events">
							<h3> <?php _e( 'Related events', 'ecwd' ) ?></h3>

							<div class="upcoming_events_slider">

								<div class="upcoming_events_slider-arrow-left"><a href="#left"></a></div>
								<div class="upcoming_events_slider-arrow-right"><a href="#right"></a></div>
								<ul>
									<?php
									foreach ( $events as $ecwd_event ) {
										?>
										<li itemscope itemtype="http://schema.org/Event" class="upcoming_events_item"
										    data-date="<?php echo date( 'Y-m-d', strtotime( $ecwd_event['from'] ) ); ?>">
											<div class="upcoming_event_container">
												<?php $image_class = '';
												$image             = getAndReplaceFirstImage( $ecwd_event['post']->post_content );
												if ( ! has_post_thumbnail( $ecwd_event['id'] ) && $image['image'] == "" ) {
													$image_class = "ecwd-no-image";
												}
												echo '<div class="upcoming_events_item-img ' . $image_class . '">';
												if ( get_the_post_thumbnail( $ecwd_event['id'] ) ) {
													echo get_the_post_thumbnail( $ecwd_event['id'], 'thumb' );
												} elseif ( $image['image'] != null ) {
													echo '<img src="' . $image['image'] . '" />';
													$ecwd_event['post']->post_content = $image['content'];
												}
												echo '</div>'; ?>
												<div class="event-title" itemprop="name">
													<a href="<?php echo $ecwd_event['permalink'] ?>"><?php echo $ecwd_event['title'] ?></a>
												</div>
												<div class="event-date" itemprop="startDate"
												     content="<?php echo date( 'Y-m-d', strtotime( $ecwd_event['from'] ) ) . 'T' . date( 'H:i', strtotime( $ecwd_event['starttime'] ) ) ?>">

													<?php
													if ( isset( $ecwd_event['all_day_event'] ) && $ecwd_event['all_day_event'] == 1 ) {
														echo date( $date_format, strtotime( $ecwd_event['from'] ) );
														if ( $ecwd_event['to'] && date( $date_format, strtotime( $ecwd_event['from'] ) ) !== date( $date_format, strtotime( $ecwd_event['to'] ) ) ) {
															echo ' - ' . date( $date_format, strtotime( $ecwd_event['to'] ) );
														}
														echo ' ' . __( 'All day', 'ecwd' );
													} else {

														echo date( $date_format, strtotime( $ecwd_event['from'] ) ) . ' ' . date( $time_format, strtotime( $ecwd_event['starttime'] ) );

														if ( $ecwd_event['to'] ) {
															echo ' - ' . date( $date_format, strtotime( $ecwd_event['to'] ) ) . ' ' . date( $time_format, strtotime( $ecwd_event['endtime'] ) );
														}
													} ?>
												</div>


												<div
													class="upcoming_events_item-content"><?php echo( $ecwd_event['post']->post_content ? $ecwd_event['post']->post_content : 'No additional details for this event.' ); ?> </div>
											</div>
										</li>
									<?php
									}
									?>
								</ul>
							</div>
						</div>

					<?php } ?>
				<?php }?>

			</div>
		</div> <!-- #post-x -->
		<?php

		if ( comments_open() && $post->comment_status == 'open' ) { ?>
			<div class="ecwd-comments">

				<?php echo comments_template(); ?>
			</div>
		<?php } ?>
	<?php endwhile; ?>

</div>

<?php get_footer(); ?>
