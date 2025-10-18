<?php
/**
 * Date Difference Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Date Difference Calculator Class.
 */
class CMAMA_Date_Difference_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'date-difference-calculator';
		$this->name        = __( 'Date Difference Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate the difference between two dates in various units.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-date-difference-calculator" data-calculator="date-difference">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-date-start" class="cmama-label">
						<?php esc_html_e( 'Start Date', 'calculator-mama' ); ?>
					</label>
					<input type="date" id="cmama-date-start" class="cmama-input">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-date-end" class="cmama-label">
						<?php esc_html_e( 'End Date', 'calculator-mama' ); ?>
					</label>
					<input type="date" id="cmama-date-end" class="cmama-input">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate Difference', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Days:', 'calculator-mama' ); ?></span>
							<span id="cmama-date-days">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Weeks:', 'calculator-mama' ); ?></span>
							<span id="cmama-date-weeks">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Months:', 'calculator-mama' ); ?></span>
							<span id="cmama-date-months">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Working Days (Mon-Fri):', 'calculator-mama' ); ?></span>
							<span id="cmama-date-workdays">0</span>
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
			$('.cmama-date-difference-calculator .cmama-calculate-btn').on('click', function() {
				const startDate = $('#cmama-date-start').val();
				const endDate = $('#cmama-date-end').val();

				if (!startDate || !endDate) {
					alert('" . esc_js( __( 'Please select both dates.', 'calculator-mama' ) ) . "');
					return;
				}

				const start = new Date(startDate);
				const end = new Date(endDate);

				if (start > end) {
					alert('" . esc_js( __( 'Start date must be before end date.', 'calculator-mama' ) ) . "');
					return;
				}

				const diffTime = Math.abs(end - start);
				const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
				const diffWeeks = Math.floor(diffDays / 7);
				const diffMonths = Math.floor(diffDays / 30.44);

				let workdays = 0;
				const current = new Date(start);
				while (current <= end) {
					const day = current.getDay();
					if (day !== 0 && day !== 6) {
						workdays++;
					}
					current.setDate(current.getDate() + 1);
				}

				$('#cmama-date-days').text(diffDays.toLocaleString());
				$('#cmama-date-weeks').text(diffWeeks.toLocaleString());
				$('#cmama-date-months').text(diffMonths.toLocaleString());
				$('#cmama-date-workdays').text(workdays.toLocaleString());

				$('.cmama-date-difference-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Date_Difference_Calculator();
