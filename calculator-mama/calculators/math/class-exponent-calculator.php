<?php
/**
 * Exponent Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exponent Calculator Class.
 */
class CMAMA_Exponent_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'exponent-calculator';
		$this->name        = __( 'Exponent Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate powers and exponents with any base and exponent.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-exponent-calculator" data-calculator="exponent">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-exp-base" class="cmama-label">
						<?php esc_html_e( 'Base', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-exp-base" class="cmama-input" value="2" step="any">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-exp-exponent" class="cmama-label">
						<?php esc_html_e( 'Exponent', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-exp-exponent" class="cmama-input" value="8" step="any">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label" id="cmama-exp-formula">2^8 =</div>
						<div class="cmama-result-value" id="cmama-exp-result">0</div>
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
			$('.cmama-exponent-calculator .cmama-calculate-btn').on('click', function() {
				const base = parseFloat($('#cmama-exp-base').val()) || 0;
				const exponent = parseFloat($('#cmama-exp-exponent').val()) || 0;

				const result = Math.pow(base, exponent);

				$('#cmama-exp-formula').text(base + '^' + exponent + ' =');
				$('#cmama-exp-result').text(result.toFixed(6));
				$('.cmama-exponent-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Exponent_Calculator();
