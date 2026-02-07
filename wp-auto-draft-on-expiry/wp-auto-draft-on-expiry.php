<?php
/**
 * Snippet Name: WP Auto-Draft on Expiry
 * Description: Automatically changes the status of published posts to 'draft' when a specified date in a custom field has passed.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Customization:
 * - Update 'lehrgaenge' to your Custom Post Type slug.
 * - Update 'anfangsdatum' to your custom field name (Date format: Ymd).
 */

add_action('init', 'ibrohim_auto_draft_expired_posts');

function ibrohim_auto_draft_expired_posts() {
    $args = array(
        'post_type'   => 'lehrgaenge', // Change this to your CPT
        'post_status' => 'publish',
        'meta_query'  => array(
            array(
                'key'     => 'anfangsdatum', // Change this to your custom field
                'value'   => date('Ymd'),
                'compare' => '<=',
                'type'    => 'DATE'
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wp_update_post(array(
                'ID'          => get_the_ID(),
                'post_status' => 'draft'
            ));
        }
        wp_reset_postdata();
    }
}
