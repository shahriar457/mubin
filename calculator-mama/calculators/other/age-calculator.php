<?php
/**
 * Age Calculator
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Age Calculator Class
 *
 * @since 1.0.0
 */
class CMAMA_Age_Calculator extends CMAMA_Calculator_Base {

    /**
     * Initialize calculator.
     *
     * @since 1.0.0
     */
    protected function init() {
        $this->slug = 'age-calculator';
        $this->name = __('Age Calculator', CMAMA_TEXT_DOMAIN);
        $this->description = __('Calculate your exact age in years, months, days, hours, minutes, and seconds.', CMAMA_TEXT_DOMAIN);
        $this->category = 'other';
        $this->tags = array('age', 'birthday', 'date', 'time', 'years', 'months', 'days');
        $this->schema_type = 'WebApplication';

        $this->fields = array(
            'birth_date' => array(
                'type' => 'date',
                'label' => __('Birth Date', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Select your birth date', CMAMA_TEXT_DOMAIN),
                'required' => true,
                'default' => '1990-01-01',
                'description' => __('Enter your date of birth', CMAMA_TEXT_DOMAIN)
            ),
            'calculation_date' => array(
                'type' => 'date',
                'label' => __('Calculate Age On', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Select calculation date', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'default' => date('Y-m-d'),
                'description' => __('Date to calculate age on (leave blank for today)', CMAMA_TEXT_DOMAIN)
            ),
            'birth_time' => array(
                'type' => 'time',
                'label' => __('Birth Time (Optional)', CMAMA_TEXT_DOMAIN),
                'placeholder' => __('Select birth time', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'default' => '12:00',
                'description' => __('Time of birth for more precise calculation', CMAMA_TEXT_DOMAIN)
            ),
            'timezone' => array(
                'type' => 'select',
                'label' => __('Timezone', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'default' => 'UTC',
                'options' => $this->get_timezone_options(),
                'description' => __('Select your timezone', CMAMA_TEXT_DOMAIN)
            ),
            'show_detailed' => array(
                'type' => 'checkbox',
                'label' => __('Show detailed breakdown', CMAMA_TEXT_DOMAIN),
                'required' => false,
                'default' => true,
                'description' => __('Show age in various units (hours, minutes, seconds)', CMAMA_TEXT_DOMAIN)
            )
        );
    }

    /**
     * Get timezone options.
     *
     * @since 1.0.0
     * @return array Timezone options.
     */
    private function get_timezone_options() {
        $timezones = array(
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (US)',
            'America/Chicago' => 'Central Time (US)',
            'America/Denver' => 'Mountain Time (US)',
            'America/Los_Angeles' => 'Pacific Time (US)',
            'Europe/London' => 'London (GMT)',
            'Europe/Paris' => 'Paris (CET)',
            'Europe/Berlin' => 'Berlin (CET)',
            'Asia/Tokyo' => 'Tokyo (JST)',
            'Asia/Shanghai' => 'Shanghai (CST)',
            'Asia/Kolkata' => 'India (IST)',
            'Australia/Sydney' => 'Sydney (AEST)',
            'Pacific/Auckland' => 'Auckland (NZST)'
        );

        return $timezones;
    }

    /**
     * Calculate age in various units.
     *
     * @since 1.0.0
     * @param array $inputs Input values.
     * @return array Calculation results.
     */
    public function calculate($inputs) {
        $birth_date = $inputs['birth_date'];
        $calculation_date = !empty($inputs['calculation_date']) ? $inputs['calculation_date'] : date('Y-m-d');
        $birth_time = !empty($inputs['birth_time']) ? $inputs['birth_time'] : '00:00';
        $timezone = !empty($inputs['timezone']) ? $inputs['timezone'] : 'UTC';
        $show_detailed = !empty($inputs['show_detailed']);

        // Create DateTime objects
        try {
            $birth_datetime = new DateTime($birth_date . ' ' . $birth_time, new DateTimeZone($timezone));
            $calc_datetime = new DateTime($calculation_date . ' ' . date('H:i:s'), new DateTimeZone($timezone));
        } catch (Exception $e) {
            throw new Exception(__('Invalid date or timezone provided.', CMAMA_TEXT_DOMAIN));
        }

        // Check if birth date is in the future
        if ($birth_datetime > $calc_datetime) {
            throw new Exception(__('Birth date cannot be in the future.', CMAMA_TEXT_DOMAIN));
        }

        // Calculate the difference
        $interval = $birth_datetime->diff($calc_datetime);

        // Calculate various age representations
        $total_days = $calc_datetime->diff($birth_datetime)->days;
        $total_hours = $total_days * 24;
        $total_minutes = $total_hours * 60;
        $total_seconds = $total_minutes * 60;

        // More precise calculations
        $birth_timestamp = $birth_datetime->getTimestamp();
        $calc_timestamp = $calc_datetime->getTimestamp();
        $precise_seconds = $calc_timestamp - $birth_timestamp;
        $precise_minutes = floor($precise_seconds / 60);
        $precise_hours = floor($precise_seconds / 3600);
        $precise_days = floor($precise_seconds / 86400);
        $precise_weeks = floor($precise_days / 7);
        $precise_months = $this->calculateMonths($birth_datetime, $calc_datetime);

        // Calculate next birthday
        $next_birthday = $this->calculateNextBirthday($birth_datetime, $calc_datetime);

        // Calculate life milestones
        $milestones = $this->calculateMilestones($birth_datetime, $calc_datetime);

        // Fun facts
        $fun_facts = $this->calculateFunFacts($birth_datetime, $calc_datetime, $precise_days);

        return array(
            'birth_date' => $birth_date,
            'calculation_date' => $calculation_date,
            'birth_time' => $birth_time,
            'timezone' => $timezone,
            'show_detailed' => $show_detailed,
            'age' => array(
                'years' => $interval->y,
                'months' => $interval->m,
                'days' => $interval->d,
                'hours' => $interval->h,
                'minutes' => $interval->i,
                'seconds' => $interval->s
            ),
            'total' => array(
                'days' => $precise_days,
                'hours' => $precise_hours,
                'minutes' => $precise_minutes,
                'seconds' => $precise_seconds,
                'weeks' => $precise_weeks,
                'months' => $precise_months
            ),
            'next_birthday' => $next_birthday,
            'milestones' => $milestones,
            'fun_facts' => $fun_facts,
            'formatted_age' => $this->formatAge($interval)
        );
    }

    /**
     * Calculate months between two dates.
     *
     * @since 1.0.0
     * @param DateTime $start Start date.
     * @param DateTime $end End date.
     * @return int Number of months.
     */
    private function calculateMonths($start, $end) {
        $years = $end->format('Y') - $start->format('Y');
        $months = $end->format('m') - $start->format('m');
        return ($years * 12) + $months;
    }

    /**
     * Calculate next birthday.
     *
     * @since 1.0.0
     * @param DateTime $birth_date Birth date.
     * @param DateTime $current_date Current date.
     * @return array Next birthday information.
     */
    private function calculateNextBirthday($birth_date, $current_date) {
        $current_year = $current_date->format('Y');
        $birth_month_day = $birth_date->format('m-d');
        
        // Try this year's birthday
        $this_year_birthday = new DateTime($current_year . '-' . $birth_month_day);
        
        if ($this_year_birthday <= $current_date) {
            // Birthday has passed this year, calculate for next year
            $next_birthday = new DateTime(($current_year + 1) . '-' . $birth_month_day);
        } else {
            // Birthday hasn't occurred this year yet
            $next_birthday = $this_year_birthday;
        }

        $days_until = $current_date->diff($next_birthday)->days;
        $age_on_birthday = $next_birthday->format('Y') - $birth_date->format('Y');

        return array(
            'date' => $next_birthday->format('Y-m-d'),
            'formatted_date' => $next_birthday->format('F j, Y'),
            'days_until' => $days_until,
            'age_on_birthday' => $age_on_birthday,
            'day_of_week' => $next_birthday->format('l')
        );
    }

    /**
     * Calculate life milestones.
     *
     * @since 1.0.0
     * @param DateTime $birth_date Birth date.
     * @param DateTime $current_date Current date.
     * @return array Milestone information.
     */
    private function calculateMilestones($birth_date, $current_date) {
        $milestones = array();
        $birth_timestamp = $birth_date->getTimestamp();
        $current_timestamp = $current_date->getTimestamp();

        $milestone_days = array(
            100 => '100 days old',
            365 => '1 year old',
            1000 => '1000 days old',
            3650 => '10 years old',
            5000 => '5000 days old',
            7300 => '20 years old',
            10000 => '10000 days old',
            10950 => '30 years old',
            18250 => '50 years old',
            25000 => '25000 days old'
        );

        $days_lived = floor(($current_timestamp - $birth_timestamp) / 86400);

        foreach ($milestone_days as $days => $description) {
            $milestone_date = new DateTime();
            $milestone_date->setTimestamp($birth_timestamp + ($days * 86400));
            
            $milestone_info = array(
                'description' => $description,
                'date' => $milestone_date->format('Y-m-d'),
                'formatted_date' => $milestone_date->format('F j, Y')
            );

            if ($days <= $days_lived) {
                $milestone_info['status'] = 'passed';
                $milestone_info['days_ago'] = $days_lived - $days;
            } else {
                $milestone_info['status'] = 'upcoming';
                $milestone_info['days_until'] = $days - $days_lived;
            }

            $milestones[] = $milestone_info;
        }

        return $milestones;
    }

    /**
     * Calculate fun facts.
     *
     * @since 1.0.0
     * @param DateTime $birth_date Birth date.
     * @param DateTime $current_date Current date.
     * @param int $days_lived Days lived.
     * @return array Fun facts.
     */
    private function calculateFunFacts($birth_date, $current_date, $days_lived) {
        $facts = array();

        // Day of the week born
        $facts[] = sprintf(
            __('You were born on a %s', CMAMA_TEXT_DOMAIN),
            $birth_date->format('l')
        );

        // Approximate heartbeats (average 70 bpm)
        $heartbeats = $days_lived * 24 * 60 * 70;
        $facts[] = sprintf(
            __('Your heart has beaten approximately %s times', CMAMA_TEXT_DOMAIN),
            number_format($heartbeats)
        );

        // Approximate breaths (average 15 per minute)
        $breaths = $days_lived * 24 * 60 * 15;
        $facts[] = sprintf(
            __('You have taken approximately %s breaths', CMAMA_TEXT_DOMAIN),
            number_format($breaths)
        );

        // Sleep time (assuming 8 hours per day)
        $sleep_hours = $days_lived * 8;
        $sleep_days = floor($sleep_hours / 24);
        $facts[] = sprintf(
            __('You have slept for approximately %s days (%s hours)', CMAMA_TEXT_DOMAIN),
            number_format($sleep_days),
            number_format($sleep_hours)
        );

        // Birth month season (Northern Hemisphere)
        $birth_month = $birth_date->format('n');
        $seasons = array(
            'Winter' => array(12, 1, 2),
            'Spring' => array(3, 4, 5),
            'Summer' => array(6, 7, 8),
            'Fall' => array(9, 10, 11)
        );

        foreach ($seasons as $season => $months) {
            if (in_array($birth_month, $months)) {
                $facts[] = sprintf(
                    __('You were born in %s', CMAMA_TEXT_DOMAIN),
                    __($season, CMAMA_TEXT_DOMAIN)
                );
                break;
            }
        }

        return $facts;
    }

    /**
     * Format age as readable string.
     *
     * @since 1.0.0
     * @param DateInterval $interval Date interval.
     * @return string Formatted age.
     */
    private function formatAge($interval) {
        $parts = array();

        if ($interval->y > 0) {
            $parts[] = sprintf(_n('%d year', '%d years', $interval->y, CMAMA_TEXT_DOMAIN), $interval->y);
        }
        if ($interval->m > 0) {
            $parts[] = sprintf(_n('%d month', '%d months', $interval->m, CMAMA_TEXT_DOMAIN), $interval->m);
        }
        if ($interval->d > 0) {
            $parts[] = sprintf(_n('%d day', '%d days', $interval->d, CMAMA_TEXT_DOMAIN), $interval->d);
        }

        if (empty($parts)) {
            return __('Less than a day', CMAMA_TEXT_DOMAIN);
        }

        return implode(', ', $parts);
    }

    /**
     * Render field input with custom handling for date/time fields.
     *
     * @since 1.0.0
     * @param string $field_id Field ID.
     * @param array $field Field configuration.
     */
    protected function render_field_input($field_id, $field) {
        $input_attrs = array(
            'id' => $field_id,
            'name' => $field_id,
            'class' => 'cmama-field-input',
            'value' => $field['default']
        );

        if ($field['required']) {
            $input_attrs['required'] = 'required';
        }

        switch ($field['type']) {
            case 'date':
                $input_attrs['type'] = 'date';
                if ($field_id === 'birth_date') {
                    $input_attrs['max'] = date('Y-m-d');
                }
                echo '<input ' . $this->build_attributes($input_attrs) . '>';
                break;

            case 'time':
                $input_attrs['type'] = 'time';
                echo '<input ' . $this->build_attributes($input_attrs) . '>';
                break;

            default:
                parent::render_field_input($field_id, $field);
                break;
        }
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

        $output = '<div class="cmama-result-data cmama-age-results">';
        
        // Main age display
        $output .= '<div class="cmama-result-section cmama-main-age">';
        $output .= '<div class="cmama-age-display">';
        $output .= '<div class="cmama-age-number">' . $result['age']['years'] . '</div>';
        $output .= '<div class="cmama-age-label">' . _n('Year', 'Years', $result['age']['years'], CMAMA_TEXT_DOMAIN) . '</div>';
        $output .= '</div>';
        $output .= '<p class="cmama-age-detailed">' . esc_html($result['formatted_age']) . '</p>';
        $output .= '</div>';

        // Exact age breakdown
        $output .= '<div class="cmama-result-section cmama-age-breakdown">';
        $output .= '<h4>' . __('Exact Age', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<div class="cmama-age-grid">';
        
        $output .= '<div class="cmama-age-item">';
        $output .= '<span class="cmama-age-value">' . $result['age']['years'] . '</span>';
        $output .= '<span class="cmama-age-unit">' . _n('Year', 'Years', $result['age']['years'], CMAMA_TEXT_DOMAIN) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-age-item">';
        $output .= '<span class="cmama-age-value">' . $result['age']['months'] . '</span>';
        $output .= '<span class="cmama-age-unit">' . _n('Month', 'Months', $result['age']['months'], CMAMA_TEXT_DOMAIN) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-age-item">';
        $output .= '<span class="cmama-age-value">' . $result['age']['days'] . '</span>';
        $output .= '<span class="cmama-age-unit">' . _n('Day', 'Days', $result['age']['days'], CMAMA_TEXT_DOMAIN) . '</span>';
        $output .= '</div>';

        $output .= '</div>';
        $output .= '</div>';

        // Total time lived
        if ($result['show_detailed']) {
            $output .= '<div class="cmama-result-section cmama-total-time">';
            $output .= '<h4>' . __('Total Time Lived', CMAMA_TEXT_DOMAIN) . '</h4>';
            $output .= '<div class="cmama-time-grid">';
            
            $time_units = array(
                'months' => __('Months', CMAMA_TEXT_DOMAIN),
                'weeks' => __('Weeks', CMAMA_TEXT_DOMAIN),
                'days' => __('Days', CMAMA_TEXT_DOMAIN),
                'hours' => __('Hours', CMAMA_TEXT_DOMAIN),
                'minutes' => __('Minutes', CMAMA_TEXT_DOMAIN),
                'seconds' => __('Seconds', CMAMA_TEXT_DOMAIN)
            );

            foreach ($time_units as $unit => $label) {
                $output .= '<div class="cmama-time-item">';
                $output .= '<span class="cmama-time-value">' . number_format($result['total'][$unit]) . '</span>';
                $output .= '<span class="cmama-time-unit">' . $label . '</span>';
                $output .= '</div>';
            }

            $output .= '</div>';
            $output .= '</div>';
        }

        // Next birthday
        $output .= '<div class="cmama-result-section cmama-next-birthday">';
        $output .= '<h4>' . __('Next Birthday', CMAMA_TEXT_DOMAIN) . '</h4>';
        $output .= '<div class="cmama-birthday-info">';
        
        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Date', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . esc_html($result['next_birthday']['formatted_date']) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Day of Week', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . esc_html($result['next_birthday']['day_of_week']) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Days Until', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . number_format($result['next_birthday']['days_until']) . '</span>';
        $output .= '</div>';

        $output .= '<div class="cmama-result-item">';
        $output .= '<span class="cmama-result-label">' . __('Age on Birthday', CMAMA_TEXT_DOMAIN) . ':</span> ';
        $output .= '<span class="cmama-result-value">' . $result['next_birthday']['age_on_birthday'] . '</span>';
        $output .= '</div>';

        $output .= '</div>';
        $output .= '</div>';

        // Fun facts
        if (!empty($result['fun_facts'])) {
            $output .= '<div class="cmama-result-section cmama-fun-facts">';
            $output .= '<h4>' . __('Fun Facts', CMAMA_TEXT_DOMAIN) . '</h4>';
            $output .= '<ul class="cmama-facts-list">';
            foreach ($result['fun_facts'] as $fact) {
                $output .= '<li>' . esc_html($fact) . '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }

        // Life milestones (show next few)
        $upcoming_milestones = array_filter($result['milestones'], function($milestone) {
            return $milestone['status'] === 'upcoming';
        });
        
        if (!empty($upcoming_milestones)) {
            $output .= '<div class="cmama-result-section cmama-milestones">';
            $output .= '<h4>' . __('Upcoming Milestones', CMAMA_TEXT_DOMAIN) . '</h4>';
            $output .= '<div class="cmama-milestones-list">';
            
            foreach (array_slice($upcoming_milestones, 0, 3) as $milestone) {
                $output .= '<div class="cmama-milestone-item">';
                $output .= '<span class="cmama-milestone-desc">' . esc_html($milestone['description']) . '</span>';
                $output .= '<span class="cmama-milestone-date">' . esc_html($milestone['formatted_date']) . '</span>';
                $output .= '<span class="cmama-milestone-countdown">' . sprintf(__('in %d days', CMAMA_TEXT_DOMAIN), $milestone['days_until']) . '</span>';
                $output .= '</div>';
            }
            
            $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }
}

// Register the calculator
add_action('cmama_register_calculators', function($registry) {
    $registry->register_calculator('age-calculator', new CMAMA_Age_Calculator());
});