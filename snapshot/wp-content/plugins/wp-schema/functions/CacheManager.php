<?php
/**
 * Cache Manager for WP Schema Plugin
 * Handles custom cache table for resource data
 */

// Ensure the custom cache table exists
function ensure_resource_transients_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ResourceTransients';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        resource_key VARCHAR(64) NOT NULL UNIQUE,
        data LONGTEXT NOT NULL,
        expires_at DATETIME NOT NULL,
        INDEX (resource_key),
        INDEX (expires_at)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Set cache
function set_resource_transient($key, $data, $ttl_seconds = 3600) {
    global $wpdb;
    $table = $wpdb->prefix . 'ResourceTransients';
    $expires = date('Y-m-d H:i:s', time() + $ttl_seconds);
    $json = wp_json_encode($data);
    $wpdb->replace(
        $table,
        [
            'resource_key' => $key,
            'data' => $json,
            'expires_at' => $expires
        ],
        [
            '%s', '%s', '%s'
        ]
    );
}

// Get cache
function get_resource_transient($key) {
    global $wpdb;
    $table = $wpdb->prefix . 'ResourceTransients';
    $now = date('Y-m-d H:i:s');
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT data FROM $table WHERE resource_key = %s AND expires_at > %s",
        $key, $now
    ));
    return $row ? json_decode($row->data, true) : false;
}

// Purge expired cache
function purge_expired_resource_transients() {
    global $wpdb;
    $table = $wpdb->prefix . 'ResourceTransients';
    $now = date('Y-m-d H:i:s');
    $wpdb->query($wpdb->prepare(
        "DELETE FROM $table WHERE expires_at <= %s",
        $now
    ));
}

// Purge all cache (utility function)
function purge_all_resource_transients() {
    global $wpdb;
    $table = $wpdb->prefix . 'ResourceTransients';
    $wpdb->query("DELETE FROM $table");
} 