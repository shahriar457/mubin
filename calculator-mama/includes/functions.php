<?php
/**
 * Helper Functions
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Calculator Mama settings.
 *
 * @since 1.0.0
 * @param string $key Optional. Setting key to retrieve.
 * @param mixed $default Optional. Default value if setting not found.
 * @return mixed Settings value or array of all settings.
 */
function cmama_get_setting($key = '', $default = null) {
    $settings = get_option('cmama_settings', array());
    
    if (empty($key)) {
        return $settings;
    }
    
    // Support dot notation for nested settings
    if (strpos($key, '.') !== false) {
        $keys = explode('.', $key);
        $value = $settings;
        
        foreach ($keys as $nested_key) {
            if (isset($value[$nested_key])) {
                $value = $value[$nested_key];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
    
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Update Calculator Mama setting.
 *
 * @since 1.0.0
 * @param string $key Setting key.
 * @param mixed $value Setting value.
 * @return bool True on success, false on failure.
 */
function cmama_update_setting($key, $value) {
    $settings = get_option('cmama_settings', array());
    
    // Support dot notation for nested settings
    if (strpos($key, '.') !== false) {
        $keys = explode('.', $key);
        $current = &$settings;
        
        for ($i = 0; $i < count($keys) - 1; $i++) {
            if (!isset($current[$keys[$i]]) || !is_array($current[$keys[$i]])) {
                $current[$keys[$i]] = array();
            }
            $current = &$current[$keys[$i]];
        }
        
        $current[end($keys)] = $value;
    } else {
        $settings[$key] = $value;
    }
    
    return update_option('cmama_settings', $settings);
}

/**
 * Get calculator by slug.
 *
 * @since 1.0.0
 * @param string $slug Calculator slug.
 * @return CMAMA_Calculator_Base|null Calculator instance or null.
 */
function cmama_get_calculator($slug) {
    $registry = CMAMA_Calculator_Registry::get_instance();
    return $registry->get_calculator($slug);
}

/**
 * Get all calculators.
 *
 * @since 1.0.0
 * @param array $args Query arguments.
 * @return array Array of calculator instances.
 */
function cmama_get_calculators($args = array()) {
    $registry = CMAMA_Calculator_Registry::get_instance();
    return $registry->get_calculators($args);
}

/**
 * Check if calculator is active.
 *
 * @since 1.0.0
 * @param string $slug Calculator slug.
 * @return bool True if calculator is active.
 */
function cmama_is_calculator_active($slug) {
    $active_calculators = cmama_get_setting('active_calculators', array());
    return in_array($slug, $active_calculators);
}

/**
 * Activate calculator.
 *
 * @since 1.0.0
 * @param string $slug Calculator slug.
 * @return bool True on success, false on failure.
 */
function cmama_activate_calculator($slug) {
    $registry = CMAMA_Calculator_Registry::get_instance();
    
    if (!$registry->calculator_exists($slug)) {
        return false;
    }
    
    $active_calculators = cmama_get_setting('active_calculators', array());
    
    if (!in_array($slug, $active_calculators)) {
        $active_calculators[] = $slug;
        return cmama_update_setting('active_calculators', $active_calculators);
    }
    
    return true; // Already active
}

/**
 * Deactivate calculator.
 *
 * @since 1.0.0
 * @param string $slug Calculator slug.
 * @return bool True on success, false on failure.
 */
function cmama_deactivate_calculator($slug) {
    $active_calculators = cmama_get_setting('active_calculators', array());
    $key = array_search($slug, $active_calculators);
    
    if ($key !== false) {
        unset($active_calculators[$key]);
        $active_calculators = array_values($active_calculators); // Reindex array
        return cmama_update_setting('active_calculators', $active_calculators);
    }
    
    return true; // Already inactive
}

/**
 * Render calculator by slug.
 *
 * @since 1.0.0
 * @param string $slug Calculator slug.
 * @param array $args Additional arguments.
 * @return string Calculator HTML or error message.
 */
function cmama_render_calculator($slug, $args = array()) {
    $calculator = cmama_get_calculator($slug);
    
    if (!$calculator) {
        return '<div class="cmama-error">' . __('Calculator not found.', CMAMA_TEXT_DOMAIN) . '</div>';
    }
    
    // Check if calculator is active (skip check in admin)
    if (!is_admin() && !cmama_is_calculator_active($slug)) {
        return '<div class="cmama-error">' . __('Calculator is not active.', CMAMA_TEXT_DOMAIN) . '</div>';
    }
    
    return $calculator->render($args);
}

/**
 * Format number for display.
 *
 * @since 1.0.0
 * @param float $number Number to format.
 * @param int $decimals Number of decimal places.
 * @param bool $currency Whether to format as currency.
 * @param string $currency_symbol Currency symbol.
 * @return string Formatted number.
 */
function cmama_format_number($number, $decimals = 2, $currency = false, $currency_symbol = '$') {
    if (!is_numeric($number)) {
        return $number;
    }
    
    $formatted = number_format($number, $decimals);
    
    if ($currency) {
        $formatted = $currency_symbol . $formatted;
    }
    
    return $formatted;
}

/**
 * Format percentage for display.
 *
 * @since 1.0.0
 * @param float $number Number to format as percentage.
 * @param int $decimals Number of decimal places.
 * @return string Formatted percentage.
 */
function cmama_format_percentage($number, $decimals = 2) {
    if (!is_numeric($number)) {
        return $number;
    }
    
    return number_format($number, $decimals) . '%';
}

/**
 * Sanitize calculator input.
 *
 * @since 1.0.0
 * @param mixed $input Input value.
 * @param string $type Input type.
 * @return mixed Sanitized input.
 */
function cmama_sanitize_input($input, $type = 'text') {
    switch ($type) {
        case 'number':
        case 'float':
            return is_numeric($input) ? floatval($input) : 0;
            
        case 'int':
        case 'integer':
            return is_numeric($input) ? intval($input) : 0;
            
        case 'email':
            return sanitize_email($input);
            
        case 'url':
            return esc_url_raw($input);
            
        case 'textarea':
            return sanitize_textarea_field($input);
            
        case 'html':
            return wp_kses_post($input);
            
        case 'text':
        default:
            return sanitize_text_field($input);
    }
}

/**
 * Validate calculator input.
 *
 * @since 1.0.0
 * @param mixed $input Input value.
 * @param array $rules Validation rules.
 * @return bool|string True if valid, error message if invalid.
 */
function cmama_validate_input($input, $rules = array()) {
    $rules = wp_parse_args($rules, array(
        'required' => false,
        'type' => 'text',
        'min' => null,
        'max' => null,
        'pattern' => null
    ));
    
    // Check required
    if ($rules['required'] && empty($input)) {
        return __('This field is required.', CMAMA_TEXT_DOMAIN);
    }
    
    // Skip other validations if input is empty and not required
    if (empty($input) && !$rules['required']) {
        return true;
    }
    
    // Type validation
    switch ($rules['type']) {
        case 'number':
        case 'float':
            if (!is_numeric($input)) {
                return __('Please enter a valid number.', CMAMA_TEXT_DOMAIN);
            }
            $input = floatval($input);
            break;
            
        case 'int':
        case 'integer':
            if (!is_numeric($input) || intval($input) != $input) {
                return __('Please enter a valid integer.', CMAMA_TEXT_DOMAIN);
            }
            $input = intval($input);
            break;
            
        case 'email':
            if (!is_email($input)) {
                return __('Please enter a valid email address.', CMAMA_TEXT_DOMAIN);
            }
            break;
            
        case 'url':
            if (!filter_var($input, FILTER_VALIDATE_URL)) {
                return __('Please enter a valid URL.', CMAMA_TEXT_DOMAIN);
            }
            break;
    }
    
    // Min/Max validation for numbers
    if (($rules['type'] === 'number' || $rules['type'] === 'float' || $rules['type'] === 'int') && is_numeric($input)) {
        if ($rules['min'] !== null && $input < $rules['min']) {
            return sprintf(__('Value must be at least %s.', CMAMA_TEXT_DOMAIN), $rules['min']);
        }
        if ($rules['max'] !== null && $input > $rules['max']) {
            return sprintf(__('Value must be no more than %s.', CMAMA_TEXT_DOMAIN), $rules['max']);
        }
    }
    
    // Pattern validation
    if ($rules['pattern'] && !preg_match($rules['pattern'], $input)) {
        return __('Please enter a value in the correct format.', CMAMA_TEXT_DOMAIN);
    }
    
    return true;
}

/**
 * Get calculator categories.
 *
 * @since 1.0.0
 * @return array Array of categories.
 */
function cmama_get_categories() {
    $registry = CMAMA_Calculator_Registry::get_instance();
    return $registry->get_categories();
}

/**
 * Get calculator statistics.
 *
 * @since 1.0.0
 * @return array Statistics data.
 */
function cmama_get_statistics() {
    $registry = CMAMA_Calculator_Registry::get_instance();
    return $registry->get_statistics();
}

/**
 * Log calculator usage.
 *
 * @since 1.0.0
 * @param string $calculator_slug Calculator slug.
 * @param array $input_data Input data.
 * @param array $result_data Result data.
 * @return bool True on success, false on failure.
 */
function cmama_log_usage($calculator_slug, $input_data = array(), $result_data = array()) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cmama_usage';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return false;
    }
    
    $usage_data = array(
        'input' => $input_data,
        'result' => $result_data,
        'timestamp' => current_time('mysql')
    );
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'calculator_slug' => $calculator_slug,
            'user_ip' => cmama_get_user_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'calculation_data' => wp_json_encode($usage_data),
            'created_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );
    
    return $result !== false;
}

/**
 * Get user IP address.
 *
 * @since 1.0.0
 * @return string User IP address.
 */
function cmama_get_user_ip() {
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
}

/**
 * Generate calculator shortcode.
 *
 * @since 1.0.0
 * @param string $slug Calculator slug.
 * @param array $args Additional shortcode attributes.
 * @return string Shortcode string.
 */
function cmama_generate_shortcode($slug, $args = array()) {
    $shortcode = '[cmama_calculator slug="' . esc_attr($slug) . '"';
    
    foreach ($args as $key => $value) {
        $shortcode .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
    }
    
    $shortcode .= ']';
    
    return $shortcode;
}

/**
 * Check if current page has calculators.
 *
 * @since 1.0.0
 * @param WP_Post $post Optional. Post object.
 * @return bool True if page has calculators.
 */
function cmama_page_has_calculators($post = null) {
    if (!$post) {
        global $post;
    }
    
    if (!$post) {
        return false;
    }
    
    // Check for shortcodes
    if (has_shortcode($post->post_content, 'cmama_calculator')) {
        return true;
    }
    
    // Check for Gutenberg blocks
    if (function_exists('has_block') && has_block('calculator-mama/calculator', $post)) {
        return true;
    }
    
    return false;
}

/**
 * Get calculators on current page.
 *
 * @since 1.0.0
 * @param WP_Post $post Optional. Post object.
 * @return array Array of calculator slugs.
 */
function cmama_get_page_calculators($post = null) {
    if (!$post) {
        global $post;
    }
    
    if (!$post) {
        return array();
    }
    
    $calculators = array();
    
    // Check for shortcodes
    if (has_shortcode($post->post_content, 'cmama_calculator')) {
        preg_match_all('/\[cmama_calculator[^\]]*slug=["\']([^"\']+)["\'][^\]]*\]/', $post->post_content, $matches);
        if (!empty($matches[1])) {
            $calculators = array_merge($calculators, $matches[1]);
        }
    }
    
    // Check for Gutenberg blocks
    if (function_exists('has_block') && has_block('calculator-mama/calculator', $post)) {
        $blocks = parse_blocks($post->post_content);
        $block_calculators = cmama_extract_calculators_from_blocks($blocks);
        $calculators = array_merge($calculators, $block_calculators);
    }
    
    return array_unique($calculators);
}

/**
 * Extract calculator slugs from Gutenberg blocks.
 *
 * @since 1.0.0
 * @param array $blocks Array of block data.
 * @return array Array of calculator slugs.
 */
function cmama_extract_calculators_from_blocks($blocks) {
    $calculators = array();
    
    foreach ($blocks as $block) {
        if ($block['blockName'] === 'calculator-mama/calculator') {
            if (!empty($block['attrs']['calculatorSlug'])) {
                $calculators[] = $block['attrs']['calculatorSlug'];
            }
        }
        
        // Check inner blocks recursively
        if (!empty($block['innerBlocks'])) {
            $inner_calculators = cmama_extract_calculators_from_blocks($block['innerBlocks']);
            $calculators = array_merge($calculators, $inner_calculators);
        }
    }
    
    return $calculators;
}

/**
 * Get calculator usage statistics.
 *
 * @since 1.0.0
 * @param string $calculator_slug Optional. Calculator slug.
 * @param array $args Optional. Query arguments.
 * @return array Usage statistics.
 */
function cmama_get_usage_stats($calculator_slug = '', $args = array()) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cmama_usage';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return array();
    }
    
    $args = wp_parse_args($args, array(
        'days' => 30,
        'limit' => 100
    ));
    
    $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)";
    $query_args = array($args['days']);
    
    if (!empty($calculator_slug)) {
        $where .= " AND calculator_slug = %s";
        $query_args[] = $calculator_slug;
    }
    
    $query = "SELECT calculator_slug, COUNT(*) as usage_count, DATE(created_at) as usage_date 
              FROM $table_name 
              $where 
              GROUP BY calculator_slug, DATE(created_at) 
              ORDER BY created_at DESC 
              LIMIT %d";
    
    $query_args[] = $args['limit'];
    
    $results = $wpdb->get_results($wpdb->prepare($query, $query_args));
    
    return $results ? $results : array();
}

/**
 * Clear calculator usage data.
 *
 * @since 1.0.0
 * @param int $days Optional. Number of days to keep. Default 0 (clear all).
 * @return bool True on success, false on failure.
 */
function cmama_clear_usage_data($days = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cmama_usage';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return false;
    }
    
    if ($days > 0) {
        $result = $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    } else {
        $result = $wpdb->query("TRUNCATE TABLE $table_name");
    }
    
    return $result !== false;
}