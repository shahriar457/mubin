<?php
/**
 * Styling system
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Styles class
 */
class Calculator_Mama_Styles {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_head', array($this, 'add_custom_styles'));
        add_action('admin_head', array($this, 'add_admin_styles'));
    }

    /**
     * Add custom styles to head
     */
    public function add_custom_styles() {
        $database = new Calculator_Mama_Database();
        $appearance_settings = $database->get_appearance_settings();

        $css = $this->generate_custom_css($appearance_settings);
        
        if (!empty($css)) {
            echo '<style type="text/css" id="cmama-custom-styles">' . $css . '</style>' . "\n";
        }
    }

    /**
     * Add admin styles
     */
    public function add_admin_styles() {
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'calculator-mama') === false) {
            return;
        }

        echo '<style type="text/css" id="cmama-admin-styles">' . $this->get_admin_css() . '</style>' . "\n";
    }

    /**
     * Generate custom CSS
     *
     * @param array $settings
     * @return string
     */
    private function generate_custom_css($settings) {
        $css = '';

        // Primary color
        if (!empty($settings['primary_color'])) {
            $css .= '.cmama-calculator { --cmama-primary-color: ' . esc_attr($settings['primary_color']) . '; }' . "\n";
        }

        // Button color
        if (!empty($settings['button_color'])) {
            $css .= '.cmama-calculator { --cmama-button-color: ' . esc_attr($settings['button_color']) . '; }' . "\n";
        }

        // Input color
        if (!empty($settings['input_color'])) {
            $css .= '.cmama-calculator { --cmama-input-color: ' . esc_attr($settings['input_color']) . '; }' . "\n";
        }

        // Result color
        if (!empty($settings['result_color'])) {
            $css .= '.cmama-calculator { --cmama-result-color: ' . esc_attr($settings['result_color']) . '; }' . "\n";
        }

        // Font family
        if (!empty($settings['font_family']) && $settings['font_family'] !== 'inherit') {
            $css .= '.cmama-calculator { font-family: ' . esc_attr($settings['font_family']) . '; }' . "\n";
        }

        // Border radius
        if (!empty($settings['border_radius'])) {
            $css .= '.cmama-calculator { --cmama-border-radius: ' . esc_attr($settings['border_radius']) . '; }' . "\n";
        }

        // Padding
        if (!empty($settings['padding'])) {
            $css .= '.cmama-calculator { --cmama-padding: ' . esc_attr($settings['padding']) . '; }' . "\n";
        }

        // Custom CSS
        if (!empty($settings['custom_css'])) {
            $css .= "\n" . $settings['custom_css'] . "\n";
        }

        return $css;
    }

    /**
     * Get admin CSS
     *
     * @return string
     */
    private function get_admin_css() {
        return '
            .cmama-dashboard {
                max-width: 1200px;
            }
            .cmama-dashboard-welcome {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                margin: 20px 0;
            }
            .cmama-dashboard-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .cmama-stat-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                text-align: center;
            }
            .cmama-stat-number {
                font-size: 2em;
                font-weight: bold;
                color: #0073aa;
            }
            .cmama-stat-label {
                color: #666;
                margin-top: 5px;
            }
            .cmama-categories-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .cmama-category-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                display: flex;
                align-items: center;
            }
            .cmama-category-icon {
                font-size: 2em;
                color: #0073aa;
                margin-right: 15px;
            }
            .cmama-actions-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .cmama-action-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                text-decoration: none;
                color: inherit;
                display: flex;
                align-items: center;
                transition: box-shadow 0.2s;
            }
            .cmama-action-card:hover {
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .cmama-action-icon {
                font-size: 2em;
                color: #0073aa;
                margin-right: 15px;
            }
            .cmama-library-filters {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                margin: 20px 0;
            }
            .cmama-filter-form {
                display: flex;
                gap: 15px;
                align-items: end;
                flex-wrap: wrap;
            }
            .cmama-filter-group {
                display: flex;
                flex-direction: column;
                min-width: 150px;
            }
            .cmama-filter-group label {
                font-weight: 600;
                margin-bottom: 5px;
            }
            .cmama-calculators-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .cmama-calculator-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                transition: box-shadow 0.2s;
            }
            .cmama-calculator-card:hover {
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .cmama-calculator-card.active {
                border-color: #0073aa;
            }
            .cmama-calculator-header {
                display: flex;
                align-items: flex-start;
                margin-bottom: 15px;
            }
            .cmama-calculator-icon {
                font-size: 1.5em;
                color: #0073aa;
                margin-right: 10px;
                margin-top: 5px;
            }
            .cmama-calculator-name {
                margin: 0 0 5px 0;
                font-size: 1.1em;
            }
            .cmama-calculator-description {
                margin: 0;
                color: #666;
                font-size: 0.9em;
            }
            .cmama-calculator-meta {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                font-size: 0.9em;
                color: #666;
            }
            .cmama-calculator-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .cmama-toggle {
                display: flex;
                align-items: center;
                cursor: pointer;
            }
            .cmama-toggle-input {
                display: none;
            }
            .cmama-toggle-slider {
                width: 40px;
                height: 20px;
                background: #ccc;
                border-radius: 20px;
                position: relative;
                transition: background 0.2s;
                margin-right: 10px;
            }
            .cmama-toggle-slider:before {
                content: "";
                position: absolute;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: white;
                top: 2px;
                left: 2px;
                transition: transform 0.2s;
            }
            .cmama-toggle-input:checked + .cmama-toggle-slider {
                background: #0073aa;
            }
            .cmama-toggle-input:checked + .cmama-toggle-slider:before {
                transform: translateX(20px);
            }
            .cmama-calculator-buttons {
                display: flex;
                gap: 10px;
            }
            .cmama-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .cmama-modal-content {
                background: white;
                border-radius: 4px;
                max-width: 90%;
                max-height: 90%;
                overflow: auto;
            }
            .cmama-modal-header {
                padding: 20px;
                border-bottom: 1px solid #ccd0d4;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .cmama-modal-close {
                background: none;
                border: none;
                font-size: 1.5em;
                cursor: pointer;
            }
            .cmama-modal-body {
                padding: 20px;
            }
        ';
    }
}