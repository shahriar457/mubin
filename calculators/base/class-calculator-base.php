<?php
/**
 * Base calculator class
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Base calculator class
 */
abstract class Calculator_Mama_Base {

    /**
     * Calculator info
     *
     * @var array
     */
    protected $info = array();

    /**
     * Calculator attributes
     *
     * @var array
     */
    protected $atts = array();

    /**
     * Get calculator information
     *
     * @return array
     */
    abstract public function get_info();

    /**
     * Render calculator
     *
     * @param array $atts
     * @return string
     */
    abstract public function render($atts = array());

    /**
     * Calculate result
     *
     * @param array $data
     * @return array
     */
    abstract public function calculate($data);

    /**
     * Validate input data
     *
     * @param array $data
     * @return array|WP_Error
     */
    protected function validate_input($data) {
        $errors = new WP_Error();

        if (empty($data)) {
            $errors->add('empty_data', __('No data provided.', 'calculator-mama'));
            return $errors;
        }

        return $data;
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @return array
     */
    protected function sanitize_input($data) {
        $sanitized = array();

        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $sanitized[$key] = floatval($value);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }

    /**
     * Format number
     *
     * @param float $number
     * @param int $decimals
     * @return string
     */
    protected function format_number($number, $decimals = 2) {
        return number_format($number, $decimals);
    }

    /**
     * Format currency
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    protected function format_currency($amount, $currency = 'USD') {
        return '$' . $this->format_number($amount);
    }

    /**
     * Get calculator wrapper classes
     *
     * @return string
     */
    protected function get_wrapper_classes() {
        $classes = array(
            'cmama-calculator',
            'cmama-calculator-' . $this->info['slug']
        );

        if (isset($this->atts['class'])) {
            $classes[] = $this->atts['class'];
        }

        return implode(' ', $classes);
    }

    /**
     * Get calculator wrapper attributes
     *
     * @return string
     */
    protected function get_wrapper_attributes() {
        $attributes = array(
            'class' => $this->get_wrapper_classes(),
            'data-calculator' => $this->info['slug']
        );

        if (isset($this->atts['id'])) {
            $attributes['id'] = $this->atts['id'];
        }

        $attr_string = '';
        foreach ($attributes as $key => $value) {
            $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
        }

        return $attr_string;
    }

    /**
     * Render input field
     *
     * @param string $name
     * @param string $label
     * @param string $type
     * @param array $attributes
     * @return string
     */
    protected function render_input($name, $label, $type = 'number', $attributes = array()) {
        $default_attributes = array(
            'type' => $type,
            'name' => $name,
            'id' => 'cmama-' . $name,
            'class' => 'cmama-input'
        );

        $attributes = wp_parse_args($attributes, $default_attributes);

        $attr_string = '';
        foreach ($attributes as $key => $value) {
            $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
        }

        $html = '<div class="cmama-field">';
        $html .= '<label for="cmama-' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        $html .= '<input' . $attr_string . '>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render button
     *
     * @param string $text
     * @param string $type
     * @param array $attributes
     * @return string
     */
    protected function render_button($text, $type = 'button', $attributes = array()) {
        $default_attributes = array(
            'type' => $type,
            'class' => 'cmama-button'
        );

        $attributes = wp_parse_args($attributes, $default_attributes);

        $attr_string = '';
        foreach ($attributes as $key => $value) {
            $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
        }

        return '<button' . $attr_string . '>' . esc_html($text) . '</button>';
    }

    /**
     * Render result
     *
     * @param string $label
     * @param string $value
     * @param string $class
     * @return string
     */
    protected function render_result($label, $value, $class = '') {
        $html = '<div class="cmama-result' . ($class ? ' ' . esc_attr($class) : '') . '">';
        $html .= '<span class="cmama-result-label">' . esc_html($label) . '</span>';
        $html .= '<span class="cmama-result-value">' . esc_html($value) . '</span>';
        $html .= '</div>';

        return $html;
    }
}