<?php
/**
 * Snippet Name: WP Unset Cloudflare IPCountry Header
 * Description: Unsets the HTTP_CF_IPCOUNTRY server variable early in the WordPress lifecycle to prevent compatibility issues.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Customization:
 * This script is useful for local testing or debugging geolocation-dependent plugins.
 * It will log 'NOT SET' to the browser console for administrators if successful.
 */

add_action( 'plugins_loaded', 'ibrohim_unset_cf_country_header', 0 );
function ibrohim_unset_cf_country_header() {
	if ( isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
		unset( $_SERVER['HTTP_CF_IPCOUNTRY'] );
	}
}

add_action( 'wp_footer', 'ibrohim_log_cf_country_status' );
function ibrohim_log_cf_country_status() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$val = isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : 'NOT SET';

	echo '<script>console.log("CF-IPCountry (after unset): ' . esc_js( $val ) . '");</script>';
}
