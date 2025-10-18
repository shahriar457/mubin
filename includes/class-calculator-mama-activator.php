<?php
/**
 * Plugin activation
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activator class
 */
class Calculator_Mama_Activator {

    /**
     * Activate plugin
     */
    public static function activate() {
        // Initialize database
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        // No custom tables needed - using wp_options
    }

    /**
     * Set default options
     */
    private static function set_default_options() {
        $database = new Calculator_Mama_Database();
        
        // Get current settings
        $settings = $database->get_settings();
        
        // If no settings exist, set defaults
        if (empty($settings) || $settings['version'] !== CMAMA_VERSION) {
            $default_settings = array(
                'version' => CMAMA_VERSION,
                'calculators' => array(),
                'appearance' => array(
                    'primary_color' => '#0073aa',
                    'button_color' => '#0073aa',
                    'input_color' => '#ffffff',
                    'result_color' => '#28a745',
                    'font_family' => 'inherit',
                    'border_radius' => '4px',
                    'padding' => '20px',
                    'custom_css' => ''
                ),
                'seo' => array(
                    'enable_schema' => true,
                    'enable_meta_description' => true
                )
            );
            
            $database->update_settings($default_settings);
        }
    }
}