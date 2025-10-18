<?php
/**
 * Tip Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tip Calculator Class.
 */
class CMAMA_Tip_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'tip-calculator';
		$this->name        = __( 'Tip Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate tips and split bills among multiple people.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-tip-calculator" data-calculator="tip">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<label for="cmama-tip-bill" class="cmama-label">
						<?php esc_html_e( 'Bill Amount ($)', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-tip-bill" class="cmama-input" value="100" min="0" step="0.01">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-tip-percent" class="cmama-label">
						<?php esc_html_e( 'Tip Percentage (%)', 'calculator-mama' ); ?>
					</label>
					<div class="cmama-tip-buttons">
						<button type="button" class="cmama-tip-preset" data-percent="10">10%</button>
						<button type="button" class="cmama-tip-preset" data-percent="15">15%</button>
						<button type="button" class="cmama-tip-preset" data-percent="18">18%</button>
						<button type="button" class="cmama-tip-preset" data-percent="20">20%</button>
						<button type="button" class="cmama-tip-preset" data-percent="25">25%</button>
					</div>
					<input type="number" id="cmama-tip-percent" class="cmama-input" value="15" min="0" max="100" step="1">
				</div>

				<div class="cmama-field-group">
					<label for="cmama-tip-people" class="cmama-label">
						<?php esc_html_e( 'Number of People', 'calculator-mama' ); ?>
					</label>
					<input type="number" id="cmama-tip-people" class="cmama-input" value="1" min="1" step="1">
				</div>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Total Amount', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-tip-total">$0.00</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Tip Amount:', 'calculator-mama' ); ?></span>
							<span id="cmama-tip-amount">$0.00</span>
						</div>
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Per Person:', 'calculator-mama' ); ?></span>
							<span id="cmama-tip-per-person">$0.00</span>
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
			$('.cmama-tip-preset').on('click', function() {
				const percent = $(this).data('percent');
				$('#cmama-tip-percent').val(percent);
				$('.cmama-tip-preset').removeClass('active');
				$(this).addClass('active');
			});

			$('.cmama-tip-calculator .cmama-calculate-btn').on('click', function() {
				const bill = parseFloat($('#cmama-tip-bill').val()) || 0;
				const percent = parseFloat($('#cmama-tip-percent').val()) || 0;
				const people = parseInt($('#cmama-tip-people').val()) || 1;

				if (bill <= 0) {
					alert('" . esc_js( __( 'Please enter a valid bill amount.', 'calculator-mama' ) ) . "');
					return;
				}

				const tipAmount = bill * (percent / 100);
				const total = bill + tipAmount;
				const perPerson = total / people;

				$('#cmama-tip-total').text('$' + total.toFixed(2));
				$('#cmama-tip-amount').text('$' + tipAmount.toFixed(2));
				$('#cmama-tip-per-person').text('$' + perPerson.toFixed(2));

				$('.cmama-tip-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_Tip_Calculator();
