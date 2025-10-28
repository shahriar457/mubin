<?php
/**
 * The main plugin class
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Calculator Mama class
 */
class Calculator_Mama {

    /**
     * Plugin instance
     *
     * @var Calculator_Mama
     */
    private static $instance = null;

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = CMAMA_VERSION;

    /**
     * Get plugin instance
     *
     * @return Calculator_Mama
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define plugin constants
     */
    private function define_constants() {
        $this->define('CMAMA_ABSPATH', CMAMA_PLUGIN_DIR . 'includes/');
        $this->define('CMAMA_ADMIN_DIR', CMAMA_PLUGIN_DIR . 'admin/');
        $this->define('CMAMA_PUBLIC_DIR', CMAMA_PLUGIN_DIR . 'public/');
        $this->define('CMAMA_CALCULATORS_DIR', CMAMA_PLUGIN_DIR . 'calculators/');
        $this->define('CMAMA_ASSETS_URL', CMAMA_PLUGIN_URL . 'assets/');
    }

    /**
     * Define constant if not already set
     *
     * @param string $name
     * @param string $value
     */
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Include required files
     */
    private function includes() {
        // Core classes
        require_once CMAMA_ABSPATH . 'class-calculator-mama-i18n.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-loader.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-database.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-calculator.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-seo.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-styles.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-shortcode.php';
        require_once CMAMA_ABSPATH . 'class-calculator-mama-gutenberg.php';

        // Admin classes
        if (is_admin()) {
            require_once CMAMA_ADMIN_DIR . 'class-calculator-mama-admin.php';
            require_once CMAMA_ADMIN_DIR . 'class-calculator-mama-dashboard.php';
            require_once CMAMA_ADMIN_DIR . 'class-calculator-mama-library.php';
            require_once CMAMA_ADMIN_DIR . 'class-calculator-mama-appearance.php';
        }

        // Public classes
        if (!is_admin() || wp_doing_ajax()) {
            require_once CMAMA_PUBLIC_DIR . 'class-calculator-mama-public.php';
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'), 0);
        add_action('init', array($this, 'load_textdomain'));
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize components
        $this->loader = new Calculator_Mama_Loader();
        $this->database = new Calculator_Mama_Database();
        $this->calculator = new Calculator_Mama_Calculator();
        $this->seo = new Calculator_Mama_SEO();
        $this->styles = new Calculator_Mama_Styles();
        $this->shortcode = new Calculator_Mama_Shortcode();
        $this->gutenberg = new Calculator_Mama_Gutenberg();

        // Initialize admin
        if (is_admin()) {
            $this->admin = new Calculator_Mama_Admin();
        }

        // Initialize public
        if (!is_admin() || wp_doing_ajax()) {
            $this->public = new Calculator_Mama_Public();
        }

        // Run the loader
        $this->loader->run();
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        $i18n = new Calculator_Mama_i18n();
        $i18n->load_plugin_textdomain();
    }

    /**
     * Get plugin version
     *
     * @return string
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Get plugin name
     *
     * @return string
     */
    public function get_plugin_name() {
        return 'Calculator Mama';
    }
}