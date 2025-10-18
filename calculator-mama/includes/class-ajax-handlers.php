<?php
/**
 * AJAX Handlers Class
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX Handlers Class
 *
 * Handles all AJAX requests for the plugin.
 *
 * @since 1.0.0
 */
class CMAMA_AJAX_Handlers {

    /**
     * AJAX Handlers instance.
     *
     * @since 1.0.0
     * @var CMAMA_AJAX_Handlers
     */
    private static $instance = null;

    /**
     * Get AJAX Handlers instance.
     *
     * @since 1.0.0
     * @return CMAMA_AJAX_Handlers
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
        // Admin AJAX handlers
        add_action('wp_ajax_cmama_toggle_calculator', array($this, 'ajax_toggle_calculator'));
        add_action('wp_ajax_cmama_save_appearance', array($this, 'ajax_save_appearance'));
        add_action('wp_ajax_cmama_get_calculator_preview', array($this, 'ajax_get_calculator_preview'));
        add_action('wp_ajax_cmama_search_calculators', array($this, 'ajax_search_calculators'));
        add_action('wp_ajax_cmama_export_settings', array($this, 'ajax_export_settings'));
        add_action('wp_ajax_cmama_import_settings', array($this, 'ajax_import_settings'));
        add_action('wp_ajax_cmama_dismiss_welcome', array($this, 'ajax_dismiss_welcome'));

        // Frontend AJAX handlers (both logged in and logged out users)
        add_action('wp_ajax_cmama_calculate', array($this, 'ajax_calculate'));
        add_action('wp_ajax_nopriv_cmama_calculate', array($this, 'ajax_calculate'));

        // REST API endpoints for Gutenberg
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * AJAX handler for toggling calculator status.
     *
     * @since 1.0.0
     */
    public function ajax_toggle_calculator() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator_slug']);
        $action = sanitize_text_field($_POST['action_type']); // 'activate' or 'deactivate'

        $registry = CMAMA_Calculator_Registry::get_instance();
        
        if (!$registry->calculator_exists($calculator_slug)) {
            wp_send_json_error(__('Calculator not found.', CMAMA_TEXT_DOMAIN));
        }

        if ($action === 'activate') {
            $result = cmama_activate_calculator($calculator_slug);
            $message = __('Calculator activated successfully.', CMAMA_TEXT_DOMAIN);
        } else {
            $result = cmama_deactivate_calculator($calculator_slug);
            $message = __('Calculator deactivated successfully.', CMAMA_TEXT_DOMAIN);
        }

        if ($result) {
            // Log activity
            $admin = CMAMA_Admin::get_instance();
            $admin->log_activity('calculator_' . ($action === 'activate' ? 'activated' : 'deactivated'), $calculator_slug);

            wp_send_json_success(array(
                'message' => $message,
                'active' => $action === 'activate'
            ));
        } else {
            wp_send_json_error(__('Failed to update calculator status.', CMAMA_TEXT_DOMAIN));
        }
    }

    /**
     * AJAX handler for saving appearance settings.
     *
     * @since 1.0.0
     */
    public function ajax_save_appearance() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['cmama_nonce'], 'cmama_appearance_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $appearance_data = array();

        // Sanitize appearance settings
        if (isset($_POST['primary_color'])) {
            $appearance_data['primary_color'] = sanitize_hex_color($_POST['primary_color']);
        }
        if (isset($_POST['button_color'])) {
            $appearance_data['button_color'] = sanitize_hex_color($_POST['button_color']);
        }
        if (isset($_POST['input_color'])) {
            $appearance_data['input_color'] = sanitize_hex_color($_POST['input_color']);
        }
        if (isset($_POST['result_color'])) {
            $appearance_data['result_color'] = sanitize_hex_color($_POST['result_color']);
        }
        if (isset($_POST['font_family'])) {
            $appearance_data['font_family'] = sanitize_text_field($_POST['font_family']);
        }
        if (isset($_POST['border_radius'])) {
            $appearance_data['border_radius'] = absint($_POST['border_radius']);
        }
        if (isset($_POST['padding'])) {
            $appearance_data['padding'] = absint($_POST['padding']);
        }
        if (isset($_POST['custom_css'])) {
            $appearance_data['custom_css'] = wp_strip_all_tags($_POST['custom_css']);
        }

        // Update settings
        $settings = get_option('cmama_settings', array());
        $settings['appearance'] = array_merge(
            isset($settings['appearance']) ? $settings['appearance'] : array(),
            $appearance_data
        );

        $result = update_option('cmama_settings', $settings);

        if ($result) {
            // Log activity
            $admin = CMAMA_Admin::get_instance();
            $admin->log_activity('settings_updated', '', array('type' => 'appearance'));

            wp_send_json_success(__('Appearance settings saved successfully.', CMAMA_TEXT_DOMAIN));
        } else {
            wp_send_json_error(__('Failed to save appearance settings.', CMAMA_TEXT_DOMAIN));
        }
    }

    /**
     * AJAX handler for getting calculator preview.
     *
     * @since 1.0.0
     */
    public function ajax_get_calculator_preview() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator_slug']);
        
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator) {
            wp_send_json_error(__('Calculator not found.', CMAMA_TEXT_DOMAIN));
        }

        // Render calculator preview
        $preview_html = $calculator->render(array(
            'show_title' => true,
            'show_description' => true,
            'css_class' => 'cmama-preview'
        ));

        wp_send_json_success(array(
            'html' => $preview_html,
            'calculator' => array(
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category(),
                'slug' => $calculator->get_slug()
            )
        ));
    }

    /**
     * AJAX handler for searching calculators.
     *
     * @since 1.0.0
     */
    public function ajax_search_calculators() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $search_term = sanitize_text_field($_POST['search']);
        $category = sanitize_text_field($_POST['category']);

        $args = array();
        if (!empty($search_term)) {
            $args['search'] = $search_term;
        }
        if (!empty($category)) {
            $args['category'] = $category;
        }

        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculators = $registry->get_calculators($args);

        $results = array();
        $settings = get_option('cmama_settings', array());
        $active_calculators = isset($settings['active_calculators']) ? $settings['active_calculators'] : array();

        foreach ($calculators as $slug => $calculator) {
            $results[] = array(
                'slug' => $calculator->get_slug(),
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category(),
                'tags' => $calculator->get_tags(),
                'active' => in_array($slug, $active_calculators),
                'shortcode' => '[cmama_calculator slug="' . $slug . '"]'
            );
        }

        wp_send_json_success($results);
    }

    /**
     * AJAX handler for exporting settings.
     *
     * @since 1.0.0
     */
    public function ajax_export_settings() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $admin = CMAMA_Admin::get_instance();
        $export_data = $admin->export_settings();

        wp_send_json_success($export_data);
    }

    /**
     * AJAX handler for importing settings.
     *
     * @since 1.0.0
     */
    public function ajax_import_settings() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $import_data_json = wp_unslash($_POST['import_data']);
        $import_data = json_decode($import_data_json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('Invalid JSON data.', CMAMA_TEXT_DOMAIN));
        }

        $admin = CMAMA_Admin::get_instance();
        $result = $admin->import_settings($import_data);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(__('Settings imported successfully.', CMAMA_TEXT_DOMAIN));
        }
    }

    /**
     * AJAX handler for dismissing welcome notice.
     *
     * @since 1.0.0
     */
    public function ajax_dismiss_welcome() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        update_option('cmama_show_welcome_notice', false);
        wp_send_json_success(__('Welcome notice dismissed.', CMAMA_TEXT_DOMAIN));
    }

    /**
     * AJAX handler for calculator calculations.
     *
     * @since 1.0.0
     */
    public function ajax_calculate() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_frontend_nonce')) {
            wp_send_json_error(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator_slug']);
        $inputs = $_POST['inputs'];

        if (empty($calculator_slug)) {
            wp_send_json_error(__('Calculator slug is required.', CMAMA_TEXT_DOMAIN));
        }

        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator) {
            wp_send_json_error(__('Calculator not found.', CMAMA_TEXT_DOMAIN));
        }

        // Check if calculator is active
        if (!cmama_is_calculator_active($calculator_slug)) {
            wp_send_json_error(__('Calculator is not active.', CMAMA_TEXT_DOMAIN));
        }

        // Sanitize inputs
        $sanitized_inputs = array();
        foreach ($inputs as $key => $value) {
            $sanitized_inputs[sanitize_key($key)] = cmama_sanitize_input($value);
        }

        // Validate inputs
        $validated_inputs = $calculator->validate_inputs($sanitized_inputs);
        
        if (is_wp_error($validated_inputs)) {
            wp_send_json_error($validated_inputs->get_error_message());
        }

        try {
            // Perform calculation
            $result = $calculator->calculate($validated_inputs);
            
            // Format result for display
            $formatted_result = $calculator->format_result($result);
            
            // Log usage
            cmama_log_usage($calculator_slug, $validated_inputs, $result);
            
            wp_send_json_success(array(
                'result' => $result,
                'formatted_result' => $formatted_result,
                'calculator' => array(
                    'slug' => $calculator->get_slug(),
                    'name' => $calculator->get_name()
                )
            ));
            
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Register REST API routes for Gutenberg.
     *
     * @since 1.0.0
     */
    public function register_rest_routes() {
        register_rest_route('calculator-mama/v1', '/calculators', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_calculators'),
            'permission_callback' => array($this, 'rest_permissions_check')
        ));

        register_rest_route('calculator-mama/v1', '/preview/(?P<slug>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_calculator_preview'),
            'permission_callback' => array($this, 'rest_permissions_check'),
            'args' => array(
                'slug' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));
    }

    /**
     * REST API permissions check.
     *
     * @since 1.0.0
     * @return bool
     */
    public function rest_permissions_check() {
        return current_user_can('edit_posts');
    }

    /**
     * REST API endpoint to get calculators.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_get_calculators($request) {
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculators = $registry->get_calculators(array('active_only' => false));

        $response_data = array();
        foreach ($calculators as $slug => $calculator) {
            $response_data[] = array(
                'slug' => $calculator->get_slug(),
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category(),
                'tags' => $calculator->get_tags()
            );
        }

        return new WP_REST_Response($response_data, 200);
    }

    /**
     * REST API endpoint to get calculator preview.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_get_calculator_preview($request) {
        $slug = $request->get_param('slug');
        
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($slug);

        if (!$calculator) {
            return new WP_Error('calculator_not_found', __('Calculator not found.', CMAMA_TEXT_DOMAIN), array('status' => 404));
        }

        // Render calculator preview
        $preview_html = $calculator->render(array(
            'show_title' => true,
            'show_description' => true,
            'css_class' => 'cmama-block-preview'
        ));

        $response_data = array(
            'html' => $preview_html,
            'calculator' => array(
                'slug' => $calculator->get_slug(),
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category()
            )
        );

        return new WP_REST_Response($response_data, 200);
    }
}