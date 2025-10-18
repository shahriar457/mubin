<?php
/**
 * Compound Interest Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compound Interest Calculator Class.
 */
class CMAMA_Compound_Interest_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'compound-interest-calculator';
		$this->name        = __( 'Compound Interest Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate compound interest and future investment value with regular contributions.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-compound-interest-calculator" data-calculator="compound-interest">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-ci-principal" class="cmama-label">
						<?php esc_html_e( 'Initial Investment ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ci-principal" class="cmama-input" value="5000" min="0" step="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ci-contribution" class="cmama-label">
						<?php esc_html_e( 'Monthly Contribution ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ci-contribution" class="cmama-input" value="200" min="0" step="10">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ci-rate" class="cmama-label">
						<?php esc_html_e( 'Annual Interest Rate (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ci-rate" class="cmama-input" value="7" min="0" max="100" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ci-years" class="cmama-label">
						<?php esc_html_e( 'Investment Period (years)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ci-years" class="cmama-input" value="10" min="1" step="1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Future Value', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-ci-result">$0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Contributions:', 'calculator-mama' ); ?></span>
							<span id="cmama-ci-contributions">$0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Interest Earned:', 'calculator-mama' ); ?></span>
							<span id="cmama-ci-interest">$0</span>
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
			$('.cmama-compound-interest-calculator .cmama-calculate-btn').on('click', function() {
				const principal = parseFloat($('#cmama-ci-principal').val()) || 0;
				const monthlyContribution = parseFloat($('#cmama-ci-contribution').val()) || 0;
				const annualRate = parseFloat($('#cmama-ci-rate').val()) || 0;
				const years = parseInt($('#cmama-ci-years').val()) || 0;

				if (principal < 0 || years <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				const monthlyRate = annualRate / 100 / 12;
				const months = years * 12;
				const totalContributions = principal + (monthlyContribution * months);

				let futureValue = principal * Math.pow(1 + monthlyRate, months);
				
				if (monthlyContribution > 0 && monthlyRate > 0) {
					futureValue += monthlyContribution * ((Math.pow(1 + monthlyRate, months) - 1) / monthlyRate);
				} else if (monthlyContribution > 0) {
					futureValue += monthlyContribution * months;
				}

				const totalInterest = futureValue - totalContributions;

				$('#cmama-ci-result').text('$' + futureValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-ci-contributions').text('$' + totalContributions.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-ci-interest').text('$' + totalInterest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

				$('.cmama-compound-interest-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Compound_Interest_Calculator();
