<?php
/**
 * Pregnancy Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pregnancy Calculator Class.
 */
class CMAMA_Pregnancy_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'pregnancy-calculator';
		$this->name        = __( 'Pregnancy Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate due date and pregnancy milestones based on last menstrual period.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-pregnancy-calculator" data-calculator="pregnancy">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-preg-date" class="cmama-label">
						<?php esc_html_e( 'First Day of Last Period', 'calculator-mama' ); ?>
					</label>
					<input type="date" id="cmama-preg-date" class="cmama-input">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Estimated Due Date', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-preg-due-date">-</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Current Week:', 'calculator-mama' ); ?></span>
							<span id="cmama-preg-week">-</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Trimester:', 'calculator-mama' ); ?></span>
							<span id="cmama-preg-trimester">-</span>
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
			$('.cmama-pregnancy-calculator .cmama-calculate-btn').on('click', function() {
				const lmpDate = $('#cmama-preg-date').val();
				
				if (!lmpDate) {
					alert('" . esc_js( __( 'Please select a date.', 'calculator-mama' ) ) . "');
					return;
				}

				const lmp = new Date(lmpDate);
				const today = new Date();
				const dueDate = new Date(lmp.getTime() + (280 * 24 * 60 * 60 * 1000));
				
				const daysDiff = Math.floor((today - lmp) / (24 * 60 * 60 * 1000));
				const weeks = Math.floor(daysDiff / 7);
				const days = daysDiff % 7;
				
				let trimester;
				if (weeks < 13) trimester = '" . esc_js( __( 'First Trimester', 'calculator-mama' ) ) . "';
				else if (weeks < 27) trimester = '" . esc_js( __( 'Second Trimester', 'calculator-mama' ) ) . "';
				else trimester = '" . esc_js( __( 'Third Trimester', 'calculator-mama' ) ) . "';

				const options = { year: 'numeric', month: 'long', day: 'numeric' };
				const dueDateStr = dueDate.toLocaleDateString(undefined, options);

				$('#cmama-preg-due-date').text(dueDateStr);
				$('#cmama-preg-week').text(weeks + ' weeks, ' + days + ' days');
				$('#cmama-preg-trimester').text(trimester);

				$('.cmama-pregnancy-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Pregnancy_Calculator();
