<?php
/**
 * Asset Manager Class
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Asset Manager Class
 *
 * Handles conditional loading of CSS and JavaScript assets.
 *
 * @since 1.0.0
 */
class CMAMA_Asset_Manager {

    /**
     * Asset Manager instance.
     *
     * @since 1.0.0
     * @var CMAMA_Asset_Manager
     */
    private static $instance = null;

    /**
     * Loaded assets.
     *
     * @since 1.0.0
     * @var array
     */
    private $loaded_assets = array();

    /**
     * Asset dependencies.
     *
     * @since 1.0.0
     * @var array
     */
    private $dependencies = array();

    /**
     * Get Asset Manager instance.
     *
     * @since 1.0.0
     * @return CMAMA_Asset_Manager
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->init_dependencies();
    }

    /**
     * Initialize asset dependencies.
     *
     * @since 1.0.0
     */
    private function init_dependencies() {
        $this->dependencies = array(
            'css' => array(
                'cmama-frontend' => array(),
                'cmama-admin' => array('wp-color-picker')
            ),
            'js' => array(
                'cmama-frontend' => array('jquery'),
                'cmama-admin' => array('jquery', 'wp-color-picker')
            )
        );
    }

    /**
     * Enqueue calculator assets conditionally.
     *
     * @since 1.0.0
     * @param string $calculator_slug Calculator slug.
     */
    public function enqueue_calculator_assets($calculator_slug) {
        if (in_array($calculator_slug, $this->loaded_assets)) {
            return; // Already loaded
        }

        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator) {
            return;
        }

        $category = $calculator->get_category();

        // Enqueue base frontend assets if not already loaded
        $this->enqueue_base_frontend_assets();

        // Enqueue category-specific assets
        $this->enqueue_category_assets($category);

        // Enqueue calculator-specific assets
        $this->enqueue_specific_calculator_assets($calculator_slug, $category);

        // Mark as loaded
        $this->loaded_assets[] = $calculator_slug;
    }

    /**
     * Enqueue base frontend assets.
     *
     * @since 1.0.0
     */
    private function enqueue_base_frontend_assets() {
        if (wp_style_is('cmama-frontend', 'enqueued')) {
            return; // Already enqueued
        }

        // Frontend CSS
        wp_enqueue_style(
            'cmama-frontend',
            CMAMA_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CMAMA_VERSION
        );

        // Frontend JS
        wp_enqueue_script(
            'cmama-frontend',
            CMAMA_PLUGIN_URL . 'assets/js/frontend.js',
            $this->dependencies['js']['cmama-frontend'],
            CMAMA_VERSION,
            true
        );

        // Localize frontend script
        wp_localize_script('cmama-frontend', 'cmamaFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_frontend_nonce'),
            'strings' => array(
                'calculating' => __('Calculating...', CMAMA_TEXT_DOMAIN),
                'error' => __('Calculation error. Please check your inputs.', CMAMA_TEXT_DOMAIN),
                'required_field' => __('This field is required.', CMAMA_TEXT_DOMAIN),
                'invalid_number' => __('Please enter a valid number.', CMAMA_TEXT_DOMAIN)
            )
        ));

        // Add inline styles for appearance customization
        $this->add_inline_appearance_styles();
    }

    /**
     * Enqueue category-specific assets.
     *
     * @since 1.0.0
     * @param string $category Category slug.
     */
    private function enqueue_category_assets($category) {
        $category_css = CMAMA_PLUGIN_DIR . 'assets/css/categories/' . $category . '.css';
        $category_js = CMAMA_PLUGIN_DIR . 'assets/js/categories/' . $category . '.js';

        // Category CSS
        if (file_exists($category_css)) {
            wp_enqueue_style(
                'cmama-category-' . $category,
                CMAMA_PLUGIN_URL . 'assets/css/categories/' . $category . '.css',
                array('cmama-frontend'),
                CMAMA_VERSION
            );
        }

        // Category JS
        if (file_exists($category_js)) {
            wp_enqueue_script(
                'cmama-category-' . $category,
                CMAMA_PLUGIN_URL . 'assets/js/categories/' . $category . '.js',
                array('cmama-frontend'),
                CMAMA_VERSION,
                true
            );
        }
    }

    /**
     * Enqueue calculator-specific assets.
     *
     * @since 1.0.0
     * @param string $calculator_slug Calculator slug.
     * @param string $category Category slug.
     */
    private function enqueue_specific_calculator_assets($calculator_slug, $category) {
        $calculator_css = CMAMA_PLUGIN_DIR . 'calculators/' . $category . '/' . $calculator_slug . '.css';
        $calculator_js = CMAMA_PLUGIN_DIR . 'calculators/' . $category . '/' . $calculator_slug . '.js';

        // Calculator-specific CSS
        if (file_exists($calculator_css)) {
            wp_enqueue_style(
                'cmama-calculator-' . $calculator_slug,
                CMAMA_PLUGIN_URL . 'calculators/' . $category . '/' . $calculator_slug . '.css',
                array('cmama-frontend'),
                CMAMA_VERSION
            );
        }

        // Calculator-specific JS
        if (file_exists($calculator_js)) {
            wp_enqueue_script(
                'cmama-calculator-' . $calculator_slug,
                CMAMA_PLUGIN_URL . 'calculators/' . $category . '/' . $calculator_slug . '.js',
                array('cmama-frontend'),
                CMAMA_VERSION,
                true
            );
        }
    }

    /**
     * Add inline appearance styles.
     *
     * @since 1.0.0
     */
    private function add_inline_appearance_styles() {
        $settings = get_option('cmama_settings', array());
        $appearance = isset($settings['appearance']) ? $settings['appearance'] : array();

        if (empty($appearance)) {
            return;
        }

        $css = ':root {';
        
        if (!empty($appearance['primary_color'])) {
            $css .= '--cmama-primary-color: ' . esc_attr($appearance['primary_color']) . ';';
        }
        if (!empty($appearance['button_color'])) {
            $css .= '--cmama-button-color: ' . esc_attr($appearance['button_color']) . ';';
        }
        if (!empty($appearance['input_color'])) {
            $css .= '--cmama-input-color: ' . esc_attr($appearance['input_color']) . ';';
        }
        if (!empty($appearance['result_color'])) {
            $css .= '--cmama-result-color: ' . esc_attr($appearance['result_color']) . ';';
        }
        if (!empty($appearance['border_radius'])) {
            $css .= '--cmama-border-radius: ' . esc_attr($appearance['border_radius']) . 'px;';
        }
        if (!empty($appearance['padding'])) {
            $css .= '--cmama-padding: ' . esc_attr($appearance['padding']) . 'px;';
        }
        if (!empty($appearance['font_family']) && $appearance['font_family'] !== 'inherit') {
            $css .= '--cmama-font-family: ' . esc_attr($appearance['font_family']) . ';';
        }

        $css .= '}';

        // Add custom CSS
        if (!empty($appearance['custom_css'])) {
            $css .= "\n" . wp_strip_all_tags($appearance['custom_css']);
        }

        wp_add_inline_style('cmama-frontend', $css);
    }

    /**
     * Enqueue admin assets.
     *
     * @since 1.0.0
     * @param string $hook_suffix Current admin page.
     */
    public function enqueue_admin_assets($hook_suffix) {
        // Only load on Calculator Mama admin pages
        if (strpos($hook_suffix, 'calculator-mama') === false) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'cmama-admin',
            CMAMA_PLUGIN_URL . 'assets/css/admin.css',
            $this->dependencies['css']['cmama-admin'],
            CMAMA_VERSION
        );

        // Admin JS
        wp_enqueue_script(
            'cmama-admin',
            CMAMA_PLUGIN_URL . 'assets/js/admin.js',
            $this->dependencies['js']['cmama-admin'],
            CMAMA_VERSION,
            true
        );

        // Localize admin script
        wp_localize_script('cmama-admin', 'cmamaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_admin_nonce'),
            'strings' => array(
                'saving' => __('Saving...', CMAMA_TEXT_DOMAIN),
                'saved' => __('Settings saved!', CMAMA_TEXT_DOMAIN),
                'error' => __('Error saving settings.', CMAMA_TEXT_DOMAIN),
                'confirm_deactivate' => __('Are you sure you want to deactivate this calculator?', CMAMA_TEXT_DOMAIN),
                'confirm_activate' => __('Are you sure you want to activate this calculator?', CMAMA_TEXT_DOMAIN)
            ),
            'calculators' => $this->get_calculators_for_js()
        ));

        // Page-specific assets
        $this->enqueue_page_specific_admin_assets($hook_suffix);
    }

    /**
     * Enqueue page-specific admin assets.
     *
     * @since 1.0.0
     * @param string $hook_suffix Current admin page.
     */
    private function enqueue_page_specific_admin_assets($hook_suffix) {
        // Library page assets
        if (strpos($hook_suffix, 'calculator-mama-library') !== false) {
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_style('dashicons');
        }

        // Appearance page assets
        if (strpos($hook_suffix, 'calculator-mama-appearance') !== false) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_media();
        }
    }

    /**
     * Get calculators data for JavaScript.
     *
     * @since 1.0.0
     * @return array Calculators data.
     */
    private function get_calculators_for_js() {
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculators = $registry->get_calculators();
        $js_data = array();

        foreach ($calculators as $slug => $calculator) {
            $js_data[$slug] = array(
                'name' => $calculator->get_name(),
                'category' => $calculator->get_category(),
                'description' => $calculator->get_description()
            );
        }

        return $js_data;
    }

    /**
     * Preload critical assets.
     *
     * @since 1.0.0
     */
    public function preload_critical_assets() {
        // Preload critical CSS
        echo '<link rel="preload" href="' . CMAMA_PLUGIN_URL . 'assets/css/frontend.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        
        // Preload critical JS
        echo '<link rel="preload" href="' . CMAMA_PLUGIN_URL . 'assets/js/frontend.js" as="script">' . "\n";
    }

    /**
     * Get asset URL with version.
     *
     * @since 1.0.0
     * @param string $asset_path Asset path relative to plugin assets directory.
     * @return string Asset URL with version.
     */
    public function get_asset_url($asset_path) {
        return CMAMA_PLUGIN_URL . 'assets/' . ltrim($asset_path, '/') . '?ver=' . CMAMA_VERSION;
    }

    /**
     * Check if asset exists.
     *
     * @since 1.0.0
     * @param string $asset_path Asset path relative to plugin assets directory.
     * @return bool True if asset exists.
     */
    public function asset_exists($asset_path) {
        return file_exists(CMAMA_PLUGIN_DIR . 'assets/' . ltrim($asset_path, '/'));
    }

    /**
     * Get loaded assets.
     *
     * @since 1.0.0
     * @return array Array of loaded asset identifiers.
     */
    public function get_loaded_assets() {
        return $this->loaded_assets;
    }

    /**
     * Clear loaded assets cache.
     *
     * @since 1.0.0
     */
    public function clear_loaded_assets() {
        $this->loaded_assets = array();
    }

    /**
     * Add asset dependency.
     *
     * @since 1.0.0
     * @param string $asset_type Asset type (css or js).
     * @param string $handle Asset handle.
     * @param array $dependencies Array of dependencies.
     */
    public function add_dependency($asset_type, $handle, $dependencies) {
        if (!isset($this->dependencies[$asset_type])) {
            $this->dependencies[$asset_type] = array();
        }
        
        $this->dependencies[$asset_type][$handle] = $dependencies;
    }

    /**
     * Get asset dependencies.
     *
     * @since 1.0.0
     * @param string $asset_type Asset type (css or js).
     * @param string $handle Asset handle.
     * @return array Array of dependencies.
     */
    public function get_dependencies($asset_type, $handle) {
        if (isset($this->dependencies[$asset_type][$handle])) {
            return $this->dependencies[$asset_type][$handle];
        }
        
        return array();
    }
}