<?php
/**
 * Appearance settings
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Appearance class
 */
class Calculator_Mama_Appearance {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue scripts
     *
     * @param string $hook
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'calculator-mama_page_calculator-mama-appearance') {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * Render appearance page
     */
    public function render() {
        $database = new Calculator_Mama_Database();
        $appearance_settings = $database->get_appearance_settings();
        ?>
        <div class="wrap cmama-appearance">
            <h1><?php _e('Appearance Settings', 'calculator-mama'); ?></h1>
            
            <form id="cmama-appearance-form">
                <div class="cmama-appearance-sections">
                    <div class="cmama-section">
                        <h2><?php _e('Colors', 'calculator-mama'); ?></h2>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="primary_color"><?php _e('Primary Color', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="primary_color" name="primary_color" value="<?php echo esc_attr($appearance_settings['primary_color']); ?>" class="cmama-color-picker">
                                        <p class="description"><?php _e('Main color for calculator elements.', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="button_color"><?php _e('Button Color', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="button_color" name="button_color" value="<?php echo esc_attr($appearance_settings['button_color']); ?>" class="cmama-color-picker">
                                        <p class="description"><?php _e('Color for buttons and interactive elements.', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="input_color"><?php _e('Input Color', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="input_color" name="input_color" value="<?php echo esc_attr($appearance_settings['input_color']); ?>" class="cmama-color-picker">
                                        <p class="description"><?php _e('Background color for input fields.', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="result_color"><?php _e('Result Color', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="result_color" name="result_color" value="<?php echo esc_attr($appearance_settings['result_color']); ?>" class="cmama-color-picker">
                                        <p class="description"><?php _e('Color for result values and highlights.', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="cmama-section">
                        <h2><?php _e('Typography', 'calculator-mama'); ?></h2>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="font_family"><?php _e('Font Family', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <select id="font_family" name="font_family">
                                            <option value="inherit" <?php selected($appearance_settings['font_family'], 'inherit'); ?>><?php _e('Inherit from theme', 'calculator-mama'); ?></option>
                                            <option value="Arial, sans-serif" <?php selected($appearance_settings['font_family'], 'Arial, sans-serif'); ?>>Arial</option>
                                            <option value="Helvetica, sans-serif" <?php selected($appearance_settings['font_family'], 'Helvetica, sans-serif'); ?>>Helvetica</option>
                                            <option value="Georgia, serif" <?php selected($appearance_settings['font_family'], 'Georgia, serif'); ?>>Georgia</option>
                                            <option value="Times New Roman, serif" <?php selected($appearance_settings['font_family'], 'Times New Roman, serif'); ?>>Times New Roman</option>
                                            <option value="Courier New, monospace" <?php selected($appearance_settings['font_family'], 'Courier New, monospace'); ?>>Courier New</option>
                                        </select>
                                        <p class="description"><?php _e('Font family for calculator text.', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="cmama-section">
                        <h2><?php _e('Layout', 'calculator-mama'); ?></h2>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="border_radius"><?php _e('Border Radius', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="border_radius" name="border_radius" value="<?php echo esc_attr($appearance_settings['border_radius']); ?>" placeholder="4px">
                                        <p class="description"><?php _e('Border radius for calculator elements (e.g., 4px, 8px).', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="padding"><?php _e('Padding', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="padding" name="padding" value="<?php echo esc_attr($appearance_settings['padding']); ?>" placeholder="20px">
                                        <p class="description"><?php _e('Internal spacing for calculator container (e.g., 20px, 1rem).', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="cmama-section">
                        <h2><?php _e('Custom CSS', 'calculator-mama'); ?></h2>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="custom_css"><?php _e('Additional CSS', 'calculator-mama'); ?></label>
                                    </th>
                                    <td>
                                        <textarea id="custom_css" name="custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($appearance_settings['custom_css']); ?></textarea>
                                        <p class="description"><?php _e('Add custom CSS to further customize calculator appearance.', 'calculator-mama'); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cmama-preview-section">
                    <h2><?php _e('Live Preview', 'calculator-mama'); ?></h2>
                    <div class="cmama-preview-container">
                        <div class="cmama-preview-calculator">
                            <div class="cmama-calculator-header">
                                <h3 class="cmama-calculator-title"><?php _e('Sample Calculator', 'calculator-mama'); ?></h3>
                                <p class="cmama-calculator-description"><?php _e('This is how your calculators will look with the current settings.', 'calculator-mama'); ?></p>
                            </div>
                            <form class="cmama-calculator-form">
                                <div class="cmama-inputs">
                                    <div class="cmama-field">
                                        <label for="preview-input"><?php _e('Sample Input', 'calculator-mama'); ?></label>
                                        <input type="number" id="preview-input" class="cmama-input" value="100">
                                    </div>
                                </div>
                                <div class="cmama-actions">
                                    <button type="button" class="cmama-button"><?php _e('Calculate', 'calculator-mama'); ?></button>
                                </div>
                                <div class="cmama-results">
                                    <h4><?php _e('Results', 'calculator-mama'); ?></h4>
                                    <div class="cmama-results-grid">
                                        <div class="cmama-result">
                                            <span class="cmama-result-label"><?php _e('Result', 'calculator-mama'); ?></span>
                                            <span class="cmama-result-value">$1,000.00</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Changes', 'calculator-mama'); ?></button>
                    <button type="button" class="button" id="cmama-reset-appearance"><?php _e('Reset to Default', 'calculator-mama'); ?></button>
                </p>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Initialize color pickers
            $('.cmama-color-picker').wpColorPicker();

            // Live preview update
            function updatePreview() {
                var primaryColor = $('#primary_color').val();
                var buttonColor = $('#button_color').val();
                var inputColor = $('#input_color').val();
                var resultColor = $('#result_color').val();
                var fontFamily = $('#font_family').val();
                var borderRadius = $('#border_radius').val();
                var padding = $('#padding').val();

                var css = '';
                if (primaryColor) css += '.cmama-preview-calculator { --cmama-primary-color: ' + primaryColor + '; }';
                if (buttonColor) css += '.cmama-preview-calculator { --cmama-button-color: ' + buttonColor + '; }';
                if (inputColor) css += '.cmama-preview-calculator { --cmama-input-color: ' + inputColor + '; }';
                if (resultColor) css += '.cmama-preview-calculator { --cmama-result-color: ' + resultColor + '; }';
                if (fontFamily && fontFamily !== 'inherit') css += '.cmama-preview-calculator { font-family: ' + fontFamily + '; }';
                if (borderRadius) css += '.cmama-preview-calculator { --cmama-border-radius: ' + borderRadius + '; }';
                if (padding) css += '.cmama-preview-calculator { --cmama-padding: ' + padding + '; }';

                $('#cmama-preview-styles').remove();
                $('head').append('<style id="cmama-preview-styles">' + css + '</style>');
            }

            // Update preview on change
            $('input, select').on('change input', updatePreview);
            updatePreview();

            // Form submission
            $('#cmama-appearance-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cmama_save_appearance',
                        nonce: '<?php echo wp_create_nonce('cmama_admin_nonce'); ?>',
                        ...Object.fromEntries(new URLSearchParams(formData))
                    },
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).text('<?php _e('Saving...', 'calculator-mama'); ?>');
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('<?php _e('Settings saved successfully!', 'calculator-mama'); ?>');
                        } else {
                            alert('<?php _e('Error saving settings. Please try again.', 'calculator-mama'); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php _e('Error saving settings. Please try again.', 'calculator-mama'); ?>');
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).text('<?php _e('Save Changes', 'calculator-mama'); ?>');
                    }
                });
            });

            // Reset to default
            $('#cmama-reset-appearance').on('click', function() {
                if (confirm('<?php _e('Are you sure you want to reset all appearance settings to default?', 'calculator-mama'); ?>')) {
                    $('#primary_color').val('#0073aa').trigger('change');
                    $('#button_color').val('#0073aa').trigger('change');
                    $('#input_color').val('#ffffff').trigger('change');
                    $('#result_color').val('#28a745').trigger('change');
                    $('#font_family').val('inherit').trigger('change');
                    $('#border_radius').val('4px').trigger('change');
                    $('#padding').val('20px').trigger('change');
                    $('#custom_css').val('').trigger('change');
                }
            });
        });
        </script>
        <?php
    }
}