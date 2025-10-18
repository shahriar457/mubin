<?php
/**
 * Database operations
 *
 * @package Calculator_Mama
 * @since 1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database operations class
 */
class Calculator_Mama_Database {

    /**
     * Option name for plugin settings
     */
    const SETTINGS_OPTION = 'cmama_settings';

    /**
     * Default settings
     *
     * @var array
     */
    private $default_settings = array(
        'version' => CMAMA_VERSION,
        'calculators' => array(),
        'appearance' => array(
            'primary_color' => '#0073aa',
            'button_color' => '#0073aa',
            'input_color' => '#ffffff',
            'result_color' => '#28a745',
            'font_family' => 'inherit',
            'border_radius' => '4px',
            'padding' => '20px',
            'custom_css' => ''
        ),
        'seo' => array(
            'enable_schema' => true,
            'enable_meta_description' => true
        )
    );

    /**
     * Get plugin settings
     *
     * @return array
     */
    public function get_settings() {
        $settings = get_option(self::SETTINGS_OPTION, array());
        return wp_parse_args($settings, $this->default_settings);
    }

    /**
     * Update plugin settings
     *
     * @param array $settings
     * @return bool
     */
    public function update_settings($settings) {
        $current_settings = $this->get_settings();
        $updated_settings = wp_parse_args($settings, $current_settings);
        return update_option(self::SETTINGS_OPTION, $updated_settings);
    }

    /**
     * Get calculator settings
     *
     * @param string $calculator_slug
     * @return array
     */
    public function get_calculator_settings($calculator_slug) {
        $settings = $this->get_settings();
        return isset($settings['calculators'][$calculator_slug]) ? $settings['calculators'][$calculator_slug] : array();
    }

    /**
     * Update calculator settings
     *
     * @param string $calculator_slug
     * @param array $calculator_settings
     * @return bool
     */
    public function update_calculator_settings($calculator_slug, $calculator_settings) {
        $settings = $this->get_settings();
        $settings['calculators'][$calculator_slug] = $calculator_settings;
        return $this->update_settings($settings);
    }

    /**
     * Get appearance settings
     *
     * @return array
     */
    public function get_appearance_settings() {
        $settings = $this->get_settings();
        return $settings['appearance'];
    }

    /**
     * Update appearance settings
     *
     * @param array $appearance_settings
     * @return bool
     */
    public function update_appearance_settings($appearance_settings) {
        $settings = $this->get_settings();
        $settings['appearance'] = wp_parse_args($appearance_settings, $settings['appearance']);
        return $this->update_settings($settings);
    }

    /**
     * Get SEO settings
     *
     * @return array
     */
    public function get_seo_settings() {
        $settings = $this->get_settings();
        return $settings['seo'];
    }

    /**
     * Update SEO settings
     *
     * @param array $seo_settings
     * @return bool
     */
    public function update_seo_settings($seo_settings) {
        $settings = $this->get_settings();
        $settings['seo'] = wp_parse_args($seo_settings, $settings['seo']);
        return $this->update_settings($settings);
    }

    /**
     * Reset settings to default
     *
     * @return bool
     */
    public function reset_settings() {
        return update_option(self::SETTINGS_OPTION, $this->default_settings);
    }
}