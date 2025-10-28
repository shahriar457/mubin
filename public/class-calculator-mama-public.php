<?php
/**
 * Public functionality
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Public class
 */
class Calculator_Mama_Public {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_cmama_calculate', array($this, 'handle_calculation'));
        add_action('wp_ajax_nopriv_cmama_calculate', array($this, 'handle_calculation'));
    }

    /**
     * Enqueue public scripts
     */
    public function enqueue_scripts() {
        // Only load on pages with calculators
        if (!$this->has_calculators()) {
            return;
        }

        wp_enqueue_style(
            'cmama-public',
            CMAMA_ASSETS_URL . 'css/public.css',
            array(),
            CMAMA_VERSION
        );

        wp_enqueue_script(
            'cmama-public',
            CMAMA_ASSETS_URL . 'js/public.js',
            array('jquery'),
            CMAMA_VERSION,
            true
        );

        wp_localize_script('cmama-public', 'cmama_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_public_nonce'),
            'strings' => array(
                'calculating' => __('Calculating...', 'calculator-mama'),
                'error' => __('An error occurred. Please try again.', 'calculator-mama'),
                'invalid_input' => __('Please check your input values.', 'calculator-mama')
            )
        ));
    }

    /**
     * Check if current page has calculators
     *
     * @return bool
     */
    private function has_calculators() {
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
     * Handle calculation via AJAX
     */
    public function handle_calculation() {
        check_ajax_referer('cmama_public_nonce', 'nonce');

        $calculator_slug = sanitize_text_field($_POST['calculator']);
        $data = $_POST['data'];

        if (empty($calculator_slug) || empty($data)) {
            wp_send_json_error(array(
                'message' => __('Invalid request.', 'calculator-mama')
            ));
        }

        $calculator = new Calculator_Mama_Calculator();
        $calculator_info = $calculator->get_calculator($calculator_slug);

        if (!$calculator_info) {
            wp_send_json_error(array(
                'message' => __('Calculator not found.', 'calculator-mama')
            ));
        }

        // Check if calculator is active
        $database = new Calculator_Mama_Database();
        $calculator_settings = $database->get_calculator_settings($calculator_slug);
        if (!isset($calculator_settings['active']) || !$calculator_settings['active']) {
            wp_send_json_error(array(
                'message' => __('This calculator is not available.', 'calculator-mama')
            ));
        }

        // Load calculator class
        $class_name = 'Calculator_Mama_' . str_replace('-', '_', ucwords($calculator_slug, '-'));
        if (!class_exists($class_name)) {
            wp_send_json_error(array(
                'message' => __('Calculator class not found.', 'calculator-mama')
            ));
        }

        $calculator_instance = new $class_name();
        $result = $calculator_instance->calculate($data);

        if (isset($result['error'])) {
            wp_send_json_error(array(
                'message' => $result['error']
            ));
        }

        wp_send_json_success($result);
    }
}