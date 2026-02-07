<?php
/**
 * Snippet Name: Elementor Custom Query: Related Posts via ACF Relationship
 * Description: Registers a custom query for Elementor to display related posts based on an ACF Relationship field.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Customization:
 * 1. Change 'relatedPosts' in the hook to your desired Query ID.
 * 2. Update $post_type to your specific Post Type slug.
 * 3. Update $relationship_field to your ACF Relationship field name.
 */

add_action( 'elementor/query/relatedPosts', 'ibrohim_elementor_rel_query' );

function ibrohim_elementor_rel_query( $query ) {
    static $is_running = false;

    if ( $is_running ) {
        return;
    }

    // Adjust these to your needs
    $post_type          = 'your_post_type_slug';
    $relationship_field = 'your_acf_relationship_field';

    if ( is_singular( $post_type ) ) {
        $is_running      = true;
        $current_post_id = get_queried_object_id();
        $related_ids     = get_field( $relationship_field, $current_post_id );

        if ( ! empty( $related_ids ) && is_array( $related_ids ) ) {
            $query->set( 'post__in', $related_ids );
            $query->set( 'post_type', $post_type );
            $query->set( 'orderby', 'post__in' );
        } else {
            $query->set( 'post__in', [ 0 ] );
        }

        $is_running = false;
    }
}
