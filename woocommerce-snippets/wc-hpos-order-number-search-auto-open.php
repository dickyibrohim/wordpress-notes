<?php
/**
 * Snippet Name: WC HPOS Search by Custom Order Number (Auto Open)
 * Description: Enables searching for orders by custom order number or formatted number when using High-Performance Order Storage (HPOS). Automatically opens the order if a single match is found.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

add_action('current_screen', 'ibrohim_hpos_search_auto_open_handler');

function ibrohim_hpos_search_auto_open_handler($screen) {
    if (!is_admin() || !current_user_can('manage_woocommerce')) return;
    if (!$screen || empty($screen->id) || $screen->id !== 'woocommerce_page_wc-orders') return;
    if (empty($_GET['s'])) return;

    $raw = (string) $_GET['s'];
    if (!preg_match('/^#?\d+$/', $raw)) return;
    $num = preg_replace('/\D+/', '', $raw);
    if ($num === '') return;

    global $wpdb;
    $keys = [
        '_order_number','_order_number_formatted',
        '_alg_wc_custom_order_number','_alg_wc_full_custom_order_number',
        '_sequential_order_number','order_number','order_number_formatted',
        'wt_ons_order_number','ywpo_number'
    ];

    $wco  = $wpdb->prefix . 'wc_orders';
    $wcom = $wpdb->prefix . 'wc_orders_meta';
    $placeholders = implode(',', array_fill(0, count($keys), '%s'));
    $sql = $wpdb->prepare(
        "SELECT om.order_id FROM $wcom AS om JOIN $wco AS o ON o.id = om.order_id WHERE om.meta_value = %s AND om.meta_key IN ($placeholders)",
        array_merge([$num], $keys)
    );
    $meta_matches = array_map('absint', (array) $wpdb->get_col($sql));
    $meta_matches = array_values(array_unique(array_filter($meta_matches)));

    $id_match = [];
    $maybe = absint($num);
    if ($maybe && wc_get_order($maybe)) $id_match = [$maybe];

    if (!empty($meta_matches)) {
        if (count($meta_matches) === 1) {
            wp_safe_redirect(admin_url('admin.php?page=wc-orders&action=edit&id=' . $meta_matches[0]));
            exit;
        }
        $url = remove_query_arg(['s']);
        $url = add_query_arg(['page' => 'wc-orders', 'ibrohim_order_ids' => implode(',', $meta_matches)], admin_url('admin.php'));
        wp_safe_redirect($url);
        exit;
    }

    if (!empty($id_match)) {
        wp_safe_redirect(admin_url('admin.php?page=wc-orders&action=edit&id=' . $id_match[0]));
        exit;
    }
}

add_filter('woocommerce_orders_table_query_clauses', 'ibrohim_hpos_search_auto_open_clauses');

function ibrohim_hpos_search_auto_open_clauses($clauses) {
    if (empty($_GET['ibrohim_order_ids'])) return $clauses;
    $ids = array_filter(array_map('absint', explode(',', $_GET['ibrohim_order_ids'])));
    if (empty($ids)) return $clauses;
    global $wpdb;
    $table = $wpdb->prefix . 'wc_orders';
    $in = implode(',', array_map('intval', $ids));
    $clauses['where'] .= " AND {$table}.id IN ($in) ";
    return $clauses;
}
