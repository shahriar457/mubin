<?php
/**
 * Frontend Renderer
 *
 * Handles rendering calculators on the frontend.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend Renderer Class.
 *
 * @since 1.0.0
 */
class CMAMA_Frontend_Renderer {

	/**
	 * Single instance of the class.
	 *
	 * @var CMAMA_Frontend_Renderer
	 */
	private static $instance = null;

	/**
	 * Track which calculators have been rendered.
	 *
	 * @var array
	 */
	private $rendered_calculators = array();

	/**
	 * Get the singleton instance.
	 *
	 * @return CMAMA_Frontend_Renderer
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_base_assets' ) );
		add_action( 'wp_footer', array( $this, 'print_calculator_scripts' ), 999 );
	}

	/**
	 * Enqueue base calculator assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_base_assets() {
		// Only enqueue if we're displaying a calculator.
		if ( ! is_singular() && ! is_page() ) {
			return;
		}

		wp_enqueue_style(
			'cmama-calculator-base',
			CMAMA_PLUGIN_URL . 'assets/css/calculator-base.css',
			array(),
			CMAMA_VERSION
		);

		wp_enqueue_script(
			'cmama-calculator-base',
			CMAMA_PLUGIN_URL . 'assets/js/calculator-base.js',
			array( 'jquery' ),
			CMAMA_VERSION,
			true
		);
	}

	/**
	 * Render a calculator.
	 *
	 * @param string $slug Calculator slug.
	 * @param array  $atts Shortcode attributes.
	 * @return string Calculator HTML.
	 */
	public function render_calculator( $slug, $atts = array() ) {
		$registry   = CMAMA_Calculator_Registry::instance();
		$calculator = $registry->get( $slug );

		if ( ! $calculator ) {
			return '<p class="cmama-error">' . esc_html__( 'Calculator not found.', 'calculator-mama' ) . '</p>';
		}

		if ( ! $calculator->is_active() ) {
			return '<p class="cmama-error">' . esc_html__( 'This calculator is not active.', 'calculator-mama' ) . '</p>';
		}

		// Track this calculator for script printing.
		if ( ! in_array( $slug, $this->rendered_calculators, true ) ) {
			$this->rendered_calculators[] = $slug;
		}

		// Enqueue calculator assets.
		$calculator->enqueue_assets();

		// Render calculator HTML.
		return $calculator->render( $atts );
	}

	/**
	 * Print calculator-specific JavaScript in footer.
	 *
	 * @since 1.0.0
	 */
	public function print_calculator_scripts() {
		if ( empty( $this->rendered_calculators ) ) {
			return;
		}

		$registry = CMAMA_Calculator_Registry::instance();

		echo '<script type="text/javascript">';
		foreach ( $this->rendered_calculators as $slug ) {
			$calculator = $registry->get( $slug );
			if ( $calculator ) {
				$js = $calculator->get_javascript();
				if ( ! empty( $js ) ) {
					echo $js; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
		echo '</script>';
	}
}
