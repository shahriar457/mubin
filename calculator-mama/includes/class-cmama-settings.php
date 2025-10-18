<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Settings {
    const OPTION_KEY = 'cmama_settings';

    public static function ensure_defaults() {
        $current = get_option( self::OPTION_KEY );
        if ( false === $current || ! is_array( $current ) ) {
            update_option( self::OPTION_KEY, self::defaults() );
        }
    }

    public static function defaults() {
        return [
            'active_calculators' => [ 'mortgage', 'bmi' ],
            'appearance'         => [
                'primary_color' => '#4F46E5',
                'button_color'  => '#4338CA',
                'input_color'   => '#111827',
                'result_color'  => '#065F46',
                'border_radius' => '8px',
                'padding'       => '12px',
                'font_family'   => 'inherit',
            ],
            'custom_css'         => '',
        ];
    }

    public static function get_all() {
        $saved = get_option( self::OPTION_KEY, [] );
        $defaults = self::defaults();
        if ( ! is_array( $saved ) ) {
            $saved = [];
        }
        return wp_parse_args( $saved, $defaults );
    }

    public static function get( $key, $default = null ) {
        $all = self::get_all();
        return isset( $all[ $key ] ) ? $all[ $key ] : $default;
    }

    public static function update( $new_settings ) {
        $sanitized = self::sanitize( $new_settings );
        return update_option( self::OPTION_KEY, $sanitized );
    }

    public static function sanitize( $settings ) {
        $defaults = self::defaults();
        $result   = $defaults;

        if ( isset( $settings['active_calculators'] ) && is_array( $settings['active_calculators'] ) ) {
            $result['active_calculators'] = array_values( array_unique( array_map( 'sanitize_key', $settings['active_calculators'] ) ) );
        }

        if ( isset( $settings['appearance'] ) && is_array( $settings['appearance'] ) ) {
            $app = $settings['appearance'];
            $result['appearance'] = [
                'primary_color' => isset( $app['primary_color'] ) ? sanitize_hex_color( $app['primary_color'] ) : $defaults['appearance']['primary_color'],
                'button_color'  => isset( $app['button_color'] ) ? sanitize_hex_color( $app['button_color'] ) : $defaults['appearance']['button_color'],
                'input_color'   => isset( $app['input_color'] ) ? sanitize_hex_color( $app['input_color'] ) : $defaults['appearance']['input_color'],
                'result_color'  => isset( $app['result_color'] ) ? sanitize_hex_color( $app['result_color'] ) : $defaults['appearance']['result_color'],
                'border_radius' => isset( $app['border_radius'] ) ? sanitize_text_field( $app['border_radius'] ) : $defaults['appearance']['border_radius'],
                'padding'       => isset( $app['padding'] ) ? sanitize_text_field( $app['padding'] ) : $defaults['appearance']['padding'],
                'font_family'   => isset( $app['font_family'] ) ? sanitize_text_field( $app['font_family'] ) : $defaults['appearance']['font_family'],
            ];
        }

        if ( isset( $settings['custom_css'] ) ) {
            $css = wp_kses( $settings['custom_css'], [] );
            $result['custom_css'] = is_string( $css ) ? $css : '';
        }

        return $result;
    }
}
