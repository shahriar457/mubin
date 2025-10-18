<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Shortcode {
    public static function register() {
        add_shortcode( 'cmama_calculator', [ __CLASS__, 'render_shortcode' ] );
    }

    public static function render_shortcode( $atts ) {
        $atts = shortcode_atts(
            [ 'slug' => '' ],
            $atts,
            'cmama_calculator'
        );

        $slug = sanitize_key( $atts['slug'] );
        if ( empty( $slug ) ) {
            return '';
        }

        $html = CMama_Registry::render_calculator( $slug );
        return $html;
    }
}
