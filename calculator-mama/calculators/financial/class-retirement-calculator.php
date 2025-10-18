<?php
/**
 * Retirement Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retirement Calculator Class.
 */
class CMAMA_Retirement_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'retirement-calculator';
		$this->name        = __( 'Retirement Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate how much you need to save for retirement and monthly savings required.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-retirement-calculator" data-calculator="retirement">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-ret-age" class="cmama-label">
						<?php esc_html_e( 'Current Age', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ret-age" class="cmama-input" value="30" min="18" max="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ret-retire-age" class="cmama-label">
						<?php esc_html_e( 'Retirement Age', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ret-retire-age" class="cmama-input" value="65" min="18" max="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ret-income" class="cmama-label">
						<?php esc_html_e( 'Desired Annual Retirement Income ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ret-income" class="cmama-input" value="60000" min="0" step="1000">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ret-years" class="cmama-label">
						<?php esc_html_e( 'Years in Retirement', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ret-years" class="cmama-input" value="25" min="1" step="1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ret-savings" class="cmama-label">
						<?php esc_html_e( 'Current Savings ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ret-savings" class="cmama-input" value="50000" min="0" step="1000">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-ret-return" class="cmama-label">
						<?php esc_html_e( 'Expected Return Rate (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-ret-return" class="cmama-input" value="7" min="0" max="100" step="0.1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Total Needed at Retirement', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-ret-result">$0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Monthly Savings Required:', 'calculator-mama' ); ?></span>
							<span id="cmama-ret-monthly">$0</span>
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
			$('.cmama-retirement-calculator .cmama-calculate-btn').on('click', function() {
				const currentAge = parseInt($('#cmama-ret-age').val()) || 0;
				const retireAge = parseInt($('#cmama-ret-retire-age').val()) || 0;
				const annualIncome = parseFloat($('#cmama-ret-income').val()) || 0;
				const retireYears = parseInt($('#cmama-ret-years').val()) || 0;
				const currentSavings = parseFloat($('#cmama-ret-savings').val()) || 0;
				const returnRate = parseFloat($('#cmama-ret-return').val()) || 0;

				if (retireAge <= currentAge || annualIncome <= 0 || retireYears <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				const yearsToRetire = retireAge - currentAge;
				const totalNeeded = annualIncome * retireYears;
				const futureCurrentSavings = currentSavings * Math.pow(1 + returnRate / 100, yearsToRetire);
				const stillNeeded = totalNeeded - futureCurrentSavings;

				const monthlyRate = returnRate / 100 / 12;
				const months = yearsToRetire * 12;
				
				let monthlySavings = 0;
				if (stillNeeded > 0) {
					if (monthlyRate > 0) {
						monthlySavings = stillNeeded * monthlyRate / (Math.pow(1 + monthlyRate, months) - 1);
					} else {
						monthlySavings = stillNeeded / months;
					}
				}

				$('#cmama-ret-result').text('$' + totalNeeded.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-ret-monthly').text('$' + monthlySavings.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

				$('.cmama-retirement-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Retirement_Calculator();
