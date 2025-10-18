<?php
/**
 * Admin Dashboard View
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get dashboard statistics
$admin = CMAMA_Admin::get_instance();
$stats = $admin->get_dashboard_stats();
$recent_activity = $admin->get_recent_activity();
$registry = CMAMA_Calculator_Registry::get_instance();
?>

<div class="wrap cmama-dashboard">
    <h1 class="wp-heading-inline">
        <?php echo esc_html(get_admin_page_title()); ?>
        <span class="cmama-version">v<?php echo CMAMA_VERSION; ?></span>
    </h1>

    <div class="cmama-dashboard-content">
        <!-- Welcome Section -->
        <div class="cmama-welcome-section">
            <div class="cmama-welcome-card">
                <div class="cmama-welcome-content">
                    <h2><?php _e('Welcome to Calculator Mama', CMAMA_TEXT_DOMAIN); ?></h2>
                    <p><?php _e('Transform your website into a powerful content marketing engine with over 150 interactive calculators, each optimized for search engines and designed to engage your visitors.', CMAMA_TEXT_DOMAIN); ?></p>
                    
                    <div class="cmama-quick-actions">
                        <a href="<?php echo admin_url('admin.php?page=calculator-mama-library'); ?>" class="button button-primary button-large">
                            <span class="dashicons dashicons-calculator"></span>
                            <?php _e('Browse Calculator Library', CMAMA_TEXT_DOMAIN); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=calculator-mama-appearance'); ?>" class="button button-secondary button-large">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php _e('Customize Appearance', CMAMA_TEXT_DOMAIN); ?>
                        </a>
                    </div>
                </div>
                <div class="cmama-welcome-image">
                    <img src="<?php echo CMAMA_PLUGIN_URL; ?>assets/images/calculator-preview.png" alt="<?php _e('Calculator Preview', CMAMA_TEXT_DOMAIN); ?>" />
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="cmama-stats-grid">
            <div class="cmama-stat-card">
                <div class="cmama-stat-icon">
                    <span class="dashicons dashicons-calculator"></span>
                </div>
                <div class="cmama-stat-content">
                    <h3><?php echo number_format($stats['total_calculators']); ?></h3>
                    <p><?php _e('Total Calculators', CMAMA_TEXT_DOMAIN); ?></p>
                </div>
            </div>

            <div class="cmama-stat-card active">
                <div class="cmama-stat-icon">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="cmama-stat-content">
                    <h3><?php echo number_format($stats['active_calculators']); ?></h3>
                    <p><?php _e('Active Calculators', CMAMA_TEXT_DOMAIN); ?></p>
                </div>
            </div>

            <div class="cmama-stat-card">
                <div class="cmama-stat-icon">
                    <span class="dashicons dashicons-category"></span>
                </div>
                <div class="cmama-stat-content">
                    <h3><?php echo count($stats['categories']); ?></h3>
                    <p><?php _e('Categories', CMAMA_TEXT_DOMAIN); ?></p>
                </div>
            </div>

            <div class="cmama-stat-card">
                <div class="cmama-stat-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="cmama-stat-content">
                    <h3><?php echo number_format($stats['total_usage_30_days']); ?></h3>
                    <p><?php _e('Usage (30 days)', CMAMA_TEXT_DOMAIN); ?></p>
                </div>
            </div>
        </div>

        <div class="cmama-dashboard-grid">
            <!-- Categories Overview -->
            <div class="cmama-dashboard-widget">
                <h3><?php _e('Calculator Categories', CMAMA_TEXT_DOMAIN); ?></h3>
                <div class="cmama-categories-list">
                    <?php foreach ($stats['categories'] as $category_slug => $category): ?>
                        <div class="cmama-category-item">
                            <div class="cmama-category-icon">
                                <span class="dashicons <?php echo esc_attr($category['icon']); ?>"></span>
                            </div>
                            <div class="cmama-category-content">
                                <h4><?php echo esc_html($category['name']); ?></h4>
                                <p><?php echo esc_html($category['description']); ?></p>
                                <span class="cmama-category-count">
                                    <?php 
                                    $count = isset($stats['category_counts'][$category_slug]) ? $stats['category_counts'][$category_slug] : 0;
                                    printf(_n('%d calculator', '%d calculators', $count, CMAMA_TEXT_DOMAIN), $count);
                                    ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Popular Calculators -->
            <div class="cmama-dashboard-widget">
                <h3><?php _e('Popular Calculators', CMAMA_TEXT_DOMAIN); ?></h3>
                <?php if (!empty($stats['popular_calculators'])): ?>
                    <div class="cmama-popular-list">
                        <?php foreach ($stats['popular_calculators'] as $slug => $usage_count): ?>
                            <?php $calculator = $registry->get_calculator($slug); ?>
                            <?php if ($calculator): ?>
                                <div class="cmama-popular-item">
                                    <div class="cmama-popular-content">
                                        <h4><?php echo esc_html($calculator->get_name()); ?></h4>
                                        <p><?php echo esc_html($calculator->get_description()); ?></p>
                                        <span class="cmama-popular-category">
                                            <?php echo esc_html(ucfirst($calculator->get_category())); ?>
                                        </span>
                                    </div>
                                    <div class="cmama-popular-usage">
                                        <span class="cmama-usage-count"><?php echo number_format($usage_count); ?></span>
                                        <span class="cmama-usage-label"><?php _e('uses', CMAMA_TEXT_DOMAIN); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="cmama-empty-state">
                        <p><?php _e('No usage data available yet. Activate some calculators to see statistics.', CMAMA_TEXT_DOMAIN); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=calculator-mama-library'); ?>" class="button">
                            <?php _e('Activate Calculators', CMAMA_TEXT_DOMAIN); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Activity -->
            <div class="cmama-dashboard-widget">
                <h3><?php _e('Recent Activity', CMAMA_TEXT_DOMAIN); ?></h3>
                <?php if (!empty($recent_activity)): ?>
                    <div class="cmama-activity-list">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="cmama-activity-item">
                                <div class="cmama-activity-icon">
                                    <?php
                                    $icon = 'admin-generic';
                                    switch ($activity['action']) {
                                        case 'calculator_activated':
                                            $icon = 'yes-alt';
                                            break;
                                        case 'calculator_deactivated':
                                            $icon = 'dismiss';
                                            break;
                                        case 'settings_updated':
                                            $icon = 'admin-settings';
                                            break;
                                    }
                                    ?>
                                    <span class="dashicons dashicons-<?php echo esc_attr($icon); ?>"></span>
                                </div>
                                <div class="cmama-activity-content">
                                    <p>
                                        <?php
                                        switch ($activity['action']) {
                                            case 'calculator_activated':
                                                printf(__('Calculator "%s" was activated', CMAMA_TEXT_DOMAIN), esc_html($activity['calculator_slug']));
                                                break;
                                            case 'calculator_deactivated':
                                                printf(__('Calculator "%s" was deactivated', CMAMA_TEXT_DOMAIN), esc_html($activity['calculator_slug']));
                                                break;
                                            case 'settings_updated':
                                                _e('Settings were updated', CMAMA_TEXT_DOMAIN);
                                                break;
                                            default:
                                                echo esc_html($activity['action']);
                                                break;
                                        }
                                        ?>
                                    </p>
                                    <span class="cmama-activity-time">
                                        <?php echo human_time_diff(strtotime($activity['timestamp']), current_time('timestamp')) . ' ' . __('ago', CMAMA_TEXT_DOMAIN); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="cmama-empty-state">
                        <p><?php _e('No recent activity to display.', CMAMA_TEXT_DOMAIN); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Start Guide -->
            <div class="cmama-dashboard-widget">
                <h3><?php _e('Quick Start Guide', CMAMA_TEXT_DOMAIN); ?></h3>
                <div class="cmama-quick-start">
                    <div class="cmama-step">
                        <div class="cmama-step-number">1</div>
                        <div class="cmama-step-content">
                            <h4><?php _e('Activate Calculators', CMAMA_TEXT_DOMAIN); ?></h4>
                            <p><?php _e('Browse the calculator library and activate the ones you want to use on your website.', CMAMA_TEXT_DOMAIN); ?></p>
                            <a href="<?php echo admin_url('admin.php?page=calculator-mama-library'); ?>" class="cmama-step-link">
                                <?php _e('Go to Library', CMAMA_TEXT_DOMAIN); ?>
                            </a>
                        </div>
                    </div>

                    <div class="cmama-step">
                        <div class="cmama-step-number">2</div>
                        <div class="cmama-step-content">
                            <h4><?php _e('Customize Appearance', CMAMA_TEXT_DOMAIN); ?></h4>
                            <p><?php _e('Match your brand by customizing colors, fonts, and styling of the calculators.', CMAMA_TEXT_DOMAIN); ?></p>
                            <a href="<?php echo admin_url('admin.php?page=calculator-mama-appearance'); ?>" class="cmama-step-link">
                                <?php _e('Customize Now', CMAMA_TEXT_DOMAIN); ?>
                            </a>
                        </div>
                    </div>

                    <div class="cmama-step">
                        <div class="cmama-step-number">3</div>
                        <div class="cmama-step-content">
                            <h4><?php _e('Add to Pages', CMAMA_TEXT_DOMAIN); ?></h4>
                            <p><?php _e('Use the Gutenberg block or shortcode to add calculators to your posts and pages.', CMAMA_TEXT_DOMAIN); ?></p>
                            <a href="<?php echo admin_url('post-new.php'); ?>" class="cmama-step-link">
                                <?php _e('Create New Post', CMAMA_TEXT_DOMAIN); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="cmama-dashboard-widget">
                <h3><?php _e('System Information', CMAMA_TEXT_DOMAIN); ?></h3>
                <div class="cmama-system-info">
                    <div class="cmama-info-item">
                        <span class="cmama-info-label"><?php _e('Plugin Version:', CMAMA_TEXT_DOMAIN); ?></span>
                        <span class="cmama-info-value"><?php echo CMAMA_VERSION; ?></span>
                    </div>
                    <div class="cmama-info-item">
                        <span class="cmama-info-label"><?php _e('WordPress Version:', CMAMA_TEXT_DOMAIN); ?></span>
                        <span class="cmama-info-value"><?php echo get_bloginfo('version'); ?></span>
                    </div>
                    <div class="cmama-info-item">
                        <span class="cmama-info-label"><?php _e('PHP Version:', CMAMA_TEXT_DOMAIN); ?></span>
                        <span class="cmama-info-value"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="cmama-info-item">
                        <span class="cmama-info-label"><?php _e('Active Theme:', CMAMA_TEXT_DOMAIN); ?></span>
                        <span class="cmama-info-value"><?php echo wp_get_theme()->get('Name'); ?></span>
                    </div>
                </div>

                <div class="cmama-system-actions">
                    <button type="button" class="button" id="cmama-export-settings">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Export Settings', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                    <button type="button" class="button" id="cmama-import-settings">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Import Settings', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Settings Modal -->
<div id="cmama-import-modal" class="cmama-modal" style="display: none;">
    <div class="cmama-modal-content">
        <div class="cmama-modal-header">
            <h3><?php _e('Import Settings', CMAMA_TEXT_DOMAIN); ?></h3>
            <button type="button" class="cmama-modal-close">&times;</button>
        </div>
        <div class="cmama-modal-body">
            <p><?php _e('Select a Calculator Mama settings file to import:', CMAMA_TEXT_DOMAIN); ?></p>
            <input type="file" id="cmama-import-file" accept=".json" />
            <div class="cmama-import-preview" style="display: none;">
                <h4><?php _e('Import Preview:', CMAMA_TEXT_DOMAIN); ?></h4>
                <div class="cmama-import-details"></div>
            </div>
        </div>
        <div class="cmama-modal-footer">
            <button type="button" class="button button-primary" id="cmama-confirm-import" disabled>
                <?php _e('Import Settings', CMAMA_TEXT_DOMAIN); ?>
            </button>
            <button type="button" class="button cmama-modal-close">
                <?php _e('Cancel', CMAMA_TEXT_DOMAIN); ?>
            </button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Export settings
    $('#cmama-export-settings').on('click', function() {
        var data = {
            action: 'cmama_export_settings',
            nonce: cmamaAdmin.nonce
        };

        $.post(cmamaAdmin.ajaxUrl, data, function(response) {
            if (response.success) {
                var dataStr = JSON.stringify(response.data, null, 2);
                var dataBlob = new Blob([dataStr], {type: 'application/json'});
                var url = URL.createObjectURL(dataBlob);
                var link = document.createElement('a');
                link.href = url;
                link.download = 'calculator-mama-settings-' + new Date().toISOString().slice(0, 10) + '.json';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            } else {
                alert(response.data || 'Export failed');
            }
        });
    });

    // Import settings modal
    $('#cmama-import-settings').on('click', function() {
        $('#cmama-import-modal').show();
    });

    $('.cmama-modal-close').on('click', function() {
        $('#cmama-import-modal').hide();
        $('#cmama-import-file').val('');
        $('.cmama-import-preview').hide();
        $('#cmama-confirm-import').prop('disabled', true);
    });

    // File selection for import
    $('#cmama-import-file').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                try {
                    var importData = JSON.parse(e.target.result);
                    if (importData.settings) {
                        $('.cmama-import-details').html(
                            '<p><strong>Version:</strong> ' + (importData.version || 'Unknown') + '</p>' +
                            '<p><strong>Export Date:</strong> ' + (importData.export_date || 'Unknown') + '</p>' +
                            '<p><strong>Settings:</strong> ' + Object.keys(importData.settings).length + ' items</p>'
                        );
                        $('.cmama-import-preview').show();
                        $('#cmama-confirm-import').prop('disabled', false);
                    } else {
                        alert('Invalid settings file format');
                    }
                } catch (error) {
                    alert('Invalid JSON file');
                }
            };
            reader.readAsText(file);
        }
    });

    // Confirm import
    $('#cmama-confirm-import').on('click', function() {
        var file = $('#cmama-import-file')[0].files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            var data = {
                action: 'cmama_import_settings',
                nonce: cmamaAdmin.nonce,
                import_data: e.target.result
            };

            $.post(cmamaAdmin.ajaxUrl, data, function(response) {
                if (response.success) {
                    alert('Settings imported successfully!');
                    location.reload();
                } else {
                    alert(response.data || 'Import failed');
                }
            });
        };
        reader.readAsText(file);
    });
});
</script>