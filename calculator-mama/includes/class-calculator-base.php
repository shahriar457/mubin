<?php
/**
 * Calculator Base Class
 *
 * Abstract base class for all calculators. Each calculator extends this class
 * and implements the required methods.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Calculator Base Class.
 *
 * @since 1.0.0
 */
abstract class CMAMA_Calculator_Base {

	/**
	 * Calculator slug (unique identifier).
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Calculator name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Calculator description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Calculator category.
	 *
	 * @var string
	 */
	protected $category = '';

	/**
	 * Schema.org type for SEO.
	 *
	 * @var string
	 */
	protected $schema_type = 'WebApplication';

	/**
	 * Get calculator slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get calculator name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get calculator description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get calculator category.
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Get Schema.org type.
	 *
	 * @return string
	 */
	public function get_schema_type() {
		return $this->schema_type;
	}

	/**
	 * Check if calculator is active.
	 *
	 * @return bool
	 */
	public function is_active() {
		$settings           = get_option( 'cmama_settings', array() );
		$active_calculators = isset( $settings['active_calculators'] ) ? $settings['active_calculators'] : array();
		return in_array( $this->slug, $active_calculators, true );
	}

	/**
	 * Render calculator HTML.
	 *
	 * Must be implemented by child classes.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Calculator HTML.
	 */
	abstract public function render( $atts = array() );

	/**
	 * Get calculator-specific JavaScript.
	 *
	 * @return string JavaScript code.
	 */
	public function get_javascript() {
		return '';
	}

	/**
	 * Get calculator-specific CSS.
	 *
	 * @return string CSS code.
	 */
	public function get_css() {
		return '';
	}

	/**
	 * Get calculator fields configuration.
	 *
	 * Returns an array of field definitions used for rendering and validation.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array();
	}

	/**
	 * Enqueue calculator assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		// Enqueue base calculator styles.
		wp_enqueue_style(
			'cmama-calculator-base',
			CMAMA_PLUGIN_URL . 'assets/css/calculator-base.css',
			array(),
			CMAMA_VERSION
		);

		// Enqueue base calculator script.
		wp_enqueue_script(
			'cmama-calculator-base',
			CMAMA_PLUGIN_URL . 'assets/js/calculator-base.js',
			array( 'jquery' ),
			CMAMA_VERSION,
			true
		);

		// Add inline styles from appearance settings.
		$this->add_custom_styles();
	}

	/**
	 * Add custom styles from appearance settings.
	 *
	 * @since 1.0.0
	 */
	protected function add_custom_styles() {
		$settings   = get_option( 'cmama_settings', array() );
		$appearance = isset( $settings['appearance'] ) ? $settings['appearance'] : array();

		$primary_color  = isset( $appearance['primary_color'] ) ? $appearance['primary_color'] : '#2271b1';
		$button_color   = isset( $appearance['button_color'] ) ? $appearance['button_color'] : '#2271b1';
		$input_bg       = isset( $appearance['input_bg'] ) ? $appearance['input_bg'] : '#ffffff';
		$result_bg      = isset( $appearance['result_bg'] ) ? $appearance['result_bg'] : '#f0f0f1';
		$border_radius  = isset( $appearance['border_radius'] ) ? $appearance['border_radius'] : '4';
		$padding        = isset( $appearance['padding'] ) ? $appearance['padding'] : '20';
		$font_family    = isset( $appearance['font_family'] ) ? $appearance['font_family'] : 'inherit';
		$custom_css     = isset( $appearance['custom_css'] ) ? $appearance['custom_css'] : '';

		$custom_css_output = "
			.cmama-calculator {
				--cmama-primary-color: {$primary_color};
				--cmama-button-color: {$button_color};
				--cmama-input-bg: {$input_bg};
				--cmama-result-bg: {$result_bg};
				--cmama-border-radius: {$border_radius}px;
				--cmama-padding: {$padding}px;
				font-family: {$font_family};
			}
			{$custom_css}
		";

		wp_add_inline_style( 'cmama-calculator-base', $custom_css_output );
	}
}
