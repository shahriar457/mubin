<?php
/**
 * Admin dashboard
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard class
 */
class Calculator_Mama_Dashboard {

    /**
     * Render dashboard page
     */
    public function render() {
        $database = new Calculator_Mama_Database();
        $calculator = new Calculator_Mama_Calculator();
        
        $settings = $database->get_settings();
        $calculators = $calculator->get_calculators();
        $active_calculators = $calculator->get_active_calculators();
        
        $categories = array(
            'financial' => __('Financial', 'calculator-mama'),
            'health' => __('Health & Fitness', 'calculator-mama'),
            'math' => __('Math & Science', 'calculator-mama'),
            'other' => __('Other', 'calculator-mama')
        );

        $category_counts = array();
        foreach ($calculators as $calc) {
            $category = $calc['category'];
            if (!isset($category_counts[$category])) {
                $category_counts[$category] = 0;
            }
            $category_counts[$category]++;
        }
        ?>
        <div class="wrap cmama-dashboard">
            <h1><?php _e('Calculator Mama Dashboard', 'calculator-mama'); ?></h1>
            
            <div class="cmama-dashboard-welcome">
                <div class="cmama-welcome-content">
                    <h2><?php _e('Welcome to Calculator Mama!', 'calculator-mama'); ?></h2>
                    <p><?php _e('Your comprehensive library of interactive calculators is ready to help you engage visitors and boost your SEO.', 'calculator-mama'); ?></p>
                </div>
            </div>

            <div class="cmama-dashboard-stats">
                <div class="cmama-stat-card">
                    <div class="cmama-stat-number"><?php echo count($calculators); ?></div>
                    <div class="cmama-stat-label"><?php _e('Total Calculators', 'calculator-mama'); ?></div>
                </div>
                <div class="cmama-stat-card">
                    <div class="cmama-stat-number"><?php echo count($active_calculators); ?></div>
                    <div class="cmama-stat-label"><?php _e('Active Calculators', 'calculator-mama'); ?></div>
                </div>
                <div class="cmama-stat-card">
                    <div class="cmama-stat-number"><?php echo count($categories); ?></div>
                    <div class="cmama-stat-label"><?php _e('Categories', 'calculator-mama'); ?></div>
                </div>
            </div>

            <div class="cmama-dashboard-categories">
                <h3><?php _e('Calculator Categories', 'calculator-mama'); ?></h3>
                <div class="cmama-categories-grid">
                    <?php foreach ($categories as $slug => $name): ?>
                        <div class="cmama-category-card">
                            <div class="cmama-category-icon dashicons dashicons-<?php echo esc_attr($this->get_category_icon($slug)); ?>"></div>
                            <div class="cmama-category-info">
                                <h4><?php echo esc_html($name); ?></h4>
                                <p><?php echo isset($category_counts[$slug]) ? $category_counts[$slug] : 0; ?> <?php _e('calculators', 'calculator-mama'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cmama-dashboard-actions">
                <h3><?php _e('Quick Actions', 'calculator-mama'); ?></h3>
                <div class="cmama-actions-grid">
                    <a href="<?php echo admin_url('admin.php?page=calculator-mama-library'); ?>" class="cmama-action-card">
                        <div class="cmama-action-icon dashicons dashicons-grid-view"></div>
                        <div class="cmama-action-content">
                            <h4><?php _e('Manage Calculators', 'calculator-mama'); ?></h4>
                            <p><?php _e('Activate, deactivate, and organize your calculator library.', 'calculator-mama'); ?></p>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=calculator-mama-appearance'); ?>" class="cmama-action-card">
                        <div class="cmama-action-icon dashicons dashicons-admin-appearance"></div>
                        <div class="cmama-action-content">
                            <h4><?php _e('Customize Appearance', 'calculator-mama'); ?></h4>
                            <p><?php _e('Match calculators to your website\'s design.', 'calculator-mama'); ?></p>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=calculator-mama-seo'); ?>" class="cmama-action-card">
                        <div class="cmama-action-icon dashicons dashicons-search"></div>
                        <div class="cmama-action-content">
                            <h4><?php _e('SEO Settings', 'calculator-mama'); ?></h4>
                            <p><?php _e('Optimize calculators for search engines.', 'calculator-mama'); ?></p>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="cmama-action-card">
                        <div class="cmama-action-icon dashicons dashicons-plus-alt"></div>
                        <div class="cmama-action-content">
                            <h4><?php _e('Add Calculator to Page', 'calculator-mama'); ?></h4>
                            <p><?php _e('Create a new page with a calculator using the Gutenberg block.', 'calculator-mama'); ?></p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="cmama-dashboard-help">
                <h3><?php _e('Getting Started', 'calculator-mama'); ?></h3>
                <div class="cmama-help-content">
                    <ol>
                        <li><?php _e('Go to the Library page to activate the calculators you want to use.', 'calculator-mama'); ?></li>
                        <li><?php _e('Customize the appearance to match your website\'s design.', 'calculator-mama'); ?></li>
                        <li><?php _e('Add calculators to your pages using the Gutenberg block or shortcodes.', 'calculator-mama'); ?></li>
                        <li><?php _e('Configure SEO settings to maximize search engine visibility.', 'calculator-mama'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get category icon
     *
     * @param string $category
     * @return string
     */
    private function get_category_icon($category) {
        $icons = array(
            'financial' => 'money-alt',
            'health' => 'heart',
            'math' => 'calculator',
            'other' => 'admin-tools'
        );

        return isset($icons[$category]) ? $icons[$category] : 'admin-tools';
    }
}