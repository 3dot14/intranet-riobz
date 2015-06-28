<?php
/**
 * Display for Calendar post metas
 */
global $post;
$post_id = $post->ID;
// Load up all post meta data
$ecwd_calendar_description = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_description', true);
$ecwd_calendar_id = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_id', true);

$ecwd_calendar_default_year = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_default_year', true);
$ecwd_calendar_default_month = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_default_month', true);
$ecwd_calendar_theme = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_theme', true);
$ecwd_calendar_12_hour_time_format = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_12_hour_time_format', true);
?>

<table class="form-table">
    <?php if($post->post_status!=='auto-draft'){?>
        <tr>
            <th></th>
            <td><a href="#ecwd-modal-preview"><?php _e('Preview', 'ecwd');?> / <?php _e('Add Event', 'ecwd'); ?></a></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Calendar Shortcode', 'ecwd'); ?></th>
            <td>
                <code>[ecwd id="<?php echo $post_id; ?>"]</code>
                <div id="ecwd-modal-preview" class="ecwd-modal">
                    <div class="ecwd-preview">
                        <a href="#ecwd-close" title="Close" class="ecwd-close close"></a>
                        <div class="event_cal_add hidden">
                            <div>
                                Dates:
                            <span class="ecwd-dates">

                            </span>
                            </div>
                            <div>
                                Title:
                                <input type="text" name="ecwd_event_name"  id="ecwd_event_name"/>
                                <br/>
                                <span class="ecwd_error"></span>
                                <input type="hidden" id="ecwd_event_date_from" name="ecwd_event_date_from" />
                                <input type="hidden" id="ecwd_event_date_to" name="ecwd_event_date_to" />

                                <span id="add_event_to_cal" class="add_event_to_cal"> Save</span>
                                <span class="ecwd_notification"> </span>
                            </div>
                            <span class="close ecwd-close"></span>
                        </div>
                        <?php echo ecwd_print_calendar($post_id, 'full');?>
                    </div>
                </div>

                <p class="description">
                    <?php _e('Copy and paste this shortcode to display this Calendar event on any post or page.', 'ecwd'); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Events', 'ecwd'); ?></th>
            <td>
                <div class="ecwd-events">
                    <?php if ($events) { ?>
                        <?php foreach ($events as $event) { ?>
                            <span class="ecwd-calendar-event"> <span><?php echo $event->post_title; ?></span>
                            <input type="hidden" name="ecwd-calendar-event-id[]" value="<?php echo $event->ID; ?>"/>
                            <span class="ecwd-calendar-event-edit"><a href="post.php?post=<?php echo $event->ID; ?>&action=edit" target="_blank">e</a></span>
                            <span class="ecwd-calendar-event-delete">x</span>
                        </span>
                        <?php } ?>
                    <?php } ?>
                </div>

            <span class="ecwd-calendar-event-add">
                <?php if($excluded_events){?>
                <a href="#ecwd-modal"><?php _e('Select Events from the list', 'ecwd'); ?></a>
                <a href="#ecwd-modal"><span class="add_event_plus">+</span></a></span>
                <?php }else{?>
                    <a href="<?php echo get_admin_url()?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id;?>" target="_blank"><?php _e('Add Event', 'ecwd'); ?></a>
                    <a href="<?php echo get_admin_url()?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id;?>" target="_blank"><span class="add_event_plus">+</span></a></span>
                <?php }?>


                <div id="ecwd-modal" class="ecwd-modal">
                    <div class="ecwd-excluded-events">
                        <a href="#ecwd-close" title="Close" class="ecwd-close"></a>

                        <h2><?php _e('Events', 'ecwd'); ?></h2>

                        <?php if ($excluded_events) { ?>
                            <?php foreach ($excluded_events as $event) { ?>
                                <span class="ecwd-calendar-event"><span><?php echo $event->post_title; ?></span>
                                <input type="hidden" name="ecwd-calendar-excluded-event-id[]"
                                       value="<?php echo $event->ID; ?>"/>
                                <span class="ecwd-calendar-event-edit hidden"><a href="post.php?post=<?php echo $event->ID; ?>&action=edit" target="_blank">e</a></span>
                                <span class="ecwd-calendar-event-add">+</span>
                            </span>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

            </td>
        </tr>
    <?php }?>
    <tr>
        <th scope="row"><?php _e('Theme', 'ecwd'); ?></th>
        <td>
            <a href="<?php echo admin_url('edit.php?post_type=ecwd_calendar&page=ecwd_themes');?>"><?php _e('Default', 'ecwd');?></a> <sup style="color: #ba281e;">pro</sup>
        </td>
    </tr>
</table>