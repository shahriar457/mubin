<?php
/**
 * Mortgage Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mortgage Calculator Class.
 */
class CMAMA_Mortgage_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'mortgage-calculator';
		$this->name        = __( 'Mortgage Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate monthly mortgage payments including principal, interest, taxes, and insurance.', 'calculator-mama' );
		$this->category    = 'financial';
		$this->schema_type = 'WebApplication';

		// Register this calculator.
		CMAMA_Calculator_Registry::instance()->register( $this );
	}

	/**
	 * Render calculator HTML.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Calculator HTML.
	 */
	public function render( $atts = array() ) {
		ob_start();
		?>
		<div class="cmama-calculator cmama-mortgage-calculator" data-calculator="mortgage">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-mortgage-amount" class="cmama-label">
						<?php esc_html_e( 'Home Price ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-mortgage-amount" class="cmama-input" value="300000" min="0" step="1000">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-mortgage-down-payment" class="cmama-label">
						<?php esc_html_e( 'Down Payment ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-mortgage-down-payment" class="cmama-input" value="60000" min="0" step="1000">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-mortgage-rate" class="cmama-label">
						<?php esc_html_e( 'Interest Rate (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-mortgage-rate" class="cmama-input" value="3.5" min="0" max="100" step="0.01">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-mortgage-term" class="cmama-label">
						<?php esc_html_e( 'Loan Term (years)', 'calculator-mama' ); ?>
					</label>
					<select id="cmama-mortgage-term" class="cmama-input">
						<option value="15">15 years</option>
						<option value="20">20 years</option>
						<option value="30" selected>30 years</option>
					</select>
				</div>

				<div class="cmama-field-group">
					<label for="cmama-mortgage-tax" class="cmama-label">
						<?php esc_html_e( 'Annual Property Tax ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-mortgage-tax" class="cmama-input" value="3000" min="0" step="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-mortgage-insurance" class="cmama-label">
						<?php esc_html_e( 'Annual Home Insurance ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-mortgage-insurance" class="cmama-input" value="1200" min="0" step="100">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate Payment', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Monthly Payment', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-mortgage-result">$0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Principal & Interest:', 'calculator-mama' ); ?></span>
							<span id="cmama-mortgage-pi">$0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Property Tax:', 'calculator-mama' ); ?></span>
							<span id="cmama-mortgage-tax-result">$0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Home Insurance:', 'calculator-mama' ); ?></span>
							<span id="cmama-mortgage-insurance-result">$0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Loan Amount:', 'calculator-mama' ); ?></span>
							<span id="cmama-mortgage-loan-amount">$0</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get calculator-specific JavaScript.
	 *
	 * @return string JavaScript code.
	 */
	public function get_javascript() {
		return "
		jQuery(document).ready(function($) {
			$('.cmama-mortgage-calculator .cmama-calculate-btn').on('click', function() {
				const homePrice = parseFloat($('#cmama-mortgage-amount').val()) || 0;
				const downPayment = parseFloat($('#cmama-mortgage-down-payment').val()) || 0;
				const rate = parseFloat($('#cmama-mortgage-rate').val()) || 0;
				const term = parseInt($('#cmama-mortgage-term').val()) || 30;
				const propertyTax = parseFloat($('#cmama-mortgage-tax').val()) || 0;
				const insurance = parseFloat($('#cmama-mortgage-insurance').val()) || 0;

				if (homePrice <= 0 || downPayment < 0 || rate < 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				const loanAmount = homePrice - downPayment;
				const monthlyRate = rate / 100 / 12;
				const numPayments = term * 12;

				let principalInterest = 0;
				if (monthlyRate > 0) {
					principalInterest = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) / (Math.pow(1 + monthlyRate, numPayments) - 1);
				} else {
					principalInterest = loanAmount / numPayments;
				}

				const monthlyTax = propertyTax / 12;
				const monthlyInsurance = insurance / 12;
				const totalMonthly = principalInterest + monthlyTax + monthlyInsurance;

				$('#cmama-mortgage-result').text('$' + totalMonthly.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-mortgage-pi').text('$' + principalInterest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-mortgage-tax-result').text('$' + monthlyTax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-mortgage-insurance-result').text('$' + monthlyInsurance.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-mortgage-loan-amount').text('$' + loanAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

				$('.cmama-mortgage-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

// Initialize the calculator.
new CMAMA_Mortgage_Calculator();
