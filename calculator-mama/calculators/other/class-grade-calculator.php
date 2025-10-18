<?php
/**
 * Grade Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Grade Calculator Class.
 */
class CMAMA_Grade_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'grade-calculator';
		$this->name        = __( 'Grade Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate final grades and what you need to score on remaining assignments.', 'calculator-mama' );
		$this->category    = 'other';
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
		<div class="cmama-calculator cmama-grade-calculator" data-calculator="grade">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-grade-current" class="cmama-label">
						<?php esc_html_e( 'Current Grade (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-grade-current" class="cmama-input" value="85" min="0" max="100" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-grade-weight" class="cmama-label">
						<?php esc_html_e( 'Weight of Current Work (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-grade-weight" class="cmama-input" value="70" min="0" max="100" step="1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-grade-desired" class="cmama-label">
						<?php esc_html_e( 'Desired Final Grade (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-grade-desired" class="cmama-input" value="90" min="0" max="100" step="0.1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Grade Needed on Remaining Work', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-grade-result">0%</div>
					</div>
					<div class="cmama-result-note" id="cmama-grade-note"></div>
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
			$('.cmama-grade-calculator .cmama-calculate-btn').on('click', function() {
				const currentGrade = parseFloat($('#cmama-grade-current').val()) || 0;
				const currentWeight = parseFloat($('#cmama-grade-weight').val()) || 0;
				const desiredGrade = parseFloat($('#cmama-grade-desired').val()) || 0;

				if (currentWeight <= 0 || currentWeight >= 100) {
					alert('" . esc_js( __( 'Weight of current work must be between 0 and 100.', 'calculator-mama' ) ) . "');
					return;
				}

				const remainingWeight = 100 - currentWeight;
				const neededGrade = (desiredGrade - (currentGrade * currentWeight / 100)) / (remainingWeight / 100);

				let note;
				if (neededGrade < 0) {
					note = '" . esc_js( __( 'Great news! You can score 0% and still achieve your desired grade!', 'calculator-mama' ) ) . "';
				} else if (neededGrade > 100) {
					note = '" . esc_js( __( 'Unfortunately, it is not possible to achieve your desired grade with the remaining work.', 'calculator-mama' ) ) . "';
				} else if (neededGrade >= 90) {
					note = '" . esc_js( __( 'You need to score very high on the remaining work.', 'calculator-mama' ) ) . "';
				} else {
					note = '" . esc_js( __( 'This is achievable with good effort!', 'calculator-mama' ) ) . "';
				}

				$('#cmama-grade-result').text(Math.max(0, Math.min(100, neededGrade)).toFixed(2) + '%');
				$('#cmama-grade-note').text(note);

				$('.cmama-grade-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Grade_Calculator();
