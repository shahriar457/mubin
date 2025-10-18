<?php
/**
 * Square Root Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Square Root Calculator Class.
 */
class CMAMA_Square_Root_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'square-root-calculator';
		$this->name        = __( 'Square Root Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate square roots and cube roots with precision.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-square-root-calculator" data-calculator="square-root">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-sqrt-number" class="cmama-label">
						<?php esc_html_e( 'Number', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-sqrt-number" class="cmama-input" value="16" step="any">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-sqrt-root" class="cmama-label">
						<?php esc_html_e( 'Root Type', 'calculator-mama' ); ?>
					</label>
					<select id="cmama-sqrt-root" class="cmama-input">
						<option value="2"><?php esc_html_e( 'Square Root (√)', 'calculator-mama' ); ?></option>
						<option value="3"><?php esc_html_e( 'Cube Root (∛)', 'calculator-mama' ); ?></option>
						<option value="4"><?php esc_html_e( '4th Root', 'calculator-mama' ); ?></option>
						<option value="5"><?php esc_html_e( '5th Root', 'calculator-mama' ); ?></option>
					</select>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Result', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-sqrt-result">0</div>
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
			$('.cmama-square-root-calculator .cmama-calculate-btn').on('click', function() {
				const number = parseFloat($('#cmama-sqrt-number').val()) || 0;
				const root = parseInt($('#cmama-sqrt-root').val()) || 2;

				if (number < 0 && root % 2 === 0) {
					alert('" . esc_js( __( 'Cannot calculate even root of negative number.', 'calculator-mama' ) ) . "');
					return;
				}

				const result = Math.pow(Math.abs(number), 1 / root) * (number < 0 ? -1 : 1);

				$('#cmama-sqrt-result').text(result.toFixed(6));
				$('.cmama-square-root-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Square_Root_Calculator();
