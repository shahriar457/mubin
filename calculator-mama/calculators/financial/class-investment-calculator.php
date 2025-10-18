<?php
/**
 * Investment Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Investment Calculator Class.
 */
class CMAMA_Investment_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'investment-calculator';
		$this->name        = __( 'Investment Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate return on investment and future value of investments.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-investment-calculator" data-calculator="investment">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-inv-initial" class="cmama-label">
						<?php esc_html_e( 'Initial Investment ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-inv-initial" class="cmama-input" value="10000" min="0" step="100">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-inv-return" class="cmama-label">
						<?php esc_html_e( 'Expected Return Rate (%)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-inv-return" class="cmama-input" value="8" min="0" max="100" step="0.1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-inv-years" class="cmama-label">
						<?php esc_html_e( 'Investment Period (years)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-inv-years" class="cmama-input" value="20" min="1" step="1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Future Value', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-inv-result">$0</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Gain:', 'calculator-mama' ); ?></span>
							<span id="cmama-inv-gain">$0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'ROI:', 'calculator-mama' ); ?></span>
							<span id="cmama-inv-roi">0%</span>
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
			$('.cmama-investment-calculator .cmama-calculate-btn').on('click', function() {
				const initial = parseFloat($('#cmama-inv-initial').val()) || 0;
				const returnRate = parseFloat($('#cmama-inv-return').val()) || 0;
				const years = parseInt($('#cmama-inv-years').val()) || 0;

				if (initial <= 0 || years <= 0) {
					alert('" . esc_js( __( 'Please enter valid values.', 'calculator-mama' ) ) . "');
					return;
				}

				const futureValue = initial * Math.pow(1 + returnRate / 100, years);
				const gain = futureValue - initial;
				const roi = ((futureValue - initial) / initial) * 100;

				$('#cmama-inv-result').text('$' + futureValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-inv-gain').text('$' + gain.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
				$('#cmama-inv-roi').text(roi.toFixed(2) + '%');

				$('.cmama-investment-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Investment_Calculator();
