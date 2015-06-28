<?php
/**
 * Delete all settings when the plugin is uninstalled.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$general = get_option( ECWD_PLUGIN_PREFIX.'_settings_general' );

// If this is empty then it means it is unchecked and we should delete everything
if ( empty( $general['save_settings'] ) ) {
	
	
	// Remove ecwd calendar posts
	$posts = get_posts( array( 
		'post_type' => ECWD_PLUGIN_PREFIX.'_event',
		/*'post_status' => array( 
			'any',
			'trash',
			'auto-draft'
		)*/
	));

	foreach( $posts as $post ) {
		delete_transient( ECWD_PLUGIN_PREFIX.'_event_' . $post->ID );
		wp_delete_post( $post->ID, true );
	}

	// Remove all post meta
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_gcalendar_id' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_retrieve_from' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_retrieve_until' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_retrieve_max' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_date_format' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_time_format' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_cache' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_multi_day_events' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_display_mode' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_custom_from' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_custom_until' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_paging' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_list_max_num' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_list_max_length' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_list_start_offset_num' );
	delete_post_meta_by_key( ECWD_PLUGIN_PREFIX.'_list_start_offset_direction' );

	// Remove options
	delete_option( ECWD_PLUGIN_PREFIX.'_upgrade_has_run' );
	delete_option( ECWD_PLUGIN_PREFIX.'_version' );
	delete_option( ECWD_PLUGIN_PREFIX.'_settings_general' );
	delete_option( ECWD_PLUGIN_PREFIX.'_cpt_setup' );

	// Remove widgets
	delete_option( ECWD_PLUGIN_PREFIX.'_calendar_widget' );
}
