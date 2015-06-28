<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

//global $ecwd_options;

?>

<div class="wrap">
    <?php settings_errors(); ?>
    <div id="ecwd-settings">
        <div id="ecwd-settings-content">
            <h2 id="add_on_title"><?php echo esc_html(get_admin_page_title()); ?></h2>
            <?php
            if($addons){
                foreach ($addons as $addon) {
                    ?>
                    <div class="ecwd-add-on">
                        <a href="<?php echo $addon['url']?>" target="_blank">
                            <?php if($addon['image']){?>
                                <img src="<?php echo $addon['image']?>" />
                            <?php }?>
                            <h2><?php echo $addon['name']?></h2>
                        </a>
                        <div class="ecwd-addon-descr">
                            <?php if($addon['icon']){?>
                                <img src="<?php echo $addon['icon']?>" />
                            <?php }?>
                            <?php echo $addon['description']?>
                            <?php if($addon['url']!=='#'){?>
                                <a href="<?php echo $addon['url']?>" target="_blank"><span>GET THIS ADD ON</span></a>
                            <?php }else{?>
                                <div class="ecwd_coming_soon" >
                                    <img src="<?php echo plugins_url( '../../assets/coming_soon.png', __FILE__ );?>" />
                                </div>
                            <?php }?>
                        </div>
                    </div>
                <?php
                }
            }
            ?>

        </div>
        <!-- #ecwd-settings-content -->
    </div>
    <!-- #ecwd-settings -->
</div><!-- .wrap -->