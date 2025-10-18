<?php
/**
 * Shortcode Handler
 *
 * Handles calculator shortcodes.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode Handler Class.
 *
 * @since 1.0.0
 */
class CMAMA_Shortcode {

	/**
	 * Single instance of the class.
	 *
	 * @var CMAMA_Shortcode
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return CMAMA_Shortcode
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_shortcode( 'cmama_calculator', array( $this, 'render_calculator' ) );
	}

	/**
	 * Render calculator shortcode.
	 *
	 * Usage: [cmama_calculator slug="mortgage-calculator"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Calculator HTML.
	 */
	public function render_calculator( $atts ) {
		$atts = shortcode_atts(
			array(
				'slug' => '',
			),
			$atts,
			'cmama_calculator'
		);

		if ( empty( $atts['slug'] ) ) {
			return '<p class="cmama-error">' . esc_html__( 'Please specify a calculator slug.', 'calculator-mama' ) . '</p>';
		}

		$renderer = CMAMA_Frontend_Renderer::instance();
		return $renderer->render_calculator( $atts['slug'], $atts );
	}
}
