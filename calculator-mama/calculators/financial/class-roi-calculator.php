<?php
/**
 * ROI Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ROI Calculator Class.
 */
class CMAMA_ROI_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'roi-calculator';
		$this->name        = __( 'ROI Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate return on investment percentage and profitability.', 'calculator-mama' );
		$this->category    = 'financial';
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
		<div class="cmama-calculator cmama-roi-calculator" data-calculator="roi">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-roi-cost" class="cmama-label">
						<?php esc_html_e( 'Investment Cost ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-roi-cost" class="cmama-input" value="5000" min="0" step="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-roi-gain" class="cmama-label">
						<?php esc_html_e( 'Investment Gain ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-roi-gain" class="cmama-input" value="7000" min="0" step="100">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate ROI', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Return on Investment', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-roi-result">0%</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Net Profit:', 'calculator-mama' ); ?></span>
							<span id="cmama-roi-profit">$0</span>
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
			$('.cmama-roi-calculator .cmama-calculate-btn').on('click', function() {
				const cost = parseFloat($('#cmama-roi-cost').val()) || 0;
				const gain = parseFloat($('#cmama-roi-gain').val()) || 0;

				if (cost <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				const profit = gain - cost;
				const roi = (profit / cost) * 100;

				$('#cmama-roi-result').text(roi.toFixed(2) + '%');
				$('#cmama-roi-profit').text('$' + profit.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

				$('.cmama-roi-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_ROI_Calculator();
