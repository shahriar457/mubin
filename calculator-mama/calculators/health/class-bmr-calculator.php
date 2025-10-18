<?php
/**
 * BMR Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BMR Calculator Class.
 */
class CMAMA_BMR_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'bmr-calculator';
		$this->name        = __( 'BMR Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate your Basal Metabolic Rate (BMR) - the calories you burn at rest.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-bmr-calculator" data-calculator="bmr">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label class="cmama-label"><?php esc_html_e( 'Gender', 'calculator-mama' ); ?></label>
					<div class="cmama-radio-group">
						<label>
							<input type="radio" name="bmr-gender" value="male" checked>
							<?php esc_html_e( 'Male', 'calculator-mama' ); ?>
						</label>
						<label>
							<input type="radio" name="bmr-gender" value="female">
							<?php esc_html_e( 'Female', 'calculator-mama' ); ?>
						</label>
					</div>
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bmr-age" class="cmama-label">
						<?php esc_html_e( 'Age (years)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bmr-age" class="cmama-input" value="30" min="15" max="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bmr-weight" class="cmama-label">
						<?php esc_html_e( 'Weight (kg)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bmr-weight" class="cmama-input" value="70" min="0" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bmr-height" class="cmama-label">
						<?php esc_html_e( 'Height (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bmr-height" class="cmama-input" value="170" min="0" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bmr-activity" class="cmama-label">
						<?php esc_html_e( 'Activity Level', 'calculator-mama' ); ?>
					</label>
					<select id="cmama-bmr-activity" class="cmama-input">
						<option value="1.2"><?php esc_html_e( 'Sedentary (little or no exercise)', 'calculator-mama' ); ?></option>
						<option value="1.375"><?php esc_html_e( 'Lightly active (1-3 days/week)', 'calculator-mama' ); ?></option>
						<option value="1.55" selected><?php esc_html_e( 'Moderately active (3-5 days/week)', 'calculator-mama' ); ?></option>
						<option value="1.725"><?php esc_html_e( 'Very active (6-7 days/week)', 'calculator-mama' ); ?></option>
						<option value="1.9"><?php esc_html_e( 'Extra active (very hard exercise)', 'calculator-mama' ); ?></option>
					</select>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate BMR', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Your BMR', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-bmr-result">0</div>
						<div class="cmama-result-sublabel"><?php esc_html_e( 'calories/day', 'calculator-mama' ); ?></div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Daily Calorie Needs (TDEE):', 'calculator-mama' ); ?></span>
							<span id="cmama-bmr-tdee">0</span>
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
			$('.cmama-bmr-calculator .cmama-calculate-btn').on('click', function() {
				const gender = $('input[name=\"bmr-gender\"]:checked').val();
				const age = parseFloat($('#cmama-bmr-age').val()) || 0;
				const weight = parseFloat($('#cmama-bmr-weight').val()) || 0;
				const height = parseFloat($('#cmama-bmr-height').val()) || 0;
				const activity = parseFloat($('#cmama-bmr-activity').val()) || 1.2;

				if (age <= 0 || weight <= 0 || height <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				let bmr;
				if (gender === 'male') {
					bmr = 10 * weight + 6.25 * height - 5 * age + 5;
				} else {
					bmr = 10 * weight + 6.25 * height - 5 * age - 161;
				}

				const tdee = bmr * activity;

				$('#cmama-bmr-result').text(Math.round(bmr));
				$('#cmama-bmr-tdee').text(Math.round(tdee) + ' calories/day');

				$('.cmama-bmr-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_BMR_Calculator();
