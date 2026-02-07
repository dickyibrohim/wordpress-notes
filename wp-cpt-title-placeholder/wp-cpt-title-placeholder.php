<?php
/**
 * Snippet Name: WP CPT Title Placeholder
 * Description: Customizes the "Enter title here" placeholder text for specific Custom Post Types in the WordPress admin editor.
 * Author: Dicky Ibrohim
 */

/**
 * Customization:
 * - Update 'YOUR-CPT-SLUG-HERE' to your actual Custom Post Type slug.
 * - Change the placeholder text as needed.
 */

add_filter( 'enter_title_here', 'wp_customize_cpt_title_placeholder', 20, 2 );

function wp_customize_cpt_title_placeholder( $title, $post ) {
    if ( $post->post_type == 'YOUR-CPT-SLUG-HERE' ) {
        return 'Custom new placeholder for title here';
    }
    return $title;
}
