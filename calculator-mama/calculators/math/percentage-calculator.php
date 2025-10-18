<?php
/**
 * Percentage Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Percentage Calculator Class
 *
 * @since 1.0.0
 */
class CMAMA_Percentage_Calculator extends CMAMA_Calculator_Base {

    /**
     * Initialize calculator.
     *
     * @since 1.0.0
     */
    protected function init() {
        $this->slug = 'percentage-calculator';
        $this->name = __('Percentage Calculator', CMAMA_TEXT_DOMAIN);
        $this->description = __('Calculate percentages, percentage increase/decrease, and find what percent one number is of another.', CMAMA_TEXT_DOMAIN);
        $this->category = 'math';
        $this->tags = array('percentage', 'percent', 'math', 'calculation', 'increase', 'decrease', 'proportion');
        $this->schema_type = 'MathSolver';

        $this->fields = array(
            'calculation_type' => array(
                'type' => 'select',
                'label' => __('Calculation Type', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'default' => 'percentage_of',
                'options' => array(
                    'percentage_of' => __('What is X% of Y?', CMAMA_TEXT_DOMAIN),
                    'what_percent' => __('What percent is X of Y?', CMAMA_TEXT_DOMAIN),
                    'percent_change' => __('Percentage increase/decrease from X to Y', CMAMA_TEXT_DOMAIN),
                    'find_total' => __('X is Y% of what number?', CMAMA_TEXT_DOMAIN),
                    'add_percentage' => __('Add X% to a number', CMAMA_TEXT_DOMAIN),
                    'subtract_percentage' => __('Subtract X% from a number', CMAMA_TEXT_DOMAIN)
                ),
                'description' => __('Choose the type of percentage calculation', CMAMA_TEXT_DOMAIN)
            ),
            'number1' => array(
                'type' => 'number',
                'label' => __('First Number', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter first number', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'step' => '0.01',
                'default' => '25',
                'description' => __('The first number in your calculation', CMAMA_TEXT_DOMAIN)
            ),
            'number2' => array(
                'type' => 'number',
                'label' => __('Second Number', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter second number', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'step' => '0.01',
                'default' => '200',
                'description' => __('The second number in your calculation', CMAMA_TEXT_DOMAIN)
            ),
            'decimal_places' => array(
                'type' => 'select',
                'label' => __('Decimal Places', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'default' => '2',
                'options' => array(
                    '0' => __('0 decimal places', CMAMA_TEXT_DOMAIN),
                    '1' => __('1 decimal place', CMAMA_TEXT_DOMAIN),
                    '2' => __('2 decimal places', CMAMA_TEXT_DOMAIN),
                    '3' => __('3 decimal places', CMAMA_TEXT_DOMAIN),
                    '4' => __('4 decimal places', CMAMA_TEXT_DOMAIN)
                ),
                'description' => __('Number of decimal places in the result', CMAMA_TEXT_DOMAIN)
            )
        );
    }

    /**
     * Calculate percentage based on selected type.
     *
     * @since 1.0.0
     * @param array $inputs Input values.
     * @return array Calculation results.
     */
    public function calculate($inputs) {
        $type = $inputs['calculation_type'];
        $num1 = floatval($inputs['number1']);
        $num2 = floatval($inputs['number2']);
        $decimal_places = intval($inputs['decimal_places']);

        $result = array(
            'calculation_type' => $type,
            'number1' => $num1,
            'number2' => $num2,
            'decimal_places' => $decimal_places
        );

        switch ($type) {
            case 'percentage_of':
                // What is X% of Y?
                $answer = ($num1 / 100) * $num2;
                $result['answer'] = $answer;
                $result['formula'] = "({$num1} ÷ 100) × {$num2} = " . number_format($answer, $decimal_places);
                $result['explanation'] = sprintf(
                    __('%s%% of %s is %s', CMAMA_TEXT_DOMAIN),
                    number_format($num1, $decimal_places),
                    number_format($num2, $decimal_places),
                    number_format($answer, $decimal_places)
                );
                break;

            case 'what_percent':
                // What percent is X of Y?
                if ($num2 != 0) {
                    $answer = ($num1 / $num2) * 100;
                    $result['answer'] = $answer;
                    $result['formula'] = "({$num1} ÷ {$num2}) × 100 = " . number_format($answer, $decimal_places) . "%";
                    $result['explanation'] = sprintf(
                        __('%s is %s%% of %s', CMAMA_TEXT_DOMAIN),
                        number_format($num1, $decimal_places),
                        number_format($answer, $decimal_places),
                        number_format($num2, $decimal_places)
                    );
                } else {
                    throw new Exception(__('Cannot divide by zero', CMAMA_TEXT_DOMAIN));
                }
                break;

            case 'percent_change':
                // Percentage increase/decrease from X to Y
                if ($num1 != 0) {
                    $change = $num2 - $num1;
                    $percent_change = ($change / abs($num1)) * 100;
                    $result['answer'] = $percent_change;
                    $result['change_amount'] = $change;
                    $result['is_increase'] = $change > 0;
                    $result['formula'] = "(({$num2} - {$num1}) ÷ {$num1}) × 100 = " . number_format($percent_change, $decimal_places) . "%";
                    
                    if ($change > 0) {
                        $result['explanation'] = sprintf(
                            __('Increase from %s to %s is %s%% (%s)', CMAMA_TEXT_DOMAIN),
                            number_format($num1, $decimal_places),
                            number_format($num2, $decimal_places),
                            number_format(abs($percent_change), $decimal_places),
                            number_format($change, $decimal_places)
                        );
                    } elseif ($change < 0) {
                        $result['explanation'] = sprintf(
                            __('Decrease from %s to %s is %s%% (%s)', CMAMA_TEXT_DOMAIN),
                            number_format($num1, $decimal_places),
                            number_format($num2, $decimal_places),
                            number_format(abs($percent_change), $decimal_places),
                            number_format($change, $decimal_places)
                        );
                    } else {
                        $result['explanation'] = __('No change - both numbers are equal', CMAMA_TEXT_DOMAIN);
                    }
                } else {
                    throw new Exception(__('Cannot calculate percentage change from zero', CMAMA_TEXT_DOMAIN));
                }
                break;

            case 'find_total':
                // X is Y% of what number?
                if ($num2 != 0) {
                    $answer = ($num1 * 100) / $num2;
                    $result['answer'] = $answer;
                    $result['formula'] = "({$num1} × 100) ÷ {$num2} = " . number_format($answer, $decimal_places);
                    $result['explanation'] = sprintf(
                        __('%s is %s%% of %s', CMAMA_TEXT_DOMAIN),
                        number_format($num1, $decimal_places),
                        number_format($num2, $decimal_places),
                        number_format($answer, $decimal_places)
                    );
                } else {
                    throw new Exception(__('Percentage cannot be zero', CMAMA_TEXT_DOMAIN));
                }
                break;

            case 'add_percentage':
                // Add X% to a number
                $percentage_amount = ($num1 / 100) * $num2;
                $answer = $num2 + $percentage_amount;
                $result['answer'] = $answer;
                $result['percentage_amount'] = $percentage_amount;
                $result['formula'] = "{$num2} + ({$num1}% × {$num2}) = {$num2} + " . number_format($percentage_amount, $decimal_places) . " = " . number_format($answer, $decimal_places);
                $result['explanation'] = sprintf(
                    __('Adding %s%% to %s gives %s', CMAMA_TEXT_DOMAIN),
                    number_format($num1, $decimal_places),
                    number_format($num2, $decimal_places),
                    number_format($answer, $decimal_places)
                );
                break;

            case 'subtract_percentage':
                // Subtract X% from a number
                $percentage_amount = ($num1 / 100) * $num2;
                $answer = $num2 - $percentage_amount;
                $result['answer'] = $answer;
                $result['percentage_amount'] = $percentage_amount;
                $result['formula'] = "{$num2} - ({$num1}% × {$num2}) = {$num2} - " . number_format($percentage_amount, $decimal_places) . " = " . number_format($answer, $decimal_places);
                $result['explanation'] = sprintf(
                    __('Subtracting %s%% from %s gives %s', CMAMA_TEXT_DOMAIN),
                    number_format($num1, $decimal_places),
                    number_format($num2, $decimal_places),
                    number_format($answer, $decimal_places)
                );
                break;

            default:
                throw new Exception(__('Invalid calculation type', CMAMA_TEXT_DOMAIN));
        }

        return $result;
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

        $output = '<div class="cmama-result-data cmama-percentage-results">';
        
        // Main result
        $output .= '<div class="cmama-result-section cmama-main-result">';
        $output .= '<div class="cmama-result-item cmama-primary-result">';
        $output .= '<span class="cmama-result-label">' . __('Result', CMAMA_TEXT_DOMAIN) . ':</span> ';
        
        if (in_array($result['calculation_type'], array('what_percent', 'percent_change'))) {
            $output .= '<span class="cmama-result-value">' . number_format($result['answer'], $result['decimal_places']) . '%</span>';
        } else {
            $output .= '<span class="cmama-result-value">' . number_format($result['answer'], $result['decimal_places']) . '</span>';
        }
        $output .= '</div>';
        $output .= '</div>';

        // Explanation
        $output .= '<div class="cmama-result-section cmama-explanation">';
        $output .= '<h4>' . __('Explanation', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<p class="cmama-explanation-text">' . esc_html($result['explanation']) . '</p>';
        $output .= '</div>';

        // Formula
        $output .= '<div class="cmama-result-section cmama-formula">';
        $output .= '<h4>' . __('Formula', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<div class="cmama-formula-display">';
        $output .= '<code>' . esc_html($result['formula']) . '</code>';
        $output .= '</div>';
        $output .= '</div>';

        // Additional details for specific calculation types
        if ($result['calculation_type'] === 'percent_change') {
            $output .= '<div class="cmama-result-section cmama-change-details">';
            $output .= '<h4>' . __('Change Details', CMAMA_TEXT_DOMAIN) . '</h4>';
            
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Original Value', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['number1'], $result['decimal_places']) . '</span>';
            $output .= '</div>';

            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('New Value', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['number2'], $result['decimal_places']) . '</span>';
            $output .= '</div>';

            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Absolute Change', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['change_amount'], $result['decimal_places']) . '</span>';
            $output .= '</div>';

            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Type of Change', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value cmama-change-type ' . ($result['is_increase'] ? 'increase' : 'decrease') . '">';
            $output .= $result['is_increase'] ? __('Increase', CMAMA_TEXT_DOMAIN) : __('Decrease', CMAMA_TEXT_DOMAIN);
            $output .= '</span>';
            $output .= '</div>';
            $output .= '</div>';
        }

        if (in_array($result['calculation_type'], array('add_percentage', 'subtract_percentage'))) {
            $output .= '<div class="cmama-result-section cmama-percentage-details">';
            $output .= '<h4>' . __('Calculation Details', CMAMA_TEXT_DOMAIN) . '</h4>';
            
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Original Number', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['number2'], $result['decimal_places']) . '</span>';
            $output .= '</div>';

            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Percentage Amount', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['percentage_amount'], $result['decimal_places']) . '</span>';
            $output .= '</div>';

            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Final Result', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['answer'], $result['decimal_places']) . '</span>';
            $output .= '</div>';
            $output .= '</div>';
        }

        // Quick reference for percentage calculations
        $output .= '<div class="cmama-result-section cmama-quick-reference">';
        $output .= '<h4>' . __('Quick Reference', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<div class="cmama-reference-grid">';
        
        $output .= '<div class="cmama-reference-item">';
        $output .= '<strong>' . __('To find X% of Y:', CMAMA_TEXT_DOMAIN) . '</strong><br>';
        $output .= '<code>(X ÷ 100) × Y</code>';
        $output .= '</div>';

        $output .= '<div class="cmama-reference-item">';
        $output .= '<strong>' . __('To find what % X is of Y:', CMAMA_TEXT_DOMAIN) . '</strong><br>';
        $output .= '<code>(X ÷ Y) × 100</code>';
        $output .= '</div>';

        $output .= '<div class="cmama-reference-item">';
        $output .= '<strong>' . __('Percentage change:', CMAMA_TEXT_DOMAIN) . '</strong><br>';
        $output .= '<code>((New - Old) ÷ Old) × 100</code>';
        $output .= '</div>';

        $output .= '<div class="cmama-reference-item">';
        $output .= '<strong>' . __('If X is Y% of Z:', CMAMA_TEXT_DOMAIN) . '</strong><br>';
        $output .= '<code>Z = (X × 100) ÷ Y</code>';
        $output .= '</div>';

        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }
}

// Register the calculator
add_action('cmama_register_calculators', function($registry) {
    $registry->register_calculator('percentage-calculator', new CMAMA_Percentage_Calculator());
});