<?php
/**
 * Plugin deactivation
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Deactivator class
 */
class Calculator_Mama_Deactivator {

    /**
     * Deactivate plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear any scheduled events
        wp_clear_scheduled_hook('cmama_cleanup');
    }
}