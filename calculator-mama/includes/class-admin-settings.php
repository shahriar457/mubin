<?php
/**
 * Admin Settings
 *
 * Manages the admin interface including dashboard, calculator library, and settings.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Settings Class.
 *
 * @since 1.0.0
 */
class CMAMA_Admin_Settings {

	/**
	 * Single instance of the class.
	 *
	 * @var CMAMA_Admin_Settings
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return CMAMA_Admin_Settings
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
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_activation_redirect' ) );
		add_action( 'wp_ajax_cmama_toggle_calculator', array( $this, 'ajax_toggle_calculator' ) );
		add_action( 'wp_ajax_cmama_activate_all', array( $this, 'ajax_activate_all' ) );
		add_action( 'wp_ajax_cmama_deactivate_all', array( $this, 'ajax_deactivate_all' ) );
	}

	/**
	 * Handle activation redirect.
	 *
	 * @since 1.0.0
	 */
	public function handle_activation_redirect() {
		if ( get_transient( 'cmama_activation_redirect' ) ) {
			delete_transient( 'cmama_activation_redirect' );
			if ( ! isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=calculator-mama' ) );
				exit;
			}
		}
	}

	/**
	 * Add admin menu pages.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		// Main menu.
		add_menu_page(
			__( 'Calculator Mama', 'calculator-mama' ),
			__( 'Calculator Mama', 'calculator-mama' ),
			'manage_options',
			'calculator-mama',
			array( $this, 'render_dashboard_page' ),
			'dashicons-calculator',
			30
		);

		// Dashboard submenu.
		add_submenu_page(
			'calculator-mama',
			__( 'Dashboard', 'calculator-mama' ),
			__( 'Dashboard', 'calculator-mama' ),
			'manage_options',
			'calculator-mama',
			array( $this, 'render_dashboard_page' )
		);

		// Calculator Library.
		add_submenu_page(
			'calculator-mama',
			__( 'Calculator Library', 'calculator-mama' ),
			__( 'Calculator Library', 'calculator-mama' ),
			'manage_options',
			'calculator-mama-library',
			array( $this, 'render_library_page' )
		);

		// Appearance Settings.
		add_submenu_page(
			'calculator-mama',
			__( 'Appearance', 'calculator-mama' ),
			__( 'Appearance', 'calculator-mama' ),
			'manage_options',
			'calculator-mama-appearance',
			array( $this, 'render_appearance_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			'cmama_settings',
			'cmama_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Settings input.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		if ( isset( $input['appearance'] ) ) {
			$sanitized['appearance'] = array(
				'primary_color'  => sanitize_hex_color( $input['appearance']['primary_color'] ),
				'button_color'   => sanitize_hex_color( $input['appearance']['button_color'] ),
				'input_bg'       => sanitize_hex_color( $input['appearance']['input_bg'] ),
				'result_bg'      => sanitize_hex_color( $input['appearance']['result_bg'] ),
				'border_radius'  => absint( $input['appearance']['border_radius'] ),
				'padding'        => absint( $input['appearance']['padding'] ),
				'font_family'    => sanitize_text_field( $input['appearance']['font_family'] ),
				'custom_css'     => wp_strip_all_tags( $input['appearance']['custom_css'] ),
			);
		}

		// Preserve active calculators.
		$existing_settings = get_option( 'cmama_settings', array() );
		if ( isset( $existing_settings['active_calculators'] ) ) {
			$sanitized['active_calculators'] = $existing_settings['active_calculators'];
		}

		return $sanitized;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 * @since 1.0.0
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on our admin pages.
		if ( strpos( $hook, 'calculator-mama' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'cmama-admin',
			CMAMA_PLUGIN_URL . 'admin/css/admin.css',
			array( 'wp-color-picker' ),
			CMAMA_VERSION
		);

		wp_enqueue_script(
			'cmama-admin',
			CMAMA_PLUGIN_URL . 'admin/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			CMAMA_VERSION,
			true
		);

		wp_localize_script(
			'cmama-admin',
			'cmamaAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cmama_admin_nonce' ),
			)
		);
	}

	/**
	 * Render dashboard page.
	 *
	 * @since 1.0.0
	 */
	public function render_dashboard_page() {
		$registry            = CMAMA_Calculator_Registry::instance();
		$total_calculators   = count( $registry->get_all() );
		$active_calculators  = count( $registry->get_active() );
		$categories          = $registry->get_categories();

		require_once CMAMA_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Render calculator library page.
	 *
	 * @since 1.0.0
	 */
	public function render_library_page() {
		$registry    = CMAMA_Calculator_Registry::instance();
		$calculators = $registry->get_all();
		$categories  = $registry->get_categories();

		// Get filter parameters.
		$filter_category = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
		$filter_search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		// Apply filters.
		if ( $filter_category ) {
			$calculators = $registry->get_by_category( $filter_category );
		}

		if ( $filter_search ) {
			$calculators = array_filter(
				$calculators,
				function ( $calc ) use ( $filter_search ) {
					return stripos( $calc->get_name(), $filter_search ) !== false ||
						   stripos( $calc->get_description(), $filter_search ) !== false;
				}
			);
		}

		require_once CMAMA_PLUGIN_DIR . 'admin/views/library.php';
	}

	/**
	 * Render appearance settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_appearance_page() {
		$settings   = get_option( 'cmama_settings', array() );
		$appearance = isset( $settings['appearance'] ) ? $settings['appearance'] : array();

		// Set defaults.
		$defaults = array(
			'primary_color'  => '#2271b1',
			'button_color'   => '#2271b1',
			'input_bg'       => '#ffffff',
			'result_bg'      => '#f0f0f1',
			'border_radius'  => '4',
			'padding'        => '20',
			'font_family'    => 'inherit',
			'custom_css'     => '',
		);

		$appearance = wp_parse_args( $appearance, $defaults );

		require_once CMAMA_PLUGIN_DIR . 'admin/views/appearance.php';
	}

	/**
	 * AJAX handler for toggling calculator status.
	 *
	 * @since 1.0.0
	 */
	public function ajax_toggle_calculator() {
		check_ajax_referer( 'cmama_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'calculator-mama' ) ) );
		}

		$slug   = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
		$active = isset( $_POST['active'] ) ? (bool) $_POST['active'] : false;

		if ( empty( $slug ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid calculator slug.', 'calculator-mama' ) ) );
		}

		if ( $active ) {
			CMAMA_Calculator_Manager::activate( $slug );
		} else {
			CMAMA_Calculator_Manager::deactivate( $slug );
		}

		wp_send_json_success();
	}

	/**
	 * AJAX handler for activating all calculators.
	 *
	 * @since 1.0.0
	 */
	public function ajax_activate_all() {
		check_ajax_referer( 'cmama_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'calculator-mama' ) ) );
		}

		CMAMA_Calculator_Manager::activate_all();
		wp_send_json_success();
	}

	/**
	 * AJAX handler for deactivating all calculators.
	 *
	 * @since 1.0.0
	 */
	public function ajax_deactivate_all() {
		check_ajax_referer( 'cmama_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'calculator-mama' ) ) );
		}

		CMAMA_Calculator_Manager::deactivate_all();
		wp_send_json_success();
	}
}
