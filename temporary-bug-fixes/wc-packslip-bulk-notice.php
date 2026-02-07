<?php
/**
 * Snippet Name: WC Packing Slip Bulk Action Notice
 * Description: Displays a custom success banner after bulk handling packing slips or labels in WooCommerce. Resolves issues with default notices being lost on redirect.
 * Author: Dicky Ibrohim
 */

if ( ! defined( 'PACKSLIP_BULK_NOTICE_META' ) ) {
	define( 'PACKSLIP_BULK_NOTICE_META', '_packslip_bulk_notice_html' );
}

if ( ! function_exists( 'save_packslip_bulk_notice' ) ) {
	function save_packslip_bulk_notice( $handler, $action ) {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}

		if ( ! $handler || ! method_exists( $handler, 'get_success_message' ) ) {
			return;
		}

		$message = $handler->get_success_message();

		if ( empty( $message ) ) {
			return;
		}

		$hash_source = $message . '|' . microtime( true );

		if ( method_exists( $handler, 'get_filename' ) ) {
			$hash_source .= '|' . $handler->get_filename();
		}

		$run_id = md5( $hash_source );
		$type   = method_exists( $handler, 'get_shipment_type' ) ? $handler->get_shipment_type() : 'simple';

		update_user_meta(
			get_current_user_id(),
			PACKSLIP_BULK_NOTICE_META,
			array(
				'text'      => $message,
				'run'       => $run_id,
				'rendered'  => '',
				'dismissed' => '',
				'time'      => time(),
				'action'    => $action,
				'screen'    => $type,
			)
		);
	}
}

$bulk_notice_hooks = array_unique(
	array(
		'woocommerce_shiptastic_shipments_table_bulk_action_labels_handled',
		'woocommerce_shiptastic_shipments_table_bulk_action_packing_slips_handled',
		'woocommerce_shiptastic_return_shipments_table_bulk_action_labels_handled',
		'woocommerce_shiptastic_return_shipments_table_bulk_action_packing_slips_handled',
		'woocommerce_gzd_shipments_table_bulk_action_labels_handled',
		'woocommerce_gzd_shipments_table_bulk_action_packing_slips_handled',
		'woocommerce_gzd_return_shipments_table_bulk_action_labels_handled',
		'woocommerce_gzd_return_shipments_table_bulk_action_packing_slips_handled',
	)
);

foreach ( $bulk_notice_hooks as $bulk_notice_hook ) {
	add_action( $bulk_notice_hook, 'save_packslip_bulk_notice', 20, 2 );
}
unset( $bulk_notice_hooks );

if ( ! function_exists( 'render_packslip_notice_banner' ) ) {
	function render_packslip_notice_banner() {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		$allowed_screens = array(
			'woocommerce_page_wc-stc-shipments'        => 'simple',
			'woocommerce_page_wc-stc-return-shipments' => 'return',
		);

		if ( ! $screen || ! isset( $allowed_screens[ $screen->id ] ) ) {
			return;
		}

		$current_context = $allowed_screens[ $screen->id ];
		$data            = get_user_meta( get_current_user_id(), PACKSLIP_BULK_NOTICE_META, true );

		if ( empty( $data ) || empty( $data['text'] ) || empty( $data['run'] ) ) {
			return;
		}

		if ( ! empty( $data['screen'] ) && $data['screen'] !== $current_context ) {
			return;
		}

		if ( ! empty( $data['dismissed'] ) && $data['dismissed'] === $data['run'] ) {
			return;
		}

		if ( ! empty( $data['rendered'] ) && $data['rendered'] === $data['run'] ) {
			return;
		}

		$page_slug = ( 'return' === $current_context ) ? 'wc-stc-return-shipments' : 'wc-stc-shipments';

		$close_url = add_query_arg(
			array(
				'packslip_notice_close' => $data['run'],
				'_wpnonce'              => wp_create_nonce( 'packslip_notice' ),
			),
			admin_url( 'admin.php?page=' . $page_slug )
		);

		$payload = array(
			'message'      => wp_kses_post( $data['text'] ),
			'dismiss_url'  => esc_url( $close_url ),
			'dismiss_text' => esc_html__( 'To Overview', 'woocommerce' ), // Translated from 'Zur Übersicht'
		);
		?>
		<style>
			#packslip-banner {
				border-left: 4px solid #2271b1;
				background: #f0f6fc;
				padding: 14px 18px;
				margin-bottom: 16px;
				box-shadow: 0 1px 1px rgba(0,0,0,0.04);
				border-radius: 2px;
			}
			#packslip-banner .packslip-message {
				margin: 0 0 10px;
				font-size: 14px;
				line-height: 1.5;
			}
			#packslip-banner .packslip-actions {
				margin: 0;
			}
		</style>
		<script>
			(function() {
				var data = <?php echo wp_json_encode( $payload ); ?>;

				function insertBanner() {
					var wrapper = document.querySelector( '#wpbody-content .wrap' ) || document.querySelector( '#wpbody-content' ) || document.body;

					if ( ! wrapper || document.getElementById( 'packslip-banner' ) ) {
						return;
					}

					var banner = document.createElement( 'div' );

					banner.id = 'packslip-banner';
					banner.innerHTML =
						'<p class="packslip-message">' + data.message + '</p>' +
						'<p class="packslip-actions"><a class="button" href="' + data.dismiss_url + '">' + data.dismiss_text + '</a></p>';

					wrapper.insertBefore( banner, wrapper.firstChild );
				}

				if ( document.readyState === 'complete' || document.readyState === 'interactive' ) {
					insertBanner();
				} else {
					document.addEventListener( 'DOMContentLoaded', insertBanner );
				}
			})();
		</script>
		<?php

		update_user_meta(
			get_current_user_id(),
			PACKSLIP_BULK_NOTICE_META,
			array(
				'text'      => $data['text'],
				'run'       => $data['run'],
				'rendered'  => $data['run'],
				'dismissed' => isset( $data['dismissed'] ) ? $data['dismissed'] : '',
				'time'      => isset( $data['time'] ) ? $data['time'] : time(),
				'action'    => isset( $data['action'] ) ? $data['action'] : 'labels',
				'screen'    => $current_context,
			)
		);
	}
}
add_action( 'admin_footer', 'render_packslip_notice_banner' );

if ( ! function_exists( 'close_packslip_notice_banner' ) ) {
	function close_packslip_notice_banner() {
		if ( empty( $_GET['packslip_notice_close'] ) ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'packslip_notice' ) ) {
			return;
		}

		$run  = sanitize_text_field( wp_unslash( $_GET['packslip_notice_close'] ) );
		$data = get_user_meta( get_current_user_id(), PACKSLIP_BULK_NOTICE_META, true );

		if ( empty( $data ) || empty( $data['run'] ) || $data['run'] !== $run ) {
			return;
		}

		if ( isset( $data['dismissed'] ) && $data['dismissed'] === $data['run'] ) {
			return;
		}

		update_user_meta(
			get_current_user_id(),
			PACKSLIP_BULK_NOTICE_META,
			array(
				'text'      => $data['text'],
				'run'       => $data['run'],
				'dismissed' => $data['run'],
				'rendered'  => $data['run'],
				'time'      => isset( $data['time'] ) ? $data['time'] : time(),
				'action'    => isset( $data['action'] ) ? $data['action'] : 'labels',
				'screen'    => isset( $data['screen'] ) ? $data['screen'] : 'simple',
			)
		);

		wp_safe_redirect( remove_query_arg( array( 'packslip_notice_close', '_wpnonce' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'close_packslip_notice_banner' );
