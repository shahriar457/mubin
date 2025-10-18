<?php
/**
 * Age Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Age Calculator Class.
 */
class CMAMA_Age_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'age-calculator';
		$this->name        = __( 'Age Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate age in years, months, weeks, days, hours, and minutes.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-age-calculator" data-calculator="age">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-age-birthdate" class="cmama-label">
						<?php esc_html_e( 'Date of Birth', 'calculator-mama' ); ?>
					</label>
					<input type="date" id="cmama-age-birthdate" class="cmama-input">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-age-currentdate" class="cmama-label">
						<?php esc_html_e( 'Calculate Age On', 'calculator-mama' ); ?>
					</label>
					<input type="date" id="cmama-age-currentdate" class="cmama-input">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate Age', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Your Age', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-age-result">0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Months:', 'calculator-mama' ); ?></span>
							<span id="cmama-age-months">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Weeks:', 'calculator-mama' ); ?></span>
							<span id="cmama-age-weeks">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Days:', 'calculator-mama' ); ?></span>
							<span id="cmama-age-days">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Hours:', 'calculator-mama' ); ?></span>
							<span id="cmama-age-hours">0</span>
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
			const today = new Date().toISOString().split('T')[0];
			$('#cmama-age-currentdate').val(today);

			$('.cmama-age-calculator .cmama-calculate-btn').on('click', function() {
				const birthdate = $('#cmama-age-birthdate').val();
				const currentdate = $('#cmama-age-currentdate').val();

				if (!birthdate || !currentdate) {
					alert('" . esc_js( __( 'Please select both dates.', 'calculator-mama' ) ) . "');
					return;
				}

				const birth = new Date(birthdate);
				const current = new Date(currentdate);

				if (birth > current) {
					alert('" . esc_js( __( 'Birth date cannot be after current date.', 'calculator-mama' ) ) . "');
					return;
				}

				let years = current.getFullYear() - birth.getFullYear();
				let months = current.getMonth() - birth.getMonth();
				let days = current.getDate() - birth.getDate();

				if (days < 0) {
					months--;
					const prevMonth = new Date(current.getFullYear(), current.getMonth(), 0);
					days += prevMonth.getDate();
				}

				if (months < 0) {
					years--;
					months += 12;
				}

				const totalDays = Math.floor((current - birth) / (1000 * 60 * 60 * 24));
				const totalWeeks = Math.floor(totalDays / 7);
				const totalMonths = years * 12 + months;
				const totalHours = totalDays * 24;

				$('#cmama-age-result').text(years + ' years, ' + months + ' months, ' + days + ' days');
				$('#cmama-age-months').text(totalMonths.toLocaleString());
				$('#cmama-age-weeks').text(totalWeeks.toLocaleString());
				$('#cmama-age-days').text(totalDays.toLocaleString());
				$('#cmama-age-hours').text(totalHours.toLocaleString());

				$('.cmama-age-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Age_Calculator();
