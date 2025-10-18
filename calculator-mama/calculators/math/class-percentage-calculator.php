<?php
/**
 * Percentage Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Percentage Calculator Class.
 */
class CMAMA_Percentage_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'percentage-calculator';
		$this->name        = __( 'Percentage Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate percentages, percentage increase/decrease, and percentage differences.', 'calculator-mama' );
		$this->category    = 'math';
		$this->schema_type = 'MathSolver';

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
		<div class="cmama-calculator cmama-percentage-calculator" data-calculator="percentage">
			<div class="cmama-calculator-body">
				<div class="cmama-tabs">
					<button type="button" class="cmama-tab-btn active" data-tab="basic"><?php esc_html_e( 'Basic', 'calculator-mama' ); ?></button>
					<button type="button" class="cmama-tab-btn" data-tab="change"><?php esc_html_e( '% Change', 'calculator-mama' ); ?></button>
					<button type="button" class="cmama-tab-btn" data-tab="difference"><?php esc_html_e( '% Difference', 'calculator-mama' ); ?></button>
				</div>

				<div class="cmama-tab-content active" id="tab-basic">
					<div class="cmama-field-group">
						<label for="cmama-perc-what" class="cmama-label">
							<?php esc_html_e( 'What is', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-perc-what" class="cmama-input" value="25" step="0.01">
						<span class="cmama-field-suffix">%</span>
					</div>

					<div class="cmama-field-group">
						<label for="cmama-perc-of" class="cmama-label">
							<?php esc_html_e( 'of', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-perc-of" class="cmama-input" value="200" step="0.01">
					</div>
				</div>

				<div class="cmama-tab-content" id="tab-change">
					<div class="cmama-field-group">
						<label for="cmama-perc-from" class="cmama-label">
							<?php esc_html_e( 'From', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-perc-from" class="cmama-input" value="100" step="0.01">
					</div>

					<div class="cmama-field-group">
						<label for="cmama-perc-to" class="cmama-label">
							<?php esc_html_e( 'To', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-perc-to" class="cmama-input" value="150" step="0.01">
					</div>
				</div>

				<div class="cmama-tab-content" id="tab-difference">
					<div class="cmama-field-group">
						<label for="cmama-perc-val1" class="cmama-label">
							<?php esc_html_e( 'Value 1', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-perc-val1" class="cmama-input" value="100" step="0.01">
					</div>

					<div class="cmama-field-group">
						<label for="cmama-perc-val2" class="cmama-label">
							<?php esc_html_e( 'Value 2', 'calculator-mama' ); ?>
						</label>
						<input type="number" id="cmama-perc-val2" class="cmama-input" value="120" step="0.01">
					</div>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Result', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-perc-result">0</div>
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
			$('.cmama-percentage-calculator .cmama-tab-btn').on('click', function() {
				const tab = $(this).data('tab');
				$('.cmama-percentage-calculator .cmama-tab-btn').removeClass('active');
				$('.cmama-percentage-calculator .cmama-tab-content').removeClass('active');
				$(this).addClass('active');
				$('#tab-' + tab).addClass('active');
			});

			$('.cmama-percentage-calculator .cmama-calculate-btn').on('click', function() {
				const activeTab = $('.cmama-percentage-calculator .cmama-tab-btn.active').data('tab');
				let result;

				if (activeTab === 'basic') {
					const percent = parseFloat($('#cmama-perc-what').val()) || 0;
					const of = parseFloat($('#cmama-perc-of').val()) || 0;
					result = (percent / 100) * of;
				} else if (activeTab === 'change') {
					const from = parseFloat($('#cmama-perc-from').val()) || 0;
					const to = parseFloat($('#cmama-perc-to').val()) || 0;
					if (from === 0) {
						alert('" . esc_js( __( 'From value cannot be zero.', 'calculator-mama' ) ) . "');
						return;
					}
					result = ((to - from) / from) * 100;
					$('#cmama-perc-result').text(result.toFixed(2) + '%');
					$('.cmama-percentage-calculator .cmama-result').slideDown();
					return;
				} else if (activeTab === 'difference') {
					const val1 = parseFloat($('#cmama-perc-val1').val()) || 0;
					const val2 = parseFloat($('#cmama-perc-val2').val()) || 0;
					const avg = (val1 + val2) / 2;
					if (avg === 0) {
						alert('" . esc_js( __( 'Average cannot be zero.', 'calculator-mama' ) ) . "');
						return;
					}
					result = Math.abs((val1 - val2) / avg) * 100;
					$('#cmama-perc-result').text(result.toFixed(2) + '%');
					$('.cmama-percentage-calculator .cmama-result').slideDown();
					return;
				}

				$('#cmama-perc-result').text(result.toFixed(2));
				$('.cmama-percentage-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Percentage_Calculator();
