<?php
/**
 * BMI Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BMI Calculator Class.
 */
class CMAMA_BMI_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'bmi-calculator';
		$this->name        = __( 'BMI Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate your Body Mass Index (BMI) and weight category.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-bmi-calculator" data-calculator="bmi">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label class="cmama-label"><?php esc_html_e( 'Unit System', 'calculator-mama' ); ?></label>
					<div class="cmama-radio-group">
						<label>
							<input type="radio" name="bmi-unit" value="imperial" checked>
							<?php esc_html_e( 'Imperial (lbs, ft, in)', 'calculator-mama' ); ?>
						</label>
						<label>
							<input type="radio" name="bmi-unit" value="metric">
							<?php esc_html_e( 'Metric (kg, cm)', 'calculator-mama' ); ?>
						</label>
					</div>
				</div>

				<div id="bmi-imperial">
					<div class="cmama-field-group">
						<label for="cmama-bmi-feet" class="cmama-label">
							<?php esc_html_e( 'Height (feet)', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-bmi-feet" class="cmama-input" value="5" min="0" step="1">
					</div>

					<div class="cmama-field-group">
						<label for="cmama-bmi-inches" class="cmama-label">
							<?php esc_html_e( 'Height (inches)', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-bmi-inches" class="cmama-input" value="10" min="0" max="11" step="1">
					</div>

					<div class="cmama-field-group">
						<label for="cmama-bmi-pounds" class="cmama-label">
							<?php esc_html_e( 'Weight (pounds)', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-bmi-pounds" class="cmama-input" value="180" min="0" step="0.1">
					</div>
				</div>

				<div id="bmi-metric" style="display: none;">
					<div class="cmama-field-group">
						<label for="cmama-bmi-cm" class="cmama-label">
							<?php esc_html_e( 'Height (cm)', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-bmi-cm" class="cmama-input" value="178" min="0" step="0.1">
					</div>

					<div class="cmama-field-group">
						<label for="cmama-bmi-kg" class="cmama-label">
							<?php esc_html_e( 'Weight (kg)', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-bmi-kg" class="cmama-input" value="82" min="0" step="0.1">
					</div>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate BMI', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Your BMI', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-bmi-result">0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Category:', 'calculator-mama' ); ?></span>
							<span id="cmama-bmi-category">-</span>
						</div>
						<div class="cmama-result-note" id="cmama-bmi-note"></div>
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
			$('input[name=\"bmi-unit\"]').on('change', function() {
				if ($(this).val() === 'imperial') {
					$('#bmi-imperial').show();
					$('#bmi-metric').hide();
				} else {
					$('#bmi-imperial').hide();
					$('#bmi-metric').show();
				}
			});

			$('.cmama-bmi-calculator .cmama-calculate-btn').on('click', function() {
				const unit = $('input[name=\"bmi-unit\"]:checked').val();
				let heightM, weightKg;

				if (unit === 'imperial') {
					const feet = parseFloat($('#cmama-bmi-feet').val()) || 0;
					const inches = parseFloat($('#cmama-bmi-inches').val()) || 0;
					const pounds = parseFloat($('#cmama-bmi-pounds').val()) || 0;

					if (feet <= 0 || pounds <= 0) {
						alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
						return;
					}

					const totalInches = (feet * 12) + inches;
					heightM = totalInches * 0.0254;
					weightKg = pounds * 0.453592;
				} else {
					const cm = parseFloat($('#cmama-bmi-cm').val()) || 0;
					const kg = parseFloat($('#cmama-bmi-kg').val()) || 0;

					if (cm <= 0 || kg <= 0) {
						alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
						return;
					}

					heightM = cm / 100;
					weightKg = kg;
				}

				const bmi = weightKg / (heightM * heightM);
				let category, note;

				if (bmi < 18.5) {
					category = '" . esc_js( __( 'Underweight', 'calculator-mama' ) ) . "';
					note = '" . esc_js( __( 'Consider consulting with a healthcare provider about healthy weight gain.', 'calculator-mama' ) ) . "';
				} else if (bmi < 25) {
					category = '" . esc_js( __( 'Normal weight', 'calculator-mama' ) ) . "';
					note = '" . esc_js( __( 'You have a healthy weight. Maintain it through balanced diet and exercise.', 'calculator-mama' ) ) . "';
				} else if (bmi < 30) {
					category = '" . esc_js( __( 'Overweight', 'calculator-mama' ) ) . "';
					note = '" . esc_js( __( 'Consider lifestyle changes to reach a healthier weight.', 'calculator-mama' ) ) . "';
				} else {
					category = '" . esc_js( __( 'Obese', 'calculator-mama' ) ) . "';
					note = '" . esc_js( __( 'Consult with a healthcare provider about weight management strategies.', 'calculator-mama' ) ) . "';
				}

				$('#cmama-bmi-result').text(bmi.toFixed(1));
				$('#cmama-bmi-category').text(category);
				$('#cmama-bmi-note').text(note);

				$('.cmama-bmi-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_BMI_Calculator();
