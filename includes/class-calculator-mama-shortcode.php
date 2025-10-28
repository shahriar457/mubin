<?php
/**
 * Shortcode functionality
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode class
 */
class Calculator_Mama_Shortcode {

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('cmama_calculator', array($this, 'render_shortcode'));
        add_action('init', array($this, 'add_shortcode_button'));
    }

    /**
     * Render shortcode
     *
     * @param array $atts
     * @return string
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'slug' => '',
            'title' => '',
            'description' => '',
            'class' => '',
            'id' => ''
        ), $atts, 'cmama_calculator');

        if (empty($atts['slug'])) {
            return '<p>' . __('Please specify a calculator slug.', 'calculator-mama') . '</p>';
        }

        $calculator = new Calculator_Mama_Calculator();
        $calculator_info = $calculator->get_calculator($atts['slug']);

        if (!$calculator_info) {
            return '<p>' . __('Calculator not found.', 'calculator-mama') . '</p>';
        }

        // Check if calculator is active
        $database = new Calculator_Mama_Database();
        $calculator_settings = $database->get_calculator_settings($atts['slug']);
        if (!isset($calculator_settings['active']) || !$calculator_settings['active']) {
            return '<p>' . __('This calculator is not active.', 'calculator-mama') . '</p>';
        }

        ob_start();
        ?>
        <div class="cmama-calculator-shortcode <?php echo esc_attr($atts['class']); ?>" <?php echo !empty($atts['id']) ? 'id="' . esc_attr($atts['id']) . '"' : ''; ?>>
            <?php if (!empty($atts['title'])): ?>
                <h3 class="cmama-calculator-shortcode-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>

            <?php if (!empty($atts['description'])): ?>
                <p class="cmama-calculator-shortcode-description"><?php echo esc_html($atts['description']); ?></p>
            <?php endif; ?>

            <?php echo $calculator->render_calculator($atts['slug']); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Add shortcode button to editor
     */
    public function add_shortcode_button() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        add_filter('mce_external_plugins', array($this, 'add_shortcode_button_script'));
        add_filter('mce_buttons', array($this, 'register_shortcode_button'));
    }

    /**
     * Add shortcode button script
     *
     * @param array $plugins
     * @return array
     */
    public function add_shortcode_button_script($plugins) {
        $plugins['cmama_shortcode'] = CMAMA_ASSETS_URL . 'js/shortcode-button.js';
        return $plugins;
    }

    /**
     * Register shortcode button
     *
     * @param array $buttons
     * @return array
     */
    public function register_shortcode_button($buttons) {
        array_push($buttons, 'cmama_shortcode');
        return $buttons;
    }
}