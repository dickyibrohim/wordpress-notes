<?php
/**
 * Snippet Name: WP Trash Posts Without Featured Image or Video
 * Description: Automatically moves published posts to the trash if they do not have a featured image AND do not have a video URL.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * WARNING: BACKUP YOUR DATABASE BEFORE RUNNING THIS SCRIPT.
 * This script runs every time you visit the admin posts page. 
 * Once the cleanup is done, please DISABLE OR REMOVE this script.
 */

add_action('admin_init', 'ibrohim_trash_missing_assets');

function ibrohim_trash_missing_assets() {
    // Only run on the posts list page to avoid overhead
    global $pagenow;
    if ($pagenow !== 'edit.php') {
        return;
    }

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 20, // Process in small batches to avoid timeouts
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => '_thumbnail_id',
                'compare' => 'NOT EXISTS'
            ),
            array(
                'key'     => '_video_url',
                'compare' => 'NOT EXISTS'
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wp_update_post(array(
                'ID'          => get_the_ID(),
                'post_status' => 'trash'
            ));
        }
        wp_reset_postdata();
    }
}
