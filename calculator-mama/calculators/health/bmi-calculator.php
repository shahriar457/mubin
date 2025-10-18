<?php
/**
 * BMI Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * BMI Calculator Class
 *
 * @since 1.0.0
 */
class CMAMA_BMI_Calculator extends CMAMA_Calculator_Base {

    /**
     * Initialize calculator.
     *
     * @since 1.0.0
     */
    protected function init() {
        $this->slug = 'bmi-calculator';
        $this->name = __('BMI Calculator', CMAMA_TEXT_DOMAIN);
        $this->description = __('Calculate your Body Mass Index (BMI) and determine your weight category.', CMAMA_TEXT_DOMAIN);
        $this->category = 'health';
        $this->tags = array('bmi', 'body mass index', 'weight', 'height', 'health', 'fitness', 'obesity');
        $this->schema_type = 'MedicalRiskCalculator';

        $this->fields = array(
            'unit_system' => array(
                'type' => 'radio',
                'label' => __('Unit System', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'default' => 'metric',
                'options' => array(
                    'metric' => __('Metric (kg, cm)', CMAMA_TEXT_DOMAIN),
                    'imperial' => __('Imperial (lbs, ft/in)', CMAMA_TEXT_DOMAIN)
                ),
                'description' => __('Choose your preferred unit system', CMAMA_TEXT_DOMAIN)
            ),
            'weight_kg' => array(
                'type' => 'number',
                'label' => __('Weight (kg)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter weight in kilograms', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '20',
                'max' => '300',
                'step' => '0.1',
                'default' => '70',
                'description' => __('Your weight in kilograms', CMAMA_TEXT_DOMAIN),
                'css_class' => 'cmama-metric-field'
            ),
            'height_cm' => array(
                'type' => 'number',
                'label' => __('Height (cm)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter height in centimeters', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '100',
                'max' => '250',
                'step' => '0.1',
                'default' => '170',
                'description' => __('Your height in centimeters', CMAMA_TEXT_DOMAIN),
                'css_class' => 'cmama-metric-field'
            ),
            'weight_lbs' => array(
                'type' => 'number',
                'label' => __('Weight (lbs)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter weight in pounds', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '50',
                'max' => '700',
                'step' => '0.1',
                'default' => '154',
                'description' => __('Your weight in pounds', CMAMA_TEXT_DOMAIN),
                'css_class' => 'cmama-imperial-field'
            ),
            'height_ft' => array(
                'type' => 'number',
                'label' => __('Height (feet)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter feet', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '3',
                'max' => '8',
                'step' => '1',
                'default' => '5',
                'description' => __('Height in feet', CMAMA_TEXT_DOMAIN),
                'css_class' => 'cmama-imperial-field'
            ),
            'height_in' => array(
                'type' => 'number',
                'label' => __('Height (inches)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter inches', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '0',
                'max' => '11',
                'step' => '1',
                'default' => '7',
                'description' => __('Additional inches', CMAMA_TEXT_DOMAIN),
                'css_class' => 'cmama-imperial-field'
            ),
            'age' => array(
                'type' => 'number',
                'label' => __('Age (optional)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Enter your age', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'min' => '2',
                'max' => '120',
                'step' => '1',
                'default' => '30',
                'description' => __('Age for additional health insights', CMAMA_TEXT_DOMAIN)
            ),
            'gender' => array(
                'type' => 'select',
                'label' => __('Gender (optional)', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'default' => '',
                'options' => array(
                    '' => __('Select gender', CMAMA_TEXT_DOMAIN),
                    'male' => __('Male', CMAMA_TEXT_DOMAIN),
                    'female' => __('Female', CMAMA_TEXT_DOMAIN)
                ),
                'description' => __('Gender for additional health insights', CMAMA_TEXT_DOMAIN)
            )
        );
    }

    /**
     * Calculate BMI and health category.
     *
     * @since 1.0.0
     * @param array $inputs Input values.
     * @return array Calculation results.
     */
    public function calculate($inputs) {
        $unit_system = $inputs['unit_system'];
        
        // Convert to metric if needed
        if ($unit_system === 'metric') {
            $weight_kg = floatval($inputs['weight_kg']);
            $height_cm = floatval($inputs['height_cm']);
        } else {
            $weight_lbs = floatval($inputs['weight_lbs']);
            $height_ft = floatval($inputs['height_ft']);
            $height_in = floatval($inputs['height_in']);
            
            // Convert to metric
            $weight_kg = $weight_lbs * 0.453592;
            $height_cm = ($height_ft * 12 + $height_in) * 2.54;
        }

        $height_m = $height_cm / 100;
        
        // Calculate BMI
        $bmi = $weight_kg / ($height_m * $height_m);
        
        // Determine BMI category
        $category = $this->get_bmi_category($bmi);
        $category_info = $this->get_category_info($category);
        
        // Calculate ideal weight range
        $ideal_weight_range = $this->calculate_ideal_weight_range($height_cm, $unit_system);
        
        // Additional calculations
        $age = !empty($inputs['age']) ? intval($inputs['age']) : null;
        $gender = !empty($inputs['gender']) ? $inputs['gender'] : null;
        
        $health_risks = $this->get_health_risks($bmi, $age, $gender);
        $recommendations = $this->get_recommendations($category, $bmi);

        return array(
            'bmi' => $bmi,
            'category' => $category,
            'category_info' => $category_info,
            'weight_kg' => $weight_kg,
            'weight_lbs' => $weight_kg * 2.20462,
            'height_cm' => $height_cm,
            'height_ft_in' => $this->cm_to_feet_inches($height_cm),
            'ideal_weight_range' => $ideal_weight_range,
            'health_risks' => $health_risks,
            'recommendations' => $recommendations,
            'unit_system' => $unit_system
        );
    }

    /**
     * Get BMI category.
     *
     * @since 1.0.0
     * @param float $bmi BMI value.
     * @return string BMI category.
     */
    private function get_bmi_category($bmi) {
        if ($bmi < 18.5) {
            return 'underweight';
        } elseif ($bmi < 25) {
            return 'normal';
        } elseif ($bmi < 30) {
            return 'overweight';
        } else {
            return 'obese';
        }
    }

    /**
     * Get category information.
     *
     * @since 1.0.0
     * @param string $category BMI category.
     * @return array Category information.
     */
    private function get_category_info($category) {
        $categories = array(
            'underweight' => array(
                'name' => __('Underweight', CMAMA_TEXT_DOMAIN),
                'range' => __('Below 18.5', CMAMA_TEXT_DOMAIN),
                'color' => '#3498db',
                'description' => __('You may be underweight. Consider consulting with a healthcare provider.', CMAMA_TEXT_DOMAIN)
            ),
            'normal' => array(
                'name' => __('Normal Weight', CMAMA_TEXT_DOMAIN),
                'range' => __('18.5 - 24.9', CMAMA_TEXT_DOMAIN),
                'color' => '#27ae60',
                'description' => __('You have a healthy weight. Maintain your current lifestyle.', CMAMA_TEXT_DOMAIN)
            ),
            'overweight' => array(
                'name' => __('Overweight', CMAMA_TEXT_DOMAIN),
                'range' => __('25.0 - 29.9', CMAMA_TEXT_DOMAIN),
                'color' => '#f39c12',
                'description' => __('You may be overweight. Consider a healthy diet and regular exercise.', CMAMA_TEXT_DOMAIN)
            ),
            'obese' => array(
                'name' => __('Obese', CMAMA_TEXT_DOMAIN),
                'range' => __('30.0 and above', CMAMA_TEXT_DOMAIN),
                'color' => '#e74c3c',
                'description' => __('You may be obese. Consider consulting with a healthcare provider for a weight management plan.', CMAMA_TEXT_DOMAIN)
            )
        );

        return isset($categories[$category]) ? $categories[$category] : $categories['normal'];
    }

    /**
     * Calculate ideal weight range.
     *
     * @since 1.0.0
     * @param float $height_cm Height in centimeters.
     * @param string $unit_system Unit system.
     * @return array Ideal weight range.
     */
    private function calculate_ideal_weight_range($height_cm, $unit_system) {
        $height_m = $height_cm / 100;
        
        // Normal BMI range is 18.5 - 24.9
        $min_weight_kg = 18.5 * ($height_m * $height_m);
        $max_weight_kg = 24.9 * ($height_m * $height_m);
        
        if ($unit_system === 'imperial') {
            return array(
                'min' => $min_weight_kg * 2.20462,
                'max' => $max_weight_kg * 2.20462,
                'unit' => 'lbs'
            );
        } else {
            return array(
                'min' => $min_weight_kg,
                'max' => $max_weight_kg,
                'unit' => 'kg'
            );
        }
    }

    /**
     * Convert centimeters to feet and inches.
     *
     * @since 1.0.0
     * @param float $cm Height in centimeters.
     * @return array Feet and inches.
     */
    private function cm_to_feet_inches($cm) {
        $total_inches = $cm / 2.54;
        $feet = floor($total_inches / 12);
        $inches = round($total_inches % 12);
        
        return array(
            'feet' => $feet,
            'inches' => $inches,
            'display' => $feet . "' " . $inches . '"'
        );
    }

    /**
     * Get health risks based on BMI.
     *
     * @since 1.0.0
     * @param float $bmi BMI value.
     * @param int $age Age.
     * @param string $gender Gender.
     * @return array Health risks.
     */
    private function get_health_risks($bmi, $age = null, $gender = null) {
        $risks = array();

        if ($bmi < 18.5) {
            $risks[] = __('Nutritional deficiencies', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Weakened immune system', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Osteoporosis', CMAMA_TEXT_DOMAIN);
        } elseif ($bmi >= 25 && $bmi < 30) {
            $risks[] = __('Increased risk of heart disease', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Higher blood pressure', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Type 2 diabetes risk', CMAMA_TEXT_DOMAIN);
        } elseif ($bmi >= 30) {
            $risks[] = __('High risk of heart disease', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Type 2 diabetes', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Sleep apnea', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Stroke risk', CMAMA_TEXT_DOMAIN);
            $risks[] = __('Certain cancers', CMAMA_TEXT_DOMAIN);
        }

        return $risks;
    }

    /**
     * Get recommendations based on BMI category.
     *
     * @since 1.0.0
     * @param string $category BMI category.
     * @param float $bmi BMI value.
     * @return array Recommendations.
     */
    private function get_recommendations($category, $bmi) {
        $recommendations = array();

        switch ($category) {
            case 'underweight':
                $recommendations[] = __('Increase caloric intake with healthy foods', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Include protein-rich foods in your diet', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Consider strength training exercises', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Consult with a healthcare provider', CMAMA_TEXT_DOMAIN);
                break;

            case 'normal':
                $recommendations[] = __('Maintain your current healthy lifestyle', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Continue regular physical activity', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Eat a balanced diet', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Monitor your weight regularly', CMAMA_TEXT_DOMAIN);
                break;

            case 'overweight':
                $recommendations[] = __('Create a moderate caloric deficit', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Increase physical activity to 150+ minutes per week', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Focus on whole foods and reduce processed foods', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Consider consulting with a nutritionist', CMAMA_TEXT_DOMAIN);
                break;

            case 'obese':
                $recommendations[] = __('Consult with a healthcare provider for a comprehensive plan', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Consider medically supervised weight loss', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Start with low-impact exercises', CMAMA_TEXT_DOMAIN);
                $recommendations[] = __('Focus on sustainable lifestyle changes', CMAMA_TEXT_DOMAIN);
                break;
        }

        return $recommendations;
    }

    /**
     * Format result for display.
     *
     * @since 1.0.0
     * @param array $result Raw result data.
     * @return string Formatted HTML.
     */
    public function format_result($result) {
        if (empty($result)) {
            return '<p>' . __('No result to display.', CMAMA_TEXT_DOMAIN) . '</p>';
        }

        $output = '<div class="cmama-result-data cmama-bmi-results">';
        
        // BMI Score and Category
        $output .= '<div class="cmama-result-section cmama-bmi-score">';
        $output .= '<div class="cmama-bmi-display">';
        $output .= '<div class="cmama-bmi-number" style="color: ' . esc_attr($result['category_info']['color']) . '">';
        $output .= number_format($result['bmi'], 1);
        $output .= '</div>';
        $output .= '<div class="cmama-bmi-category" style="background-color: ' . esc_attr($result['category_info']['color']) . '">';
        $output .= esc_html($result['category_info']['name']);
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<p class="cmama-bmi-description">' . esc_html($result['category_info']['description']) . '</p>';
        $output .= '</div>';

        // BMI Scale
        $output .= '<div class="cmama-result-section cmama-bmi-scale">';
        $output .= '<h4>' . __('BMI Scale', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<div class="cmama-bmi-scale-bar">';
        $output .= '<div class="cmama-scale-segment cmama-underweight" title="' . __('Underweight: Below 18.5', CMAMA_TEXT_DOMAIN) . '"></div>';
        $output .= '<div class="cmama-scale-segment cmama-normal" title="' . __('Normal: 18.5 - 24.9', CMAMA_TEXT_DOMAIN) . '"></div>';
        $output .= '<div class="cmama-scale-segment cmama-overweight" title="' . __('Overweight: 25.0 - 29.9', CMAMA_TEXT_DOMAIN) . '"></div>';
        $output .= '<div class="cmama-scale-segment cmama-obese" title="' . __('Obese: 30.0+', CMAMA_TEXT_DOMAIN) . '"></div>';
        $output .= '</div>';
        $output .= '</div>';

        // Measurements
        $output .= '<div class="cmama-result-section cmama-measurements">';
        $output .= '<h4>' . __('Your Measurements', CMAMA_TEXT_DOMAIN) . '</h4>';
        
        if ($result['unit_system'] === 'metric') {
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Weight', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['weight_kg'], 1) . ' kg</span>';
            $output .= '</div>';
            
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Height', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['height_cm'], 1) . ' cm</span>';
            $output .= '</div>';
        } else {
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Weight', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . number_format($result['weight_lbs'], 1) . ' lbs</span>';
            $output .= '</div>';
            
            $output .= '<div class="cmama-result-item">';
            $output .= '<span class="cmama-result-label">' . __('Height', CMAMA_TEXT_DOMAIN) . ':</span> ';
            $output .= '<span class="cmama-result-value">' . esc_html($result['height_ft_in']['display']) . '</span>';
            $output .= '</div>';
        }
        $output .= '</div>';

        // Ideal Weight Range
        $output .= '<div class="cmama-result-section cmama-ideal-weight">';
        $output .= '<h4>' . __('Healthy Weight Range', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Ideal Weight Range', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">';
        $output .= number_format($result['ideal_weight_range']['min'], 1) . ' - ';
        $output .= number_format($result['ideal_weight_range']['max'], 1) . ' ';
        $output .= esc_html($result['ideal_weight_range']['unit']);
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';

        // Health Risks
        if (!empty($result['health_risks'])) {
            $output .= '<div class="cmama-result-section cmama-health-risks">';
            $output .= '<h4>' . __('Potential Health Risks', CMAMA_TEXT_DOMAIN) . '</h4>';
            $output .= '<ul class="cmama-risk-list">';
            foreach ($result['health_risks'] as $risk) {
                $output .= '<li>' . esc_html($risk) . '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }

        // Recommendations
        if (!empty($result['recommendations'])) {
            $output .= '<div class="cmama-result-section cmama-recommendations">';
            $output .= '<h4>' . __('Recommendations', CMAMA_TEXT_DOMAIN) . '</h4>';
            $output .= '<ul class="cmama-recommendation-list">';
            foreach ($result['recommendations'] as $recommendation) {
                $output .= '<li>' . esc_html($recommendation) . '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }

        $output .= '<div class="cmama-disclaimer">';
        $output .= '<p><small>' . __('Disclaimer: This BMI calculator is for informational purposes only and should not replace professional medical advice. Consult with a healthcare provider for personalized health guidance.', CMAMA_TEXT_DOMAIN) . '</small></p>';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }
}

// Register the calculator
add_action('cmama_register_calculators', function($registry) {
    $registry->register_calculator('bmi-calculator', new CMAMA_BMI_Calculator());
});