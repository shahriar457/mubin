<?php
/**
 * Plugin Name: Calculator Mama
 * Plugin URI: https://calculatormama.com
 * Description: A comprehensive library of over 150 interactive calculators with built-in SEO optimization for WordPress websites.
 * Version: 1.0.0
 * Author: Calculator Mama Team
 * Author URI: https://calculatormama.com
 * Text Domain: calculator-mama
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 *
 * @package CalculatorMama
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CMAMA_VERSION', '1.0.0');
define('CMAMA_PLUGIN_FILE', __FILE__);
define('CMAMA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CMAMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CMAMA_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('CMAMA_TEXT_DOMAIN', 'calculator-mama');
define('CMAMA_PREFIX', 'cmama_');

/**
 * Main Calculator Mama Plugin Class
 *
 * @since 1.0.0
 */
final class Calculator_Mama {

    /**
     * Plugin instance.
     *
     * @since 1.0.0
     * @var Calculator_Mama
     */
    private static $instance = null;

    /**
     * Get plugin instance.
     *
     * @since 1.0.0
     * @return Calculator_Mama
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
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // Plugin activation and deactivation hooks
        register_activation_hook(CMAMA_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(CMAMA_PLUGIN_FILE, array($this, 'deactivate'));

        // Initialize plugin after WordPress loads
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Admin hooks
        if (is_admin()) {
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }

        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        add_action('wp_head', array($this, 'output_schema_markup'));
    }

    /**
     * Plugin activation.
     *
     * @since 1.0.0
     */
    public function activate() {
        // Set default options
        $default_settings = array(
            'version' => CMAMA_VERSION,
            'active_calculators' => array(),
            'appearance' => array(
                'primary_color' => '#007cba',
                'button_color' => '#007cba',
                'input_color' => '#ffffff',
                'result_color' => '#f0f0f0',
                'font_family' => 'inherit',
                'border_radius' => '4',
                'padding' => '20',
                'custom_css' => ''
            ),
            'seo' => array(
                'enable_schema' => true,
                'default_author' => get_option('blogname', 'Calculator Mama')
            )
        );

        // Only set defaults if settings don't exist
        if (!get_option('cmama_settings')) {
            update_option('cmama_settings', $default_settings);
        }

        // Create database tables if needed (for future use)
        $this->create_tables();

        // Clear rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     *
     * @since 1.0.0
     */
    public function deactivate() {
        // Clear rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin.
     *
     * @since 1.0.0
     */
    public function init() {
        // Load required files
        $this->load_dependencies();

        // Initialize shortcodes
        add_shortcode('cmama_calculator', array($this, 'render_calculator_shortcode'));

        // Initialize Gutenberg blocks
        if (function_exists('register_block_type')) {
            add_action('init', array($this, 'register_blocks'));
        }

        // Initialize AJAX handlers
        CMAMA_AJAX_Handlers::get_instance();
    }

    /**
     * Load plugin text domain.
     *
     * @since 1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            CMAMA_TEXT_DOMAIN,
            false,
            dirname(CMAMA_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Load plugin dependencies.
     *
     * @since 1.0.0
     */
    private function load_dependencies() {
        // Core includes
        require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-base.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-registry.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-seo-engine.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-asset-manager.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-ajax-handlers.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/functions.php';

        // Admin includes
        if (is_admin()) {
            require_once CMAMA_PLUGIN_DIR . 'admin/class-admin.php';
            require_once CMAMA_PLUGIN_DIR . 'admin/class-settings.php';
        }

        // Load calculator modules
        $this->load_calculators();
    }

    /**
     * Load calculator modules.
     *
     * @since 1.0.0
     */
    private function load_calculators() {
        $calculator_dirs = array('financial', 'health', 'math', 'other');
        
        foreach ($calculator_dirs as $dir) {
            $calculator_path = CMAMA_PLUGIN_DIR . 'calculators/' . $dir . '/';
            if (is_dir($calculator_path)) {
                $calculator_files = glob($calculator_path . '*.php');
                foreach ($calculator_files as $file) {
                    require_once $file;
                }
            }
        }
    }

    /**
     * Admin initialization.
     *
     * @since 1.0.0
     */
    public function admin_init() {
        // Initialize admin settings
        if (class_exists('CMAMA_Admin')) {
            CMAMA_Admin::get_instance();
        }
    }

    /**
     * Add admin menu.
     *
     * @since 1.0.0
     */
    public function admin_menu() {
        // Main menu page
        add_menu_page(
            __('Calculator Mama', CMAMA_TEXT_DOMAIN),
            __('Calculator Mama', CMAMA_TEXT_DOMAIN),
            'manage_options',
            'calculator-mama',
            array($this, 'admin_dashboard_page'),
            'dashicons-calculator',
            30
        );

        // Dashboard submenu (same as main)
        add_submenu_page(
            'calculator-mama',
            __('Dashboard', CMAMA_TEXT_DOMAIN),
            __('Dashboard', CMAMA_TEXT_DOMAIN),
            'manage_options',
            'calculator-mama',
            array($this, 'admin_dashboard_page')
        );

        // Calculator Library submenu
        add_submenu_page(
            'calculator-mama',
            __('Calculator Library', CMAMA_TEXT_DOMAIN),
            __('Calculator Library', CMAMA_TEXT_DOMAIN),
            'manage_options',
            'calculator-mama-library',
            array($this, 'admin_library_page')
        );

        // Appearance submenu
        add_submenu_page(
            'calculator-mama',
            __('Appearance', CMAMA_TEXT_DOMAIN),
            __('Appearance', CMAMA_TEXT_DOMAIN),
            'manage_options',
            'calculator-mama-appearance',
            array($this, 'admin_appearance_page')
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @since 1.0.0
     * @param string $hook_suffix The current admin page.
     */
    public function admin_enqueue_scripts($hook_suffix) {
        // Only load on our admin pages
        if (strpos($hook_suffix, 'calculator-mama') === false) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'cmama-admin',
            CMAMA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CMAMA_VERSION
        );

        // Admin JS
        wp_enqueue_script(
            'cmama-admin',
            CMAMA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            CMAMA_VERSION,
            true
        );

        // Color picker
        wp_enqueue_style('wp-color-picker');

        // Localize script
        wp_localize_script('cmama-admin', 'cmamaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_admin_nonce'),
            'strings' => array(
                'saving' => __('Saving...', CMAMA_TEXT_DOMAIN),
                'saved' => __('Settings saved!', CMAMA_TEXT_DOMAIN),
                'error' => __('Error saving settings.', CMAMA_TEXT_DOMAIN)
            )
        ));
    }

    /**
     * Enqueue frontend scripts and styles.
     *
     * @since 1.0.0
     */
    public function frontend_enqueue_scripts() {
        // Only enqueue if we have calculators on the page
        if ($this->has_calculators_on_page()) {
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
                array('jquery'),
                CMAMA_VERSION,
                true
            );

            // Localize script
            wp_localize_script('cmama-frontend', 'cmamaFrontend', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cmama_frontend_nonce'),
                'strings' => array(
                    'calculating' => __('Calculating...', CMAMA_TEXT_DOMAIN),
                    'error' => __('Calculation error. Please check your inputs.', CMAMA_TEXT_DOMAIN)
                )
            ));
        }
    }

    /**
     * Check if current page has calculators.
     *
     * @since 1.0.0
     * @return bool
     */
    private function has_calculators_on_page() {
        global $post;
        
        if (!$post) {
            return false;
        }

        // Check for shortcodes
        if (has_shortcode($post->post_content, 'cmama_calculator')) {
            return true;
        }

        // Check for Gutenberg blocks
        if (has_block('calculator-mama/calculator', $post)) {
            return true;
        }

        return false;
    }

    /**
     * Register Gutenberg blocks.
     *
     * @since 1.0.0
     */
    public function register_blocks() {
        // Register calculator block
        wp_register_script(
            'cmama-block-editor',
            CMAMA_PLUGIN_URL . 'blocks/calculator-block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            CMAMA_VERSION
        );

        wp_register_style(
            'cmama-block-editor',
            CMAMA_PLUGIN_URL . 'blocks/calculator-block.css',
            array('wp-edit-blocks'),
            CMAMA_VERSION
        );

        register_block_type('calculator-mama/calculator', array(
            'editor_script' => 'cmama-block-editor',
            'editor_style' => 'cmama-block-editor',
            'render_callback' => array($this, 'render_calculator_block'),
            'attributes' => array(
                'calculatorSlug' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'introduction' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'instructions' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'faqs' => array(
                    'type' => 'array',
                    'default' => array()
                )
            )
        ));
    }

    /**
     * Render calculator shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_calculator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'slug' => '',
            'title' => ''
        ), $atts, 'cmama_calculator');

        if (empty($atts['slug'])) {
            return '';
        }

        return $this->render_calculator($atts['slug'], array(
            'title' => $atts['title']
        ));
    }

    /**
     * Render calculator block.
     *
     * @since 1.0.0
     * @param array $attributes Block attributes.
     * @return string
     */
    public function render_calculator_block($attributes) {
        if (empty($attributes['calculatorSlug'])) {
            return '';
        }

        $output = '';

        // Add introduction if provided
        if (!empty($attributes['introduction'])) {
            $output .= '<div class="cmama-introduction">' . wp_kses_post($attributes['introduction']) . '</div>';
        }

        // Render calculator
        $output .= $this->render_calculator($attributes['calculatorSlug']);

        // Add instructions if provided
        if (!empty($attributes['instructions'])) {
            $output .= '<div class="cmama-instructions">' . wp_kses_post($attributes['instructions']) . '</div>';
        }

        // Add FAQs if provided
        if (!empty($attributes['faqs']) && is_array($attributes['faqs'])) {
            $output .= '<div class="cmama-faqs">';
            $output .= '<h3>' . __('Frequently Asked Questions', CMAMA_TEXT_DOMAIN) . '</h3>';
            foreach ($attributes['faqs'] as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $output .= '<div class="cmama-faq-item">';
                    $output .= '<h4 class="cmama-faq-question">' . esc_html($faq['question']) . '</h4>';
                    $output .= '<div class="cmama-faq-answer">' . wp_kses_post($faq['answer']) . '</div>';
                    $output .= '</div>';
                }
            }
            $output .= '</div>';
        }

        return $output;
    }

    /**
     * Render calculator.
     *
     * @since 1.0.0
     * @param string $slug Calculator slug.
     * @param array $args Additional arguments.
     * @return string
     */
    private function render_calculator($slug, $args = array()) {
        // Get calculator registry
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($slug);

        if (!$calculator) {
            return '<div class="cmama-error">' . __('Calculator not found.', CMAMA_TEXT_DOMAIN) . '</div>';
        }

        // Check if calculator is active
        $settings = get_option('cmama_settings', array());
        $active_calculators = isset($settings['active_calculators']) ? $settings['active_calculators'] : array();
        
        if (!in_array($slug, $active_calculators) && !is_admin()) {
            return '<div class="cmama-error">' . __('Calculator is not active.', CMAMA_TEXT_DOMAIN) . '</div>';
        }

        // Render calculator
        return $calculator->render($args);
    }

    /**
     * Output Schema.org markup.
     *
     * @since 1.0.0
     */
    public function output_schema_markup() {
        if (class_exists('CMAMA_SEO_Engine')) {
            $seo_engine = CMAMA_SEO_Engine::get_instance();
            $seo_engine->output_schema_markup();
        }
    }

    /**
     * Admin dashboard page.
     *
     * @since 1.0.0
     */
    public function admin_dashboard_page() {
        include CMAMA_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Admin library page.
     *
     * @since 1.0.0
     */
    public function admin_library_page() {
        include CMAMA_PLUGIN_DIR . 'admin/views/library.php';
    }

    /**
     * Admin appearance page.
     *
     * @since 1.0.0
     */
    public function admin_appearance_page() {
        include CMAMA_PLUGIN_DIR . 'admin/views/appearance.php';
    }

    /**
     * Create database tables.
     *
     * @since 1.0.0
     */
    private function create_tables() {
        // For future use - calculator usage statistics, etc.
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Calculator usage table (for future analytics)
        $table_name = $wpdb->prefix . 'cmama_usage';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            calculator_slug varchar(100) NOT NULL,
            user_ip varchar(45) DEFAULT '',
            user_agent text DEFAULT '',
            calculation_data longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY calculator_slug (calculator_slug),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 * @return Calculator_Mama
 */
function calculator_mama() {
    return Calculator_Mama::get_instance();
}

// Initialize the plugin
calculator_mama();