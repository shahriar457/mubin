<?php
/**
 * Calculator Manager
 *
 * Manages calculator activation, deactivation, and settings.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculator Manager Class.
 *
 * @since 1.0.0
 */
class CMAMA_Calculator_Manager {

	/**
	 * Activate a calculator.
	 *
	 * @param string $slug Calculator slug.
	 * @return bool Success status.
	 */
	public static function activate( $slug ) {
		$settings           = get_option( 'cmama_settings', array() );
		$active_calculators = isset( $settings['active_calculators'] ) ? $settings['active_calculators'] : array();

		if ( ! in_array( $slug, $active_calculators, true ) ) {
			$active_calculators[] = $slug;
			$settings['active_calculators'] = $active_calculators;
			return update_option( 'cmama_settings', $settings );
		}

		return true;
	}

	/**
	 * Deactivate a calculator.
	 *
	 * @param string $slug Calculator slug.
	 * @return bool Success status.
	 */
	public static function deactivate( $slug ) {
		$settings           = get_option( 'cmama_settings', array() );
		$active_calculators = isset( $settings['active_calculators'] ) ? $settings['active_calculators'] : array();

		$key = array_search( $slug, $active_calculators, true );
		if ( false !== $key ) {
			unset( $active_calculators[ $key ] );
			$settings['active_calculators'] = array_values( $active_calculators );
			return update_option( 'cmama_settings', $settings );
		}

		return true;
	}

	/**
	 * Check if a calculator is active.
	 *
	 * @param string $slug Calculator slug.
	 * @return bool
	 */
	public static function is_active( $slug ) {
		$settings           = get_option( 'cmama_settings', array() );
		$active_calculators = isset( $settings['active_calculators'] ) ? $settings['active_calculators'] : array();
		return in_array( $slug, $active_calculators, true );
	}

	/**
	 * Get all active calculators.
	 *
	 * @return array
	 */
	public static function get_active_calculators() {
		$settings = get_option( 'cmama_settings', array() );
		return isset( $settings['active_calculators'] ) ? $settings['active_calculators'] : array();
	}

	/**
	 * Activate all calculators.
	 *
	 * @return bool Success status.
	 */
	public static function activate_all() {
		$registry    = CMAMA_Calculator_Registry::instance();
		$calculators = $registry->get_all();
		$slugs       = array_keys( $calculators );

		$settings                       = get_option( 'cmama_settings', array() );
		$settings['active_calculators'] = $slugs;

		return update_option( 'cmama_settings', $settings );
	}

	/**
	 * Deactivate all calculators.
	 *
	 * @return bool Success status.
	 */
	public static function deactivate_all() {
		$settings                       = get_option( 'cmama_settings', array() );
		$settings['active_calculators'] = array();

		return update_option( 'cmama_settings', $settings );
	}
}
