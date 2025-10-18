<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Plugin {
    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Core includes that are not autoloaded
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-settings.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-registry.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-assets.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-seo.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-block.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-shortcode.php';
        require_once CMAMA_PLUGIN_DIR . 'includes/class-cmama-admin.php';

        add_action( 'init', [ $this, 'init' ] );
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_action( 'admin_menu', [ 'CMama_Admin', 'register_menus' ] );
        add_action( 'admin_enqueue_scripts', [ 'CMama_Admin', 'enqueue_admin_assets' ] );
    }

    public function init() {
        CMama_Assets::register();
        CMama_Block::register();
        // Localize editor data for the block
        add_action( 'enqueue_block_editor_assets', [ 'CMama_Block', 'localize_editor' ] );
        CMama_Shortcode::register();
        CMama_SEO::register();

        // Frontend CSS variables from settings
        add_action( 'wp_head', [ 'CMama_Assets', 'output_css_variables' ], 20 );
    }

    public function admin_init() {
        CMama_Admin::register_settings();
        CMama_Admin::maybe_handle_post();
    }
}
