<?php
/**
 * Snippet Name: WC Hide Price Suffix on Shop & Single Product
 * Description: Removes the price display suffix (like "incl. VAT") on the main shop and individual product pages, while keeping it elsewhere.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Customization:
 * Change the conditional checks inside if() to include or exclude specific pages.
 */

add_filter( 'woocommerce_get_price_suffix', 'ibrohim_hide_price_suffix_shop', 10, 4 );

function ibrohim_hide_price_suffix_shop( $suffix, $product, $price, $qty ) {
    if ( is_shop() || is_product() ) {
        return '';
    }
    return $suffix;
}
