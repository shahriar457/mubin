<?php
/**
 * Calculator management system
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Calculator management class
 */
class Calculator_Mama_Calculator {

    /**
     * Available calculators
     *
     * @var array
     */
    private $calculators = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->load_calculators();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Load all available calculators
     */
    private function load_calculators() {
        $calculator_files = glob(CMAMA_CALCULATORS_DIR . '*/class-calculator-*.php');
        
        foreach ($calculator_files as $file) {
            require_once $file;
            $class_name = $this->get_class_name_from_file($file);
            if (class_exists($class_name)) {
                $calculator = new $class_name();
                if (method_exists($calculator, 'get_info')) {
                    $info = $calculator->get_info();
                    $this->calculators[$info['slug']] = $info;
                }
            }
        }
    }

    /**
     * Get class name from file path
     *
     * @param string $file_path
     * @return string
     */
    private function get_class_name_from_file($file_path) {
        $filename = basename($file_path, '.php');
        $filename = str_replace('class-calculator-', '', $filename);
        $filename = str_replace('-', ' ', $filename);
        $filename = ucwords($filename);
        $filename = str_replace(' ', '_', $filename);
        return 'Calculator_Mama_' . $filename;
    }

    /**
     * Get all calculators
     *
     * @return array
     */
    public function get_calculators() {
        return $this->calculators;
    }

    /**
     * Get calculator by slug
     *
     * @param string $slug
     * @return array|false
     */
    public function get_calculator($slug) {
        return isset($this->calculators[$slug]) ? $this->calculators[$slug] : false;
    }

    /**
     * Get active calculators
     *
     * @return array
     */
    public function get_active_calculators() {
        $settings = $this->get_database()->get_settings();
        $active_calculators = array();

        foreach ($this->calculators as $slug => $calculator) {
            $calculator_settings = isset($settings['calculators'][$slug]) ? $settings['calculators'][$slug] : array();
            if (isset($calculator_settings['active']) && $calculator_settings['active']) {
                $active_calculators[$slug] = $calculator;
            }
        }

        return $active_calculators;
    }

    /**
     * Render calculator
     *
     * @param string $slug
     * @param array $atts
     * @return string
     */
    public function render_calculator($slug, $atts = array()) {
        $calculator = $this->get_calculator($slug);
        if (!$calculator) {
            return '<p>' . esc_html__('Calculator not found.', 'calculator-mama') . '</p>';
        }

        $class_name = 'Calculator_Mama_' . str_replace('-', '_', ucwords($slug, '-'));
        if (!class_exists($class_name)) {
            return '<p>' . esc_html__('Calculator class not found.', 'calculator-mama') . '</p>';
        }

        $calculator_instance = new $class_name();
        return $calculator_instance->render($atts);
    }

    /**
     * Enqueue calculator scripts
     *
     * @param string $slug
     */
    public function enqueue_calculator_scripts($slug) {
        $calculator = $this->get_calculator($slug);
        if (!$calculator) {
            return;
        }

        // Enqueue calculator-specific CSS
        if (isset($calculator['css_file']) && file_exists($calculator['css_file'])) {
            wp_enqueue_style(
                'cmama-calculator-' . $slug,
                CMAMA_PLUGIN_URL . 'calculators/' . $slug . '/style.css',
                array(),
                CMAMA_VERSION
            );
        }

        // Enqueue calculator-specific JS
        if (isset($calculator['js_file']) && file_exists($calculator['js_file'])) {
            wp_enqueue_script(
                'cmama-calculator-' . $slug,
                CMAMA_PLUGIN_URL . 'calculators/' . $slug . '/script.js',
                array('jquery'),
                CMAMA_VERSION,
                true
            );
        }
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        // Enqueue common styles
        wp_enqueue_style(
            'cmama-common',
            CMAMA_ASSETS_URL . 'css/common.css',
            array(),
            CMAMA_VERSION
        );

        // Enqueue common scripts
        wp_enqueue_script(
            'cmama-common',
            CMAMA_ASSETS_URL . 'js/common.js',
            array('jquery'),
            CMAMA_VERSION,
            true
        );

        // Localize script
        wp_localize_script('cmama-common', 'cmama_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_nonce')
        ));
    }

    /**
     * Get database instance
     *
     * @return Calculator_Mama_Database
     */
    private function get_database() {
        global $cmama_database;
        if (!$cmama_database) {
            $cmama_database = new Calculator_Mama_Database();
        }
        return $cmama_database;
    }
}