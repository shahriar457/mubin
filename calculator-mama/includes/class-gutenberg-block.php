<?php
/**
 * Gutenberg Block
 *
 * Handles Gutenberg block registration for calculator embedding.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gutenberg Block Class.
 *
 * @since 1.0.0
 */
class CMAMA_Gutenberg_Block {

	/**
	 * Single instance of the class.
	 *
	 * @var CMAMA_Gutenberg_Block
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return CMAMA_Gutenberg_Block
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
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register Gutenberg block.
	 *
	 * @since 1.0.0
	 */
	public function register_block() {
		// Check if Gutenberg is active.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register block with PHP render callback.
		register_block_type(
			'calculator-mama/calculator',
			array(
				'attributes'      => array(
					'slug'          => array(
						'type'    => 'string',
						'default' => '',
					),
					'introduction'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'instructions'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'faqs'          => array(
						'type'    => 'array',
						'default' => array(),
					),
				),
				'render_callback' => array( $this, 'render_block' ),
			)
		);

		// Enqueue block editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script(
			'cmama-block-editor',
			CMAMA_PLUGIN_URL . 'assets/js/block-editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' ),
			CMAMA_VERSION,
			true
		);

		// Pass calculator data to JavaScript.
		$registry    = CMAMA_Calculator_Registry::instance();
		$calculators = array();

		foreach ( $registry->get_all() as $slug => $calculator ) {
			$calculators[] = array(
				'slug'        => $slug,
				'name'        => $calculator->get_name(),
				'description' => $calculator->get_description(),
				'category'    => $calculator->get_category(),
				'active'      => $calculator->is_active(),
			);
		}

		wp_localize_script(
			'cmama-block-editor',
			'cmamaBlockData',
			array(
				'calculators' => $calculators,
				'categories'  => $registry->get_categories(),
			)
		);

		wp_enqueue_style(
			'cmama-block-editor',
			CMAMA_PLUGIN_URL . 'assets/css/block-editor.css',
			array( 'wp-edit-blocks' ),
			CMAMA_VERSION
		);
	}

	/**
	 * Render block on frontend.
	 *
	 * @param array $attributes Block attributes.
	 * @return string Block HTML.
	 */
	public function render_block( $attributes ) {
		$slug = isset( $attributes['slug'] ) ? $attributes['slug'] : '';

		if ( empty( $slug ) ) {
			return '<p class="cmama-error">' . esc_html__( 'Please select a calculator.', 'calculator-mama' ) . '</p>';
		}

		$output = '';

		// Introduction.
		if ( ! empty( $attributes['introduction'] ) ) {
			$output .= '<div class="cmama-introduction">' . wp_kses_post( $attributes['introduction'] ) . '</div>';
		}

		// Calculator.
		$renderer = CMAMA_Frontend_Renderer::instance();
		$output  .= $renderer->render_calculator( $slug, $attributes );

		// Instructions.
		if ( ! empty( $attributes['instructions'] ) ) {
			$output .= '<div class="cmama-instructions">' . wp_kses_post( $attributes['instructions'] ) . '</div>';
		}

		// FAQs.
		if ( ! empty( $attributes['faqs'] ) && is_array( $attributes['faqs'] ) ) {
			$output .= '<div class="cmama-faq-section">';
			$output .= '<h3>' . esc_html__( 'Frequently Asked Questions', 'calculator-mama' ) . '</h3>';
			
			foreach ( $attributes['faqs'] as $faq ) {
				if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
					$output .= '<div class="cmama-faq-item">';
					$output .= '<h4 class="cmama-faq-question">' . esc_html( $faq['question'] ) . '</h4>';
					$output .= '<div class="cmama-faq-answer">' . wp_kses_post( $faq['answer'] ) . '</div>';
					$output .= '</div>';
				}
			}
			
			$output .= '</div>';
		}

		return $output;
	}
}
