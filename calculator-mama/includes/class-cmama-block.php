<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Block {
    public static function register() {
        add_action( 'init', [ __CLASS__, 'register_block_type' ] );
    }

    public static function register_block_type() {
        // Basic block using render_callback for server-side render
        register_block_type( 'cmama/calculator', [
            'api_version'     => 2,
            'render_callback' => [ __CLASS__, 'render' ],
            'attributes'      => [
                'slug' => [ 'type' => 'string', 'default' => 'mortgage' ],
                'intro' => [ 'type' => 'string', 'default' => '' ],
                'usage' => [ 'type' => 'string', 'default' => '' ],
                'faq' => [ 'type' => 'array', 'default' => [] ],
            ],
            'editor_script'   => 'cmama-block-editor',
            'editor_style'    => 'cmama-frontend',
            'style'           => 'cmama-frontend',
        ] );

        wp_register_script(
            'cmama-block-editor',
            CMAMA_PLUGIN_URL . 'assets/blocks/block.js',
            [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n', 'wp-data' ],
            CMAMA_VERSION,
            true
        );
    }

    public static function localize_editor() {
        $active = CMama_Settings::get( 'active_calculators', [] );
        wp_localize_script( 'cmama-block-editor', 'wpm', [ 'cmama' => [ 'active' => array_values( $active ) ] ] );
    }

    public static function render( $attributes ) {
        $slug  = isset( $attributes['slug'] ) ? sanitize_key( $attributes['slug'] ) : '';
        $intro = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
        $usage = isset( $attributes['usage'] ) ? wp_kses_post( $attributes['usage'] ) : '';
        $faq   = isset( $attributes['faq'] ) && is_array( $attributes['faq'] ) ? $attributes['faq'] : [];

        if ( empty( $slug ) ) {
            return '';
        }

        // Save FAQ to post meta for schema generation
        if ( is_admin() && function_exists( 'get_current_screen' ) ) {
            $post_id = get_the_ID();
            if ( $post_id ) {
                update_post_meta( $post_id, '_cmama_faq', $faq );
            }
        }

        // Mark calculator for schema injection
        if ( ! isset( $GLOBALS['cmama_rendered_calculators'] ) || ! is_array( $GLOBALS['cmama_rendered_calculators'] ) ) {
            $GLOBALS['cmama_rendered_calculators'] = [];
        }
        if ( ! in_array( $slug, $GLOBALS['cmama_rendered_calculators'], true ) ) {
            $GLOBALS['cmama_rendered_calculators'][] = $slug;
        }

        $html  = '';
        if ( ! empty( $intro ) ) {
            $html .= '<div class="cmama-card cmama-intro">' . $intro . '</div>';
        }
        $html .= CMama_Registry::render_calculator( $slug );
        if ( ! empty( $usage ) ) {
            $html .= '<div class="cmama-card cmama-usage">' . $usage . '</div>';
        }

        // Optional FAQ list output
        if ( ! empty( $faq ) ) {
            $html .= '<div class="cmama-card cmama-faq">';
            $html .= '<h3>' . esc_html__( 'FAQs', 'calculator-mama' ) . '</h3>';
            $html .= '<dl>';
            foreach ( $faq as $row ) {
                if ( empty( $row['q'] ) || empty( $row['a'] ) ) { continue; }
                $html .= '<dt>' . esc_html( wp_strip_all_tags( $row['q'] ) ) . '</dt>';
                $html .= '<dd>' . wp_kses_post( $row['a'] ) . '</dd>';
            }
            $html .= '</dl></div>';
        }

        return $html;
    }
}
