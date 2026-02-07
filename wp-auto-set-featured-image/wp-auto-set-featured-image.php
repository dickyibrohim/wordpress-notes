<?php
/**
 * Snippet Name: WP Auto-Set Featured Image
 * Description: Automatically sets the first attached image as the featured image for a post if one is not already set.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

add_action('the_post', 'ibrohim_auto_set_featured_image');
add_action('save_post', 'ibrohim_auto_set_featured_image');
add_action('draft_to_publish', 'ibrohim_auto_set_featured_image');
add_action('new_to_publish', 'ibrohim_auto_set_featured_image');
add_action('pending_to_publish', 'ibrohim_auto_set_featured_image');
add_action('future_to_publish', 'ibrohim_auto_set_featured_image');

function ibrohim_auto_set_featured_image() {
    global $post;
    
    if ( ! isset($post->ID) ) return;

    if ( ! has_post_thumbnail($post->ID) ) {
        $attached_image = get_children( array(
            'post_parent'    => $post->ID,
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'numberposts'    => 1
        ) );
        
        if ($attached_image) {
            foreach ($attached_image as $attachment_id => $attachment) {
                set_post_thumbnail($post->ID, $attachment_id);
            }
        }
    }
}
