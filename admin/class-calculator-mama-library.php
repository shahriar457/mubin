<?php
/**
 * Calculator library management
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Library class
 */
class Calculator_Mama_Library {

    /**
     * Render library page
     */
    public function render() {
        $database = new Calculator_Mama_Database();
        $calculator = new Calculator_Mama_Calculator();
        
        $calculators = $calculator->get_calculators();
        $settings = $database->get_settings();
        
        // Handle search and filter
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

        // Filter calculators
        $filtered_calculators = $this->filter_calculators($calculators, $search, $category, $status);
        
        $categories = array(
            'all' => __('All Categories', 'calculator-mama'),
            'financial' => __('Financial', 'calculator-mama'),
            'health' => __('Health & Fitness', 'calculator-mama'),
            'math' => __('Math & Science', 'calculator-mama'),
            'other' => __('Other', 'calculator-mama')
        );

        $statuses = array(
            'all' => __('All Status', 'calculator-mama'),
            'active' => __('Active', 'calculator-mama'),
            'inactive' => __('Inactive', 'calculator-mama')
        );
        ?>
        <div class="wrap cmama-library">
            <h1><?php _e('Calculator Library', 'calculator-mama'); ?></h1>
            
            <div class="cmama-library-filters">
                <form method="get" class="cmama-filter-form">
                    <input type="hidden" name="page" value="calculator-mama-library">
                    
                    <div class="cmama-filter-group">
                        <label for="search"><?php _e('Search:', 'calculator-mama'); ?></label>
                        <input type="text" id="search" name="search" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Search calculators...', 'calculator-mama'); ?>">
                    </div>
                    
                    <div class="cmama-filter-group">
                        <label for="category"><?php _e('Category:', 'calculator-mama'); ?></label>
                        <select id="category" name="category">
                            <?php foreach ($categories as $slug => $name): ?>
                                <option value="<?php echo esc_attr($slug); ?>" <?php selected($category, $slug); ?>><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="cmama-filter-group">
                        <label for="status"><?php _e('Status:', 'calculator-mama'); ?></label>
                        <select id="status" name="status">
                            <?php foreach ($statuses as $slug => $name): ?>
                                <option value="<?php echo esc_attr($slug); ?>" <?php selected($status, $slug); ?>><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="cmama-filter-group">
                        <button type="submit" class="button"><?php _e('Filter', 'calculator-mama'); ?></button>
                        <a href="<?php echo admin_url('admin.php?page=calculator-mama-library'); ?>" class="button"><?php _e('Clear', 'calculator-mama'); ?></a>
                    </div>
                </form>
            </div>

            <div class="cmama-library-stats">
                <p><?php printf(__('Showing %d of %d calculators', 'calculator-mama'), count($filtered_calculators), count($calculators)); ?></p>
            </div>

            <div class="cmama-calculators-grid">
                <?php foreach ($filtered_calculators as $slug => $calc): ?>
                    <?php
                    $calculator_settings = isset($settings['calculators'][$slug]) ? $settings['calculators'][$slug] : array();
                    $is_active = isset($calculator_settings['active']) && $calculator_settings['active'];
                    ?>
                    <div class="cmama-calculator-card <?php echo $is_active ? 'active' : 'inactive'; ?>" data-calculator="<?php echo esc_attr($slug); ?>">
                        <div class="cmama-calculator-header">
                            <div class="cmama-calculator-icon dashicons dashicons-<?php echo esc_attr($calc['icon']); ?>"></div>
                            <div class="cmama-calculator-info">
                                <h3 class="cmama-calculator-name"><?php echo esc_html($calc['name']); ?></h3>
                                <p class="cmama-calculator-description"><?php echo esc_html($calc['description']); ?></p>
                            </div>
                        </div>
                        
                        <div class="cmama-calculator-meta">
                            <span class="cmama-calculator-category"><?php echo esc_html($this->get_category_name($calc['category'])); ?></span>
                            <span class="cmama-calculator-version">v<?php echo esc_html($calc['version']); ?></span>
                        </div>
                        
                        <div class="cmama-calculator-actions">
                            <label class="cmama-toggle">
                                <input type="checkbox" class="cmama-toggle-input" <?php checked($is_active); ?>>
                                <span class="cmama-toggle-slider"></span>
                                <span class="cmama-toggle-label"><?php echo $is_active ? __('Active', 'calculator-mama') : __('Inactive', 'calculator-mama'); ?></span>
                            </label>
                            
                            <div class="cmama-calculator-buttons">
                                <button type="button" class="button button-small cmama-preview-btn" data-calculator="<?php echo esc_attr($slug); ?>">
                                    <?php _e('Preview', 'calculator-mama'); ?>
                                </button>
                                <button type="button" class="button button-small cmama-shortcode-btn" data-calculator="<?php echo esc_attr($slug); ?>">
                                    <?php _e('Shortcode', 'calculator-mama'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($filtered_calculators)): ?>
                <div class="cmama-no-results">
                    <p><?php _e('No calculators found matching your criteria.', 'calculator-mama'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Preview Modal -->
        <div id="cmama-preview-modal" class="cmama-modal" style="display: none;">
            <div class="cmama-modal-content">
                <div class="cmama-modal-header">
                    <h3 id="cmama-preview-title"><?php _e('Calculator Preview', 'calculator-mama'); ?></h3>
                    <button type="button" class="cmama-modal-close">&times;</button>
                </div>
                <div class="cmama-modal-body" id="cmama-preview-body">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Shortcode Modal -->
        <div id="cmama-shortcode-modal" class="cmama-modal" style="display: none;">
            <div class="cmama-modal-content">
                <div class="cmama-modal-header">
                    <h3><?php _e('Shortcode', 'calculator-mama'); ?></h3>
                    <button type="button" class="cmama-modal-close">&times;</button>
                </div>
                <div class="cmama-modal-body">
                    <p><?php _e('Use this shortcode to add the calculator to any page or post:', 'calculator-mama'); ?></p>
                    <div class="cmama-shortcode-container">
                        <input type="text" id="cmama-shortcode-input" readonly value="" class="cmama-shortcode-input">
                        <button type="button" class="button cmama-copy-btn" data-target="cmama-shortcode-input">
                            <?php _e('Copy', 'calculator-mama'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Filter calculators based on search, category, and status
     *
     * @param array $calculators
     * @param string $search
     * @param string $category
     * @param string $status
     * @return array
     */
    private function filter_calculators($calculators, $search, $category, $status) {
        $filtered = $calculators;

        // Search filter
        if (!empty($search)) {
            $filtered = array_filter($filtered, function($calc) use ($search) {
                return stripos($calc['name'], $search) !== false || 
                       stripos($calc['description'], $search) !== false;
            });
        }

        // Category filter
        if (!empty($category) && $category !== 'all') {
            $filtered = array_filter($filtered, function($calc) use ($category) {
                return $calc['category'] === $category;
            });
        }

        // Status filter
        if (!empty($status) && $status !== 'all') {
            $database = new Calculator_Mama_Database();
            $settings = $database->get_settings();
            
            $filtered = array_filter($filtered, function($calc) use ($status, $settings) {
                $calculator_settings = isset($settings['calculators'][$calc['slug']]) ? $settings['calculators'][$calc['slug']] : array();
                $is_active = isset($calculator_settings['active']) && $calculator_settings['active'];
                
                return ($status === 'active' && $is_active) || ($status === 'inactive' && !$is_active);
            });
        }

        return $filtered;
    }

    /**
     * Get category name
     *
     * @param string $category
     * @return string
     */
    private function get_category_name($category) {
        $categories = array(
            'financial' => __('Financial', 'calculator-mama'),
            'health' => __('Health & Fitness', 'calculator-mama'),
            'math' => __('Math & Science', 'calculator-mama'),
            'other' => __('Other', 'calculator-mama')
        );

        return isset($categories[$category]) ? $categories[$category] : ucfirst($category);
    }
}