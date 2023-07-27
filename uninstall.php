<?php
/**
 * Uninstall KKG Videos.
 *
 * Remove:
 * - Entries table
 *
 * @since 1.1
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


global $wpdb;

// Delete entries table.
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'kkg_video_submissions' );

