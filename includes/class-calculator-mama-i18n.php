<?php
/**
 * Internationalization functionality
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Internationalization class
 */
class Calculator_Mama_i18n {

    /**
     * Load plugin text domain
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'calculator-mama',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}