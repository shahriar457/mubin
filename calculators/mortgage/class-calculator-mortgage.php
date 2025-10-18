<?php
/**
 * Mortgage Calculator
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once CMAMA_CALCULATORS_DIR . 'base/class-calculator-base.php';

/**
 * Mortgage Calculator class
 */
class Calculator_Mama_Mortgage extends Calculator_Mama_Base {

    /**
     * Get calculator information
     *
     * @return array
     */
    public function get_info() {
        return array(
            'slug' => 'mortgage',
            'name' => __('Mortgage Calculator', 'calculator-mama'),
            'description' => __('Calculate monthly mortgage payments, total interest, and amortization schedule.', 'calculator-mama'),
            'category' => 'financial',
            'icon' => 'dashicons-calculator',
            'version' => '1.0',
            'css_file' => CMAMA_CALCULATORS_DIR . 'mortgage/style.css',
            'js_file' => CMAMA_CALCULATORS_DIR . 'mortgage/script.js'
        );
    }

    /**
     * Render calculator
     *
     * @param array $atts
     * @return string
     */
    public function render($atts = array()) {
        $this->atts = $atts;
        $this->info = $this->get_info();

        ob_start();
        ?>
        <div<?php echo $this->get_wrapper_attributes(); ?>>
            <div class="cmama-calculator-header">
                <h3 class="cmama-calculator-title"><?php echo esc_html($this->info['name']); ?></h3>
                <p class="cmama-calculator-description"><?php echo esc_html($this->info['description']); ?></p>
            </div>

            <form class="cmama-calculator-form" id="cmama-mortgage-form">
                <div class="cmama-inputs">
                    <?php echo $this->render_input('loan_amount', __('Loan Amount', 'calculator-mama'), 'number', array('step' => '0.01', 'min' => '0', 'required' => true)); ?>
                    <?php echo $this->render_input('interest_rate', __('Annual Interest Rate (%)', 'calculator-mama'), 'number', array('step' => '0.01', 'min' => '0', 'max' => '100', 'required' => true)); ?>
                    <?php echo $this->render_input('loan_term', __('Loan Term (years)', 'calculator-mama'), 'number', array('step' => '1', 'min' => '1', 'max' => '50', 'required' => true)); ?>
                    <?php echo $this->render_input('down_payment', __('Down Payment', 'calculator-mama'), 'number', array('step' => '0.01', 'min' => '0')); ?>
                </div>

                <div class="cmama-actions">
                    <?php echo $this->render_button(__('Calculate', 'calculator-mama'), 'submit'); ?>
                    <button type="button" class="cmama-button cmama-button-secondary" id="cmama-reset"><?php _e('Reset', 'calculator-mama'); ?></button>
                </div>

                <div class="cmama-results" id="cmama-mortgage-results" style="display: none;">
                    <h4><?php _e('Results', 'calculator-mama'); ?></h4>
                    <div class="cmama-results-grid">
                        <?php echo $this->render_result(__('Monthly Payment', 'calculator-mama'), '', 'cmama-monthly-payment'); ?>
                        <?php echo $this->render_result(__('Total Interest', 'calculator-mama'), '', 'cmama-total-interest'); ?>
                        <?php echo $this->render_result(__('Total Payment', 'calculator-mama'), '', 'cmama-total-payment'); ?>
                        <?php echo $this->render_result(__('Loan Amount', 'calculator-mama'), '', 'cmama-loan-amount'); ?>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Calculate mortgage
     *
     * @param array $data
     * @return array
     */
    public function calculate($data) {
        $data = $this->sanitize_input($data);
        $validated = $this->validate_input($data);

        if (is_wp_error($validated)) {
            return array('error' => $validated->get_error_message());
        }

        $loan_amount = floatval($data['loan_amount']);
        $interest_rate = floatval($data['interest_rate']);
        $loan_term = intval($data['loan_term']);
        $down_payment = isset($data['down_payment']) ? floatval($data['down_payment']) : 0;

        // Calculate loan amount after down payment
        $principal = $loan_amount - $down_payment;

        if ($principal <= 0) {
            return array('error' => __('Loan amount must be greater than down payment.', 'calculator-mama'));
        }

        // Convert annual rate to monthly rate
        $monthly_rate = $interest_rate / 100 / 12;

        // Calculate number of payments
        $num_payments = $loan_term * 12;

        // Calculate monthly payment using the mortgage formula
        if ($monthly_rate > 0) {
            $monthly_payment = $principal * ($monthly_rate * pow(1 + $monthly_rate, $num_payments)) / (pow(1 + $monthly_rate, $num_payments) - 1);
        } else {
            $monthly_payment = $principal / $num_payments;
        }

        $total_payment = $monthly_payment * $num_payments;
        $total_interest = $total_payment - $principal;

        return array(
            'monthly_payment' => $this->format_currency($monthly_payment),
            'total_interest' => $this->format_currency($total_interest),
            'total_payment' => $this->format_currency($total_payment),
            'loan_amount' => $this->format_currency($principal),
            'raw_monthly_payment' => $monthly_payment,
            'raw_total_interest' => $total_interest,
            'raw_total_payment' => $total_payment,
            'raw_loan_amount' => $principal
        );
    }
}