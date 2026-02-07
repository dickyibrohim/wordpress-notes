<?php
/**
 * Snippet Name: WC Hide Price Suffix on Shop & Single Product
 * Description: Removes the price display suffix (like "incl. VAT") on the main shop and individual product pages, while keeping it elsewhere.
 * Author: Dicky Ibrohim
 */

/**
 * Customization:
 * Change the conditional checks inside if() to include or exclude specific pages.
 */

add_filter( 'woocommerce_get_price_suffix', function( $suffix, $product, $price, $qty ) {
    if ( is_shop() || is_product() ) {
        return '';
    }
    return $suffix;
}, 10, 4 );
