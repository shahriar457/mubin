<?php
/**
 * Loan Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loan Calculator Class.
 */
class CMAMA_Loan_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'loan-calculator';
		$this->name        = __( 'Loan Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate loan payments, total interest, and amortization schedule.', 'calculator-mama' );
		$this->category    = 'financial';
		$this->schema_type = 'WebApplication';

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
		<div class="cmama-calculator cmama-loan-calculator" data-calculator="loan">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-loan-amount" class="cmama-label">
						<?php esc_html_e( 'Loan Amount ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-loan-amount" class="cmama-input" value="10000" min="0" step="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-loan-rate" class="cmama-label">
						<?php esc_html_e( 'Annual Interest Rate (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-loan-rate" class="cmama-input" value="5" min="0" max="100" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-loan-term" class="cmama-label">
						<?php esc_html_e( 'Loan Term (months)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-loan-term" class="cmama-input" value="36" min="1" step="1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Monthly Payment', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-loan-payment">$0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Interest:', 'calculator-mama' ); ?></span>
							<span id="cmama-loan-interest">$0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Amount:', 'calculator-mama' ); ?></span>
							<span id="cmama-loan-total">$0</span>
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
			$('.cmama-loan-calculator .cmama-calculate-btn').on('click', function() {
				const principal = parseFloat($('#cmama-loan-amount').val()) || 0;
				const annualRate = parseFloat($('#cmama-loan-rate').val()) || 0;
				const months = parseInt($('#cmama-loan-term').val()) || 0;

				if (principal <= 0 || months <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				const monthlyRate = annualRate / 100 / 12;
				let monthlyPayment = 0;

				if (monthlyRate > 0) {
					monthlyPayment = principal * (monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
				} else {
					monthlyPayment = principal / months;
				}

				const totalAmount = monthlyPayment * months;
				const totalInterest = totalAmount - principal;

				$('#cmama-loan-payment').text('$' + monthlyPayment.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-loan-interest').text('$' + totalInterest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-loan-total').text('$' + totalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

				$('.cmama-loan-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Loan_Calculator();
