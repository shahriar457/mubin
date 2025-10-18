<?php
/**
 * Base Calculator Class
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract base class for all calculators.
 *
 * @since 1.0.0
 */
abstract class CMAMA_Calculator_Base {

    /**
     * Calculator slug.
     *
     * @since 1.0.0
     * @var string
     */
    protected $slug = '';

    /**
     * Calculator name.
     *
     * @since 1.0.0
     * @var string
     */
    protected $name = '';

    /**
     * Calculator description.
     *
     * @since 1.0.0
     * @var string
     */
    protected $description = '';

    /**
     * Calculator category.
     *
     * @since 1.0.0
     * @var string
     */
    protected $category = '';

    /**
     * Calculator tags for search.
     *
     * @since 1.0.0
     * @var array
     */
    protected $tags = array();

    /**
     * Calculator fields configuration.
     *
     * @since 1.0.0
     * @var array
     */
    protected $fields = array();

    /**
     * Calculator Schema.org type.
     *
     * @since 1.0.0
     * @var string
     */
    protected $schema_type = 'WebApplication';

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Initialize calculator.
     *
     * @since 1.0.0
     */
    protected function init() {
        // Override in child classes
    }

    /**
     * Get calculator slug.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_slug() {
        return $this->slug;
    }

    /**
     * Get calculator name.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get calculator description.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Get calculator category.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_category() {
        return $this->category;
    }

    /**
     * Get calculator tags.
     *
     * @since 1.0.0
     * @return array
     */
    public function get_tags() {
        return $this->tags;
    }

    /**
     * Get calculator fields.
     *
     * @since 1.0.0
     * @return array
     */
    public function get_fields() {
        return $this->fields;
    }

    /**
     * Get calculator Schema.org type.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_schema_type() {
        return $this->schema_type;
    }

    /**
     * Render calculator HTML.
     *
     * @since 1.0.0
     * @param array $args Additional arguments.
     * @return string
     */
    public function render($args = array()) {
        $args = wp_parse_args($args, array(
            'title' => $this->name,
            'show_title' => true,
            'show_description' => true,
            'css_class' => ''
        ));

        // Start output buffering
        ob_start();

        // Get appearance settings
        $settings = get_option('cmama_settings', array());
        $appearance = isset($settings['appearance']) ? $settings['appearance'] : array();

        // Build CSS classes
        $css_classes = array(
            'cmama-calculator',
            'cmama-calculator-' . $this->slug,
            'cmama-category-' . $this->category
        );

        if (!empty($args['css_class'])) {
            $css_classes[] = $args['css_class'];
        }

        // Enqueue calculator-specific assets
        $this->enqueue_assets();

        ?>
        <div class="<?php echo esc_attr(implode(' ', $css_classes)); ?>" 
             data-calculator="<?php echo esc_attr($this->slug); ?>"
             style="<?php echo esc_attr($this->get_inline_styles($appearance)); ?>">
            
            <?php if ($args['show_title'] && !empty($args['title'])): ?>
                <h3 class="cmama-calculator-title"><?php echo esc_html($args['title']); ?></h3>
            <?php endif; ?>

            <?php if ($args['show_description'] && !empty($this->description)): ?>
                <p class="cmama-calculator-description"><?php echo esc_html($this->description); ?></p>
            <?php endif; ?>

            <form class="cmama-calculator-form" data-calculator-slug="<?php echo esc_attr($this->slug); ?>">
                <?php wp_nonce_field('cmama_calculate_' . $this->slug, 'cmama_nonce'); ?>
                
                <div class="cmama-calculator-fields">
                    <?php $this->render_fields(); ?>
                </div>

                <div class="cmama-calculator-actions">
                    <button type="submit" class="cmama-calculate-btn">
                        <?php _e('Calculate', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                    <button type="button" class="cmama-reset-btn">
                        <?php _e('Reset', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                </div>

                <div class="cmama-calculator-result" style="display: none;">
                    <h4 class="cmama-result-title"><?php _e('Result', CMAMA_TEXT_DOMAIN); ?></h4>
                    <div class="cmama-result-content"></div>
                </div>

                <div class="cmama-calculator-error" style="display: none;">
                    <p class="cmama-error-message"></p>
                </div>
            </form>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Render calculator fields.
     *
     * @since 1.0.0
     */
    protected function render_fields() {
        foreach ($this->fields as $field_id => $field) {
            $this->render_field($field_id, $field);
        }
    }

    /**
     * Render individual field.
     *
     * @since 1.0.0
     * @param string $field_id Field ID.
     * @param array $field Field configuration.
     */
    protected function render_field($field_id, $field) {
        $field = wp_parse_args($field, array(
            'type' => 'number',
            'label' => '',
            'placeholder' => '',
            'required' => false,
            'min' => '',
            'max' => '',
            'step' => '',
            'default' => '',
            'options' => array(),
            'description' => '',
            'css_class' => ''
        ));

        $field_classes = array('cmama-field', 'cmama-field-' . $field['type']);
        if (!empty($field['css_class'])) {
            $field_classes[] = $field['css_class'];
        }

        ?>
        <div class="<?php echo esc_attr(implode(' ', $field_classes)); ?>">
            <?php if (!empty($field['label'])): ?>
                <label for="<?php echo esc_attr($field_id); ?>" class="cmama-field-label">
                    <?php echo esc_html($field['label']); ?>
                    <?php if ($field['required']): ?>
                        <span class="cmama-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <?php $this->render_field_input($field_id, $field); ?>

            <?php if (!empty($field['description'])): ?>
                <p class="cmama-field-description"><?php echo esc_html($field['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render field input.
     *
     * @since 1.0.0
     * @param string $field_id Field ID.
     * @param array $field Field configuration.
     */
    protected function render_field_input($field_id, $field) {
        $input_attrs = array(
            'id' => $field_id,
            'name' => $field_id,
            'class' => 'cmama-field-input',
            'placeholder' => $field['placeholder'],
            'value' => $field['default']
        );

        if ($field['required']) {
            $input_attrs['required'] = 'required';
        }

        switch ($field['type']) {
            case 'number':
                $input_attrs['type'] = 'number';
                if (!empty($field['min'])) {
                    $input_attrs['min'] = $field['min'];
                }
                if (!empty($field['max'])) {
                    $input_attrs['max'] = $field['max'];
                }
                if (!empty($field['step'])) {
                    $input_attrs['step'] = $field['step'];
                }
                echo '<input ' . $this->build_attributes($input_attrs) . '>';
                break;

            case 'text':
                $input_attrs['type'] = 'text';
                echo '<input ' . $this->build_attributes($input_attrs) . '>';
                break;

            case 'select':
                unset($input_attrs['type'], $input_attrs['placeholder'], $input_attrs['value']);
                echo '<select ' . $this->build_attributes($input_attrs) . '>';
                foreach ($field['options'] as $value => $label) {
                    $selected = ($value == $field['default']) ? 'selected' : '';
                    echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';
                break;

            case 'radio':
                unset($input_attrs['type'], $input_attrs['placeholder'], $input_attrs['value']);
                foreach ($field['options'] as $value => $label) {
                    $radio_attrs = $input_attrs;
                    $radio_attrs['type'] = 'radio';
                    $radio_attrs['value'] = $value;
                    $radio_attrs['id'] = $field_id . '_' . $value;
                    if ($value == $field['default']) {
                        $radio_attrs['checked'] = 'checked';
                    }
                    echo '<label class="cmama-radio-label">';
                    echo '<input ' . $this->build_attributes($radio_attrs) . '>';
                    echo '<span>' . esc_html($label) . '</span>';
                    echo '</label>';
                }
                break;

            case 'checkbox':
                $input_attrs['type'] = 'checkbox';
                $input_attrs['value'] = '1';
                if ($field['default']) {
                    $input_attrs['checked'] = 'checked';
                }
                echo '<label class="cmama-checkbox-label">';
                echo '<input ' . $this->build_attributes($input_attrs) . '>';
                echo '<span>' . esc_html($field['label']) . '</span>';
                echo '</label>';
                break;

            default:
                $input_attrs['type'] = 'text';
                echo '<input ' . $this->build_attributes($input_attrs) . '>';
                break;
        }
    }

    /**
     * Build HTML attributes string.
     *
     * @since 1.0.0
     * @param array $attrs Attributes array.
     * @return string
     */
    protected function build_attributes($attrs) {
        $output = array();
        foreach ($attrs as $key => $value) {
            if ($value !== '' && $value !== null) {
                $output[] = esc_attr($key) . '="' . esc_attr($value) . '"';
            }
        }
        return implode(' ', $output);
    }

    /**
     * Get inline styles based on appearance settings.
     *
     * @since 1.0.0
     * @param array $appearance Appearance settings.
     * @return string
     */
    protected function get_inline_styles($appearance) {
        $styles = array();

        if (!empty($appearance['primary_color'])) {
            $styles[] = '--cmama-primary-color: ' . esc_attr($appearance['primary_color']);
        }
        if (!empty($appearance['button_color'])) {
            $styles[] = '--cmama-button-color: ' . esc_attr($appearance['button_color']);
        }
        if (!empty($appearance['input_color'])) {
            $styles[] = '--cmama-input-color: ' . esc_attr($appearance['input_color']);
        }
        if (!empty($appearance['result_color'])) {
            $styles[] = '--cmama-result-color: ' . esc_attr($appearance['result_color']);
        }
        if (!empty($appearance['border_radius'])) {
            $styles[] = '--cmama-border-radius: ' . esc_attr($appearance['border_radius']) . 'px';
        }
        if (!empty($appearance['padding'])) {
            $styles[] = '--cmama-padding: ' . esc_attr($appearance['padding']) . 'px';
        }

        return implode('; ', $styles);
    }

    /**
     * Enqueue calculator-specific assets.
     *
     * @since 1.0.0
     */
    protected function enqueue_assets() {
        // Check if calculator-specific CSS exists
        $css_file = CMAMA_PLUGIN_DIR . 'calculators/' . $this->category . '/' . $this->slug . '.css';
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'cmama-calculator-' . $this->slug,
                CMAMA_PLUGIN_URL . 'calculators/' . $this->category . '/' . $this->slug . '.css',
                array('cmama-frontend'),
                CMAMA_VERSION
            );
        }

        // Check if calculator-specific JS exists
        $js_file = CMAMA_PLUGIN_DIR . 'calculators/' . $this->category . '/' . $this->slug . '.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'cmama-calculator-' . $this->slug,
                CMAMA_PLUGIN_URL . 'calculators/' . $this->category . '/' . $this->slug . '.js',
                array('cmama-frontend'),
                CMAMA_VERSION,
                true
            );
        }
    }

    /**
     * Calculate result (abstract method).
     *
     * @since 1.0.0
     * @param array $inputs Input values.
     * @return array Result data.
     */
    abstract public function calculate($inputs);

    /**
     * Validate inputs.
     *
     * @since 1.0.0
     * @param array $inputs Input values.
     * @return array|WP_Error Validated inputs or error.
     */
    public function validate_inputs($inputs) {
        $validated = array();
        $errors = array();

        foreach ($this->fields as $field_id => $field) {
            $value = isset($inputs[$field_id]) ? $inputs[$field_id] : '';

            // Check required fields
            if ($field['required'] && empty($value)) {
                $errors[] = sprintf(
                    __('%s is required.', CMAMA_TEXT_DOMAIN),
                    $field['label']
                );
                continue;
            }

            // Validate by field type
            switch ($field['type']) {
                case 'number':
                    if (!empty($value) && !is_numeric($value)) {
                        $errors[] = sprintf(
                            __('%s must be a number.', CMAMA_TEXT_DOMAIN),
                            $field['label']
                        );
                        continue 2;
                    }
                    
                    $value = floatval($value);
                    
                    // Check min/max
                    if (!empty($field['min']) && $value < $field['min']) {
                        $errors[] = sprintf(
                            __('%s must be at least %s.', CMAMA_TEXT_DOMAIN),
                            $field['label'],
                            $field['min']
                        );
                        continue 2;
                    }
                    if (!empty($field['max']) && $value > $field['max']) {
                        $errors[] = sprintf(
                            __('%s must be no more than %s.', CMAMA_TEXT_DOMAIN),
                            $field['label'],
                            $field['max']
                        );
                        continue 2;
                    }
                    break;

                case 'select':
                case 'radio':
                    if (!empty($value) && !array_key_exists($value, $field['options'])) {
                        $errors[] = sprintf(
                            __('Invalid value for %s.', CMAMA_TEXT_DOMAIN),
                            $field['label']
                        );
                        continue 2;
                    }
                    break;

                case 'checkbox':
                    $value = !empty($value) ? true : false;
                    break;

                default:
                    $value = sanitize_text_field($value);
                    break;
            }

            $validated[$field_id] = $value;
        }

        if (!empty($errors)) {
            return new WP_Error('validation_failed', implode(' ', $errors));
        }

        return $validated;
    }

    /**
     * Format result for display.
     *
     * @since 1.0.0
     * @param array $result Raw result data.
     * @return string Formatted HTML.
     */
    public function format_result($result) {
        if (empty($result)) {
            return '<p>' . __('No result to display.', CMAMA_TEXT_DOMAIN) . '</p>';
        }

        $output = '<div class="cmama-result-data">';
        
        foreach ($result as $key => $value) {
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . esc_html($key) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . esc_html($value) . '</span>';
            $output .= '</div>';
        }
        
        $output .= '</div>';

        return $output;
    }

    /**
     * Get calculator data for JSON response.
     *
     * @since 1.0.0
     * @return array
     */
    public function to_array() {
        return array(
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'tags' => $this->tags,
            'fields' => $this->fields,
            'schema_type' => $this->schema_type
        );
    }
}