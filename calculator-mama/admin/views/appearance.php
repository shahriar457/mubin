<?php
/**
 * Admin Appearance Settings View
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current appearance settings
$settings = get_option('cmama_settings', array());
$appearance = isset($settings['appearance']) ? $settings['appearance'] : array();

// Default values
$defaults = array(
    'primary_color' => '#007cba',
    'button_color' => '#007cba',
    'input_color' => '#ffffff',
    'result_color' => '#f0f0f0',
    'font_family' => 'inherit',
    'border_radius' => '4',
    'padding' => '20',
    'custom_css' => ''
);

$appearance = wp_parse_args($appearance, $defaults);

// Available font families
$font_families = array(
    'inherit' => __('Inherit from theme', CMAMA_TEXT_DOMAIN),
    'Arial, sans-serif' => 'Arial',
    'Helvetica, sans-serif' => 'Helvetica',
    'Georgia, serif' => 'Georgia',
    'Times New Roman, serif' => 'Times New Roman',
    'Courier New, monospace' => 'Courier New',
    'Verdana, sans-serif' => 'Verdana',
    'Trebuchet MS, sans-serif' => 'Trebuchet MS',
    'Impact, sans-serif' => 'Impact',
    'Comic Sans MS, cursive' => 'Comic Sans MS'
);
?>

<div class="wrap cmama-appearance">
    <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <p class="description">
        <?php _e('Customize the appearance of your calculators to match your website\'s design and branding.', CMAMA_TEXT_DOMAIN); ?>
    </p>

    <div class="cmama-appearance-container">
        <!-- Settings Form -->
        <div class="cmama-appearance-settings">
            <form id="cmama-appearance-form">
                <?php wp_nonce_field('cmama_appearance_nonce', 'cmama_nonce'); ?>

                <!-- Color Settings -->
                <div class="cmama-settings-section">
                    <h2><?php _e('Color Settings', CMAMA_TEXT_DOMAIN); ?></h2>
                    <p class="description">
                        <?php _e('Choose colors that match your brand and website design.', CMAMA_TEXT_DOMAIN); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="primary_color"><?php _e('Primary Color', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="text" id="primary_color" name="primary_color" 
                                       value="<?php echo esc_attr($appearance['primary_color']); ?>" 
                                       class="cmama-color-picker" />
                                <p class="description">
                                    <?php _e('Used for accents, borders, and highlights.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="button_color"><?php _e('Button Color', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="text" id="button_color" name="button_color" 
                                       value="<?php echo esc_attr($appearance['button_color']); ?>" 
                                       class="cmama-color-picker" />
                                <p class="description">
                                    <?php _e('Background color for calculator buttons.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="input_color"><?php _e('Input Background', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="text" id="input_color" name="input_color" 
                                       value="<?php echo esc_attr($appearance['input_color']); ?>" 
                                       class="cmama-color-picker" />
                                <p class="description">
                                    <?php _e('Background color for input fields.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="result_color"><?php _e('Result Background', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="text" id="result_color" name="result_color" 
                                       value="<?php echo esc_attr($appearance['result_color']); ?>" 
                                       class="cmama-color-picker" />
                                <p class="description">
                                    <?php _e('Background color for result display areas.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Typography Settings -->
                <div class="cmama-settings-section">
                    <h2><?php _e('Typography', CMAMA_TEXT_DOMAIN); ?></h2>
                    <p class="description">
                        <?php _e('Control the font family used in calculators.', CMAMA_TEXT_DOMAIN); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="font_family"><?php _e('Font Family', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <select id="font_family" name="font_family">
                                    <?php foreach ($font_families as $value => $label): ?>
                                        <option value="<?php echo esc_attr($value); ?>" 
                                                <?php selected($appearance['font_family'], $value); ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">
                                    <?php _e('Choose a font family or inherit from your theme.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Layout Settings -->
                <div class="cmama-settings-section">
                    <h2><?php _e('Layout & Spacing', CMAMA_TEXT_DOMAIN); ?></h2>
                    <p class="description">
                        <?php _e('Adjust the spacing and border radius of calculator elements.', CMAMA_TEXT_DOMAIN); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="border_radius"><?php _e('Border Radius', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="range" id="border_radius" name="border_radius" 
                                       min="0" max="20" step="1" 
                                       value="<?php echo esc_attr($appearance['border_radius']); ?>" />
                                <span class="cmama-range-value"><?php echo esc_html($appearance['border_radius']); ?>px</span>
                                <p class="description">
                                    <?php _e('Roundness of corners for buttons and inputs.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="padding"><?php _e('Padding', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <input type="range" id="padding" name="padding" 
                                       min="10" max="40" step="2" 
                                       value="<?php echo esc_attr($appearance['padding']); ?>" />
                                <span class="cmama-range-value"><?php echo esc_html($appearance['padding']); ?>px</span>
                                <p class="description">
                                    <?php _e('Internal spacing within calculator containers.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Custom CSS -->
                <div class="cmama-settings-section">
                    <h2><?php _e('Custom CSS', CMAMA_TEXT_DOMAIN); ?></h2>
                    <p class="description">
                        <?php _e('Add custom CSS to further customize the appearance of your calculators.', CMAMA_TEXT_DOMAIN); ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="custom_css"><?php _e('Additional CSS', CMAMA_TEXT_DOMAIN); ?></label>
                            </th>
                            <td>
                                <textarea id="custom_css" name="custom_css" rows="10" cols="50" 
                                          class="large-text code"><?php echo esc_textarea($appearance['custom_css']); ?></textarea>
                                <p class="description">
                                    <?php _e('Enter custom CSS rules. Use .cmama-calculator as the base selector.', CMAMA_TEXT_DOMAIN); ?>
                                </p>
                                <details class="cmama-css-help">
                                    <summary><?php _e('CSS Selectors Reference', CMAMA_TEXT_DOMAIN); ?></summary>
                                    <div class="cmama-css-examples">
                                        <h4><?php _e('Common Selectors:', CMAMA_TEXT_DOMAIN); ?></h4>
                                        <ul>
                                            <li><code>.cmama-calculator</code> - <?php _e('Main calculator container', CMAMA_TEXT_DOMAIN); ?></li>
                                            <li><code>.cmama-calculator-title</code> - <?php _e('Calculator title', CMAMA_TEXT_DOMAIN); ?></li>
                                            <li><code>.cmama-field-input</code> - <?php _e('Input fields', CMAMA_TEXT_DOMAIN); ?></li>
                                            <li><code>.cmama-calculate-btn</code> - <?php _e('Calculate button', CMAMA_TEXT_DOMAIN); ?></li>
                                            <li><code>.cmama-calculator-result</code> - <?php _e('Result container', CMAMA_TEXT_DOMAIN); ?></li>
                                        </ul>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="cmama-settings-actions">
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-saved"></span>
                        <?php _e('Save Appearance Settings', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="cmama-reset-appearance">
                        <span class="dashicons dashicons-undo"></span>
                        <?php _e('Reset to Defaults', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                    <button type="button" class="button" id="cmama-preview-changes">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php _e('Preview Changes', CMAMA_TEXT_DOMAIN); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Preview -->
        <div class="cmama-appearance-preview">
            <div class="cmama-preview-header">
                <h3><?php _e('Live Preview', CMAMA_TEXT_DOMAIN); ?></h3>
                <p class="description">
                    <?php _e('See how your changes will look in real-time.', CMAMA_TEXT_DOMAIN); ?>
                </p>
            </div>

            <div class="cmama-preview-container" id="cmama-preview-container">
                <!-- Sample calculator for preview -->
                <div class="cmama-calculator cmama-calculator-sample" id="cmama-sample-calculator">
                    <h3 class="cmama-calculator-title"><?php _e('Sample Calculator', CMAMA_TEXT_DOMAIN); ?></h3>
                    <p class="cmama-calculator-description">
                        <?php _e('This is a preview of how your calculators will look with the current settings.', CMAMA_TEXT_DOMAIN); ?>
                    </p>

                    <form class="cmama-calculator-form">
                        <div class="cmama-calculator-fields">
                            <div class="cmama-field">
                                <label class="cmama-field-label"><?php _e('Sample Input', CMAMA_TEXT_DOMAIN); ?></label>
                                <input type="number" class="cmama-field-input" placeholder="<?php _e('Enter a number', CMAMA_TEXT_DOMAIN); ?>" value="100" />
                            </div>
                            <div class="cmama-field">
                                <label class="cmama-field-label"><?php _e('Another Input', CMAMA_TEXT_DOMAIN); ?></label>
                                <select class="cmama-field-input">
                                    <option><?php _e('Option 1', CMAMA_TEXT_DOMAIN); ?></option>
                                    <option><?php _e('Option 2', CMAMA_TEXT_DOMAIN); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="cmama-calculator-actions">
                            <button type="button" class="cmama-calculate-btn">
                                <?php _e('Calculate', CMAMA_TEXT_DOMAIN); ?>
                            </button>
                            <button type="button" class="cmama-reset-btn">
                                <?php _e('Reset', CMAMA_TEXT_DOMAIN); ?>
                            </button>
                        </div>

                        <div class="cmama-calculator-result" style="display: block;">
                            <h4 class="cmama-result-title"><?php _e('Result', CMAMA_TEXT_DOMAIN); ?></h4>
                            <div class="cmama-result-content">
                                <div class="cmama-result-data">
                                    <div class="cmama-result-item">
                                        <span class="cmama-result-label"><?php _e('Sample Result', CMAMA_TEXT_DOMAIN); ?>:</span>
                                        <span class="cmama-result-value">$1,234.56</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="cmama-preview-actions">
                <button type="button" class="button button-small" id="cmama-refresh-preview">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Refresh Preview', CMAMA_TEXT_DOMAIN); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preset Colors Modal -->
<div id="cmama-presets-modal" class="cmama-modal" style="display: none;">
    <div class="cmama-modal-content">
        <div class="cmama-modal-header">
            <h3><?php _e('Color Presets', CMAMA_TEXT_DOMAIN); ?></h3>
            <button type="button" class="cmama-modal-close">&times;</button>
        </div>
        <div class="cmama-modal-body">
            <div class="cmama-presets-grid">
                <div class="cmama-preset" data-preset="default">
                    <div class="cmama-preset-colors">
                        <span style="background: #007cba;"></span>
                        <span style="background: #007cba;"></span>
                        <span style="background: #ffffff;"></span>
                        <span style="background: #f0f0f0;"></span>
                    </div>
                    <h4><?php _e('Default Blue', CMAMA_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="cmama-preset" data-preset="green">
                    <div class="cmama-preset-colors">
                        <span style="background: #28a745;"></span>
                        <span style="background: #28a745;"></span>
                        <span style="background: #ffffff;"></span>
                        <span style="background: #f8f9fa;"></span>
                    </div>
                    <h4><?php _e('Fresh Green', CMAMA_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="cmama-preset" data-preset="purple">
                    <div class="cmama-preset-colors">
                        <span style="background: #6f42c1;"></span>
                        <span style="background: #6f42c1;"></span>
                        <span style="background: #ffffff;"></span>
                        <span style="background: #f8f9fa;"></span>
                    </div>
                    <h4><?php _e('Royal Purple', CMAMA_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="cmama-preset" data-preset="orange">
                    <div class="cmama-preset-colors">
                        <span style="background: #fd7e14;"></span>
                        <span style="background: #fd7e14;"></span>
                        <span style="background: #ffffff;"></span>
                        <span style="background: #fff3cd;"></span>
                    </div>
                    <h4><?php _e('Vibrant Orange', CMAMA_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="cmama-preset" data-preset="dark">
                    <div class="cmama-preset-colors">
                        <span style="background: #343a40;"></span>
                        <span style="background: #495057;"></span>
                        <span style="background: #ffffff;"></span>
                        <span style="background: #e9ecef;"></span>
                    </div>
                    <h4><?php _e('Dark Theme', CMAMA_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="cmama-preset" data-preset="red">
                    <div class="cmama-preset-colors">
                        <span style="background: #dc3545;"></span>
                        <span style="background: #dc3545;"></span>
                        <span style="background: #ffffff;"></span>
                        <span style="background: #f8d7da;"></span>
                    </div>
                    <h4><?php _e('Bold Red', CMAMA_TEXT_DOMAIN); ?></h4>
                </div>
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
    // Initialize color pickers
    $('.cmama-color-picker').wpColorPicker({
        change: function() {
            updatePreview();
        },
        clear: function() {
            updatePreview();
        }
    });

    // Range input updates
    $('input[type="range"]').on('input', function() {
        var $range = $(this);
        var $valueDisplay = $range.next('.cmama-range-value');
        $valueDisplay.text($range.val() + 'px');
        updatePreview();
    });

    // Font family change
    $('#font_family').on('change', updatePreview);

    // Custom CSS change
    $('#custom_css').on('input', debounce(updatePreview, 500));

    // Update preview function
    function updatePreview() {
        var $calculator = $('#cmama-sample-calculator');
        var styles = {
            '--cmama-primary-color': $('#primary_color').val(),
            '--cmama-button-color': $('#button_color').val(),
            '--cmama-input-color': $('#input_color').val(),
            '--cmama-result-color': $('#result_color').val(),
            '--cmama-border-radius': $('#border_radius').val() + 'px',
            '--cmama-padding': $('#padding').val() + 'px'
        };

        var fontFamily = $('#font_family').val();
        if (fontFamily !== 'inherit') {
            styles['--cmama-font-family'] = fontFamily;
        }

        // Apply CSS custom properties
        var cssText = '';
        for (var prop in styles) {
            cssText += prop + ': ' + styles[prop] + '; ';
        }
        $calculator.attr('style', cssText);

        // Apply custom CSS
        var customCSS = $('#custom_css').val();
        var $customStyle = $('#cmama-custom-preview-css');
        if ($customStyle.length === 0) {
            $customStyle = $('<style id="cmama-custom-preview-css"></style>').appendTo('head');
        }
        $customStyle.text('#cmama-sample-calculator { ' + customCSS + ' }');
    }

    // Debounce function
    function debounce(func, wait) {
        var timeout;
        return function executedFunction() {
            var later = function() {
                clearTimeout(timeout);
                func();
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Form submission
    $('#cmama-appearance-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalText = $submitBtn.html();
        
        $submitBtn.prop('disabled', true)
                  .html('<span class="dashicons dashicons-update-alt"></span> ' + cmamaAdmin.strings.saving);

        var formData = $form.serialize();
        formData += '&action=cmama_save_appearance';

        $.post(cmamaAdmin.ajaxUrl, formData, function(response) {
            if (response.success) {
                showNotice(cmamaAdmin.strings.saved, 'success');
            } else {
                showNotice(response.data || cmamaAdmin.strings.error, 'error');
            }
        }).fail(function() {
            showNotice(cmamaAdmin.strings.error, 'error');
        }).always(function() {
            $submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Reset to defaults
    $('#cmama-reset-appearance').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to reset all appearance settings to defaults?', CMAMA_TEXT_DOMAIN); ?>')) {
            var defaults = {
                primary_color: '#007cba',
                button_color: '#007cba',
                input_color: '#ffffff',
                result_color: '#f0f0f0',
                font_family: 'inherit',
                border_radius: '4',
                padding: '20',
                custom_css: ''
            };

            for (var field in defaults) {
                var $field = $('#' + field);
                if ($field.hasClass('cmama-color-picker')) {
                    $field.wpColorPicker('color', defaults[field]);
                } else {
                    $field.val(defaults[field]);
                    if ($field.attr('type') === 'range') {
                        $field.next('.cmama-range-value').text(defaults[field] + 'px');
                    }
                }
            }

            updatePreview();
        }
    });

    // Preview changes button
    $('#cmama-preview-changes, #cmama-refresh-preview').on('click', function() {
        updatePreview();
        $('html, body').animate({
            scrollTop: $('.cmama-appearance-preview').offset().top - 50
        }, 500);
    });

    // Color presets
    $('.cmama-preset').on('click', function() {
        var preset = $(this).data('preset');
        var presets = {
            'default': {primary_color: '#007cba', button_color: '#007cba', input_color: '#ffffff', result_color: '#f0f0f0'},
            'green': {primary_color: '#28a745', button_color: '#28a745', input_color: '#ffffff', result_color: '#f8f9fa'},
            'purple': {primary_color: '#6f42c1', button_color: '#6f42c1', input_color: '#ffffff', result_color: '#f8f9fa'},
            'orange': {primary_color: '#fd7e14', button_color: '#fd7e14', input_color: '#ffffff', result_color: '#fff3cd'},
            'dark': {primary_color: '#343a40', button_color: '#495057', input_color: '#ffffff', result_color: '#e9ecef'},
            'red': {primary_color: '#dc3545', button_color: '#dc3545', input_color: '#ffffff', result_color: '#f8d7da'}
        };

        if (presets[preset]) {
            for (var field in presets[preset]) {
                $('#' + field).wpColorPicker('color', presets[preset][field]);
            }
            updatePreview();
        }

        $('#cmama-presets-modal').hide();
    });

    // Modal functionality
    $('.cmama-modal-close').on('click', function() {
        $(this).closest('.cmama-modal').hide();
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

    // Initialize preview
    updatePreview();
});
</script>