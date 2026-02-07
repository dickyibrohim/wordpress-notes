<?php
/**
 * Snippet Name: WC Auto-Complete Virtual Orders
 * Description: Automatically completes WooCommerce orders if all items are virtual or downloadable. Skips manual bank transfers (BACS).
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Customization:
 * To include BACS (Direct Bank Transfer) in auto-completion, remove the check for 'bacs' in the code.
 */

add_action( 'woocommerce_thankyou', 'ibrohim_auto_complete_virtual_orders' );

function ibrohim_auto_complete_virtual_orders( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order instanceof WC_Order ) {
		return;
	}

	if ( $order->get_status() === 'completed' ) {
		return;
	}

	$payment_method = $order->get_payment_method();

	// Skip manual bank transfer orders by default
	if ( $payment_method === 'bacs' ) {
		return;
	}

	$virtual_downloadable = true;

	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();

		if ( ! $product || ( ! $product->is_virtual() && ! $product->is_downloadable() ) ) {
			$virtual_downloadable = false;
			break;
		}
	}

	if ( $virtual_downloadable ) {
		$order->update_status( 'completed' );
	}
}
