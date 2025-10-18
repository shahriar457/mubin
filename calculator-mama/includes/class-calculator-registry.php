<?php
/**
 * Calculator Registry
 *
 * Manages registration and retrieval of all calculator instances.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculator Registry Class.
 *
 * @since 1.0.0
 */
class CMAMA_Calculator_Registry {

	/**
	 * Single instance of the class.
	 *
	 * @var CMAMA_Calculator_Registry
	 */
	private static $instance = null;

	/**
	 * Array of registered calculators.
	 *
	 * @var array
	 */
	private $calculators = array();

	/**
	 * Get the singleton instance.
	 *
	 * @return CMAMA_Calculator_Registry
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
		$this->load_calculators();
	}

	/**
	 * Load all calculator classes.
	 *
	 * @since 1.0.0
	 */
	private function load_calculators() {
		// Financial calculators.
		$this->load_calculator_files( 'financial', array(
			'mortgage',
			'loan',
			'compound-interest',
			'investment',
			'retirement',
			'roi',
		) );

		// Health calculators.
		$this->load_calculator_files( 'health', array(
			'bmi',
			'bmr',
			'calorie',
			'body-fat',
			'pregnancy',
			'ideal-weight',
		) );

		// Math calculators.
		$this->load_calculator_files( 'math', array(
			'percentage',
			'fraction',
			'scientific',
			'square-root',
			'exponent',
			'average',
		) );

		// Other calculators.
		$this->load_calculator_files( 'other', array(
			'age',
			'date-difference',
			'tip',
			'gpa',
			'grade',
			'time',
		) );
	}

	/**
	 * Load calculator files from a category.
	 *
	 * @param string $category Category slug.
	 * @param array  $files    Array of calculator file names (without .php).
	 * @since 1.0.0
	 */
	private function load_calculator_files( $category, $files ) {
		foreach ( $files as $file ) {
			$filepath = CMAMA_PLUGIN_DIR . "calculators/{$category}/class-{$file}-calculator.php";
			if ( file_exists( $filepath ) ) {
				require_once $filepath;
			}
		}
	}

	/**
	 * Register a calculator.
	 *
	 * @param CMAMA_Calculator_Base $calculator Calculator instance.
	 * @since 1.0.0
	 */
	public function register( $calculator ) {
		if ( $calculator instanceof CMAMA_Calculator_Base ) {
			$this->calculators[ $calculator->get_slug() ] = $calculator;
		}
	}

	/**
	 * Get a calculator by slug.
	 *
	 * @param string $slug Calculator slug.
	 * @return CMAMA_Calculator_Base|null
	 */
	public function get( $slug ) {
		return isset( $this->calculators[ $slug ] ) ? $this->calculators[ $slug ] : null;
	}

	/**
	 * Get all registered calculators.
	 *
	 * @return array
	 */
	public function get_all() {
		return $this->calculators;
	}

	/**
	 * Get calculators by category.
	 *
	 * @param string $category Category name.
	 * @return array
	 */
	public function get_by_category( $category ) {
		return array_filter(
			$this->calculators,
			function ( $calculator ) use ( $category ) {
				return $calculator->get_category() === $category;
			}
		);
	}

	/**
	 * Get active calculators only.
	 *
	 * @return array
	 */
	public function get_active() {
		return array_filter(
			$this->calculators,
			function ( $calculator ) {
				return $calculator->is_active();
			}
		);
	}

	/**
	 * Get all calculator categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array(
			'financial' => __( 'Financial', 'calculator-mama' ),
			'health'    => __( 'Health & Fitness', 'calculator-mama' ),
			'math'      => __( 'Math', 'calculator-mama' ),
			'other'     => __( 'Other', 'calculator-mama' ),
		);
	}
}
