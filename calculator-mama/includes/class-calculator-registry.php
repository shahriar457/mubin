<?php
/**
 * Calculator Registry Class
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Calculator Registry Class
 *
 * Manages all available calculators and provides access to them.
 *
 * @since 1.0.0
 */
class CMAMA_Calculator_Registry {

    /**
     * Registry instance.
     *
     * @since 1.0.0
     * @var CMAMA_Calculator_Registry
     */
    private static $instance = null;

    /**
     * Registered calculators.
     *
     * @since 1.0.0
     * @var array
     */
    private $calculators = array();

    /**
     * Calculator categories.
     *
     * @since 1.0.0
     * @var array
     */
    private $categories = array();

    /**
     * Get registry instance.
     *
     * @since 1.0.0
     * @return CMAMA_Calculator_Registry
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
        $this->init_categories();
        add_action('init', array($this, 'register_default_calculators'), 5);
    }

    /**
     * Initialize calculator categories.
     *
     * @since 1.0.0
     */
    private function init_categories() {
        $this->categories = array(
            'financial' => array(
                'name' => __('Financial Calculators', CMAMA_TEXT_DOMAIN),
                'description' => __('Mortgage, loan, investment, and other financial calculators.', CMAMA_TEXT_DOMAIN),
                'icon' => 'dashicons-money-alt'
            ),
            'health' => array(
                'name' => __('Health & Fitness Calculators', CMAMA_TEXT_DOMAIN),
                'description' => __('BMI, calorie, fitness, and health-related calculators.', CMAMA_TEXT_DOMAIN),
                'icon' => 'dashicons-heart'
            ),
            'math' => array(
                'name' => __('Math Calculators', CMAMA_TEXT_DOMAIN),
                'description' => __('Scientific, statistical, and mathematical calculators.', CMAMA_TEXT_DOMAIN),
                'icon' => 'dashicons-calculator'
            ),
            'other' => array(
                'name' => __('Other Calculators', CMAMA_TEXT_DOMAIN),
                'description' => __('Age, date, conversion, and miscellaneous calculators.', CMAMA_TEXT_DOMAIN),
                'icon' => 'dashicons-admin-tools'
            )
        );
    }

    /**
     * Register a calculator.
     *
     * @since 1.0.0
     * @param string $slug Calculator slug.
     * @param CMAMA_Calculator_Base $calculator Calculator instance.
     * @return bool Success status.
     */
    public function register_calculator($slug, $calculator) {
        if (!($calculator instanceof CMAMA_Calculator_Base)) {
            return false;
        }

        $this->calculators[$slug] = $calculator;
        return true;
    }

    /**
     * Unregister a calculator.
     *
     * @since 1.0.0
     * @param string $slug Calculator slug.
     * @return bool Success status.
     */
    public function unregister_calculator($slug) {
        if (isset($this->calculators[$slug])) {
            unset($this->calculators[$slug]);
            return true;
        }
        return false;
    }

    /**
     * Get a calculator by slug.
     *
     * @since 1.0.0
     * @param string $slug Calculator slug.
     * @return CMAMA_Calculator_Base|null Calculator instance or null.
     */
    public function get_calculator($slug) {
        return isset($this->calculators[$slug]) ? $this->calculators[$slug] : null;
    }

    /**
     * Get all registered calculators.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return array Array of calculator instances.
     */
    public function get_calculators($args = array()) {
        $args = wp_parse_args($args, array(
            'category' => '',
            'search' => '',
            'active_only' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));

        $calculators = $this->calculators;

        // Filter by category
        if (!empty($args['category'])) {
            $calculators = array_filter($calculators, function($calculator) use ($args) {
                return $calculator->get_category() === $args['category'];
            });
        }

        // Filter by search term
        if (!empty($args['search'])) {
            $search = strtolower($args['search']);
            $calculators = array_filter($calculators, function($calculator) use ($search) {
                $searchable = strtolower($calculator->get_name() . ' ' . $calculator->get_description() . ' ' . implode(' ', $calculator->get_tags()));
                return strpos($searchable, $search) !== false;
            });
        }

        // Filter active only
        if ($args['active_only']) {
            $settings = get_option('cmama_settings', array());
            $active_calculators = isset($settings['active_calculators']) ? $settings['active_calculators'] : array();
            
            $calculators = array_filter($calculators, function($calculator) use ($active_calculators) {
                return in_array($calculator->get_slug(), $active_calculators);
            });
        }

        // Sort calculators
        uasort($calculators, function($a, $b) use ($args) {
            switch ($args['orderby']) {
                case 'name':
                    $result = strcmp($a->get_name(), $b->get_name());
                    break;
                case 'category':
                    $result = strcmp($a->get_category(), $b->get_category());
                    break;
                case 'slug':
                    $result = strcmp($a->get_slug(), $b->get_slug());
                    break;
                default:
                    $result = 0;
                    break;
            }

            return ($args['order'] === 'DESC') ? -$result : $result;
        });

        return $calculators;
    }

    /**
     * Get calculator slugs.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return array Array of calculator slugs.
     */
    public function get_calculator_slugs($args = array()) {
        $calculators = $this->get_calculators($args);
        return array_keys($calculators);
    }

    /**
     * Get calculator names.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return array Array of calculator names keyed by slug.
     */
    public function get_calculator_names($args = array()) {
        $calculators = $this->get_calculators($args);
        $names = array();
        
        foreach ($calculators as $slug => $calculator) {
            $names[$slug] = $calculator->get_name();
        }
        
        return $names;
    }

    /**
     * Get calculators by category.
     *
     * @since 1.0.0
     * @param string $category Category slug.
     * @return array Array of calculator instances.
     */
    public function get_calculators_by_category($category) {
        return $this->get_calculators(array('category' => $category));
    }

    /**
     * Get all categories.
     *
     * @since 1.0.0
     * @return array Array of categories.
     */
    public function get_categories() {
        return $this->categories;
    }

    /**
     * Get category data.
     *
     * @since 1.0.0
     * @param string $category Category slug.
     * @return array|null Category data or null.
     */
    public function get_category($category) {
        return isset($this->categories[$category]) ? $this->categories[$category] : null;
    }

    /**
     * Check if calculator exists.
     *
     * @since 1.0.0
     * @param string $slug Calculator slug.
     * @return bool True if calculator exists.
     */
    public function calculator_exists($slug) {
        return isset($this->calculators[$slug]);
    }

    /**
     * Get calculator count.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return int Number of calculators.
     */
    public function get_calculator_count($args = array()) {
        return count($this->get_calculators($args));
    }

    /**
     * Get calculator count by category.
     *
     * @since 1.0.0
     * @return array Array of counts keyed by category.
     */
    public function get_calculator_count_by_category() {
        $counts = array();
        
        foreach ($this->categories as $category_slug => $category) {
            $counts[$category_slug] = $this->get_calculator_count(array('category' => $category_slug));
        }
        
        return $counts;
    }

    /**
     * Search calculators.
     *
     * @since 1.0.0
     * @param string $search Search term.
     * @param array $args Additional arguments.
     * @return array Array of calculator instances.
     */
    public function search_calculators($search, $args = array()) {
        $args['search'] = $search;
        return $this->get_calculators($args);
    }

    /**
     * Get calculators for admin library.
     *
     * @since 1.0.0
     * @return array Array of calculator data for admin.
     */
    public function get_calculators_for_admin() {
        $calculators = $this->get_calculators();
        $settings = get_option('cmama_settings', array());
        $active_calculators = isset($settings['active_calculators']) ? $settings['active_calculators'] : array();
        
        $admin_data = array();
        
        foreach ($calculators as $slug => $calculator) {
            $admin_data[] = array(
                'slug' => $calculator->get_slug(),
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category(),
                'tags' => $calculator->get_tags(),
                'active' => in_array($slug, $active_calculators),
                'shortcode' => '[cmama_calculator slug="' . $slug . '"]'
            );
        }
        
        return $admin_data;
    }

    /**
     * Get calculators for Gutenberg block.
     *
     * @since 1.0.0
     * @return array Array of calculator options for block.
     */
    public function get_calculators_for_block() {
        $calculators = $this->get_calculators(array('active_only' => true));
        $options = array();
        
        $options[] = array(
            'value' => '',
            'label' => __('Select a calculator...', CMAMA_TEXT_DOMAIN)
        );
        
        foreach ($calculators as $slug => $calculator) {
            $options[] = array(
                'value' => $slug,
                'label' => $calculator->get_name() . ' (' . $this->get_category_name($calculator->get_category()) . ')'
            );
        }
        
        return $options;
    }

    /**
     * Get category name.
     *
     * @since 1.0.0
     * @param string $category_slug Category slug.
     * @return string Category name.
     */
    private function get_category_name($category_slug) {
        $category = $this->get_category($category_slug);
        return $category ? $category['name'] : $category_slug;
    }

    /**
     * Register default calculators.
     *
     * @since 1.0.0
     */
    public function register_default_calculators() {
        // This will be called after all calculator files are loaded
        do_action('cmama_register_calculators', $this);
    }

    /**
     * Export calculators data.
     *
     * @since 1.0.0
     * @return array Calculators data for export.
     */
    public function export_calculators() {
        $calculators = $this->get_calculators();
        $export_data = array();
        
        foreach ($calculators as $slug => $calculator) {
            $export_data[$slug] = $calculator->to_array();
        }
        
        return $export_data;
    }

    /**
     * Get calculator statistics.
     *
     * @since 1.0.0
     * @return array Statistics data.
     */
    public function get_statistics() {
        $total_calculators = $this->get_calculator_count();
        $active_calculators = $this->get_calculator_count(array('active_only' => true));
        $category_counts = $this->get_calculator_count_by_category();
        
        return array(
            'total_calculators' => $total_calculators,
            'active_calculators' => $active_calculators,
            'inactive_calculators' => $total_calculators - $active_calculators,
            'category_counts' => $category_counts,
            'categories' => $this->categories
        );
    }
}