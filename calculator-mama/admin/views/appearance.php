<?php
/**
 * Appearance Settings View
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap cmama-appearance">
	<h1><?php esc_html_e( 'Appearance Settings', 'calculator-mama' ); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'cmama_settings' ); ?>

		<div class="cmama-appearance-grid">
			<!-- Color Settings -->
			<div class="cmama-settings-section">
				<h2><?php esc_html_e( 'Color Settings', 'calculator-mama' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cmama-primary-color"><?php esc_html_e( 'Primary Color', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="text" 
								   id="cmama-primary-color" 
								   name="cmama_settings[appearance][primary_color]" 
								   value="<?php echo esc_attr( $appearance['primary_color'] ); ?>" 
								   class="cmama-color-picker">
							<p class="description">
								<?php esc_html_e( 'Main accent color used throughout calculators.', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cmama-button-color"><?php esc_html_e( 'Button Color', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="text" 
								   id="cmama-button-color" 
								   name="cmama_settings[appearance][button_color]" 
								   value="<?php echo esc_attr( $appearance['button_color'] ); ?>" 
								   class="cmama-color-picker">
							<p class="description">
								<?php esc_html_e( 'Color for calculator buttons.', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cmama-input-bg"><?php esc_html_e( 'Input Background', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="text" 
								   id="cmama-input-bg" 
								   name="cmama_settings[appearance][input_bg]" 
								   value="<?php echo esc_attr( $appearance['input_bg'] ); ?>" 
								   class="cmama-color-picker">
							<p class="description">
								<?php esc_html_e( 'Background color for input fields.', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cmama-result-bg"><?php esc_html_e( 'Result Background', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="text" 
								   id="cmama-result-bg" 
								   name="cmama_settings[appearance][result_bg]" 
								   value="<?php echo esc_attr( $appearance['result_bg'] ); ?>" 
								   class="cmama-color-picker">
							<p class="description">
								<?php esc_html_e( 'Background color for result display areas.', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

			<!-- Layout Settings -->
			<div class="cmama-settings-section">
				<h2><?php esc_html_e( 'Layout Settings', 'calculator-mama' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cmama-border-radius"><?php esc_html_e( 'Border Radius', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="number" 
								   id="cmama-border-radius" 
								   name="cmama_settings[appearance][border_radius]" 
								   value="<?php echo esc_attr( $appearance['border_radius'] ); ?>" 
								   min="0" 
								   max="50" 
								   step="1">
							<span class="description"><?php esc_html_e( 'px', 'calculator-mama' ); ?></span>
							<p class="description">
								<?php esc_html_e( 'Border radius for calculator elements (0-50px).', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cmama-padding"><?php esc_html_e( 'Padding', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="number" 
								   id="cmama-padding" 
								   name="cmama_settings[appearance][padding]" 
								   value="<?php echo esc_attr( $appearance['padding'] ); ?>" 
								   min="0" 
								   max="100" 
								   step="5">
							<span class="description"><?php esc_html_e( 'px', 'calculator-mama' ); ?></span>
							<p class="description">
								<?php esc_html_e( 'Internal padding for calculator containers (0-100px).', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

			<!-- Typography Settings -->
			<div class="cmama-settings-section">
				<h2><?php esc_html_e( 'Typography Settings', 'calculator-mama' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cmama-font-family"><?php esc_html_e( 'Font Family', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<input type="text" 
								   id="cmama-font-family" 
								   name="cmama_settings[appearance][font_family]" 
								   value="<?php echo esc_attr( $appearance['font_family'] ); ?>" 
								   class="regular-text">
							<p class="description">
								<?php esc_html_e( 'Font family for calculators. Use "inherit" to use your theme\'s font.', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

			<!-- Custom CSS -->
			<div class="cmama-settings-section cmama-settings-section-full">
				<h2><?php esc_html_e( 'Custom CSS', 'calculator-mama' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cmama-custom-css"><?php esc_html_e( 'Custom CSS Code', 'calculator-mama' ); ?></label>
						</th>
						<td>
							<textarea id="cmama-custom-css" 
									  name="cmama_settings[appearance][custom_css]" 
									  rows="10" 
									  class="large-text code"><?php echo esc_textarea( $appearance['custom_css'] ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Add custom CSS to further customize calculator appearance.', 'calculator-mama' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php submit_button( __( 'Save Appearance Settings', 'calculator-mama' ) ); ?>
	</form>

	<!-- Live Preview -->
	<div class="cmama-preview-section">
		<h2><?php esc_html_e( 'Preview', 'calculator-mama' ); ?></h2>
		<div class="cmama-preview-calculator">
			<div class="cmama-calculator cmama-preview-demo">
				<h3><?php esc_html_e( 'Sample Calculator Preview', 'calculator-mama' ); ?></h3>
				<div class="cmama-calculator-body">
					<div class="cmama-field">
						<label><?php esc_html_e( 'Input Field', 'calculator-mama' ); ?></label>
						<input type="number" value="100" class="cmama-input">
					</div>
					<button type="button" class="cmama-button">
						<?php esc_html_e( 'Calculate', 'calculator-mama' ); ?>
					</button>
					<div class="cmama-result">
						<strong><?php esc_html_e( 'Result:', 'calculator-mama' ); ?></strong>
						<span class="cmama-result-value">200</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
