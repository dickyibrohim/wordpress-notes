<?php
/**
 * Snippet Name: WP Query Posts by Custom Field
 * Description: Example snippet to query published posts based on multiple custom field values (e.g., photos = 0 AND videos = 0).
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Customization:
 * Update the 'meta_query' array below with your specific custom field keys and values.
 */

function ibrohim_query_posts_by_custom_fields() {
    $args = array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'meta_query'  => array(
            'relation' => 'AND',
            array(
                'key'     => 'photos',
                'value'   => '0',
                'compare' => '=',
            ),
            array(
                'key'     => 'videos',
                'value'   => '0',
                'compare' => '=',
            ),
        ),
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            // Process the post here
        }
        wp_reset_postdata();
    }
}
