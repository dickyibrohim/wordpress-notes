<?php
/**
 * Snippet Name: WC Packing Slip Bulk Action Notice
 * Description: Displays a custom success banner after bulk handling packing slips or labels in WooCommerce. Resolves issues with default notices being lost on redirect.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

if ( ! defined( 'IBROHIM_PACKSLIP_NOTICE_META' ) ) {
	define( 'IBROHIM_PACKSLIP_NOTICE_META', '_ibrohim_packslip_notice' );
}

add_action('admin_init', 'ibrohim_packslip_notice_handler');
add_action('admin_footer', 'ibrohim_packslip_notice_render');

function ibrohim_packslip_notice_handler() {
    // 1. Process closing the banner
    if ( ! empty( $_GET['ibrohim_notice_close'] ) ) {
        if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'ibrohim_notice' ) ) {
            $run = sanitize_text_field( wp_unslash( $_GET['ibrohim_notice_close'] ) );
            $data = get_user_meta( get_current_user_id(), IBROHIM_PACKSLIP_NOTICE_META, true );
            
            if ( ! empty( $data ) && $data['run'] === $run ) {
                $data['dismissed'] = $run;
                update_user_meta( get_current_user_id(), IBROHIM_PACKSLIP_NOTICE_META, $data );
            }
            wp_safe_redirect( remove_query_arg( array( 'ibrohim_notice_close', '_wpnonce' ) ) );
            exit;
        }
    }

    // 2. Save notice when bulk actions occur
    $hooks = [
        'woocommerce_shiptastic_shipments_table_bulk_action_labels_handled',
        'woocommerce_shiptastic_shipments_table_bulk_action_packing_slips_handled',
        'woocommerce_gzd_shipments_table_bulk_action_labels_handled',
        'woocommerce_gzd_shipments_table_bulk_action_packing_slips_handled',
    ];

    foreach ($hooks as $hook) {
        add_action($hook, function($handler, $action) {
            if ( ! current_user_can( 'edit_shop_orders' ) ) return;
            $message = $handler->get_success_message();
            if ( empty( $message ) ) return;

            update_user_meta( get_current_user_id(), IBROHIM_PACKSLIP_NOTICE_META, [
                'text'      => $message,
                'run'       => md5( $message . microtime() ),
                'rendered'  => '',
                'dismissed' => '',
                'time'      => time(),
            ]);
        }, 20, 2);
    }
}

function ibrohim_packslip_notice_render() {
    if ( ! current_user_can( 'edit_shop_orders' ) ) return;
    $data = get_user_meta( get_current_user_id(), IBROHIM_PACKSLIP_NOTICE_META, true );

    if ( empty( $data ) || ! empty( $data['dismissed'] ) ) return;

    $close_url = add_query_arg([
        'ibrohim_notice_close' => $data['run'],
        '_wpnonce'             => wp_create_nonce( 'ibrohim_notice' ),
    ]);

    ?>
    <div id="ibrohim-banner" style="border-left:4px solid #00d084; background:#f0f6fc; padding:15px; margin:20px 0; border-radius:2px; box-shadow:0 1px 1px rgba(0,0,0,0.04);">
        <p style="margin:0 0 10px; font-size:14px;"><?php echo wp_kses_post($data['text']); ?></p>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <a class="button" href="<?php echo esc_url($close_url); ?>">Dismiss Notice</a>
            <span style="font-size:10px; opacity:0.6;">Solutions by <a href="https://www.dickyibrohim.com" target="_blank" style="color:#00d084;">www.dickyibrohim.com</a></span>
        </div>
    </div>
    <?php
}
