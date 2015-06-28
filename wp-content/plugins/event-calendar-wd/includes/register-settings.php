<?php

/**
 * Register all settings needed for the Settings API.
 *
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( isset( $_GET[ ECWD_PLUGIN_PREFIX . '_clear_cache' ] ) && $_GET[ ECWD_PLUGIN_PREFIX . '_clear_cache' ] == 1 ) {
	$cpt     = ECWD_Cpt::get_instance();
	$cleared = $cpt->delete_transient();
	if ( $cleared ) {
		try {
			echo '<div class= "updated" ><p> ' . __( 'Cache has been deleted.', 'ecwd' ) . '</p></div>';
		} catch ( Exception $e ) {

		}
	}

}

/**
 *  Main function to register all of the plugin settings
 */
function ecwd_register_settings() {

	global $ecwd_settings;
	global $ecwd_tabs;

	$ecwd_tabs     = array(
		'general'   => 'General',
		'fb'        => 'FB settings',
		'gcal'      => 'Gcal settings',
		'ical'      => 'Ical settings',
		'add_event' => 'Add Event'
	);
	$ecwd_settings = array(
		/* General Settings */

		'general' => array(
			'time_zone'      => array(
				'id'   => 'time_zone',
				'name' => __( 'TimeZone', 'ecwd' ),
				'desc' => __( 'If the timezone is not set, the server timezone will be used (if set in php settings), otherwise the Europe/Berlin timezone will be used', 'ecwd' ),
				'size' => 'medium-text',
				'type' => 'text'
			),
			'date_format'    => array(
				'id'   => 'date_format',
				'name' => __( 'Date format', 'ecwd' ),
				'desc' => __( 'Set the format for displaying event dates. Ex Y-m-d or Y/m/d', 'ecwd' ),
				'size' => 'medium-text',
				'type' => 'text'
			),
			'time_format'    => array(
				'id'   => 'time_format',
				'name' => __( 'Time format', 'ecwd' ),
				'desc' => __( 'Set the format for displaying event time. Ex H:i or H/i', 'ecwd' ),
				'size' => 'medium-text',
				'type' => 'text'
			),
			'week_starts'    => array(
				'id'   => 'week_starts',
				'name' => __( 'Week start day', 'ecwd' ),
				'desc' => __( 'Define the starting day for the week.', 'ecwd' ),
				'size' => 'medium-text',
				'type' => 'week_select'
			),
			'enable_rewrite' => array(
				'id'      => 'enable_rewrite',
				'name'    => __( 'Enable rewrite', 'ecwd' ),
				'default' => 'events',
				'desc'    => __( 'Check yes to enable event(s) url rewrite rule.', 'ecwd' ),
				'type'    => 'radio',
				'default' => 1
			),
			'events_slug'    => array(
				'id'      => 'events_slug',
				'name'    => __( 'Events slug', 'ecwd' ),
				'default' => 'events',
				'desc'    => __( 'Define the slug for the events list page.', 'ecwd' ),
				'size'    => 'medium-text',
				'type'    => 'text'
			),
			'event_slug'     => array(
				'id'      => 'event_slug',
				'name'    => __( 'Single Event slug', 'ecwd' ),
				'default' => 'event',
				'desc'    => __( 'Define the slug for the single event page.', 'ecwd' ),
				'size'    => 'medium-text',
				'type'    => 'text'
			),
			'event_comments' => array(
				'id'   => 'event_comments',
				'name' => __( 'Enable comments for events', 'ecwd' ),
				'desc' => __( 'Check to enable commenting.', 'ecwd' ),
				'type' => 'checkbox'
			),
			'event_loop'     => array(
				'id'   => 'event_loop',
				'name' => __( 'Include events in main loop', 'ecwd' ),
				'desc' => __( 'Check to display events within website post list in main pages.', 'ecwd' ),
				'type' => 'checkbox'
			),
			'social_icons'   => array(
				'id'   => 'social_icons',
				'name' => __( 'Enable Social Icons', 'ecwd' ),
				'desc' => __( 'Check to display social icons in event, organizer and venue pages.', 'ecwd' ),
				'type' => 'checkbox'
			),
			'related_events'     => array(
				'id'   => 'related_events',
				'name' => __( 'Show related events in the event page', 'ecwd' ),
				'desc' =>'',
				'type' => 'radio',
				'default'=>1
			)

		)

	);

	/* If the options do not exist then create them for each section */
	if ( false == get_option( ECWD_PLUGIN_PREFIX . '_settings' ) ) {
		add_option( ECWD_PLUGIN_PREFIX . '_settings' );
	}


	/* Add the  Settings sections */

	foreach ( $ecwd_settings as $key => $settings ) {

		add_settings_section(
			ECWD_PLUGIN_PREFIX . '_settings_' . $key, __( $ecwd_tabs[ $key ], 'ecwd' ), '__return_false', ECWD_PLUGIN_PREFIX . '_settings_' . $key
		);


		foreach ( $settings as $option ) {
			add_settings_field(
				ECWD_PLUGIN_PREFIX . '_settings_' . $key . '[' . $option['id'] . ']', $option['name'], function_exists( ECWD_PLUGIN_PREFIX . '_' . $option['type'] . '_callback' ) ? ECWD_PLUGIN_PREFIX . '_' . $option['type'] . '_callback' : ECWD_PLUGIN_PREFIX . '_missing_callback', ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_' . $key, ecwd_get_settings_field_args( $option, $key )
			);
		}

		/* Register all settings or we will get an error when trying to save */
		register_setting( ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_sanitize' );
	}
}

add_action( 'admin_init', ECWD_PLUGIN_PREFIX . '_register_settings' );

/*
 * Return generic add_settings_field $args parameter array.
 *
 * @param   string  $option   Single settings option key.
 * @param   string  $section  Section of settings apge.
 * @return  array             $args parameter to use with add_settings_field call.
 */


function ecwd_get_settings_field_args( $option, $section ) {
	$settings_args = array(
		'id'      => $option['id'],
		'desc'    => $option['desc'],
		'name'    => $option['name'],
		'section' => $section,
		'size'    => isset( $option['size'] ) ? $option['size'] : null,
		'options' => isset( $option['options'] ) ? $option['options'] : '',
		'std'     => isset( $option['std'] ) ? $option['std'] : '',
		'href'    => isset( $option['href'] ) ? $option['href'] : '',
		'default'    => isset( $option['default'] ) ? $option['default'] : ''
	);

	// Link label to input using 'label_for' argument if text, textarea, password, select, or variations of.
	// Just add to existing settings args array if needed.
	if ( in_array( $option['type'], array( 'text', 'select', 'textarea', 'password', 'number' ) ) ) {
		$settings_args = array_merge( $settings_args, array( 'label_for' => ECWD_PLUGIN_PREFIX . '_settings_' . $section . '[' . $option['id'] . ']' ) );
	}

	return $settings_args;
}


/*
 * Week select callback function
 */

function ecwd_week_select_callback( $args ) {
	global $ecwd_options;
	$html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="0" ' . selected( 0, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>Sunday</option>
        <option value="1" ' . selected( 1, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>Monday</option>
    </select>' . "\n";

	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}

function ecwd_update_select_callback( $args ) {
	global $ecwd_options;
	$html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="1" ' . selected( 1, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>1 hour</option>
        <option value="2" ' . selected( 2, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>2 hours</option>
        <option value="3" ' . selected( 3, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>3 hours</option>
        <option value="5" ' . selected( 5, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>5 hours</option>
        <option value="12" ' . selected( 12, isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>12 hours</option>
    </select>' . "\n";

	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}

function ecwd_status_select_callback( $args ) {
	global $ecwd_options;
	$html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="draft" ' . selected( 'draft', isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>Draft</option>
        <option value="publish" ' . selected( 'publish', isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>Publish</option>
        <option value="pending" ' . selected( 'pending', isset( $ecwd_options[ $args['id'] ] ) ? $ecwd_options[ $args['id'] ] : '', false ) . '>Pending</option>
    </select>' . "\n";

	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}

/*
 * Single checkbox callback function
 */

function ecwd_checkbox_callback( $args ) {
	global $ecwd_options;
	$checked = isset( $ecwd_options[ $args['id'] ] ) ? checked( 1, $ecwd_options[ $args['id'] ], false ) : (isset( $args['default'])?checked( 1, $args['default'], false):'');
	$html    = "\n" . '<div class="checkbox-div"><input type="checkbox" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']"></label></div>' . "\n";
	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}


/*
 * Radio callback function
 */

function ecwd_radio_callback( $args ) {
	global $ecwd_options;

	$checked_no = isset( $ecwd_options[ $args['id'] ] ) ? checked( 0, $ecwd_options[ $args['id'] ], false ) : '';

	$checked_yes = isset( $ecwd_options[ $args['id'] ] ) ? checked( 1, $ecwd_options[ $args['id'] ], false ) : (isset($args['default'])? checked( 1, $args['default'], false ):'');


	$html    = "\n" . ' <div class="checkbox-div"><input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_yes" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked_yes . '/><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_yes"></label></div> <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_yes">Yes</label>' . "\n";
	$html    .=  '<div class="checkbox-div"> <input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_no" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="0" ' . $checked_no . '/><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_no"></label></div> <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_no">No</label>' . "\n";
	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}

/*
 * Multiple checkboxs callback function
 */
function ecwd_cats_checkbox_callback( $args ) {
	global $ecwd_options;
	$categories = get_categories( array( 'taxonomy' => ECWD_PLUGIN_PREFIX . '_event_category' ) );
	$html       = '';
	if ( ! empty( $categories ) ) {
		foreach ( $categories as $cat ) {
			$checked = ( isset( $ecwd_options[ $args['id'] ] ) && in_array( $cat->term_id, $ecwd_options[ $args['id'] ] ) ) ? 'checked="checked"' : '';
			$html .= "\n" . '<div class="checkbox-div"><input type="checkbox" id="ecwd_settings_' . $args['section'] . '_' . $args['id'] . '[' . $cat->term_id . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . '][]" value="' . $cat->term_id . '" ' . $checked . '/><label for="ecwd_settings_' . $args['section'] . '_' . $args['id'] . '[' . $cat->term_id . ']"></label></div><label for="ecwd_settings_' . $args['section'] . '_' . $args['id'] . '[' . $cat->term_id . ']">' . $cat->name . '</label>' . "\n";
		}
	}
	//$html = "\n" . '<input type="checkbox" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>' . "\n";

	// Render description text directly to the right in a label if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}

/**
 * Textbox callback function
 * Valid built-in size CSS class values:
 * small-text, regular-text, large-text
 *
 */
function ecwd_text_callback( $args ) {
	global $ecwd_options;

	if ( isset( $ecwd_options[ $args['id'] ] ) ) {
		$value = $ecwd_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : '';
	$html = "\n" . '<input type="text" class="' . $size . '" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>' . "\n";

	// Render and style description text underneath if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}


/**
 * Button callback function
 * Valid built-in size CSS class values:
 * small-text, regular-text, large-text
 *
 */
function ecwd_link_callback( $args ) {
	global $ecwd_options;

	$value = isset( $args['name'] ) ? $args['name'] : '';
	$href  = isset( $args['href'] ) ? $args['href'] : '#';
	$html  = "\n" . '<a class="button" href="' . $href . '" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']"  >' . esc_attr( $value ) . '</a>' . "\n";
	// Render and style description text underneath if it exists.
	if ( ! empty( $args['desc'] ) ) {
		$html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
	}

	echo $html;
}

/*
 * Function we can use to sanitize the input data and return it when saving options
 * 
 */

function ecwd_settings_sanitize( $input ) {
	//add_settings_error( 'ecwd-notices', '', '', '' );
	return $input;
}

/*
 *  Default callback function if correct one does not exist
 * 
 */

function ecwd_missing_callback( $args ) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'ecwd' ), $args['id'] );
}

/*
 * Function used to return an array of all of the plugin settings
 * 
 */

function ecwd_get_settings() {
	$ecwd_tabs = array(
		'general'   => 'General',
		'fb'        => 'FB settings',
		'gcal'      => 'Gcal settings',
		'ical'      => 'Ical settings',
		'add_event' => 'Add Event'
	);
	// Set default settings
	// If this is the first time running we need to set the defaults
	if ( ! get_option( ECWD_PLUGIN_PREFIX . '_upgrade_has_run' ) ) {

		$general                  = get_option( ECWD_PLUGIN_PREFIX . '_settings_general' );
		$general['save_settings'] = 1;

		update_option( ECWD_PLUGIN_PREFIX . '_settings_general', $general );
	}
	$general_settings = array();
	foreach ( $ecwd_tabs as $key => $settings ) {
		$general_settings += is_array( get_option( ECWD_PLUGIN_PREFIX . '_settings_' . $key ) ) ? get_option( ECWD_PLUGIN_PREFIX . '_settings_' . $key ) : array();
	}


	return $general_settings;
}
