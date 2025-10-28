/**
 * Admin JavaScript
 *
 * @package Calculator_Mama
 * @since 1.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initAdmin();
    });

    /**
     * Initialize admin functionality
     */
    function initAdmin() {
        initCalculatorToggles();
        initModals();
        initCopyButtons();
        initPreviewButtons();
    }

    /**
     * Initialize calculator toggles
     */
    function initCalculatorToggles() {
        $('.cmama-toggle-input').on('change', function() {
            var $toggle = $(this);
            var $card = $toggle.closest('.cmama-calculator-card');
            var calculator = $card.data('calculator');
            var active = $toggle.is(':checked');
            
            // Show loading state
            $toggle.prop('disabled', true);
            
            // Send AJAX request
            $.ajax({
                url: cmama_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'cmama_toggle_calculator',
                    nonce: cmama_admin.nonce,
                    calculator_slug: calculator,
                    active: active
                },
                success: function(response) {
                    if (response.success) {
                        // Update card appearance
                        $card.toggleClass('active', active);
                        
                        // Update toggle label
                        var $label = $toggle.siblings('.cmama-toggle-label');
                        $label.text(active ? 'Active' : 'Inactive');
                        
                        // Show success message
                        showAdminNotice(response.data.message, 'success');
                    } else {
                        // Revert toggle state
                        $toggle.prop('checked', !active);
                        showAdminNotice(response.data.message, 'error');
                    }
                },
                error: function() {
                    // Revert toggle state
                    $toggle.prop('checked', !active);
                    showAdminNotice(cmama_admin.strings.error, 'error');
                },
                complete: function() {
                    $toggle.prop('disabled', false);
                }
            });
        });
    }

    /**
     * Initialize modals
     */
    function initModals() {
        // Close modal on close button click
        $('.cmama-modal-close').on('click', function() {
            $(this).closest('.cmama-modal').hide();
        });

        // Close modal on outside click
        $('.cmama-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });

        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                $('.cmama-modal:visible').hide();
            }
        });
    }

    /**
     * Initialize copy buttons
     */
    function initCopyButtons() {
        $('.cmama-copy-btn').on('click', function() {
            var $button = $(this);
            var targetId = $button.data('target');
            var $target = $('#' + targetId);
            
            if ($target.length) {
                $target.select();
                document.execCommand('copy');
                
                // Show feedback
                var originalText = $button.text();
                $button.text('Copied!');
                setTimeout(function() {
                    $button.text(originalText);
                }, 2000);
            }
        });
    }

    /**
     * Initialize preview buttons
     */
    function initPreviewButtons() {
        $('.cmama-preview-btn').on('click', function() {
            var calculator = $(this).data('calculator');
            showCalculatorPreview(calculator);
        });
    }

    /**
     * Show calculator preview
     *
     * @param {string} calculator
     */
    function showCalculatorPreview(calculator) {
        var $modal = $('#cmama-preview-modal');
        var $title = $('#cmama-preview-title');
        var $body = $('#cmama-preview-body');
        
        // Update title
        $title.text('Loading preview...');
        $body.html('<div class="cmama-loading">Loading calculator preview...</div>');
        
        // Show modal
        $modal.show();
        
        // Load preview via AJAX
        $.ajax({
            url: cmama_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'cmama_preview_calculator',
                nonce: cmama_admin.nonce,
                calculator: calculator
            },
            success: function(response) {
                if (response.success) {
                    $title.text('Calculator Preview');
                    $body.html(response.data.preview);
                } else {
                    $title.text('Preview Error');
                    $body.html('<div class="cmama-error">Failed to load preview.</div>');
                }
            },
            error: function() {
                $title.text('Preview Error');
                $body.html('<div class="cmama-error">Failed to load preview.</div>');
            }
        });
    }

    /**
     * Show shortcode modal
     *
     * @param {string} calculator
     */
    function showShortcodeModal(calculator) {
        var $modal = $('#cmama-shortcode-modal');
        var $input = $('#cmama-shortcode-input');
        
        // Generate shortcode
        var shortcode = '[cmama_calculator slug="' + calculator + '"]';
        $input.val(shortcode);
        
        // Show modal
        $modal.show();
    }

    /**
     * Show admin notice
     *
     * @param {string} message
     * @param {string} type
     */
    function showAdminNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Shortcode button functionality
    $('.cmama-shortcode-btn').on('click', function() {
        var calculator = $(this).data('calculator');
        showShortcodeModal(calculator);
    });

    // Appearance form handling
    $('#cmama-appearance-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var formData = $form.serialize();
        
        // Show loading state
        $button.prop('disabled', true).text(cmama_admin.strings.saving);
        
        // Send AJAX request
        $.ajax({
            url: cmama_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'cmama_save_appearance',
                nonce: cmama_admin.nonce,
                ...Object.fromEntries(new URLSearchParams(formData))
            },
            success: function(response) {
                if (response.success) {
                    showAdminNotice(response.data.message, 'success');
                } else {
                    showAdminNotice(response.data.message, 'error');
                }
            },
            error: function() {
                showAdminNotice(cmama_admin.strings.error, 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text('Save Changes');
            }
        });
    });

    // SEO form handling
    $('#cmama-seo-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var formData = $form.serialize();
        
        // Show loading state
        $button.prop('disabled', true).text(cmama_admin.strings.saving);
        
        // Send AJAX request
        $.ajax({
            url: cmama_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'cmama_save_seo',
                nonce: cmama_admin.nonce,
                ...Object.fromEntries(new URLSearchParams(formData))
            },
            success: function(response) {
                if (response.success) {
                    showAdminNotice(response.data.message, 'success');
                } else {
                    showAdminNotice(response.data.message, 'error');
                }
            },
            error: function() {
                showAdminNotice(cmama_admin.strings.error, 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text('Save Settings');
            }
        });
    });

})(jQuery);