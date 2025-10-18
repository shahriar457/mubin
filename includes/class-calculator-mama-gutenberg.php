<?php
/**
 * Gutenberg block functionality
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gutenberg block class
 */
class Calculator_Mama_Gutenberg {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('wp_ajax_cmama_get_calculators', array($this, 'get_calculators_ajax'));
        add_action('wp_ajax_cmama_preview_calculator', array($this, 'preview_calculator_ajax'));
    }

    /**
     * Register Gutenberg block
     */
    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('calculator-mama/calculator', array(
            'editor_script' => 'cmama-gutenberg-block',
            'editor_style' => 'cmama-gutenberg-block',
            'style' => 'cmama-gutenberg-block',
            'render_callback' => array($this, 'render_block'),
            'attributes' => array(
                'calculator' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'title' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'description' => array(
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
                'faq' => array(
                    'type' => 'array',
                    'default' => array()
                ),
                'className' => array(
                    'type' => 'string',
                    'default' => ''
                )
            )
        ));
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'cmama-gutenberg-block',
            CMAMA_ASSETS_URL . 'js/gutenberg-block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            CMAMA_VERSION,
            true
        );

        wp_enqueue_style(
            'cmama-gutenberg-block',
            CMAMA_ASSETS_URL . 'css/gutenberg-block.css',
            array('wp-edit-blocks'),
            CMAMA_VERSION
        );

        wp_localize_script('cmama-gutenberg-block', 'cmama_gutenberg', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmama_gutenberg_nonce'),
            'calculators' => $this->get_calculators_for_editor()
        ));
    }

    /**
     * Render block
     *
     * @param array $attributes
     * @return string
     */
    public function render_block($attributes) {
        if (empty($attributes['calculator'])) {
            return '<p>' . __('Please select a calculator.', 'calculator-mama') . '</p>';
        }

        $calculator = new Calculator_Mama_Calculator();
        $calculator_info = $calculator->get_calculator($attributes['calculator']);

        if (!$calculator_info) {
            return '<p>' . __('Calculator not found.', 'calculator-mama') . '</p>';
        }

        ob_start();
        ?>
        <div class="cmama-calculator-block <?php echo esc_attr($attributes['className']); ?>">
            <?php if (!empty($attributes['title'])): ?>
                <h2 class="cmama-calculator-block-title"><?php echo esc_html($attributes['title']); ?></h2>
            <?php endif; ?>

            <?php if (!empty($attributes['description'])): ?>
                <p class="cmama-calculator-block-description"><?php echo esc_html($attributes['description']); ?></p>
            <?php endif; ?>

            <?php if (!empty($attributes['introduction'])): ?>
                <div class="cmama-calculator-block-introduction">
                    <?php echo wp_kses_post(wpautop($attributes['introduction'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($attributes['instructions'])): ?>
                <div class="cmama-calculator-block-instructions">
                    <h4><?php _e('How to Use', 'calculator-mama'); ?></h4>
                    <?php echo wp_kses_post(wpautop($attributes['instructions'])); ?>
                </div>
            <?php endif; ?>

            <?php echo $calculator->render_calculator($attributes['calculator']); ?>

            <?php if (!empty($attributes['faq'])): ?>
                <div class="cmama-calculator-block-faq">
                    <h4><?php _e('Frequently Asked Questions', 'calculator-mama'); ?></h4>
                    <div class="cmama-faq-list">
                        <?php foreach ($attributes['faq'] as $faq): ?>
                            <div class="cmama-faq-item">
                                <h5 class="cmama-faq-question"><?php echo esc_html($faq['question']); ?></h5>
                                <div class="cmama-faq-answer"><?php echo wp_kses_post(wpautop($faq['answer'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get calculators for editor
     *
     * @return array
     */
    private function get_calculators_for_editor() {
        $calculator = new Calculator_Mama_Calculator();
        $calculators = $calculator->get_active_calculators();
        
        $editor_calculators = array();
        foreach ($calculators as $slug => $calc) {
            $editor_calculators[] = array(
                'value' => $slug,
                'label' => $calc['name'],
                'description' => $calc['description'],
                'category' => $calc['category']
            );
        }

        return $editor_calculators;
    }

    /**
     * Get calculators via AJAX
     */
    public function get_calculators_ajax() {
        check_ajax_referer('cmama_gutenberg_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'calculator-mama'));
        }

        wp_send_json_success($this->get_calculators_for_editor());
    }

    /**
     * Preview calculator via AJAX
     */
    public function preview_calculator_ajax() {
        check_ajax_referer('cmama_gutenberg_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'calculator-mama'));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator']);
        $calculator = new Calculator_Mama_Calculator();
        
        $preview = $calculator->render_calculator($calculator_slug);
        
        wp_send_json_success(array('preview' => $preview));
    }
}