<?php
/**
 * Snippet Name: WP Auto-Set Featured Image
 * Description: Automatically sets the first attached image as the featured image for a post if one is not already set.
 * Author: Dicky Ibrohim
 */

function wp_auto_set_featured_image_from_attachments() {
  global $post;
  
  if ( ! isset($post->ID) ) return;

  $already_has_thumb = has_post_thumbnail($post->ID);
  
  if (!$already_has_thumb) {
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

// Hook into various post-related actions
add_action('the_post', 'wp_auto_set_featured_image_from_attachments');
add_action('save_post', 'wp_auto_set_featured_image_from_attachments');
add_action('draft_to_publish', 'wp_auto_set_featured_image_from_attachments');
add_action('new_to_publish', 'wp_auto_set_featured_image_from_attachments');
add_action('pending_to_publish', 'wp_auto_set_featured_image_from_attachments');
add_action('future_to_publish', 'wp_auto_set_featured_image_from_attachments');
