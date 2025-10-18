<?php
/**
 * Ideal Weight Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ideal Weight Calculator Class.
 */
class CMAMA_Ideal_Weight_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'ideal-weight-calculator';
		$this->name        = __( 'Ideal Weight Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate your ideal body weight based on height and gender.', 'calculator-mama' );
		$this->category    = 'health';
		$this->schema_type = 'MedicalRiskCalculator';

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
		<div class="cmama-calculator cmama-ideal-weight-calculator" data-calculator="ideal-weight">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label class="cmama-label"><?php esc_html_e( 'Gender', 'calculator-mama' ); ?></label>
					<div class="cmama-radio-group">
						<label>
							<input type="radio" name="iw-gender" value="male" checked>
							<?php esc_html_e( 'Male', 'calculator-mama' ); ?>
						</label>
						<label>
							<input type="radio" name="iw-gender" value="female">
							<?php esc_html_e( 'Female', 'calculator-mama' ); ?>
						</label>
					</div>
				</div>

				<div class="cmama-field-group">
					<label for="cmama-iw-height" class="cmama-label">
						<?php esc_html_e( 'Height (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-iw-height" class="cmama-input" value="170" min="0" step="0.1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Ideal Weight Range', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-iw-result">0 kg</div>
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
			$('.cmama-ideal-weight-calculator .cmama-calculate-btn').on('click', function() {
				const gender = $('input[name=\"iw-gender\"]:checked').val();
				const height = parseFloat($('#cmama-iw-height').val()) || 0;

				if (height <= 0) {
					alert('" . esc_js( __( 'Please enter a valid height.', 'calculator-mama' ) ) . "');
					return;
				}

				// Using Robinson formula
				let idealWeight;
				if (gender === 'male') {
					idealWeight = 52 + 1.9 * ((height / 2.54) - 60);
				} else {
					idealWeight = 49 + 1.7 * ((height / 2.54) - 60);
				}

				const minWeight = idealWeight - 5;
				const maxWeight = idealWeight + 5;

				$('#cmama-iw-result').text(minWeight.toFixed(1) + ' - ' + maxWeight.toFixed(1) + ' kg');

				$('.cmama-ideal-weight-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Ideal_Weight_Calculator();
