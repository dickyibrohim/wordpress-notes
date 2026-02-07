<?php
/**
 * Snippet Name: WP Query Sort & Filter by Custom Date
 * Description: Custom Elementor query to sort posts by a custom date field and filter out posts whose date has already passed.
 * Author: Dicky Ibrohim
 */

/**
 * Customization:
 * - Change 'anfangsdatum' to your custom field name.
 * - Change 'event_sort_date' to your specific Elementor Query ID.
 */

add_action( 'elementor/query/event_sort_date', function( $query ) {
    // 1. Set sorting by the custom date field
    $query->set( 'meta_key', 'anfangsdatum' );
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'order', 'ASC' );

    // 2. Add filter to hide expired posts (where date < today)
    $meta_query = $query->get( 'meta_query' );

    if ( ! $meta_query ) {
        $meta_query = [];
    }

    $meta_query[] = [
        'key'     => 'anfangsdatum',
        'value'   => date('Ymd'),
        'compare' => '>=',
        'type'    => 'DATE',
    ];

    $query->set( 'meta_query', $meta_query );
});
