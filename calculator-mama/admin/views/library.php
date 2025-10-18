<?php
/**
 * Admin Calculator Library View
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get calculators and settings
$registry = CMAMA_Calculator_Registry::get_instance();
$calculators = $registry->get_calculators_for_admin();
$categories = $registry->get_categories();
$stats = $registry->get_statistics();
?>

<div class="wrap cmama-library">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="cmama-library-header">
        <div class="cmama-library-stats">
            <span class="cmama-stat">
                <strong><?php echo number_format($stats['total_calculators']); ?></strong>
                <?php _e('Total Calculators', CMAMA_TEXT_DOMAIN); ?>
            </span>
            <span class="cmama-stat">
                <strong><?php echo number_format($stats['active_calculators']); ?></strong>
                <?php _e('Active', CMAMA_TEXT_DOMAIN); ?>
            </span>
            <span class="cmama-stat">
                <strong><?php echo count($categories); ?></strong>
                <?php _e('Categories', CMAMA_TEXT_DOMAIN); ?>
            </span>
        </div>

        <div class="cmama-library-actions">
            <button type="button" class="button" id="cmama-activate-all">
                <?php _e('Activate All', CMAMA_TEXT_DOMAIN); ?>
            </button>
            <button type="button" class="button" id="cmama-deactivate-all">
                <?php _e('Deactivate All', CMAMA_TEXT_DOMAIN); ?>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="cmama-library-filters">
        <div class="cmama-filter-group">
            <label for="cmama-search"><?php _e('Search:', CMAMA_TEXT_DOMAIN); ?></label>
            <input type="text" id="cmama-search" placeholder="<?php _e('Search calculators...', CMAMA_TEXT_DOMAIN); ?>" />
        </div>

        <div class="cmama-filter-group">
            <label for="cmama-category-filter"><?php _e('Category:', CMAMA_TEXT_DOMAIN); ?></label>
            <select id="cmama-category-filter">
                <option value=""><?php _e('All Categories', CMAMA_TEXT_DOMAIN); ?></option>
                <?php foreach ($categories as $category_slug => $category): ?>
                    <option value="<?php echo esc_attr($category_slug); ?>">
                        <?php echo esc_html($category['name']); ?>
                        (<?php echo isset($stats['category_counts'][$category_slug]) ? $stats['category_counts'][$category_slug] : 0; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="cmama-filter-group">
            <label for="cmama-status-filter"><?php _e('Status:', CMAMA_TEXT_DOMAIN); ?></label>
            <select id="cmama-status-filter">
                <option value=""><?php _e('All Calculators', CMAMA_TEXT_DOMAIN); ?></option>
                <option value="active"><?php _e('Active Only', CMAMA_TEXT_DOMAIN); ?></option>
                <option value="inactive"><?php _e('Inactive Only', CMAMA_TEXT_DOMAIN); ?></option>
            </select>
        </div>

        <div class="cmama-filter-group">
            <button type="button" class="button" id="cmama-clear-filters">
                <?php _e('Clear Filters', CMAMA_TEXT_DOMAIN); ?>
            </button>
        </div>
    </div>

    <!-- Calculator Grid -->
    <div class="cmama-calculator-grid" id="cmama-calculator-grid">
        <?php foreach ($calculators as $calculator): ?>
            <div class="cmama-calculator-card" 
                 data-slug="<?php echo esc_attr($calculator['slug']); ?>"
                 data-category="<?php echo esc_attr($calculator['category']); ?>"
                 data-active="<?php echo $calculator['active'] ? '1' : '0'; ?>"
                 data-name="<?php echo esc_attr(strtolower($calculator['name'])); ?>"
                 data-description="<?php echo esc_attr(strtolower($calculator['description'])); ?>"
                 data-tags="<?php echo esc_attr(strtolower(implode(' ', $calculator['tags']))); ?>">
                
                <div class="cmama-card-header">
                    <div class="cmama-card-category">
                        <span class="cmama-category-badge cmama-category-<?php echo esc_attr($calculator['category']); ?>">
                            <?php echo esc_html(ucfirst($calculator['category'])); ?>
                        </span>
                    </div>
                    <div class="cmama-card-toggle">
                        <label class="cmama-toggle">
                            <input type="checkbox" 
                                   class="cmama-calculator-toggle" 
                                   data-slug="<?php echo esc_attr($calculator['slug']); ?>"
                                   <?php checked($calculator['active']); ?> />
                            <span class="cmama-toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="cmama-card-content">
                    <h3 class="cmama-card-title"><?php echo esc_html($calculator['name']); ?></h3>
                    <p class="cmama-card-description"><?php echo esc_html($calculator['description']); ?></p>
                    
                    <?php if (!empty($calculator['tags'])): ?>
                        <div class="cmama-card-tags">
                            <?php foreach (array_slice($calculator['tags'], 0, 3) as $tag): ?>
                                <span class="cmama-tag"><?php echo esc_html($tag); ?></span>
                            <?php endforeach; ?>
                            <?php if (count($calculator['tags']) > 3): ?>
                                <span class="cmama-tag-more">+<?php echo count($calculator['tags']) - 3; ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cmama-card-footer">
                    <div class="cmama-card-actions">
                        <button type="button" class="button button-small cmama-preview-btn" 
                                data-slug="<?php echo esc_attr($calculator['slug']); ?>">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php _e('Preview', CMAMA_TEXT_DOMAIN); ?>
                        </button>
                        <button type="button" class="button button-small cmama-shortcode-btn" 
                                data-shortcode="<?php echo esc_attr($calculator['shortcode']); ?>">
                            <span class="dashicons dashicons-editor-code"></span>
                            <?php _e('Shortcode', CMAMA_TEXT_DOMAIN); ?>
                        </button>
                    </div>
                    <div class="cmama-card-status">
                        <span class="cmama-status-indicator <?php echo $calculator['active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $calculator['active'] ? __('Active', CMAMA_TEXT_DOMAIN) : __('Inactive', CMAMA_TEXT_DOMAIN); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <div class="cmama-empty-state" id="cmama-empty-state" style="display: none;">
        <div class="cmama-empty-icon">
            <span class="dashicons dashicons-search"></span>
        </div>
        <h3><?php _e('No calculators found', CMAMA_TEXT_DOMAIN); ?></h3>
        <p><?php _e('Try adjusting your search criteria or clearing the filters.', CMAMA_TEXT_DOMAIN); ?></p>
        <button type="button" class="button" id="cmama-clear-search">
            <?php _e('Clear Search', CMAMA_TEXT_DOMAIN); ?>
        </button>
    </div>

    <!-- Loading State -->
    <div class="cmama-loading" id="cmama-loading" style="display: none;">
        <div class="cmama-spinner"></div>
        <p><?php _e('Loading calculators...', CMAMA_TEXT_DOMAIN); ?></p>
    </div>
</div>

<!-- Preview Modal -->
<div id="cmama-preview-modal" class="cmama-modal" style="display: none;">
    <div class="cmama-modal-content cmama-modal-large">
        <div class="cmama-modal-header">
            <h3 id="cmama-preview-title"><?php _e('Calculator Preview', CMAMA_TEXT_DOMAIN); ?></h3>
            <button type="button" class="cmama-modal-close">&times;</button>
        </div>
        <div class="cmama-modal-body">
            <div id="cmama-preview-content">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
        <div class="cmama-modal-footer">
            <button type="button" class="button button-primary" id="cmama-activate-from-preview">
                <?php _e('Activate Calculator', CMAMA_TEXT_DOMAIN); ?>
            </button>
            <button type="button" class="button cmama-modal-close">
                <?php _e('Close', CMAMA_TEXT_DOMAIN); ?>
            </button>
        </div>
    </div>
</div>

<!-- Shortcode Modal -->
<div id="cmama-shortcode-modal" class="cmama-modal" style="display: none;">
    <div class="cmama-modal-content">
        <div class="cmama-modal-header">
            <h3><?php _e('Calculator Shortcode', CMAMA_TEXT_DOMAIN); ?></h3>
            <button type="button" class="cmama-modal-close">&times;</button>
        </div>
        <div class="cmama-modal-body">
            <p><?php _e('Copy this shortcode and paste it into any post, page, or widget:', CMAMA_TEXT_DOMAIN); ?></p>
            <div class="cmama-shortcode-display">
                <input type="text" id="cmama-shortcode-input" readonly />
                <button type="button" class="button" id="cmama-copy-shortcode">
                    <span class="dashicons dashicons-admin-page"></span>
                    <?php _e('Copy', CMAMA_TEXT_DOMAIN); ?>
                </button>
            </div>
            <div class="cmama-shortcode-info">
                <h4><?php _e('Usage Examples:', CMAMA_TEXT_DOMAIN); ?></h4>
                <ul>
                    <li><code>[cmama_calculator slug="calculator-name"]</code> - <?php _e('Basic usage', CMAMA_TEXT_DOMAIN); ?></li>
                    <li><code>[cmama_calculator slug="calculator-name" title="Custom Title"]</code> - <?php _e('With custom title', CMAMA_TEXT_DOMAIN); ?></li>
                </ul>
            </div>
        </div>
        <div class="cmama-modal-footer">
            <button type="button" class="button cmama-modal-close">
                <?php _e('Close', CMAMA_TEXT_DOMAIN); ?>
            </button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var $grid = $('#cmama-calculator-grid');
    var $cards = $('.cmama-calculator-card');
    var $emptyState = $('#cmama-empty-state');
    var $loading = $('#cmama-loading');

    // Filter functionality
    function filterCalculators() {
        var searchTerm = $('#cmama-search').val().toLowerCase();
        var categoryFilter = $('#cmama-category-filter').val();
        var statusFilter = $('#cmama-status-filter').val();
        var visibleCount = 0;

        $cards.each(function() {
            var $card = $(this);
            var show = true;

            // Search filter
            if (searchTerm) {
                var searchableText = $card.data('name') + ' ' + $card.data('description') + ' ' + $card.data('tags');
                if (searchableText.indexOf(searchTerm) === -1) {
                    show = false;
                }
            }

            // Category filter
            if (categoryFilter && $card.data('category') !== categoryFilter) {
                show = false;
            }

            // Status filter
            if (statusFilter) {
                var isActive = $card.data('active') === 1;
                if ((statusFilter === 'active' && !isActive) || (statusFilter === 'inactive' && isActive)) {
                    show = false;
                }
            }

            if (show) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });

        // Show/hide empty state
        if (visibleCount === 0) {
            $emptyState.show();
        } else {
            $emptyState.hide();
        }
    }

    // Bind filter events
    $('#cmama-search, #cmama-category-filter, #cmama-status-filter').on('input change', filterCalculators);

    // Clear filters
    $('#cmama-clear-filters, #cmama-clear-search').on('click', function() {
        $('#cmama-search').val('');
        $('#cmama-category-filter').val('');
        $('#cmama-status-filter').val('');
        filterCalculators();
    });

    // Calculator toggle
    $('.cmama-calculator-toggle').on('change', function() {
        var $toggle = $(this);
        var slug = $toggle.data('slug');
        var isActive = $toggle.is(':checked');
        var $card = $toggle.closest('.cmama-calculator-card');
        var $statusIndicator = $card.find('.cmama-status-indicator');

        // Disable toggle during request
        $toggle.prop('disabled', true);

        var data = {
            action: 'cmama_toggle_calculator',
            nonce: cmamaAdmin.nonce,
            calculator_slug: slug,
            action_type: isActive ? 'activate' : 'deactivate'
        };

        $.post(cmamaAdmin.ajaxUrl, data, function(response) {
            if (response.success) {
                // Update card data and visual state
                $card.data('active', isActive ? 1 : 0);
                $statusIndicator.removeClass('active inactive')
                              .addClass(isActive ? 'active' : 'inactive')
                              .text(isActive ? cmamaAdmin.strings.active : cmamaAdmin.strings.inactive);
                
                // Show success message
                showNotice(response.data.message, 'success');
            } else {
                // Revert toggle state
                $toggle.prop('checked', !isActive);
                showNotice(response.data || cmamaAdmin.strings.error, 'error');
            }
        }).fail(function() {
            // Revert toggle state
            $toggle.prop('checked', !isActive);
            showNotice(cmamaAdmin.strings.error, 'error');
        }).always(function() {
            $toggle.prop('disabled', false);
        });
    });

    // Activate/Deactivate all
    $('#cmama-activate-all').on('click', function() {
        if (confirm(cmamaAdmin.strings.confirm_activate_all || 'Are you sure you want to activate all calculators?')) {
            toggleAllCalculators(true);
        }
    });

    $('#cmama-deactivate-all').on('click', function() {
        if (confirm(cmamaAdmin.strings.confirm_deactivate_all || 'Are you sure you want to deactivate all calculators?')) {
            toggleAllCalculators(false);
        }
    });

    function toggleAllCalculators(activate) {
        $loading.show();
        var promises = [];

        $('.cmama-calculator-toggle').each(function() {
            var $toggle = $(this);
            var slug = $toggle.data('slug');
            var currentState = $toggle.is(':checked');

            if (currentState !== activate) {
                var data = {
                    action: 'cmama_toggle_calculator',
                    nonce: cmamaAdmin.nonce,
                    calculator_slug: slug,
                    action_type: activate ? 'activate' : 'deactivate'
                };

                promises.push($.post(cmamaAdmin.ajaxUrl, data));
            }
        });

        $.when.apply($, promises).then(function() {
            location.reload();
        }).fail(function() {
            showNotice(cmamaAdmin.strings.error, 'error');
            $loading.hide();
        });
    }

    // Preview calculator
    $('.cmama-preview-btn').on('click', function() {
        var slug = $(this).data('slug');
        var $modal = $('#cmama-preview-modal');
        var $content = $('#cmama-preview-content');
        var $title = $('#cmama-preview-title');

        $modal.show();
        $content.html('<div class="cmama-loading"><div class="cmama-spinner"></div><p>Loading preview...</p></div>');

        var data = {
            action: 'cmama_get_calculator_preview',
            nonce: cmamaAdmin.nonce,
            calculator_slug: slug
        };

        $.post(cmamaAdmin.ajaxUrl, data, function(response) {
            if (response.success) {
                $title.text(response.data.calculator.name + ' - Preview');
                $content.html(response.data.html);
                $('#cmama-activate-from-preview').data('slug', slug);
            } else {
                $content.html('<div class="cmama-error">Failed to load preview: ' + (response.data || 'Unknown error') + '</div>');
            }
        }).fail(function() {
            $content.html('<div class="cmama-error">Failed to load preview.</div>');
        });
    });

    // Activate from preview
    $('#cmama-activate-from-preview').on('click', function() {
        var slug = $(this).data('slug');
        var $toggle = $('.cmama-calculator-toggle[data-slug="' + slug + '"]');
        
        if (!$toggle.is(':checked')) {
            $toggle.prop('checked', true).trigger('change');
        }
        
        $('#cmama-preview-modal').hide();
    });

    // Shortcode modal
    $('.cmama-shortcode-btn').on('click', function() {
        var shortcode = $(this).data('shortcode');
        $('#cmama-shortcode-input').val(shortcode);
        $('#cmama-shortcode-modal').show();
    });

    // Copy shortcode
    $('#cmama-copy-shortcode').on('click', function() {
        var $input = $('#cmama-shortcode-input');
        $input.select();
        document.execCommand('copy');
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes"></span> Copied!');
        
        setTimeout(function() {
            $btn.html(originalText);
        }, 2000);
    });

    // Modal close functionality
    $('.cmama-modal-close').on('click', function() {
        $(this).closest('.cmama-modal').hide();
    });

    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            $('.cmama-modal').hide();
        }
    });

    // Click outside modal to close
    $('.cmama-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });

    // Show notice function
    function showNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
        
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Initialize filters
    filterCalculators();
});
</script>