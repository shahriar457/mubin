<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_SEO {
    public static function register() {
        add_action( 'wp_head', [ __CLASS__, 'inject_schema' ], 30 );
    }

    public static function inject_schema() {
        if ( is_admin() ) {
            return;
        }

        // Attempt to detect currently rendered calculator via a global marker set during render
        if ( empty( $GLOBALS['cmama_rendered_calculators'] ) || ! is_array( $GLOBALS['cmama_rendered_calculators'] ) ) {
            return;
        }

        $calculators = $GLOBALS['cmama_rendered_calculators'];
        $schemas = [];

        foreach ( $calculators as $slug ) {
            $all = CMama_Registry::all_calculators();
            if ( ! isset( $all[ $slug ] ) ) {
                continue;
            }
            $meta = $all[ $slug ];
            $schema_type = $meta['schema_type'];

            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type'    => $schema_type,
                'name'     => $meta['name'],
                'applicationCategory' => 'Calculator',
                'operatingSystem'     => 'All',
                'url'      => esc_url( home_url( add_query_arg( null, null ) ) ),
                'description' => sprintf( __( '%s provided by Calculator Mama.', 'calculator-mama' ), $meta['name'] ),
            ];
        }

        // Optional FAQ schema from block attributes stored in post meta
        $faq = get_post_meta( get_the_ID(), '_cmama_faq', true );
        if ( is_array( $faq ) && ! empty( $faq ) ) {
            $main_entity = [];
            foreach ( $faq as $row ) {
                if ( empty( $row['q'] ) || empty( $row['a'] ) ) {
                    continue;
                }
                $main_entity[] = [
                    '@type' => 'Question',
                    'name'  => wp_strip_all_tags( $row['q'] ),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => wp_kses_post( $row['a'] ),
                    ],
                ];
            }
            if ( ! empty( $main_entity ) ) {
                $schemas[] = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    'mainEntity' => $main_entity,
                ];
            }
        }

        if ( empty( $schemas ) ) {
            return;
        }

        echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $schemas ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
