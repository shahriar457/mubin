<?php
/**
 * BMI Calculator
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once CMAMA_CALCULATORS_DIR . 'base/class-calculator-base.php';

/**
 * BMI Calculator class
 */
class Calculator_Mama_Bmi extends Calculator_Mama_Base {

    /**
     * Get calculator information
     *
     * @return array
     */
    public function get_info() {
        return array(
            'slug' => 'bmi',
            'name' => __('BMI Calculator', 'calculator-mama'),
            'description' => __('Calculate your Body Mass Index and get health recommendations.', 'calculator-mama'),
            'category' => 'health',
            'icon' => 'dashicons-heart',
            'version' => '1.0',
            'css_file' => CMAMA_CALCULATORS_DIR . 'bmi/style.css',
            'js_file' => CMAMA_CALCULATORS_DIR . 'bmi/script.js'
        );
    }

    /**
     * Render calculator
     *
     * @param array $atts
     * @return string
     */
    public function render($atts = array()) {
        $this->atts = $atts;
        $this->info = $this->get_info();

        ob_start();
        ?>
        <div<?php echo $this->get_wrapper_attributes(); ?>>
            <div class="cmama-calculator-header">
                <h3 class="cmama-calculator-title"><?php echo esc_html($this->info['name']); ?></h3>
                <p class="cmama-calculator-description"><?php echo esc_html($this->info['description']); ?></p>
            </div>

            <form class="cmama-calculator-form" id="cmama-bmi-form">
                <div class="cmama-inputs">
                    <?php echo $this->render_input('weight', __('Weight (kg)', 'calculator-mama'), 'number', array('step' => '0.1', 'min' => '1', 'max' => '500', 'required' => true)); ?>
                    <?php echo $this->render_input('height', __('Height (cm)', 'calculator-mama'), 'number', array('step' => '0.1', 'min' => '50', 'max' => '300', 'required' => true)); ?>
                </div>

                <div class="cmama-actions">
                    <?php echo $this->render_button(__('Calculate BMI', 'calculator-mama'), 'submit'); ?>
                    <button type="button" class="cmama-button cmama-button-secondary" id="cmama-reset"><?php _e('Reset', 'calculator-mama'); ?></button>
                </div>

                <div class="cmama-results" id="cmama-bmi-results" style="display: none;">
                    <h4><?php _e('Results', 'calculator-mama'); ?></h4>
                    <div class="cmama-results-grid">
                        <?php echo $this->render_result(__('BMI', 'calculator-mama'), '', 'cmama-bmi-value'); ?>
                        <?php echo $this->render_result(__('Category', 'calculator-mama'), '', 'cmama-bmi-category'); ?>
                    </div>
                    <div class="cmama-bmi-chart">
                        <div class="cmama-bmi-scale">
                            <div class="cmama-bmi-range underweight"><?php _e('Underweight', 'calculator-mama'); ?><br>< 18.5</div>
                            <div class="cmama-bmi-range normal"><?php _e('Normal', 'calculator-mama'); ?><br>18.5 - 24.9</div>
                            <div class="cmama-bmi-range overweight"><?php _e('Overweight', 'calculator-mama'); ?><br>25 - 29.9</div>
                            <div class="cmama-bmi-range obese"><?php _e('Obese', 'calculator-mama'); ?><br>> 30</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Calculate BMI
     *
     * @param array $data
     * @return array
     */
    public function calculate($data) {
        $data = $this->sanitize_input($data);
        $validated = $this->validate_input($data);

        if (is_wp_error($validated)) {
            return array('error' => $validated->get_error_message());
        }

        $weight = floatval($data['weight']);
        $height = floatval($data['height']);

        if ($weight <= 0 || $height <= 0) {
            return array('error' => __('Weight and height must be greater than 0.', 'calculator-mama'));
        }

        // Convert height from cm to meters
        $height_m = $height / 100;

        // Calculate BMI
        $bmi = $weight / ($height_m * $height_m);

        // Determine category
        $category = $this->get_bmi_category($bmi);

        return array(
            'bmi' => $this->format_number($bmi, 1),
            'category' => $category,
            'raw_bmi' => $bmi
        );
    }

    /**
     * Get BMI category
     *
     * @param float $bmi
     * @return string
     */
    private function get_bmi_category($bmi) {
        if ($bmi < 18.5) {
            return __('Underweight', 'calculator-mama');
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return __('Normal weight', 'calculator-mama');
        } elseif ($bmi >= 25 && $bmi < 30) {
            return __('Overweight', 'calculator-mama');
        } else {
            return __('Obese', 'calculator-mama');
        }
    }
}