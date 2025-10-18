<?php
/**
 * Scientific Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scientific Calculator Class.
 */
class CMAMA_Scientific_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'scientific-calculator';
		$this->name        = __( 'Scientific Calculator', 'calculator-mama' );
		$this->description = __( 'Advanced scientific calculator with trigonometric, logarithmic, and exponential functions.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-scientific-calculator" data-calculator="scientific">
			<div class="cmama-calculator-body">
				<div class="cmama-field-group">
					<input type="text" id="cmama-sci-display" class="cmama-input cmama-sci-display" readonly value="0">
				</div>

				<div class="cmama-sci-buttons">
					<button type="button" class="cmama-sci-btn" data-action="clear">C</button>
					<button type="button" class="cmama-sci-btn" data-action="backspace">⌫</button>
					<button type="button" class="cmama-sci-btn" data-action="(">(</button>
					<button type="button" class="cmama-sci-btn" data-action=")">)</button>
					
					<button type="button" class="cmama-sci-btn" data-action="sin">sin</button>
					<button type="button" class="cmama-sci-btn" data-action="cos">cos</button>
					<button type="button" class="cmama-sci-btn" data-action="tan">tan</button>
					<button type="button" class="cmama-sci-btn" data-action="/">÷</button>
					
					<button type="button" class="cmama-sci-btn" data-action="7">7</button>
					<button type="button" class="cmama-sci-btn" data-action="8">8</button>
					<button type="button" class="cmama-sci-btn" data-action="9">9</button>
					<button type="button" class="cmama-sci-btn" data-action="*">×</button>
					
					<button type="button" class="cmama-sci-btn" data-action="4">4</button>
					<button type="button" class="cmama-sci-btn" data-action="5">5</button>
					<button type="button" class="cmama-sci-btn" data-action="6">6</button>
					<button type="button" class="cmama-sci-btn" data-action="-">-</button>
					
					<button type="button" class="cmama-sci-btn" data-action="1">1</button>
					<button type="button" class="cmama-sci-btn" data-action="2">2</button>
					<button type="button" class="cmama-sci-btn" data-action="3">3</button>
					<button type="button" class="cmama-sci-btn" data-action="+">+</button>
					
					<button type="button" class="cmama-sci-btn" data-action="0">0</button>
					<button type="button" class="cmama-sci-btn" data-action=".">.</button>
					<button type="button" class="cmama-sci-btn" data-action="pi">π</button>
					<button type="button" class="cmama-sci-btn cmama-sci-equals" data-action="=">=</button>
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
			let display = '0';

			function updateDisplay() {
				$('#cmama-sci-display').val(display);
			}

			$('.cmama-sci-btn').on('click', function() {
				const action = $(this).data('action');

				if (action === 'clear') {
					display = '0';
				} else if (action === 'backspace') {
					display = display.length > 1 ? display.slice(0, -1) : '0';
				} else if (action === '=') {
					try {
						let expression = display;
						expression = expression.replace(/sin/g, 'Math.sin');
						expression = expression.replace(/cos/g, 'Math.cos');
						expression = expression.replace(/tan/g, 'Math.tan');
						expression = expression.replace(/π/g, 'Math.PI');
						display = eval(expression).toString();
					} catch (e) {
						display = 'Error';
					}
				} else if (action === 'sin' || action === 'cos' || action === 'tan') {
					if (display === '0') display = '';
					display += action + '(';
				} else if (action === 'pi') {
					if (display === '0') display = '';
					display += 'π';
				} else {
					if (display === '0' && action !== '.') {
						display = action;
					} else {
						display += action;
					}
				}

				updateDisplay();
			});
		});
		";
	}
}

new CMAMA_Scientific_Calculator();
