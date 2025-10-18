<?php
/**
 * Calorie Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calorie Calculator Class.
 */
class CMAMA_Calorie_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'calorie-calculator';
		$this->name        = __( 'Calorie Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate daily calorie needs for weight loss, maintenance, or gain.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-calorie-calculator" data-calculator="calorie">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label class="cmama-label"><?php esc_html_e( 'Gender', 'calculator-mama' ); ?></label>
					<div class="cmama-radio-group">
						<label>
							<input type="radio" name="cal-gender" value="male" checked>
							<?php esc_html_e( 'Male', 'calculator-mama' ); ?>
						</label>
						<label>
							<input type="radio" name="cal-gender" value="female">
							<?php esc_html_e( 'Female', 'calculator-mama' ); ?>
						</label>
					</div>
				</div>

				<div class="cmama-field-group">
					<label for="cmama-cal-age" class="cmama-label">
						<?php esc_html_e( 'Age (years)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-cal-age" class="cmama-input" value="30" min="15" max="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-cal-weight" class="cmama-label">
						<?php esc_html_e( 'Weight (kg)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-cal-weight" class="cmama-input" value="70" min="0" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-cal-height" class="cmama-label">
						<?php esc_html_e( 'Height (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-cal-height" class="cmama-input" value="170" min="0" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-cal-activity" class="cmama-label">
						<?php esc_html_e( 'Activity Level', 'calculator-mama' ); ?>
					</label>
					<select id="cmama-cal-activity" class="cmama-input">
						<option value="1.2"><?php esc_html_e( 'Sedentary', 'calculator-mama' ); ?></option>
						<option value="1.375"><?php esc_html_e( 'Lightly active', 'calculator-mama' ); ?></option>
						<option value="1.55" selected><?php esc_html_e( 'Moderately active', 'calculator-mama' ); ?></option>
						<option value="1.725"><?php esc_html_e( 'Very active', 'calculator-mama' ); ?></option>
						<option value="1.9"><?php esc_html_e( 'Extra active', 'calculator-mama' ); ?></option>
					</select>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Maintenance Calories', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-cal-result">0</div>
						<div class="cmama-result-sublabel"><?php esc_html_e( 'calories/day', 'calculator-mama' ); ?></div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Mild Weight Loss (0.5 lbs/week):', 'calculator-mama' ); ?></span>
							<span id="cmama-cal-mild-loss">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Weight Loss (1 lb/week):', 'calculator-mama' ); ?></span>
							<span id="cmama-cal-loss">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Weight Gain (1 lb/week):', 'calculator-mama' ); ?></span>
							<span id="cmama-cal-gain">0</span>
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
			$('.cmama-calorie-calculator .cmama-calculate-btn').on('click', function() {
				const gender = $('input[name=\"cal-gender\"]:checked').val();
				const age = parseFloat($('#cmama-cal-age').val()) || 0;
				const weight = parseFloat($('#cmama-cal-weight').val()) || 0;
				const height = parseFloat($('#cmama-cal-height').val()) || 0;
				const activity = parseFloat($('#cmama-cal-activity').val()) || 1.2;

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

				const maintenance = bmr * activity;
				const mildLoss = maintenance - 250;
				const loss = maintenance - 500;
				const gain = maintenance + 500;

				$('#cmama-cal-result').text(Math.round(maintenance));
				$('#cmama-cal-mild-loss').text(Math.round(mildLoss) + ' cal/day');
				$('#cmama-cal-loss').text(Math.round(loss) + ' cal/day');
				$('#cmama-cal-gain').text(Math.round(gain) + ' cal/day');

				$('.cmama-calorie-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Calorie_Calculator();
