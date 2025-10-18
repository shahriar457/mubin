<?php
/**
 * Admin Class
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Class
 *
 * Handles all admin functionality.
 *
 * @since 1.0.0
 */
class CMAMA_Admin {

    /**
     * Admin instance.
     *
     * @since 1.0.0
     * @var CMAMA_Admin
     */
    private static $instance = null;

    /**
     * Get admin instance.
     *
     * @since 1.0.0
     * @return CMAMA_Admin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // AJAX hooks
        add_action('wp_ajax_cmama_toggle_calculator', array($this, 'ajax_toggle_calculator'));
        add_action('wp_ajax_cmama_save_appearance', array($this, 'ajax_save_appearance'));
        add_action('wp_ajax_cmama_get_calculator_preview', array($this, 'ajax_get_calculator_preview'));
        add_action('wp_ajax_cmama_search_calculators', array($this, 'ajax_search_calculators'));

        // Admin notices
        add_action('admin_notices', array($this, 'admin_notices'));

        // Add settings link to plugins page
        add_filter('plugin_action_links_' . CMAMA_PLUGIN_BASENAME, array($this, 'plugin_action_links'));

        // Add admin body class
        add_filter('admin_body_class', array($this, 'admin_body_class'));
    }

    /**
     * AJAX handler for toggling calculator status.
     *
     * @since 1.0.0
     */
    public function ajax_toggle_calculator() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_die(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator_slug']);
        $action = sanitize_text_field($_POST['action_type']); // 'activate' or 'deactivate'

        $registry = CMAMA_Calculator_Registry::get_instance();
        
        if (!$registry->calculator_exists($calculator_slug)) {
            wp_send_json_error(__('Calculator not found.', CMAMA_TEXT_DOMAIN));
        }

        $settings = get_option('cmama_settings', array());
        $active_calculators = isset($settings['active_calculators']) ? $settings['active_calculators'] : array();

        if ($action === 'activate') {
            if (!in_array($calculator_slug, $active_calculators)) {
                $active_calculators[] = $calculator_slug;
            }
            $message = __('Calculator activated successfully.', CMAMA_TEXT_DOMAIN);
        } else {
            $key = array_search($calculator_slug, $active_calculators);
            if ($key !== false) {
                unset($active_calculators[$key]);
                $active_calculators = array_values($active_calculators);
            }
            $message = __('Calculator deactivated successfully.', CMAMA_TEXT_DOMAIN);
        }

        $settings['active_calculators'] = $active_calculators;
        update_option('cmama_settings', $settings);

        wp_send_json_success(array(
            'message' => $message,
            'active' => $action === 'activate'
        ));
    }

    /**
     * AJAX handler for saving appearance settings.
     *
     * @since 1.0.0
     */
    public function ajax_save_appearance() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_die(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $appearance_data = array();

        // Sanitize appearance settings
        if (isset($_POST['primary_color'])) {
            $appearance_data['primary_color'] = sanitize_hex_color($_POST['primary_color']);
        }
        if (isset($_POST['button_color'])) {
            $appearance_data['button_color'] = sanitize_hex_color($_POST['button_color']);
        }
        if (isset($_POST['input_color'])) {
            $appearance_data['input_color'] = sanitize_hex_color($_POST['input_color']);
        }
        if (isset($_POST['result_color'])) {
            $appearance_data['result_color'] = sanitize_hex_color($_POST['result_color']);
        }
        if (isset($_POST['font_family'])) {
            $appearance_data['font_family'] = sanitize_text_field($_POST['font_family']);
        }
        if (isset($_POST['border_radius'])) {
            $appearance_data['border_radius'] = absint($_POST['border_radius']);
        }
        if (isset($_POST['padding'])) {
            $appearance_data['padding'] = absint($_POST['padding']);
        }
        if (isset($_POST['custom_css'])) {
            $appearance_data['custom_css'] = wp_strip_all_tags($_POST['custom_css']);
        }

        // Update settings
        $settings = get_option('cmama_settings', array());
        $settings['appearance'] = array_merge(
            isset($settings['appearance']) ? $settings['appearance'] : array(),
            $appearance_data
        );

        $result = update_option('cmama_settings', $settings);

        if ($result) {
            wp_send_json_success(__('Appearance settings saved successfully.', CMAMA_TEXT_DOMAIN));
        } else {
            wp_send_json_error(__('Failed to save appearance settings.', CMAMA_TEXT_DOMAIN));
        }
    }

    /**
     * AJAX handler for getting calculator preview.
     *
     * @since 1.0.0
     */
    public function ajax_get_calculator_preview() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_die(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $calculator_slug = sanitize_text_field($_POST['calculator_slug']);
        
        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculator = $registry->get_calculator($calculator_slug);

        if (!$calculator) {
            wp_send_json_error(__('Calculator not found.', CMAMA_TEXT_DOMAIN));
        }

        // Render calculator preview
        $preview_html = $calculator->render(array(
            'show_title' => true,
            'show_description' => true,
            'css_class' => 'cmama-preview'
        ));

        wp_send_json_success(array(
            'html' => $preview_html,
            'calculator' => array(
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category()
            )
        ));
    }

    /**
     * AJAX handler for searching calculators.
     *
     * @since 1.0.0
     */
    public function ajax_search_calculators() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'cmama_admin_nonce')) {
            wp_die(__('Security check failed.', CMAMA_TEXT_DOMAIN));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', CMAMA_TEXT_DOMAIN));
        }

        $search_term = sanitize_text_field($_POST['search']);
        $category = sanitize_text_field($_POST['category']);

        $args = array();
        if (!empty($search_term)) {
            $args['search'] = $search_term;
        }
        if (!empty($category)) {
            $args['category'] = $category;
        }

        $registry = CMAMA_Calculator_Registry::get_instance();
        $calculators = $registry->get_calculators($args);

        $results = array();
        $settings = get_option('cmama_settings', array());
        $active_calculators = isset($settings['active_calculators']) ? $settings['active_calculators'] : array();

        foreach ($calculators as $slug => $calculator) {
            $results[] = array(
                'slug' => $calculator->get_slug(),
                'name' => $calculator->get_name(),
                'description' => $calculator->get_description(),
                'category' => $calculator->get_category(),
                'active' => in_array($slug, $active_calculators),
                'shortcode' => '[cmama_calculator slug="' . $slug . '"]'
            );
        }

        wp_send_json_success($results);
    }

    /**
     * Display admin notices.
     *
     * @since 1.0.0
     */
    public function admin_notices() {
        // Check if this is a Calculator Mama admin page
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'calculator-mama') === false) {
            return;
        }

        // Welcome notice for new installations
        if (get_option('cmama_show_welcome_notice', true)) {
            ?>
            <div class="notice notice-info is-dismissible cmama-welcome-notice">
                <h3><?php _e('Welcome to Calculator Mama!', CMAMA_TEXT_DOMAIN); ?></h3>
                <p><?php _e('Thank you for installing Calculator Mama. Get started by activating some calculators in the Library section.', CMAMA_TEXT_DOMAIN); ?></p>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=calculator-mama-library'); ?>" class="button button-primary">
                        <?php _e('View Calculator Library', CMAMA_TEXT_DOMAIN); ?>
                    </a>
                    <a href="#" class="button cmama-dismiss-welcome">
                        <?php _e('Dismiss', CMAMA_TEXT_DOMAIN); ?>
                    </a>
                </p>
            </div>
            <script>
            jQuery(document).ready(function($) {
                $('.cmama-dismiss-welcome').on('click', function(e) {
                    e.preventDefault();
                    $.post(ajaxurl, {
                        action: 'cmama_dismiss_welcome',
                        nonce: '<?php echo wp_create_nonce('cmama_admin_nonce'); ?>'
                    });
                    $('.cmama-welcome-notice').fadeOut();
                });
            });
            </script>
            <?php
        }

        // Check for missing dependencies
        $this->check_dependencies();
    }

    /**
     * Check for missing dependencies.
     *
     * @since 1.0.0
     */
    private function check_dependencies() {
        $missing_deps = array();

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $missing_deps[] = sprintf(
                __('PHP version 7.4 or higher is required. You are running PHP %s.', CMAMA_TEXT_DOMAIN),
                PHP_VERSION
            );
        }

        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '5.0', '<')) {
            $missing_deps[] = sprintf(
                __('WordPress version 5.0 or higher is required. You are running WordPress %s.', CMAMA_TEXT_DOMAIN),
                get_bloginfo('version')
            );
        }

        if (!empty($missing_deps)) {
            ?>
            <div class="notice notice-error">
                <h3><?php _e('Calculator Mama - Missing Requirements', CMAMA_TEXT_DOMAIN); ?></h3>
                <ul>
                    <?php foreach ($missing_deps as $dep): ?>
                        <li><?php echo esc_html($dep); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }
    }

    /**
     * Add plugin action links.
     *
     * @since 1.0.0
     * @param array $links Existing links.
     * @return array Modified links.
     */
    public function plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=calculator-mama') . '">' . __('Settings', CMAMA_TEXT_DOMAIN) . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add admin body class.
     *
     * @since 1.0.0
     * @param string $classes Existing classes.
     * @return string Modified classes.
     */
    public function admin_body_class($classes) {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'calculator-mama') !== false) {
            $classes .= ' calculator-mama-admin';
        }
        return $classes;
    }

    /**
     * Get dashboard statistics.
     *
     * @since 1.0.0
     * @return array Dashboard statistics.
     */
    public function get_dashboard_stats() {
        $registry = CMAMA_Calculator_Registry::get_instance();
        $stats = $registry->get_statistics();

        // Add usage statistics if available
        $usage_stats = cmama_get_usage_stats('', array('days' => 30));
        $total_usage = 0;
        $popular_calculators = array();

        if (!empty($usage_stats)) {
            $calculator_usage = array();
            foreach ($usage_stats as $stat) {
                $total_usage += $stat->usage_count;
                if (!isset($calculator_usage[$stat->calculator_slug])) {
                    $calculator_usage[$stat->calculator_slug] = 0;
                }
                $calculator_usage[$stat->calculator_slug] += $stat->usage_count;
            }

            // Sort by usage count
            arsort($calculator_usage);
            $popular_calculators = array_slice($calculator_usage, 0, 5, true);
        }

        $stats['total_usage_30_days'] = $total_usage;
        $stats['popular_calculators'] = $popular_calculators;

        return $stats;
    }

    /**
     * Get recent activity.
     *
     * @since 1.0.0
     * @return array Recent activity data.
     */
    public function get_recent_activity() {
        $activity = array();

        // Get recent calculator activations/deactivations from options
        $recent_changes = get_option('cmama_recent_changes', array());
        
        // Limit to last 10 activities
        $activity = array_slice($recent_changes, -10, 10, true);

        return array_reverse($activity);
    }

    /**
     * Log activity.
     *
     * @since 1.0.0
     * @param string $action Action performed.
     * @param string $calculator_slug Calculator slug.
     * @param array $extra_data Extra data.
     */
    public function log_activity($action, $calculator_slug = '', $extra_data = array()) {
        $recent_changes = get_option('cmama_recent_changes', array());
        
        $activity = array(
            'action' => $action,
            'calculator_slug' => $calculator_slug,
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql'),
            'extra_data' => $extra_data
        );

        $recent_changes[] = $activity;

        // Keep only last 50 activities
        if (count($recent_changes) > 50) {
            $recent_changes = array_slice($recent_changes, -50, 50, true);
        }

        update_option('cmama_recent_changes', $recent_changes);
    }

    /**
     * Export settings.
     *
     * @since 1.0.0
     * @return array Settings data for export.
     */
    public function export_settings() {
        $settings = get_option('cmama_settings', array());
        
        // Remove sensitive data
        unset($settings['license_key']);
        
        return array(
            'version' => CMAMA_VERSION,
            'export_date' => current_time('mysql'),
            'settings' => $settings
        );
    }

    /**
     * Import settings.
     *
     * @since 1.0.0
     * @param array $import_data Import data.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    public function import_settings($import_data) {
        if (!isset($import_data['settings']) || !is_array($import_data['settings'])) {
            return new WP_Error('invalid_data', __('Invalid import data.', CMAMA_TEXT_DOMAIN));
        }

        $current_settings = get_option('cmama_settings', array());
        $import_settings = $import_data['settings'];

        // Merge settings (keep current license key if exists)
        if (isset($current_settings['license_key'])) {
            $import_settings['license_key'] = $current_settings['license_key'];
        }

        $result = update_option('cmama_settings', $import_settings);

        if ($result) {
            $this->log_activity('settings_imported');
            return true;
        }

        return new WP_Error('import_failed', __('Failed to import settings.', CMAMA_TEXT_DOMAIN));
    }
}