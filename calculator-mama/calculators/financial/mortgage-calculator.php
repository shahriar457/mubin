<?php
/**
 * Mortgage Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Mortgage Calculator Class
 *
 * @since 1.0.0
 */
class CMAMA_Mortgage_Calculator extends CMAMA_Calculator_Base {

    /**
     * Initialize calculator.
     *
     * @since 1.0.0
     */
    protected function init() {
        $this->slug = 'mortgage-calculator';
        $this->name = __('Mortgage Calculator', CMAMA_TEXT_DOMAIN);
        $this->description = __('Calculate monthly mortgage payments, total interest, and amortization schedule.', CMAMA_TEXT_DOMAIN);
        $this->category = 'financial';
        $this->tags = array('mortgage', 'loan', 'payment', 'interest', 'amortization', 'home', 'real estate');
        $this->schema_type = 'MortgageCalculator';

        $this->fields = array(
            'loan_amount' => array(
                'type' => 'number',
                'label' => __('Loan Amount', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter loan amount', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'min' => '1000',
                'max' => '10000000',
                'step' => '1000',
                'default' => '300000',
                'description' => __('Total amount of the mortgage loan', CMAMA_TEXT_DOMAIN)
            ),
            'interest_rate' => array(
                'type' => 'number',
                'label' => __('Annual Interest Rate (%)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter interest rate', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'min' => '0.1',
                'max' => '30',
                'step' => '0.01',
                'default' => '4.5',
                'description' => __('Annual percentage rate (APR)', CMAMA_TEXT_DOMAIN)
            ),
            'loan_term' => array(
                'type' => 'number',
                'label' => __('Loan Term (Years)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter loan term', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'min' => '1',
                'max' => '50',
                'step' => '1',
                'default' => '30',
                'description' => __('Length of the loan in years', CMAMA_TEXT_DOMAIN)
            ),
            'down_payment' => array(
                'type' => 'number',
                'label' => __('Down Payment', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter down payment', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '0',
                'max' => '5000000',
                'step' => '1000',
                'default' => '60000',
                'description' => __('Initial payment made when purchasing the home', CMAMA_TEXT_DOMAIN)
            ),
            'property_tax' => array(
                'type' => 'number',
                'label' => __('Annual Property Tax', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter property tax', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '0',
                'max' => '100000',
                'step' => '100',
                'default' => '3600',
                'description' => __('Yearly property tax amount', CMAMA_TEXT_DOMAIN)
            ),
            'home_insurance' => array(
                'type' => 'number',
                'label' => __('Annual Home Insurance', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter insurance cost', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '0',
                'max' => '50000',
                'step' => '100',
                'default' => '1200',
                'description' => __('Yearly home insurance premium', CMAMA_TEXT_DOMAIN)
            ),
            'pmi' => array(
                'type' => 'number',
                'label' => __('PMI (Monthly)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter PMI amount', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '0',
                'max' => '2000',
                'step' => '10',
                'default' => '200',
                'description' => __('Private Mortgage Insurance (if down payment < 20%)', CMAMA_TEXT_DOMAIN)
            )
        );
    }

    /**
     * Calculate mortgage payment and details.
     *
     * @since 1.0.0
     * @param array $inputs Input values.
     * @return array Calculation results.
     */
    public function calculate($inputs) {
        $loan_amount = floatval($inputs['loan_amount']);
        $down_payment = floatval($inputs['down_payment']);
        $principal = $loan_amount - $down_payment;
        $annual_rate = floatval($inputs['interest_rate']) / 100;
        $monthly_rate = $annual_rate / 12;
        $num_payments = floatval($inputs['loan_term']) * 12;
        $property_tax = floatval($inputs['property_tax']);
        $home_insurance = floatval($inputs['home_insurance']);
        $pmi = floatval($inputs['pmi']);

        // Calculate monthly principal and interest payment
        if ($monthly_rate > 0) {
            $monthly_payment = $principal * ($monthly_rate * pow(1 + $monthly_rate, $num_payments)) / 
                              (pow(1 + $monthly_rate, $num_payments) - 1);
        } else {
            $monthly_payment = $principal / $num_payments;
        }

        // Calculate total payment and interest
        $total_payments = $monthly_payment * $num_payments;
        $total_interest = $total_payments - $principal;

        // Calculate monthly escrow (taxes and insurance)
        $monthly_property_tax = $property_tax / 12;
        $monthly_insurance = $home_insurance / 12;
        $monthly_escrow = $monthly_property_tax + $monthly_insurance;

        // Calculate total monthly payment
        $total_monthly_payment = $monthly_payment + $monthly_escrow + $pmi;

        // Calculate down payment percentage
        $down_payment_percent = $loan_amount > 0 ? ($down_payment / $loan_amount) * 100 : 0;

        // Calculate loan-to-value ratio
        $ltv_ratio = $loan_amount > 0 ? (($loan_amount - $down_payment) / $loan_amount) * 100 : 0;

        return array(
            'monthly_payment' => $monthly_payment,
            'total_monthly_payment' => $total_monthly_payment,
            'principal_amount' => $principal,
            'total_interest' => $total_interest,
            'total_payments' => $total_payments,
            'monthly_property_tax' => $monthly_property_tax,
            'monthly_insurance' => $monthly_insurance,
            'monthly_pmi' => $pmi,
            'down_payment_percent' => $down_payment_percent,
            'ltv_ratio' => $ltv_ratio
        );
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

        $output = '<div class="cmama-result-data cmama-mortgage-results">';
        
        // Main payment information
        $output .= '<div class="cmama-result-section cmama-payment-summary">';
        $output .= '<h4>' . __('Monthly Payment Breakdown', CMAMA_TEXT_DOMAIN) . '</h4>';
        
        $output .= '<div class="cmama-result-item cmama-primary-result">';
        $output .= '<span class="cmama-result-label">' . __('Principal & Interest', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_number($result['monthly_payment'], 2, true) . '</span>';
        $output .= '</div>';

        if ($result['monthly_property_tax'] > 0) {
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Property Tax', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . cmama_format_number($result['monthly_property_tax'], 2, true) . '</span>';
            $output .= '</div>';
        }

        if ($result['monthly_insurance'] > 0) {
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Home Insurance', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . cmama_format_number($result['monthly_insurance'], 2, true) . '</span>';
            $output .= '</div>';
        }

        if ($result['monthly_pmi'] > 0) {
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('PMI', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . cmama_format_number($result['monthly_pmi'], 2, true) . '</span>';
            $output .= '</div>';
        }

        $output .= '<div class="cmama-result-item cmama-total-result">';
        $output .= '<span class="cmama-result-label">' . __('Total Monthly Payment', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_number($result['total_monthly_payment'], 2, true) . '</span>';
        $output .= '</div>';

        $output .= '</div>';

        // Loan summary
        $output .= '<div class="cmama-result-section cmama-loan-summary">';
        $output .= '<h4>' . __('Loan Summary', CMAMA_TEXT_DOMAIN) . '</h4>';
        
        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Principal Amount', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_number($result['principal_amount'], 2, true) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Total Interest', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_number($result['total_interest'], 2, true) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Total Payments', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_number($result['total_payments'], 2, true) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Down Payment %', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_percentage($result['down_payment_percent'], 1) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Loan-to-Value Ratio', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . cmama_format_percentage($result['ltv_ratio'], 1) . '</span>';
        $output .= '</div>';

        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

// Register the calculator
add_action('cmama_register_calculators', function($registry) {
    $registry->register_calculator('mortgage-calculator', new CMAMA_Mortgage_Calculator());
});