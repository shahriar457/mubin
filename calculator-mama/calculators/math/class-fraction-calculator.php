<?php
/**
 * Fraction Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fraction Calculator Class.
 */
class CMAMA_Fraction_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'fraction-calculator';
		$this->name        = __( 'Fraction Calculator', 'calculator-mama' );
		$this->description = __( 'Add, subtract, multiply, and divide fractions with step-by-step solutions.', 'calculator-mama' );
		$this->category    = 'math';
		$this->schema_type = 'MathSolver';

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
		<div class="cmama-calculator cmama-fraction-calculator" data-calculator="fraction">
			<div class="cmama-calculator-body">
				<div class="cmama-fraction-input">
					<div class="cmama-field-group">
						<label class="cmama-label"><?php esc_html_e( 'First Fraction', 'calculator-mama' ); ?></label>
						<div class="cmama-fraction-fields">
							<input type="number" id="cmama-frac-num1" class="cmama-input" value="1" step="1">
							<span class="cmama-fraction-slash">/</span>
							<input type="number" id="cmama-frac-den1" class="cmama-input" value="2" step="1">
						</div>
					</div>

					<div class="cmama-field-group">
						<label for="cmama-frac-op" class="cmama-label"><?php esc_html_e( 'Operation', 'calculator-mama' ); ?></label>
						<select id="cmama-frac-op" class="cmama-input">
							<option value="+">+</option>
							<option value="-">-</option>
							<option value="*">×</option>
							<option value="/">÷</option>
						</select>
					</div>

					<div class="cmama-field-group">
						<label class="cmama-label"><?php esc_html_e( 'Second Fraction', 'calculator-mama' ); ?></label>
						<div class="cmama-fraction-fields">
							<input type="number" id="cmama-frac-num2" class="cmama-input" value="1" step="1">
							<span class="cmama-fraction-slash">/</span>
							<input type="number" id="cmama-frac-den2" class="cmama-input" value="3" step="1">
						</div>
					</div>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Result', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-frac-result">0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Decimal:', 'calculator-mama' ); ?></span>
							<span id="cmama-frac-decimal">0</span>
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
			function gcd(a, b) {
				return b === 0 ? a : gcd(b, a % b);
			}

			$('.cmama-fraction-calculator .cmama-calculate-btn').on('click', function() {
				const num1 = parseInt($('#cmama-frac-num1').val()) || 0;
				const den1 = parseInt($('#cmama-frac-den1').val()) || 1;
				const num2 = parseInt($('#cmama-frac-num2').val()) || 0;
				const den2 = parseInt($('#cmama-frac-den2').val()) || 1;
				const op = $('#cmama-frac-op').val();

				if (den1 === 0 || den2 === 0) {
					alert('" . esc_js( __( 'Denominator cannot be zero.', 'calculator-mama' ) ) . "');
					return;
				}

				let resultNum, resultDen;

				if (op === '+') {
					resultNum = num1 * den2 + num2 * den1;
					resultDen = den1 * den2;
				} else if (op === '-') {
					resultNum = num1 * den2 - num2 * den1;
					resultDen = den1 * den2;
				} else if (op === '*') {
					resultNum = num1 * num2;
					resultDen = den1 * den2;
				} else if (op === '/') {
					if (num2 === 0) {
						alert('" . esc_js( __( 'Cannot divide by zero.', 'calculator-mama' ) ) . "');
						return;
					}
					resultNum = num1 * den2;
					resultDen = den1 * num2;
				}

				// Simplify fraction
				const divisor = gcd(Math.abs(resultNum), Math.abs(resultDen));
				resultNum /= divisor;
				resultDen /= divisor;

				// Handle negative denominator
				if (resultDen < 0) {
					resultNum *= -1;
					resultDen *= -1;
				}

				const decimal = resultNum / resultDen;

				$('#cmama-frac-result').text(resultNum + ' / ' + resultDen);
				$('#cmama-frac-decimal').text(decimal.toFixed(4));

				$('.cmama-fraction-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Fraction_Calculator();
