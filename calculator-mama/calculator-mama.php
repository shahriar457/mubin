<?php
/**
 * Plugin Name:       Calculator Mama
 * Plugin URI:        https://calculatormama.com
 * Description:       A comprehensive library of 150+ interactive calculators with built-in SEO engine to transform your site into an organic traffic powerhouse.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Calculator Mama
 * Author URI:        https://calculatormama.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       calculator-mama
 * Domain Path:       /languages
 *
 * @package CalculatorMama
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'CMAMA_VERSION', '1.0.0' );
define( 'CMAMA_PLUGIN_FILE', __FILE__ );
define( 'CMAMA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CMAMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CMAMA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Calculator Mama Class.
 *
 * This is the main class that initializes the plugin and loads all components.
 *
 * @since 1.0.0
 */
final class Calculator_Mama {

	/**
	 * Single instance of the class.
	 *
	 * @var Calculator_Mama
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Calculator_Mama
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
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// Core classes.
		require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-manager.php';
		require_once CMAMA_PLUGIN_DIR . 'includes/class-admin-settings.php';
		require_once CMAMA_PLUGIN_DIR . 'includes/class-shortcode.php';
		require_once CMAMA_PLUGIN_DIR . 'includes/class-seo-engine.php';
		require_once CMAMA_PLUGIN_DIR . 'includes/class-gutenberg-block.php';
		require_once CMAMA_PLUGIN_DIR . 'includes/class-frontend-renderer.php';

		// Calculator modules.
		require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-base.php';
		require_once CMAMA_PLUGIN_DIR . 'includes/class-calculator-registry.php';
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Load plugin textdomain for translations.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Initialize plugin components.
		add_action( 'plugins_loaded', array( $this, 'init_components' ) );

		// Register activation and deactivation hooks.
		register_activation_hook( CMAMA_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( CMAMA_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Load plugin textdomain for translations.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'calculator-mama',
			false,
			dirname( CMAMA_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Initialize plugin components.
	 *
	 * @since 1.0.0
	 */
	public function init_components() {
		// Initialize calculator registry.
		CMAMA_Calculator_Registry::instance();

		// Initialize admin settings.
		if ( is_admin() ) {
			CMAMA_Admin_Settings::instance();
		}

		// Initialize shortcode handler.
		CMAMA_Shortcode::instance();

		// Initialize SEO engine.
		CMAMA_SEO_Engine::instance();

		// Initialize Gutenberg block.
		CMAMA_Gutenberg_Block::instance();

		// Initialize frontend renderer.
		CMAMA_Frontend_Renderer::instance();
	}

	/**
	 * Plugin activation callback.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		// Set default options on activation.
		$default_settings = array(
			'active_calculators' => array(),
			'appearance'         => array(
				'primary_color'   => '#2271b1',
				'button_color'    => '#2271b1',
				'input_bg'        => '#ffffff',
				'result_bg'       => '#f0f0f1',
				'border_radius'   => '4',
				'padding'         => '20',
				'font_family'     => 'inherit',
				'custom_css'      => '',
			),
		);

		if ( ! get_option( 'cmama_settings' ) ) {
			add_option( 'cmama_settings', $default_settings );
		}

		// Set a transient to redirect to welcome page.
		set_transient( 'cmama_activation_redirect', true, 30 );
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		// Clean up transients.
		delete_transient( 'cmama_activation_redirect' );
	}
}

/**
 * Initialize the plugin.
 *
 * @return Calculator_Mama
 */
function cmama() {
	return Calculator_Mama::instance();
}

// Start the plugin.
cmama();
