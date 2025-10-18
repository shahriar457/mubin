<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Registry {
    /**
     * Returns metadata for all available calculators (subset shipped here)
     * @return array[] list keyed by slug
     */
    public static function all_calculators() {
        return [
            'mortgage' => [
                'slug'        => 'mortgage',
                'name'        => __( 'Mortgage Calculator', 'calculator-mama' ),
                'category'    => 'financial',
                'schema_type' => 'WebApplication',
                'class'       => 'CMama_Calc_Mortgage',
                'path'        => CMAMA_PLUGIN_DIR . 'includes/calculators/financial/class-cmama-calculator-mortgage.php',
            ],
            'bmi' => [
                'slug'        => 'bmi',
                'name'        => __( 'BMI Calculator', 'calculator-mama' ),
                'category'    => 'health',
                'schema_type' => 'MedicalRiskEstimator',
                'class'       => 'CMama_Calc_BMI',
                'path'        => CMAMA_PLUGIN_DIR . 'includes/calculators/health/class-cmama-calculator-bmi.php',
            ],
        ];
    }

    /**
     * Returns active calculators (filtered by settings)
     */
    public static function active_calculators() {
        $active_slugs = CMama_Settings::get( 'active_calculators', [] );
        $all = self::all_calculators();
        $result = [];
        foreach ( $active_slugs as $slug ) {
            if ( isset( $all[ $slug ] ) ) {
                $result[ $slug ] = $all[ $slug ];
            }
        }
        return $result;
    }

    public static function is_active( $slug ) {
        $active = CMama_Settings::get( 'active_calculators', [] );
        return in_array( $slug, $active, true );
    }

    /**
     * Ensure calculator class is loaded and return instance
     */
    public static function get_calculator( $slug ) {
        $all = self::all_calculators();
        if ( ! isset( $all[ $slug ] ) ) {
            return null;
        }
        $meta = $all[ $slug ];
        if ( ! class_exists( $meta['class'] ) ) {
            if ( file_exists( $meta['path'] ) ) {
                require_once $meta['path'];
            } else {
                return null;
            }
        }
        if ( method_exists( $meta['class'], 'instance' ) ) {
            return call_user_func( [ $meta['class'], 'instance' ] );
        }
        return new $meta['class']();
    }

    public static function render_calculator( $slug, $attrs = [] ) {
        $calc = self::get_calculator( $slug );
        if ( ! $calc ) {
            return '';
        }
        if ( ! self::is_active( $slug ) ) {
            return '';
        }
        if ( method_exists( 'CMama_Assets', 'enqueue_for' ) ) {
            CMama_Assets::enqueue_for( $slug );
        }
        ob_start();
        echo $calc->render( $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
