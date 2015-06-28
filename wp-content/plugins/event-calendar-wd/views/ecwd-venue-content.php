<?php
/**
 * Display for Event Custom Post Types
 */

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

$post_id   = $post->ID;
$venue_url = get_permalink( $post_id );
$meta      = get_post_meta( $post_id );
$events    = array();

// Load up all post meta data
$ecwd_venue_location = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_venue_location', true );
$ecwd_venue_latlong  = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_venue_lat_long', true );
$ecwd_venue_zoom  = get_post_meta( $post->ID, ECWD_PLUGIN_PREFIX . '_map_zoom', true );
if(!$ecwd_venue_zoom){
	$ecwd_venue_zoom = 17;
}

$today = date( 'Y-m-d' );

$args        = array(
	'numberposts' => - 1,
	'post_type'   => ECWD_PLUGIN_PREFIX . '_event',
	'meta_query'  => array(
		array(
			'key'   => ECWD_PLUGIN_PREFIX . '_event_venue',
			'value' => $post->ID
		)
	),
	'meta_key'    => ECWD_PLUGIN_PREFIX . '_event_date_from',
	'orderby'     => 'meta_value',
	'order'       => 'ASC'
);
$ecwd_events = get_posts( $args );

//var_dump($ecwd_events);
foreach ( $ecwd_events as $ecwd_event ) {

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


	$permalink = get_permalink( $ecwd_event->ID );


	$events[ $ecwd_event->ID ] = new ECWD_Event( $ecwd_event->ID, 0, $ecwd_event->post_title, $ecwd_event->post_content, $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_location' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_from' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_date_to' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_event_url' ][0], $ecwd_event_metas[ ECWD_PLUGIN_PREFIX . '_lat_long' ][0], $permalink, $ecwd_event, $term_metas, $ecwd_event_metas );

}

$d      = new ECWD_Display( 0, '', '', $today );
$events = $d->get_event_days( $events );

?>
<?php

echo $content;
?>

<div class="ecwd-venue">
	<?php if ( $social_icons ) { ?>
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
	if ( $ecwd_venue_latlong ) {
		$map_events               = array();
		$map_events[0]['latlong'] = explode( ',', $ecwd_venue_latlong );
		$map_events[0]['zoom'] = $ecwd_venue_zoom;
		$map_events[0]['infow']   = '<div class="ecwd_map_venue">';
		$map_events[0]['infow'] .= '<span class="name">' . $post->post_title . '</span><br />';
		$map_events[0]['infow'] .= '<span class="location">' . $ecwd_venue_location . '</span>';
		$map_events[0]['infow'] .= '</div>';
		$markers = json_encode( $map_events );
		?>
		<div class="ecwd-show-map">
			<div id="ecwd_map_div" class="ecwd_map_div">
			</div>
			<textarea id="ecwd_markers" class="hidden" style="display: none;"><?php echo $markers; ?></textarea>
		</div>
	<?php } ?>

	<?php if ( count( $events ) > 0 ) { ?>

		<div class="ecwd-venue-events">
			<h3> <?php echo $post->post_title ?> <?php _e( 'events', 'ecwd' ) ?></h3>

			<div class="upcoming_events_slider">
				<div class="upcoming_events_slider-arrow-left"><a href="#left"></a></div>
				<div class="upcoming_events_slider-arrow-right"><a href="#right"></a></div>
				<ul>
					<?php
					foreach ( $events as $ecwd_event ) {
						if ( strtotime( $ecwd_event['from'] ) >= strtotime( $today ) ) {
							?>
							<li itemscope itemtype="http://schema.org/Event" class="upcoming_events_item">
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
					}
					?>
				</ul>
			</div>
		</div>
	<?php } ?>

</div>
