<?php
/**
 * Plugin Name:       Calculator Mama
 * Description:       Library of 150+ interactive calculators with built-in SEO engine.
 * Version:           1.0.0
 * Author:            Calculator Mama
 * Text Domain:       calculator-mama
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Plugin constants
if ( ! defined( 'CMAMA_VERSION' ) ) {
    define( 'CMAMA_VERSION', '1.0.0' );
}
if ( ! defined( 'CMAMA_PLUGIN_FILE' ) ) {
    define( 'CMAMA_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'CMAMA_PLUGIN_DIR' ) ) {
    define( 'CMAMA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'CMAMA_PLUGIN_URL' ) ) {
    define( 'CMAMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'CMAMA_PREFIX' ) ) {
    define( 'CMAMA_PREFIX', 'cmama_' );
}

// Simple autoloader for core classes (CMama_*)
spl_autoload_register( function ( $class ) {
    if ( 0 !== strpos( $class, 'CMama_' ) ) {
        return;
    }

    $class_slug = strtolower( str_replace( 'CMama_', 'cmama-', $class ) ); // CMama_Plugin => cmama-plugin
    $file       = CMAMA_PLUGIN_DIR . 'includes/class-' . $class_slug . '.php';

    if ( file_exists( $file ) ) {
        require_once $file;
    }
} );

// Activation/Deactivation hooks
register_activation_hook( __FILE__, function () {
    // Ensure settings option exists with defaults
    if ( class_exists( 'CMama_Settings' ) ) {
        CMama_Settings::ensure_defaults();
    } else {
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-settings.php';
        CMama_Settings::ensure_defaults();
    }
} );

register_deactivation_hook( __FILE__, function () {
    // No action needed yet; keeping data for re-activation
} );

// Bootstrap plugin
add_action( 'plugins_loaded', function () {
    // Load i18n first
    load_plugin_textdomain( 'calculator-mama', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Initialize core plugin
    if ( class_exists( 'CMama_Plugin' ) ) {
        CMama_Plugin::instance();
    } else {
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-plugin.php';
        CMama_Plugin::instance();
    }
} );
