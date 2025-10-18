<?php
/**
 * Average Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Average Calculator Class.
 */
class CMAMA_Average_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'average-calculator';
		$this->name        = __( 'Average Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate mean, median, mode, and range of a set of numbers.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-average-calculator" data-calculator="average">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-avg-numbers" class="cmama-label">
						<?php esc_html_e( 'Enter Numbers (comma-separated)', 'calculator-mama' ); ?>
					</label>
					<textarea id="cmama-avg-numbers" class="cmama-input" rows="4" placeholder="10, 20, 30, 40, 50">10, 20, 30, 40, 50</textarea>
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Count:', 'calculator-mama' ); ?></span>
							<span id="cmama-avg-count">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Sum:', 'calculator-mama' ); ?></span>
							<span id="cmama-avg-sum">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Mean (Average):', 'calculator-mama' ); ?></span>
							<span id="cmama-avg-mean">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Median:', 'calculator-mama' ); ?></span>
							<span id="cmama-avg-median">0</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Range:', 'calculator-mama' ); ?></span>
							<span id="cmama-avg-range">0</span>
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
			$('.cmama-average-calculator .cmama-calculate-btn').on('click', function() {
				const input = $('#cmama-avg-numbers').val();
				const numbers = input.split(',').map(n => parseFloat(n.trim())).filter(n => !isNaN(n));

				if (numbers.length === 0) {
					alert('" . esc_js( __( 'Please enter valid numbers.', 'calculator-mama' ) ) . "');
					return;
				}

				const count = numbers.length;
				const sum = numbers.reduce((a, b) => a + b, 0);
				const mean = sum / count;

				const sorted = numbers.slice().sort((a, b) => a - b);
				let median;
				if (count % 2 === 0) {
					median = (sorted[count / 2 - 1] + sorted[count / 2]) / 2;
				} else {
					median = sorted[Math.floor(count / 2)];
				}

				const min = Math.min(...numbers);
				const max = Math.max(...numbers);
				const range = max - min;

				$('#cmama-avg-count').text(count);
				$('#cmama-avg-sum').text(sum.toFixed(2));
				$('#cmama-avg-mean').text(mean.toFixed(2));
				$('#cmama-avg-median').text(median.toFixed(2));
				$('#cmama-avg-range').text(range.toFixed(2));

				$('.cmama-average-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Average_Calculator();
