<?php
/**
 * SEO functionality
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO class
 */
class Calculator_Mama_SEO {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_head', array($this, 'add_schema_markup'));
        add_action('wp_head', array($this, 'add_meta_description'));
    }

    /**
     * Add schema markup to head
     */
    public function add_schema_markup() {
        if (!$this->is_calculator_page()) {
            return;
        }

        $database = new Calculator_Mama_Database();
        $seo_settings = $database->get_seo_settings();

        if (!$seo_settings['enable_schema']) {
            return;
        }

        $schema = $this->generate_schema_markup();
        if ($schema) {
            echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
    }

    /**
     * Add meta description
     */
    public function add_meta_description() {
        if (!$this->is_calculator_page()) {
            return;
        }

        $database = new Calculator_Mama_Database();
        $seo_settings = $database->get_seo_settings();

        if (!$seo_settings['enable_meta_description']) {
            return;
        }

        $meta_description = $this->generate_meta_description();
        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
    }

    /**
     * Check if current page has calculators
     *
     * @return bool
     */
    private function is_calculator_page() {
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
     * Generate schema markup
     *
     * @return array|false
     */
    private function generate_schema_markup() {
        global $post;

        if (!$post) {
            return false;
        }

        $calculator = new Calculator_Mama_Calculator();
        $calculators = $this->get_calculators_from_content($post->post_content);

        if (empty($calculators)) {
            return false;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => get_the_title($post),
            'description' => $this->get_page_description($post),
            'url' => get_permalink($post),
            'applicationCategory' => 'CalculatorApplication',
            'operatingSystem' => 'Web Browser',
            'offers' => array(
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD'
            )
        );

        // Add calculator-specific schema
        if (count($calculators) === 1) {
            $calculator_info = $calculator->get_calculator($calculators[0]);
            if ($calculator_info) {
                $schema = array_merge($schema, $this->get_calculator_schema($calculator_info));
            }
        }

        return $schema;
    }

    /**
     * Get calculator-specific schema
     *
     * @param array $calculator_info
     * @return array
     */
    private function get_calculator_schema($calculator_info) {
        $schema = array();

        switch ($calculator_info['category']) {
            case 'financial':
                $schema['@type'] = 'FinancialProduct';
                $schema['category'] = 'Financial Calculator';
                break;
            case 'health':
                $schema['@type'] = 'MedicalWebPage';
                $schema['category'] = 'Health Calculator';
                break;
            case 'math':
                $schema['@type'] = 'MathSolver';
                $schema['category'] = 'Math Calculator';
                break;
            default:
                $schema['@type'] = 'WebApplication';
                $schema['category'] = 'Calculator';
        }

        $schema['name'] = $calculator_info['name'];
        $schema['description'] = $calculator_info['description'];

        return $schema;
    }

    /**
     * Generate meta description
     *
     * @return string|false
     */
    private function generate_meta_description() {
        global $post;

        if (!$post) {
            return false;
        }

        $description = $this->get_page_description($post);
        
        // Limit to 160 characters
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return $description;
    }

    /**
     * Get page description
     *
     * @param WP_Post $post
     * @return string
     */
    private function get_page_description($post) {
        // Try to get custom description from post meta
        $custom_description = get_post_meta($post->ID, '_cmama_description', true);
        if (!empty($custom_description)) {
            return $custom_description;
        }

        // Use excerpt if available
        if (!empty($post->post_excerpt)) {
            return $post->post_excerpt;
        }

        // Generate description based on calculators
        $calculators = $this->get_calculators_from_content($post->post_content);
        if (!empty($calculators)) {
            $calculator = new Calculator_Mama_Calculator();
            $calculator_names = array();
            
            foreach ($calculators as $slug) {
                $calc_info = $calculator->get_calculator($slug);
                if ($calc_info) {
                    $calculator_names[] = $calc_info['name'];
                }
            }

            if (!empty($calculator_names)) {
                return sprintf(
                    __('Use our %s to %s. Free online calculator with instant results.', 'calculator-mama'),
                    implode(', ', $calculator_names),
                    $this->get_calculator_benefits($calculators)
                );
            }
        }

        // Fallback to post content
        return wp_trim_words(strip_tags($post->post_content), 25);
    }

    /**
     * Get calculators from content
     *
     * @param string $content
     * @return array
     */
    private function get_calculators_from_content($content) {
        $calculators = array();

        // Extract from shortcodes
        preg_match_all('/\[cmama_calculator[^\]]*slug="([^"]+)"[^\]]*\]/', $content, $shortcode_matches);
        if (!empty($shortcode_matches[1])) {
            $calculators = array_merge($calculators, $shortcode_matches[1]);
        }

        // Extract from Gutenberg blocks
        preg_match_all('/"calculator":"([^"]+)"/', $content, $block_matches);
        if (!empty($block_matches[1])) {
            $calculators = array_merge($calculators, $block_matches[1]);
        }

        return array_unique($calculators);
    }

    /**
     * Get calculator benefits for description
     *
     * @param array $calculators
     * @return string
     */
    private function get_calculator_benefits($calculators) {
        $calculator = new Calculator_Mama_Calculator();
        $benefits = array();

        foreach ($calculators as $slug) {
            $calc_info = $calculator->get_calculator($slug);
            if ($calc_info) {
                switch ($calc_info['category']) {
                    case 'financial':
                        $benefits[] = __('make informed financial decisions', 'calculator-mama');
                        break;
                    case 'health':
                        $benefits[] = __('track your health and fitness goals', 'calculator-mama');
                        break;
                    case 'math':
                        $benefits[] = __('solve complex mathematical problems', 'calculator-mama');
                        break;
                    default:
                        $benefits[] = __('get accurate calculations', 'calculator-mama');
                }
            }
        }

        return implode(', ', array_unique($benefits));
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $database = new Calculator_Mama_Database();
        $seo_settings = $database->get_seo_settings();
        ?>
        <div class="wrap cmama-seo">
            <h1><?php _e('SEO Settings', 'calculator-mama'); ?></h1>
            
            <form id="cmama-seo-form">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="enable_schema"><?php _e('Schema Markup', 'calculator-mama'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" id="enable_schema" name="enable_schema" value="1" <?php checked($seo_settings['enable_schema']); ?>>
                                    <?php _e('Enable automatic schema markup generation', 'calculator-mama'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Automatically generates JSON-LD structured data for better search engine understanding.', 'calculator-mama'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="enable_meta_description"><?php _e('Meta Descriptions', 'calculator-mama'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" id="enable_meta_description" name="enable_meta_description" value="1" <?php checked($seo_settings['enable_meta_description']); ?>>
                                    <?php _e('Enable automatic meta description generation', 'calculator-mama'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Automatically generates meta descriptions for pages with calculators.', 'calculator-mama'); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="cmama-seo-preview">
                    <h3><?php _e('Schema Preview', 'calculator-mama'); ?></h3>
                    <p><?php _e('This is an example of the schema markup that will be generated:', 'calculator-mama'); ?></p>
                    <pre><code>{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Mortgage Calculator",
  "description": "Calculate monthly mortgage payments, total interest, and amortization schedule.",
  "applicationCategory": "CalculatorApplication",
  "operatingSystem": "Web Browser"
}</code></pre>
                </div>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Settings', 'calculator-mama'); ?></button>
                </p>
            </form>
        </div>
        <?php
    }
}