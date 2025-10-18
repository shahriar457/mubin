<?php
/**
 * SEO Engine
 *
 * Handles automatic Schema.org markup and SEO optimization.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEO Engine Class.
 *
 * @since 1.0.0
 */
class CMAMA_SEO_Engine {

	/**
	 * Single instance of the class.
	 *
	 * @var CMAMA_SEO_Engine
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return CMAMA_SEO_Engine
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
		add_action( 'wp_head', array( $this, 'output_schema_markup' ), 10 );
	}

	/**
	 * Output Schema.org markup for calculators on the page.
	 *
	 * @since 1.0.0
	 */
	public function output_schema_markup() {
		if ( ! is_singular() ) {
			return;
		}

		global $post;

		// Check if page contains calculator shortcode or block.
		$calculators = $this->detect_calculators_in_content( $post->post_content );

		if ( empty( $calculators ) ) {
			return;
		}

		$registry = CMAMA_Calculator_Registry::instance();

		foreach ( $calculators as $slug ) {
			$calculator = $registry->get( $slug );
			if ( ! $calculator ) {
				continue;
			}

			$schema = $this->generate_schema( $calculator, $post );
			$this->output_json_ld( $schema );
		}
	}

	/**
	 * Detect calculators in post content.
	 *
	 * @param string $content Post content.
	 * @return array Array of calculator slugs.
	 */
	private function detect_calculators_in_content( $content ) {
		$calculators = array();

		// Check for shortcode.
		preg_match_all( '/\[cmama_calculator slug=[\'"]([^\'"]+)[\'"]\]/', $content, $matches );
		if ( ! empty( $matches[1] ) ) {
			$calculators = array_merge( $calculators, $matches[1] );
		}

		// Check for Gutenberg block (simplified - actual implementation may vary).
		preg_match_all( '/<!-- wp:calculator-mama\/calculator \{[^}]*"slug":"([^"]+)"/', $content, $matches );
		if ( ! empty( $matches[1] ) ) {
			$calculators = array_merge( $calculators, $matches[1] );
		}

		return array_unique( $calculators );
	}

	/**
	 * Generate Schema.org markup for a calculator.
	 *
	 * @param CMAMA_Calculator_Base $calculator Calculator instance.
	 * @param WP_Post               $post      Post object.
	 * @return array Schema.org markup array.
	 */
	private function generate_schema( $calculator, $post ) {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => $calculator->get_schema_type(),
			'name'        => $calculator->get_name(),
			'description' => $calculator->get_description(),
			'url'         => get_permalink( $post->ID ),
			'image'       => get_the_post_thumbnail_url( $post->ID, 'large' ),
			'provider'    => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url(),
			),
		);

		// Add category-specific schema enhancements.
		$category = $calculator->get_category();

		if ( 'health' === $category ) {
			$schema['medicalSpecialty'] = 'http://health-lifesci.schema.org/Preventive';
		}

		// Add application category for web applications.
		if ( in_array( $calculator->get_schema_type(), array( 'WebApplication', 'SoftwareApplication' ), true ) ) {
			$schema['applicationCategory'] = 'UtilitiesApplication';
			$schema['operatingSystem']     = 'Web Browser';
			$schema['offers']              = array(
				'@type' => 'Offer',
				'price' => '0',
			);
		}

		return $schema;
	}

	/**
	 * Output JSON-LD script tag.
	 *
	 * @param array $schema Schema.org markup array.
	 * @since 1.0.0
	 */
	private function output_json_ld( $schema ) {
		echo '<script type="application/ld+json">';
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo '</script>' . "\n";
	}
}
