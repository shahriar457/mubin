<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Assets {
    public static function register() {
        // Base frontend styles
        wp_register_style(
            'cmama-frontend',
            CMAMA_PLUGIN_URL . 'assets/frontend/style.css',
            [],
            CMAMA_VERSION
        );

        // Calculator scripts (subset)
        wp_register_script(
            'cmama-calc-mortgage',
            CMAMA_PLUGIN_URL . 'assets/calculators/mortgage/mortgage.js',
            [ 'wp-i18n' ],
            CMAMA_VERSION,
            true
        );

        wp_register_script(
            'cmama-calc-bmi',
            CMAMA_PLUGIN_URL . 'assets/calculators/bmi/bmi.js',
            [ 'wp-i18n' ],
            CMAMA_VERSION,
            true
        );
    }

    public static function enqueue_for( $slug ) {
        wp_enqueue_style( 'cmama-frontend' );
        switch ( $slug ) {
            case 'mortgage':
                wp_enqueue_script( 'cmama-calc-mortgage' );
                break;
            case 'bmi':
                wp_enqueue_script( 'cmama-calc-bmi' );
                break;
        }
    }

    public static function output_css_variables() {
        $settings   = CMama_Settings::get_all();
        $appearance = isset( $settings['appearance'] ) ? $settings['appearance'] : [];
        $primary    = isset( $appearance['primary_color'] ) ? $appearance['primary_color'] : '#4F46E5';
        $button     = isset( $appearance['button_color'] ) ? $appearance['button_color'] : '#4338CA';
        $input      = isset( $appearance['input_color'] ) ? $appearance['input_color'] : '#111827';
        $result     = isset( $appearance['result_color'] ) ? $appearance['result_color'] : '#065F46';
        $radius     = isset( $appearance['border_radius'] ) ? $appearance['border_radius'] : '8px';
        $padding    = isset( $appearance['padding'] ) ? $appearance['padding'] : '12px';
        $font       = isset( $appearance['font_family'] ) ? $appearance['font_family'] : 'inherit';
        $custom_css = isset( $settings['custom_css'] ) ? $settings['custom_css'] : '';
        ?>
<style id="cmama-css-variables">
:root{
  --cmama-primary: <?php echo esc_html( $primary ); ?>;
  --cmama-button: <?php echo esc_html( $button ); ?>;
  --cmama-input: <?php echo esc_html( $input ); ?>;
  --cmama-result: <?php echo esc_html( $result ); ?>;
  --cmama-radius: <?php echo esc_html( $radius ); ?>;
  --cmama-padding: <?php echo esc_html( $padding ); ?>;
  --cmama-font: <?php echo esc_html( $font ); ?>;
}
<?php if ( ! empty( $custom_css ) ) : ?>
/* Custom CSS */
<?php echo $custom_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php endif; ?>
</style>
<?php
    }
}
