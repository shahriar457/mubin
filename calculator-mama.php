<?php
/**
 * Plugin Name: Calculator Mama
 * Plugin URI: https://calculator-mama.com
 * Description: A comprehensive library of over 150 interactive calculators with built-in SEO optimization for WordPress websites.
 * Version: 1.0
 * Author: Calculator Mama Team
 * Text Domain: calculator-mama
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CMAMA_VERSION', '1.0');
define('CMAMA_PLUGIN_FILE', __FILE__);
define('CMAMA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CMAMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CMAMA_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Minimum PHP version check
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', 'cmama_php_version_notice');
    return;
}

/**
 * Display PHP version notice
 */
function cmama_php_version_notice() {
    echo '<div class="notice notice-error"><p>';
    printf(
        esc_html__('Calculator Mama requires PHP version 7.4 or higher. You are running version %s.', 'calculator-mama'),
        PHP_VERSION
    );
    echo '</p></div>';
}

// Load the main plugin class
require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-mama.php';

// Initialize the plugin
function cmama_init() {
    return Calculator_Mama::get_instance();
}
add_action('plugins_loaded', 'cmama_init');

// Activation hook
register_activation_hook(__FILE__, 'cmama_activate');
function cmama_activate() {
    require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-mama-activator.php';
    Calculator_Mama_Activator::activate();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'cmama_deactivate');
function cmama_deactivate() {
    require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-mama-deactivator.php';
    Calculator_Mama_Deactivator::deactivate();
}