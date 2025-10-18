<?php
/**
 * SEO Engine Class
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO Engine Class
 *
 * Handles automatic Schema.org markup generation and SEO optimization.
 *
 * @since 1.0.0
 */
class CMAMA_SEO_Engine {

    /**
     * SEO Engine instance.
     *
     * @since 1.0.0
     * @var CMAMA_SEO_Engine
     */
    private static $instance = null;

    /**
     * Schema.org types mapping.
     *
     * @since 1.0.0
     * @var array
     */
    private $schema_types = array();

    /**
     * Get SEO Engine instance.
     *
     * @since 1.0.0
     * @return CMAMA_SEO_Engine
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
        $this->init_schema_types();
    }

    /**
     * Initialize Schema.org types mapping.
     *
     * @since 1.0.0
     */
    private function init_schema_types() {
        $this->schema_types = array(
            'financial' => array(
                'mortgage-calculator' => 'MortgageCalculator',
                'loan-calculator' => 'LoanCalculator',
                'investment-calculator' => 'InvestmentCalculator',
                'retirement-calculator' => 'RetirementCalculator',
                'tax-calculator' => 'TaxCalculator',
                'default' => 'FinanceApplication'
            ),
            'health' => array(
                'bmi-calculator' => 'MedicalRiskCalculator',
                'calorie-calculator' => 'MedicalRiskCalculator',
                'pregnancy-calculator' => 'MedicalRiskCalculator',
                'default' => 'MedicalWebApplication'
            ),
            'math' => array(
                'scientific-calculator' => 'MathSolver',
                'percentage-calculator' => 'MathSolver',
                'fraction-calculator' => 'MathSolver',
                'default' => 'MathSolver'
            ),
            'other' => array(
                'age-calculator' => 'WebApplication',
                'date-calculator' => 'WebApplication',
                'default' => 'WebApplication'
            )
        );
    }

    /**
     * Output Schema.org markup for calculators on current page.
     *
     * @since 1.0.0
     */
    public function output_schema_markup() {
        global $post;

        if (!$post || !$this->should_output_schema()) {
            return;
        }

        $calculators = $this->get_calculators_on_page($post);
        
        if (empty($calculators)) {
            return;
        }

        foreach ($calculators as $calculator_slug) {
            $schema = $this->generate_calculator_schema($calculator_slug, $post);
            if ($schema) {
                echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
            }
        }
    }

    /**
     * Check if Schema.org markup should be output.
     *
     * @since 1.0.0
     * @return bool
     */
    private function should_output_schema() {
        $settings = get_option('cmama_settings', array());
        $seo_settings = isset($settings['seo']) ? $settings['seo'] : array();
        
        return isset($seo_settings['enable_schema']) ? $seo_settings['enable_schema'] : true;
    }

    /**
     * Get calculators present on current page.
     *
     * @since 1.0.0
     * @param WP_Post $post Post object.
     * @return array Array of calculator slugs.
     */
    private function get_calculators_on_page($post) {
        $calculators = array();

        // Check for shortcodes
        if (has_shortcode($post->post_content, 'cmama_calculator')) {
            preg_match_all('/\[cmama_calculator[^\]]*slug=["\']([^"\']+)["\'][^\]]*\]/', $post->post_content, $matches);
            if (!empty($matches[1])) {
                $calculators = array_merge($calculators, $matches[1]);
            }
        }

        // Check for Gutenberg blocks
        if (has_block('calculator-mama/calculator', $post)) {
            $blocks = parse_blocks($post->post_content);
            $block_calculators = $this->extract_calculators_from_blocks($blocks);
            $calculators = array_merge($calculators, $block_calculators);
        }

        return array_unique($calculators);
    }

    /**
     * Extract calculator slugs from Gutenberg blocks.
     *
     * @since 1.0.0
     * @param array $blocks Array of block data.
     * @return array Array of calculator slugs.
     */
    private function extract_calculators_from_blocks($blocks) {
        $calculators = array();

        foreach ($blocks as $block) {
            if ($block['blockName'] === 'calculator-mama/calculator') {
                if (!empty($block['attrs']['calculatorSlug'])) {
                    $calculators[] = $block['attrs']['calculatorSlug'];
                }
            }

            // Check inner blocks recursively
            if (!empty($block['innerBlocks'])) {
                $inner_calculators = $this->extract_calculators_from_blocks($block['innerBlocks']);
                $calculators = array_merge($calculators, $inner_calculators);
            }
        }

        return $calculators;
    }

    /**
     * Generate Schema.org markup for a calculator.
     *
     * @since 1.0.0
     * @param string $calculator_slug Calculator slug.
     * @param WP_Post $post Post object.
     * @return array|null Schema.org data or null.
     */
    public function generate_calculator_schema($calculator_slug, $post = null) {
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator) {
            return null;
        }

        if (!$post) {
            global $post;
        }

        $settings = get_option('cmama_settings', array());
        $seo_settings = isset($settings['seo']) ? $settings['seo'] : array();
        $default_author = isset($seo_settings['default_author']) ? $seo_settings['default_author'] : get_option('blogname', 'Calculator Mama');

        // Get appropriate Schema.org type
        $schema_type = $this->get_schema_type($calculator);

        // Base schema data
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $schema_type,
            'name' => $calculator->get_name(),
            'description' => $calculator->get_description(),
            'url' => $post ? get_permalink($post) : home_url(),
            'applicationCategory' => 'CalculatorApplication',
            'operatingSystem' => 'Web Browser',
            'permissions' => 'browser',
            'isAccessibleForFree' => true,
            'author' => array(
                '@type' => 'Organization',
                'name' => $default_author,
                'url' => home_url()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_option('blogname', 'Calculator Mama'),
                'url' => home_url()
            ),
            'datePublished' => $post ? get_the_date('c', $post) : current_time('c'),
            'dateModified' => $post ? get_the_modified_date('c', $post) : current_time('c')
        );

        // Add category-specific properties
        $schema = $this->add_category_specific_properties($schema, $calculator);

        // Add input/output properties
        $schema = $this->add_input_output_properties($schema, $calculator);

        // Add breadcrumb if on single post/page
        if ($post && is_singular()) {
            $schema['breadcrumb'] = $this->generate_breadcrumb_schema($post);
        }

        // Add FAQ schema if available
        $faq_schema = $this->generate_faq_schema($post, $calculator_slug);
        if ($faq_schema) {
            // Return array of schemas
            return array($schema, $faq_schema);
        }

        return $schema;
    }

    /**
     * Get appropriate Schema.org type for calculator.
     *
     * @since 1.0.0
     * @param CMAMA_Calculator_Base $calculator Calculator instance.
     * @return string Schema.org type.
     */
    private function get_schema_type($calculator) {
        $category = $calculator->get_category();
        $slug = $calculator->get_slug();

        // Check for specific calculator type
        if (isset($this->schema_types[$category][$slug])) {
            return $this->schema_types[$category][$slug];
        }

        // Use category default
        if (isset($this->schema_types[$category]['default'])) {
            return $this->schema_types[$category]['default'];
        }

        // Fallback to WebApplication
        return 'WebApplication';
    }

    /**
     * Add category-specific Schema.org properties.
     *
     * @since 1.0.0
     * @param array $schema Base schema data.
     * @param CMAMA_Calculator_Base $calculator Calculator instance.
     * @return array Enhanced schema data.
     */
    private function add_category_specific_properties($schema, $calculator) {
        $category = $calculator->get_category();

        switch ($category) {
            case 'financial':
                $schema['applicationSubCategory'] = 'Finance';
                $schema['featureList'] = array(
                    'Financial calculations',
                    'Loan and mortgage calculations',
                    'Investment planning',
                    'Tax calculations'
                );
                break;

            case 'health':
                $schema['applicationSubCategory'] = 'Health & Fitness';
                $schema['featureList'] = array(
                    'Health calculations',
                    'BMI and body composition',
                    'Calorie and nutrition',
                    'Fitness planning'
                );
                break;

            case 'math':
                $schema['applicationSubCategory'] = 'Education';
                $schema['featureList'] = array(
                    'Mathematical calculations',
                    'Scientific computing',
                    'Statistical analysis',
                    'Educational tools'
                );
                break;

            case 'other':
                $schema['applicationSubCategory'] = 'Utility';
                $schema['featureList'] = array(
                    'General calculations',
                    'Date and time calculations',
                    'Unit conversions',
                    'Utility tools'
                );
                break;
        }

        return $schema;
    }

    /**
     * Add input/output properties to schema.
     *
     * @since 1.0.0
     * @param array $schema Base schema data.
     * @param CMAMA_Calculator_Base $calculator Calculator instance.
     * @return array Enhanced schema data.
     */
    private function add_input_output_properties($schema, $calculator) {
        $fields = $calculator->get_fields();

        if (!empty($fields)) {
            $inputs = array();
            foreach ($fields as $field_id => $field) {
                $inputs[] = array(
                    '@type' => 'PropertyValueSpecification',
                    'name' => $field['label'],
                    'valueRequired' => !empty($field['required']),
                    'valueName' => $field_id
                );
            }
            $schema['serviceInput'] = $inputs;
        }

        // Add generic output description
        $schema['serviceOutput'] = array(
            '@type' => 'PropertyValueSpecification',
            'name' => 'Calculation Result',
            'description' => 'Calculated result based on provided inputs'
        );

        return $schema;
    }

    /**
     * Generate breadcrumb Schema.org markup.
     *
     * @since 1.0.0
     * @param WP_Post $post Post object.
     * @return array Breadcrumb schema data.
     */
    private function generate_breadcrumb_schema($post) {
        $breadcrumbs = array(
            '@type' => 'BreadcrumbList',
            'itemListElement' => array()
        );

        $position = 1;

        // Home
        $breadcrumbs['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_option('blogname'),
            'item' => home_url()
        );

        // Category/Parent pages (if applicable)
        if ($post->post_parent) {
            $ancestors = get_post_ancestors($post);
            $ancestors = array_reverse($ancestors);
            
            foreach ($ancestors as $ancestor_id) {
                $breadcrumbs['itemListElement'][] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => get_the_title($ancestor_id),
                    'item' => get_permalink($ancestor_id)
                );
            }
        }

        // Current page
        $breadcrumbs['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title($post),
            'item' => get_permalink($post)
        );

        return $breadcrumbs;
    }

    /**
     * Generate FAQ Schema.org markup from block attributes.
     *
     * @since 1.0.0
     * @param WP_Post $post Post object.
     * @param string $calculator_slug Calculator slug.
     * @return array|null FAQ schema data or null.
     */
    private function generate_faq_schema($post, $calculator_slug) {
        if (!$post || !has_block('calculator-mama/calculator', $post)) {
            return null;
        }

        $blocks = parse_blocks($post->post_content);
        $faqs = $this->extract_faqs_from_blocks($blocks, $calculator_slug);

        if (empty($faqs)) {
            return null;
        }

        $faq_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );

        foreach ($faqs as $faq) {
            $faq_schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }

        return $faq_schema;
    }

    /**
     * Extract FAQs from Gutenberg blocks.
     *
     * @since 1.0.0
     * @param array $blocks Array of block data.
     * @param string $calculator_slug Target calculator slug.
     * @return array Array of FAQ data.
     */
    private function extract_faqs_from_blocks($blocks, $calculator_slug) {
        $faqs = array();

        foreach ($blocks as $block) {
            if ($block['blockName'] === 'calculator-mama/calculator') {
                if (!empty($block['attrs']['calculatorSlug']) && 
                    $block['attrs']['calculatorSlug'] === $calculator_slug &&
                    !empty($block['attrs']['faqs'])) {
                    
                    foreach ($block['attrs']['faqs'] as $faq) {
                        if (!empty($faq['question']) && !empty($faq['answer'])) {
                            $faqs[] = array(
                                'question' => strip_tags($faq['question']),
                                'answer' => strip_tags($faq['answer'])
                            );
                        }
                    }
                }
            }

            // Check inner blocks recursively
            if (!empty($block['innerBlocks'])) {
                $inner_faqs = $this->extract_faqs_from_blocks($block['innerBlocks'], $calculator_slug);
                $faqs = array_merge($faqs, $inner_faqs);
            }
        }

        return $faqs;
    }

    /**
     * Generate schema for calculator result page.
     *
     * @since 1.0.0
     * @param string $calculator_slug Calculator slug.
     * @param array $inputs Input values.
     * @param array $result Calculation result.
     * @return array Schema data.
     */
    public function generate_result_schema($calculator_slug, $inputs, $result) {
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator) {
            return null;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'MathSolver',
            'name' => $calculator->get_name() . ' - Calculation Result',
            'description' => 'Calculation result from ' . $calculator->get_name(),
            'mathExpression' => array(
                '@type' => 'MathSolver',
                'mathExpression' => json_encode($inputs),
                'solution' => json_encode($result)
            ),
            'dateCreated' => current_time('c'),
            'creator' => array(
                '@type' => 'SoftwareApplication',
                'name' => 'Calculator Mama',
                'url' => home_url()
            )
        );

        return $schema;
    }

    /**
     * Add meta tags for calculator pages.
     *
     * @since 1.0.0
     * @param string $calculator_slug Calculator slug.
     * @param WP_Post $post Post object.
     */
    public function add_calculator_meta_tags($calculator_slug, $post = null) {
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator || !$post) {
            return;
        }

        // Add calculator-specific meta description if not already set
        if (!has_action('wp_head', 'rel_canonical') && empty(get_post_meta($post->ID, '_yoast_wpseo_metadesc', true))) {
            $meta_description = sprintf(
                __('Use our free %s to calculate accurate results instantly. %s', CMAMA_TEXT_DOMAIN),
                strtolower($calculator->get_name()),
                $calculator->get_description()
            );
            
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }

        // Add calculator-specific keywords
        $keywords = array_merge(
            array($calculator->get_name(), $calculator->get_category() . ' calculator'),
            $calculator->get_tags()
        );
        
        echo '<meta name="keywords" content="' . esc_attr(implode(', ', $keywords)) . '">' . "\n";

        // Add Open Graph tags
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($calculator->get_name() . ' - ' . get_the_title($post)) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($calculator->get_description()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_attr(get_permalink($post)) . '">' . "\n";

        // Add Twitter Card tags
        echo '<meta name="twitter:card" content="summary">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($calculator->get_name()) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($calculator->get_description()) . '">' . "\n";
    }
}