/**
 * Calculator Mama - Admin JavaScript
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Calculator Mama Admin Class
     */
    class CalculatorMamaAdmin {
        constructor() {
            this.init();
        }

        /**
         * Initialize admin functionality
         */
        init() {
            this.bindEvents();
            this.initColorPickers();
            this.initTooltips();
            this.initModals();
        }

        /**
         * Bind event handlers
         */
        bindEvents() {
            // Calculator toggle
            $(document).on('change', '.cmama-calculator-toggle', this.handleCalculatorToggle.bind(this));
            
            // Bulk actions
            $(document).on('click', '#cmama-activate-all', this.handleActivateAll.bind(this));
            $(document).on('click', '#cmama-deactivate-all', this.handleDeactivateAll.bind(this));
            
            // Search and filters
            $(document).on('input', '#cmama-search', this.debounce(this.handleSearch.bind(this), 300));
            $(document).on('change', '#cmama-category-filter, #cmama-status-filter', this.handleFilter.bind(this));
            $(document).on('click', '#cmama-clear-filters', this.handleClearFilters.bind(this));
            
            // Preview buttons
            $(document).on('click', '.cmama-preview-btn', this.handlePreview.bind(this));
            
            // Shortcode buttons
            $(document).on('click', '.cmama-shortcode-btn', this.handleShortcode.bind(this));
            $(document).on('click', '#cmama-copy-shortcode', this.handleCopyShortcode.bind(this));
            
            // Appearance form
            $(document).on('submit', '#cmama-appearance-form', this.handleAppearanceSubmit.bind(this));
            $(document).on('click', '#cmama-reset-appearance', this.handleResetAppearance.bind(this));
            
            // Range inputs
            $(document).on('input', 'input[type="range"]', this.handleRangeInput.bind(this));
            
            // Color picker changes
            $(document).on('change', '.cmama-color-picker', this.updatePreview.bind(this));
            
            // Font family changes
            $(document).on('change', '#font_family', this.updatePreview.bind(this));
            
            // Custom CSS changes
            $(document).on('input', '#custom_css', this.debounce(this.updatePreview.bind(this), 500));
            
            // Modal close
            $(document).on('click', '.cmama-modal-close', this.closeModal.bind(this));
            $(document).on('click', '.cmama-modal', this.handleModalBackdropClick.bind(this));
            
            // Welcome notice dismiss
            $(document).on('click', '.cmama-dismiss-welcome', this.handleDismissWelcome.bind(this));
            
            // Export/Import
            $(document).on('click', '#cmama-export-settings', this.handleExportSettings.bind(this));
            $(document).on('click', '#cmama-import-settings', this.handleImportSettings.bind(this));
            $(document).on('change', '#cmama-import-file', this.handleImportFileChange.bind(this));
            $(document).on('click', '#cmama-confirm-import', this.handleConfirmImport.bind(this));
            
            // Keyboard shortcuts
            $(document).on('keydown', this.handleKeyboardShortcuts.bind(this));
        }

        /**
         * Initialize color pickers
         */
        initColorPickers() {
            if ($.fn.wpColorPicker) {
                $('.cmama-color-picker').wpColorPicker({
                    change: this.updatePreview.bind(this),
                    clear: this.updatePreview.bind(this)
                });
            }
        }

        /**
         * Initialize tooltips
         */
        initTooltips() {
            // Add tooltips to various elements
            $('[title]').each(function() {
                const $element = $(this);
                const title = $element.attr('title');
                
                if (title) {
                    $element.on('mouseenter', function() {
                        const tooltip = $('<div class="cmama-tooltip">' + title + '</div>');
                        $('body').append(tooltip);
                        
                        const offset = $element.offset();
                        tooltip.css({
                            position: 'absolute',
                            top: offset.top - tooltip.outerHeight() - 5,
                            left: offset.left + ($element.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                            zIndex: 9999
                        });
                    }).on('mouseleave', function() {
                        $('.cmama-tooltip').remove();
                    });
                }
            });
        }

        /**
         * Initialize modals
         */
        initModals() {
            // Set up modal functionality
            $('.cmama-modal').hide();
        }

        /**
         * Handle calculator toggle
         */
        handleCalculatorToggle(e) {
            const $toggle = $(e.target);
            const slug = $toggle.data('slug');
            const isActive = $toggle.is(':checked');
            const $card = $toggle.closest('.cmama-calculator-card');
            
            // Disable toggle during request
            $toggle.prop('disabled', true);
            
            const data = {
                action: 'cmama_toggle_calculator',
                nonce: cmamaAdmin.nonce,
                calculator_slug: slug,
                action_type: isActive ? 'activate' : 'deactivate'
            };
            
            $.post(cmamaAdmin.ajaxUrl, data)
                .done((response) => {
                    if (response.success) {
                        // Update card visual state
                        $card.attr('data-active', isActive ? '1' : '0');
                        
                        const $statusIndicator = $card.find('.cmama-status-indicator');
                        $statusIndicator.removeClass('active inactive')
                                      .addClass(isActive ? 'active' : 'inactive')
                                      .text(isActive ? 'Active' : 'Inactive');
                        
                        this.showNotice(response.data.message, 'success');
                        this.updateStats();
                    } else {
                        // Revert toggle state
                        $toggle.prop('checked', !isActive);
                        this.showNotice(response.data || 'Error updating calculator status.', 'error');
                    }
                })
                .fail(() => {
                    // Revert toggle state
                    $toggle.prop('checked', !isActive);
                    this.showNotice('Network error. Please try again.', 'error');
                })
                .always(() => {
                    $toggle.prop('disabled', false);
                });
        }

        /**
         * Handle activate all
         */
        handleActivateAll(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to activate all calculators? This may affect your site\'s performance.')) {
                return;
            }
            
            this.bulkToggleCalculators(true);
        }

        /**
         * Handle deactivate all
         */
        handleDeactivateAll(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to deactivate all calculators?')) {
                return;
            }
            
            this.bulkToggleCalculators(false);
        }

        /**
         * Bulk toggle calculators
         */
        bulkToggleCalculators(activate) {
            const $toggles = $('.cmama-calculator-toggle').filter(function() {
                return $(this).is(':checked') !== activate;
            });
            
            if ($toggles.length === 0) {
                this.showNotice('No calculators to ' + (activate ? 'activate' : 'deactivate') + '.', 'info');
                return;
            }
            
            const $button = activate ? $('#cmama-activate-all') : $('#cmama-deactivate-all');
            const originalText = $button.text();
            
            $button.prop('disabled', true).text('Processing...');
            
            let completed = 0;
            const total = $toggles.length;
            
            $toggles.each((index, element) => {
                const $toggle = $(element);
                const slug = $toggle.data('slug');
                
                const data = {
                    action: 'cmama_toggle_calculator',
                    nonce: cmamaAdmin.nonce,
                    calculator_slug: slug,
                    action_type: activate ? 'activate' : 'deactivate'
                };
                
                $.post(cmamaAdmin.ajaxUrl, data)
                    .always(() => {
                        completed++;
                        if (completed === total) {
                            $button.prop('disabled', false).text(originalText);
                            location.reload(); // Refresh to show updated states
                        }
                    });
            });
        }

        /**
         * Handle search
         */
        handleSearch() {
            this.filterCalculators();
        }

        /**
         * Handle filter changes
         */
        handleFilter() {
            this.filterCalculators();
        }

        /**
         * Handle clear filters
         */
        handleClearFilters(e) {
            e.preventDefault();
            
            $('#cmama-search').val('');
            $('#cmama-category-filter').val('');
            $('#cmama-status-filter').val('');
            
            this.filterCalculators();
        }

        /**
         * Filter calculators
         */
        filterCalculators() {
            const searchTerm = $('#cmama-search').val().toLowerCase();
            const categoryFilter = $('#cmama-category-filter').val();
            const statusFilter = $('#cmama-status-filter').val();
            
            let visibleCount = 0;
            
            $('.cmama-calculator-card').each(function() {
                const $card = $(this);
                let show = true;
                
                // Search filter
                if (searchTerm) {
                    const searchableText = ($card.data('name') + ' ' + 
                                         $card.data('description') + ' ' + 
                                         $card.data('tags')).toLowerCase();
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
                    const isActive = $card.data('active') === 1;
                    if ((statusFilter === 'active' && !isActive) || 
                        (statusFilter === 'inactive' && isActive)) {
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
                $('#cmama-empty-state').show();
            } else {
                $('#cmama-empty-state').hide();
            }
        }

        /**
         * Handle preview
         */
        handlePreview(e) {
            e.preventDefault();
            
            const $button = $(e.target).closest('.cmama-preview-btn');
            const slug = $button.data('slug');
            
            this.showPreviewModal(slug);
        }

        /**
         * Show preview modal
         */
        showPreviewModal(slug) {
            const $modal = $('#cmama-preview-modal');
            const $content = $('#cmama-preview-content');
            const $title = $('#cmama-preview-title');
            
            $modal.show();
            $content.html('<div class="cmama-loading"><div class="cmama-spinner"></div><p>Loading preview...</p></div>');
            
            const data = {
                action: 'cmama_get_calculator_preview',
                nonce: cmamaAdmin.nonce,
                calculator_slug: slug
            };
            
            $.post(cmamaAdmin.ajaxUrl, data)
                .done((response) => {
                    if (response.success) {
                        $title.text(response.data.calculator.name + ' - Preview');
                        $content.html(response.data.html);
                        $('#cmama-activate-from-preview').data('slug', slug);
                    } else {
                        $content.html('<div class="cmama-error">Failed to load preview: ' + 
                                    (response.data || 'Unknown error') + '</div>');
                    }
                })
                .fail(() => {
                    $content.html('<div class="cmama-error">Network error loading preview.</div>');
                });
        }

        /**
         * Handle shortcode
         */
        handleShortcode(e) {
            e.preventDefault();
            
            const $button = $(e.target).closest('.cmama-shortcode-btn');
            const shortcode = $button.data('shortcode');
            
            $('#cmama-shortcode-input').val(shortcode);
            $('#cmama-shortcode-modal').show();
        }

        /**
         * Handle copy shortcode
         */
        handleCopyShortcode(e) {
            e.preventDefault();
            
            const $input = $('#cmama-shortcode-input');
            const $button = $(e.target).closest('#cmama-copy-shortcode');
            
            $input.select();
            document.execCommand('copy');
            
            const originalHtml = $button.html();
            $button.html('<span class="dashicons dashicons-yes"></span> Copied!');
            
            setTimeout(() => {
                $button.html(originalHtml);
            }, 2000);
            
            this.showNotice('Shortcode copied to clipboard!', 'success');
        }

        /**
         * Handle appearance form submit
         */
        handleAppearanceSubmit(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.html();
            
            $submitBtn.prop('disabled', true)
                      .html('<span class="dashicons dashicons-update-alt"></span> Saving...');
            
            const formData = $form.serialize();
            
            $.post(cmamaAdmin.ajaxUrl, formData + '&action=cmama_save_appearance')
                .done((response) => {
                    if (response.success) {
                        this.showNotice('Appearance settings saved successfully!', 'success');
                    } else {
                        this.showNotice(response.data || 'Error saving settings.', 'error');
                    }
                })
                .fail(() => {
                    this.showNotice('Network error. Please try again.', 'error');
                })
                .always(() => {
                    $submitBtn.prop('disabled', false).html(originalText);
                });
        }

        /**
         * Handle reset appearance
         */
        handleResetAppearance(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to reset all appearance settings to defaults?')) {
                return;
            }
            
            const defaults = {
                primary_color: '#007cba',
                button_color: '#007cba',
                input_color: '#ffffff',
                result_color: '#f0f0f0',
                font_family: 'inherit',
                border_radius: '4',
                padding: '20',
                custom_css: ''
            };
            
            for (const field in defaults) {
                const $field = $('#' + field);
                if ($field.hasClass('cmama-color-picker')) {
                    $field.wpColorPicker('color', defaults[field]);
                } else {
                    $field.val(defaults[field]);
                    if ($field.attr('type') === 'range') {
                        $field.next('.cmama-range-value').text(defaults[field] + 'px');
                    }
                }
            }
            
            this.updatePreview();
        }

        /**
         * Handle range input
         */
        handleRangeInput(e) {
            const $range = $(e.target);
            const $valueDisplay = $range.next('.cmama-range-value');
            $valueDisplay.text($range.val() + 'px');
            this.updatePreview();
        }

        /**
         * Update preview
         */
        updatePreview() {
            const $calculator = $('#cmama-sample-calculator');
            
            if ($calculator.length === 0) {
                return;
            }
            
            const styles = {
                '--cmama-primary-color': $('#primary_color').val(),
                '--cmama-button-color': $('#button_color').val(),
                '--cmama-input-color': $('#input_color').val(),
                '--cmama-result-color': $('#result_color').val(),
                '--cmama-border-radius': $('#border_radius').val() + 'px',
                '--cmama-padding': $('#padding').val() + 'px'
            };
            
            const fontFamily = $('#font_family').val();
            if (fontFamily !== 'inherit') {
                styles['--cmama-font-family'] = fontFamily;
            }
            
            // Apply CSS custom properties
            let cssText = '';
            for (const prop in styles) {
                cssText += prop + ': ' + styles[prop] + '; ';
            }
            $calculator.attr('style', cssText);
            
            // Apply custom CSS
            const customCSS = $('#custom_css').val();
            let $customStyle = $('#cmama-custom-preview-css');
            if ($customStyle.length === 0) {
                $customStyle = $('<style id="cmama-custom-preview-css"></style>').appendTo('head');
            }
            $customStyle.text('#cmama-sample-calculator { ' + customCSS + ' }');
        }

        /**
         * Close modal
         */
        closeModal(e) {
            e.preventDefault();
            $(e.target).closest('.cmama-modal').hide();
        }

        /**
         * Handle modal backdrop click
         */
        handleModalBackdropClick(e) {
            if (e.target === e.currentTarget) {
                $(e.target).hide();
            }
        }

        /**
         * Handle dismiss welcome notice
         */
        handleDismissWelcome(e) {
            e.preventDefault();
            
            $.post(cmamaAdmin.ajaxUrl, {
                action: 'cmama_dismiss_welcome',
                nonce: cmamaAdmin.nonce
            });
            
            $('.cmama-welcome-notice').fadeOut();
        }

        /**
         * Handle export settings
         */
        handleExportSettings(e) {
            e.preventDefault();
            
            const data = {
                action: 'cmama_export_settings',
                nonce: cmamaAdmin.nonce
            };
            
            $.post(cmamaAdmin.ajaxUrl, data)
                .done((response) => {
                    if (response.success) {
                        const dataStr = JSON.stringify(response.data, null, 2);
                        const dataBlob = new Blob([dataStr], {type: 'application/json'});
                        const url = URL.createObjectURL(dataBlob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'calculator-mama-settings-' + new Date().toISOString().slice(0, 10) + '.json';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                        
                        this.showNotice('Settings exported successfully!', 'success');
                    } else {
                        this.showNotice(response.data || 'Export failed', 'error');
                    }
                })
                .fail(() => {
                    this.showNotice('Network error during export.', 'error');
                });
        }

        /**
         * Handle import settings
         */
        handleImportSettings(e) {
            e.preventDefault();
            $('#cmama-import-modal').show();
        }

        /**
         * Handle import file change
         */
        handleImportFileChange(e) {
            const file = e.target.files[0];
            if (!file) {
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const importData = JSON.parse(e.target.result);
                    if (importData.settings) {
                        $('.cmama-import-details').html(
                            '<p><strong>Version:</strong> ' + (importData.version || 'Unknown') + '</p>' +
                            '<p><strong>Export Date:</strong> ' + (importData.export_date || 'Unknown') + '</p>' +
                            '<p><strong>Settings:</strong> ' + Object.keys(importData.settings).length + ' items</p>'
                        );
                        $('.cmama-import-preview').show();
                        $('#cmama-confirm-import').prop('disabled', false);
                    } else {
                        this.showNotice('Invalid settings file format', 'error');
                    }
                } catch (error) {
                    this.showNotice('Invalid JSON file', 'error');
                }
            };
            reader.readAsText(file);
        }

        /**
         * Handle confirm import
         */
        handleConfirmImport(e) {
            e.preventDefault();
            
            const file = $('#cmama-import-file')[0].files[0];
            if (!file) {
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = {
                    action: 'cmama_import_settings',
                    nonce: cmamaAdmin.nonce,
                    import_data: e.target.result
                };
                
                $.post(cmamaAdmin.ajaxUrl, data)
                    .done((response) => {
                        if (response.success) {
                            this.showNotice('Settings imported successfully!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            this.showNotice(response.data || 'Import failed', 'error');
                        }
                    })
                    .fail(() => {
                        this.showNotice('Network error during import.', 'error');
                    });
            };
            reader.readAsText(file);
        }

        /**
         * Handle keyboard shortcuts
         */
        handleKeyboardShortcuts(e) {
            // Escape key closes modals
            if (e.keyCode === 27) {
                $('.cmama-modal:visible').hide();
            }
            
            // Ctrl/Cmd + S saves appearance settings
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
                const $appearanceForm = $('#cmama-appearance-form');
                if ($appearanceForm.length && $appearanceForm.is(':visible')) {
                    e.preventDefault();
                    $appearanceForm.submit();
                }
            }
        }

        /**
         * Show admin notice
         */
        showNotice(message, type = 'info') {
            const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $notice.fadeOut(() => {
                    $notice.remove();
                });
            }, 5000);
            
            // Make dismissible
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(() => {
                    $notice.remove();
                });
            });
        }

        /**
         * Update statistics display
         */
        updateStats() {
            // This could be enhanced to update stats in real-time
            // For now, we'll just show a simple update
            const $activeCount = $('.cmama-stat-card.active h3');
            if ($activeCount.length) {
                const currentCount = parseInt($activeCount.text());
                // This is a simplified update - in reality you'd get the actual count
                $activeCount.text(currentCount);
            }
        }

        /**
         * Debounce function
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        /**
         * Throttle function
         */
        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }

        /**
         * Format number with commas
         */
        formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        /**
         * Validate email
         */
        isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        /**
         * Get URL parameter
         */
        getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
    }

    /**
     * Initialize when document is ready
     */
    $(document).ready(() => {
        new CalculatorMamaAdmin();
    });

})(jQuery);