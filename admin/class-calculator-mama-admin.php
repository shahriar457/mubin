<?php
/**
 * Admin functionality
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 */
class Calculator_Mama_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_cmama_toggle_calculator', array($this, 'toggle_calculator'));
        add_action('wp_ajax_cmama_save_appearance', array($this, 'save_appearance'));
        add_action('wp_ajax_cmama_save_seo', array($this, 'save_seo'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Calculator Mama', 'calculator-mama'),
            __('Calculator Mama', 'calculator-mama'),
            'manage_options',
            'calculator-mama',
            array($this, 'dashboard_page'),
            'dashicons-calculator',
            30
        );

        add_submenu_page(
            'calculator-mama',
            __('Dashboard', 'calculator-mama'),
            __('Dashboard', 'calculator-mama'),
            'manage_options',
            'calculator-mama',
            array($this, 'dashboard_page')
        );

        add_submenu_page(
            'calculator-mama',
            __('Calculator Library', 'calculator-mama'),
            __('Library', 'calculator-mama'),
            'manage_options',
            'calculator-mama-library',
            array($this, 'library_page')
        );

        add_submenu_page(
            'calculator-mama',
            __('Appearance', 'calculator-mama'),
            __('Appearance', 'calculator-mama'),
            'manage_options',
            'calculator-mama-appearance',
            array($this, 'appearance_page')
        );

        add_submenu_page(
            'calculator-mama',
            __('SEO Settings', 'calculator-mama'),
            __('SEO', 'calculator-mama'),
            'manage_options',
            'calculator-mama-seo',
            array($this, 'seo_page')
        );
    }

    /**
     * Enqueue admin scripts
     *
     * @param string $hook
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'calculator-mama') === false) {
            return;
        }

        wp_enqueue_style(
            'cmama-admin',
            CMAMA_ASSETS_URL . 'css/admin.css',
            array(),
            CMAMA_VERSION
        );

        wp_enqueue_script(
            'cmama-admin',
            CMAMA_ASSETS_URL . 'js/admin.js',
            array('jquery', 'wp-color-picker'),
            CMAMA_VERSION,
            true
        );

        wp_localize_script('cmama-admin', 'cmama_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_admin_nonce'),
            'strings' => array(
                'confirm_toggle' => __('Are you sure you want to toggle this calculator?', 'calculator-mama'),
                'saving' => __('Saving...', 'calculator-mama'),
                'saved' => __('Settings saved!', 'calculator-mama'),
                'error' => __('An error occurred. Please try again.', 'calculator-mama')
            )
        ));

        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        $dashboard = new Calculator_Mama_Dashboard();
        $dashboard->render();
    }

    /**
     * Library page
     */
    public function library_page() {
        $library = new Calculator_Mama_Library();
        $library->render();
    }

    /**
     * Appearance page
     */
    public function appearance_page() {
        $appearance = new Calculator_Mama_Appearance();
        $appearance->render();
    }

    /**
     * SEO page
     */
    public function seo_page() {
        $seo = new Calculator_Mama_SEO();
        $seo->render_admin_page();
    }

    /**
     * Toggle calculator status
     */
    public function toggle_calculator() {
        check_ajax_referer('cmama_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'calculator-mama'));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator_slug']);
        $active = $_POST['active'] === 'true';

        $database = new Calculator_Mama_Database();
        $calculator_settings = $database->get_calculator_settings($calculator_slug);
        $calculator_settings['active'] = $active;

        $result = $database->update_calculator_settings($calculator_slug, $calculator_settings);

        if ($result) {
            wp_send_json_success(array(
                'message' => $active ? __('Calculator activated.', 'calculator-mama') : __('Calculator deactivated.', 'calculator-mama')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to update calculator status.', 'calculator-mama')
            ));
        }
    }

    /**
     * Save appearance settings
     */
    public function save_appearance() {
        check_ajax_referer('cmama_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'calculator-mama'));
        }

        $appearance_settings = array(
            'primary_color' => sanitize_hex_color($_POST['primary_color']),
            'button_color' => sanitize_hex_color($_POST['button_color']),
            'input_color' => sanitize_hex_color($_POST['input_color']),
            'result_color' => sanitize_hex_color($_POST['result_color']),
            'font_family' => sanitize_text_field($_POST['font_family']),
            'border_radius' => sanitize_text_field($_POST['border_radius']),
            'padding' => sanitize_text_field($_POST['padding']),
            'custom_css' => wp_strip_all_tags($_POST['custom_css'])
        );

        $database = new Calculator_Mama_Database();
        $result = $database->update_appearance_settings($appearance_settings);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Appearance settings saved.', 'calculator-mama')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to save appearance settings.', 'calculator-mama')
            ));
        }
    }

    /**
     * Save SEO settings
     */
    public function save_seo() {
        check_ajax_referer('cmama_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'calculator-mama'));
        }

        $seo_settings = array(
            'enable_schema' => $_POST['enable_schema'] === 'true',
            'enable_meta_description' => $_POST['enable_meta_description'] === 'true'
        );

        $database = new Calculator_Mama_Database();
        $result = $database->update_seo_settings($seo_settings);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('SEO settings saved.', 'calculator-mama')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to save SEO settings.', 'calculator-mama')
            ));
        }
    }
}