<?php
/**
 * Time Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Time Calculator Class.
 */
class CMAMA_Time_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'time-calculator';
		$this->name        = __( 'Time Calculator', 'calculator-mama' );
		$this->description = __( 'Add or subtract hours, minutes, and seconds.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-time-calculator" data-calculator="time">
			<div class="cmama-calculator-body">
				<div class="cmama-time-input">
					<div class="cmama-field-group">
						<label class="cmama-label"><?php esc_html_e( 'Start Time', 'calculator-mama' ); ?></label>
						<div class="cmama-time-fields">
							<input type="number" class="cmama-input cmama-time-short" placeholder="HH" value="2" min="0" max="23">
							<span>:</span>
							<input type="number" class="cmama-input cmama-time-short" placeholder="MM" value="30" min="0" max="59">
							<span>:</span>
							<input type="number" class="cmama-input cmama-time-short" placeholder="SS" value="0" min="0" max="59">
						</div>
					</div>

					<div class="cmama-field-group">
						<label class="cmama-label"><?php esc_html_e( 'End Time', 'calculator-mama' ); ?></label>
						<div class="cmama-time-fields">
							<input type="number" class="cmama-input cmama-time-short" placeholder="HH" value="5" min="0" max="23">
							<span>:</span>
							<input type="number" class="cmama-input cmama-time-short" placeholder="MM" value="45" min="0" max="59">
							<span>:</span>
							<input type="number" class="cmama-input cmama-time-short" placeholder="SS" value="30" min="0" max="59">
						</div>
					</div>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate Duration', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Duration', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-time-result">0:00:00</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Hours:', 'calculator-mama' ); ?></span>
							<span id="cmama-time-hours">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Minutes:', 'calculator-mama' ); ?></span>
							<span id="cmama-time-minutes">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Seconds:', 'calculator-mama' ); ?></span>
							<span id="cmama-time-seconds">0</span>
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
			$('.cmama-time-calculator .cmama-calculate-btn').on('click', function() {
				const startFields = $('.cmama-time-calculator .cmama-time-input .cmama-field-group').eq(0).find('.cmama-time-short');
				const endFields = $('.cmama-time-calculator .cmama-time-input .cmama-field-group').eq(1).find('.cmama-time-short');

				const startH = parseInt(startFields.eq(0).val()) || 0;
				const startM = parseInt(startFields.eq(1).val()) || 0;
				const startS = parseInt(startFields.eq(2).val()) || 0;

				const endH = parseInt(endFields.eq(0).val()) || 0;
				const endM = parseInt(endFields.eq(1).val()) || 0;
				const endS = parseInt(endFields.eq(2).val()) || 0;

				const startTotal = startH * 3600 + startM * 60 + startS;
				const endTotal = endH * 3600 + endM * 60 + endS;

				let diffSeconds = endTotal - startTotal;
				if (diffSeconds < 0) {
					diffSeconds += 24 * 3600;
				}

				const hours = Math.floor(diffSeconds / 3600);
				const minutes = Math.floor((diffSeconds % 3600) / 60);
				const seconds = diffSeconds % 60;

				const totalMinutes = Math.floor(diffSeconds / 60);
				const totalHours = (diffSeconds / 3600).toFixed(2);

				$('#cmama-time-result').text(hours + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0'));
				$('#cmama-time-hours').text(totalHours);
				$('#cmama-time-minutes').text(totalMinutes);
				$('#cmama-time-seconds').text(diffSeconds);

				$('.cmama-time-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Time_Calculator();
