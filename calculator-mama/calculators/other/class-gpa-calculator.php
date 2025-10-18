<?php
/**
 * GPA Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GPA Calculator Class.
 */
class CMAMA_GPA_Calculator extends CMAMA_Calculator_Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->slug        = 'gpa-calculator';
		$this->name        = __( 'GPA Calculator', 'calculator-mama' );
		$this->description = __( 'Calculate your Grade Point Average (GPA) from course grades and credits.', 'calculator-mama' );
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
		<div class="cmama-calculator cmama-gpa-calculator" data-calculator="gpa">
			<div class="cmama-calculator-body">
				<div id="cmama-gpa-courses">
					<div class="cmama-gpa-course">
						<div class="cmama-field-group">
							<label class="cmama-label"><?php esc_html_e( 'Grade', 'calculator-mama' ); ?></label>
							<select class="cmama-input cmama-gpa-grade">
								<option value="4.0">A (4.0)</option>
								<option value="3.7">A- (3.7)</option>
								<option value="3.3">B+ (3.3)</option>
								<option value="3.0">B (3.0)</option>
								<option value="2.7">B- (2.7)</option>
								<option value="2.3">C+ (2.3)</option>
								<option value="2.0">C (2.0)</option>
								<option value="1.7">C- (1.7)</option>
								<option value="1.0">D (1.0)</option>
								<option value="0.0">F (0.0)</option>
							</select>
						</div>
						<div class="cmama-field-group">
							<label class="cmama-label"><?php esc_html_e( 'Credits', 'calculator-mama' ); ?></label>
							<input type="number" class="cmama-input cmama-gpa-credits" value="3" min="0" step="0.5">
						</div>
					</div>
				</div>

				<button type="button" class="cmama-button cmama-button-secondary" id="cmama-gpa-add-course">
					<?php esc_html_e( '+ Add Course', 'calculator-mama' ); ?>
				</button>

				<button type="button" class="cmama-button cmama-calculate-btn">
					<?php esc_html_e( 'Calculate GPA', 'calculator-mama' ); ?>
				</button>

				<div class="cmama-result" style="display: none;">
					<div class="cmama-result-main">
						<div class="cmama-result-label"><?php esc_html_e( 'Your GPA', 'calculator-mama' ); ?></div>
						<div class="cmama-result-value" id="cmama-gpa-result">0.00</div>
					</div>
					<div class="cmama-result-breakdown">
						<div class="cmama-result-item">
							<span><?php esc_html_e( 'Total Credits:', 'calculator-mama' ); ?></span>
							<span id="cmama-gpa-credits-total">0</span>
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
			$('#cmama-gpa-add-course').on('click', function() {
				const courseHtml = `
					<div class=\"cmama-gpa-course\">
						<div class=\"cmama-field-group\">
							<label class=\"cmama-label\">" . esc_js( __( 'Grade', 'calculator-mama' ) ) . "</label>
							<select class=\"cmama-input cmama-gpa-grade\">
								<option value=\"4.0\">A (4.0)</option>
								<option value=\"3.7\">A- (3.7)</option>
								<option value=\"3.3\">B+ (3.3)</option>
								<option value=\"3.0\">B (3.0)</option>
								<option value=\"2.7\">B- (2.7)</option>
								<option value=\"2.3\">C+ (2.3)</option>
								<option value=\"2.0\">C (2.0)</option>
								<option value=\"1.7\">C- (1.7)</option>
								<option value=\"1.0\">D (1.0)</option>
								<option value=\"0.0\">F (0.0)</option>
							</select>
						</div>
						<div class=\"cmama-field-group\">
							<label class=\"cmama-label\">" . esc_js( __( 'Credits', 'calculator-mama' ) ) . "</label>
							<input type=\"number\" class=\"cmama-input cmama-gpa-credits\" value=\"3\" min=\"0\" step=\"0.5\">
						</div>
						<button type=\"button\" class=\"cmama-button cmama-button-small cmama-gpa-remove\">×</button>
					</div>
				`;
				$('#cmama-gpa-courses').append(courseHtml);
			});

			$(document).on('click', '.cmama-gpa-remove', function() {
				$(this).closest('.cmama-gpa-course').remove();
			});

			$('.cmama-gpa-calculator .cmama-calculate-btn').on('click', function() {
				let totalPoints = 0;
				let totalCredits = 0;

				$('.cmama-gpa-course').each(function() {
					const grade = parseFloat($(this).find('.cmama-gpa-grade').val()) || 0;
					const credits = parseFloat($(this).find('.cmama-gpa-credits').val()) || 0;
					
					totalPoints += grade * credits;
					totalCredits += credits;
				});

				if (totalCredits === 0) {
					alert('" . esc_js( __( 'Please enter at least one course with credits.', 'calculator-mama' ) ) . "');
					return;
				}

				const gpa = totalPoints / totalCredits;

				$('#cmama-gpa-result').text(gpa.toFixed(2));
				$('#cmama-gpa-credits-total').text(totalCredits.toFixed(1));

				$('.cmama-gpa-calculator .cmama-result').slideDown();
			});
		});
		";
	}
}

new CMAMA_GPA_Calculator();
