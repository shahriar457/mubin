<?php
/**
 * Body Fat Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Body Fat Calculator Class.
 */
class CMAMA_Body_Fat_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'body-fat-calculator';
		$this->name        = __( 'Body Fat Calculator', 'calculator-mama' );
		$this->description = __( 'Estimate body fat percentage using US Navy method.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-body-fat-calculator" data-calculator="body-fat">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label class="cmama-label"><?php esc_html_e( 'Gender', 'calculator-mama' ); ?></label>
					<div class="cmama-radio-group">
						<label>
							<input type="radio" name="bf-gender" value="male" checked>
							<?php esc_html_e( 'Male', 'calculator-mama' ); ?>
						</label>
						<label>
							<input type="radio" name="bf-gender" value="female">
							<?php esc_html_e( 'Female', 'calculator-mama' ); ?>
						</label>
					</div>
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bf-height" class="cmama-label">
						<?php esc_html_e( 'Height (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bf-height" class="cmama-input" value="170" min="0" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bf-neck" class="cmama-label">
						<?php esc_html_e( 'Neck Circumference (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bf-neck" class="cmama-input" value="37" min="0" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-bf-waist" class="cmama-label">
						<?php esc_html_e( 'Waist Circumference (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bf-waist" class="cmama-input" value="82" min="0" step="0.1">
				</div>

				<div class="cmama-field-group" id="bf-hip-group" style="display: none;">
					<label for="cmama-bf-hip" class="cmama-label">
						<?php esc_html_e( 'Hip Circumference (cm)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-bf-hip" class="cmama-input" value="95" min="0" step="0.1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Body Fat Percentage', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-bf-result">0%</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Category:', 'calculator-mama' ); ?></span>
							<span id="cmama-bf-category">-</span>
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
			$('input[name=\"bf-gender\"]').on('change', function() {
				if ($(this).val() === 'female') {
					$('#bf-hip-group').show();
				} else {
					$('#bf-hip-group').hide();
				}
			});

			$('.cmama-body-fat-calculator .cmama-calculate-btn').on('click', function() {
				const gender = $('input[name=\"bf-gender\"]:checked').val();
				const height = parseFloat($('#cmama-bf-height').val()) || 0;
				const neck = parseFloat($('#cmama-bf-neck').val()) || 0;
				const waist = parseFloat($('#cmama-bf-waist').val()) || 0;

				if (height <= 0 || neck <= 0 || waist <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				let bodyFat;
				if (gender === 'male') {
					bodyFat = 495 / (1.0324 - 0.19077 * Math.log10(waist - neck) + 0.15456 * Math.log10(height)) - 450;
				} else {
					const hip = parseFloat($('#cmama-bf-hip').val()) || 0;
					if (hip <= 0) {
						alert('" . esc_js( __( 'Please enter hip circumference for females.', 'calculator-mama' ) ) . "');
						return;
					}
					bodyFat = 495 / (1.29579 - 0.35004 * Math.log10(waist + hip - neck) + 0.22100 * Math.log10(height)) - 450;
				}

				let category;
				if (gender === 'male') {
					if (bodyFat < 6) category = '" . esc_js( __( 'Essential Fat', 'calculator-mama' ) ) . "';
					else if (bodyFat < 14) category = '" . esc_js( __( 'Athletes', 'calculator-mama' ) ) . "';
					else if (bodyFat < 18) category = '" . esc_js( __( 'Fitness', 'calculator-mama' ) ) . "';
					else if (bodyFat < 25) category = '" . esc_js( __( 'Average', 'calculator-mama' ) ) . "';
					else category = '" . esc_js( __( 'Obese', 'calculator-mama' ) ) . "';
				} else {
					if (bodyFat < 14) category = '" . esc_js( __( 'Essential Fat', 'calculator-mama' ) ) . "';
					else if (bodyFat < 21) category = '" . esc_js( __( 'Athletes', 'calculator-mama' ) ) . "';
					else if (bodyFat < 25) category = '" . esc_js( __( 'Fitness', 'calculator-mama' ) ) . "';
					else if (bodyFat < 32) category = '" . esc_js( __( 'Average', 'calculator-mama' ) ) . "';
					else category = '" . esc_js( __( 'Obese', 'calculator-mama' ) ) . "';
				}

				$('#cmama-bf-result').text(bodyFat.toFixed(1) + '%');
				$('#cmama-bf-category').text(category);

				$('.cmama-body-fat-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Body_Fat_Calculator();
