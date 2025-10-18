<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMama_Admin {
    public static function register_settings() {
        register_setting(
            'cmama_settings_group',
            CMama_Settings::OPTION_KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [ 'CMama_Settings', 'sanitize' ],
                'default'           => CMama_Settings::defaults(),
            ]
        );
    }

    public static function register_menus() {
        add_menu_page(
            __( 'Calculator Mama', 'calculator-mama' ),
            __( 'Calculator Mama', 'calculator-mama' ),
            'manage_options',
            'cmama',
            [ __CLASS__, 'render_dashboard' ],
            'dashicons-calculator',
            58
        );

        add_submenu_page(
            'cmama',
            __( 'Library', 'calculator-mama' ),
            __( 'Library', 'calculator-mama' ),
            'manage_options',
            'cmama-library',
            [ __CLASS__, 'render_library' ]
        );

        add_submenu_page(
            'cmama',
            __( 'Appearance', 'calculator-mama' ),
            __( 'Appearance', 'calculator-mama' ),
            'manage_options',
            'cmama-appearance',
            [ __CLASS__, 'render_appearance' ]
        );
    }

    public static function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'cmama' ) === false ) {
            return;
        }
        wp_enqueue_style( 'cmama-admin', CMAMA_PLUGIN_URL . 'assets/admin/admin.css', [], CMAMA_VERSION );
        wp_enqueue_script( 'cmama-admin', CMAMA_PLUGIN_URL . 'assets/admin/admin.js', [], CMAMA_VERSION, true );
    }

    public static function render_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        echo '<div class="wrap" id="cmama">';
        echo '<h1>' . esc_html__( 'Calculator Mama', 'calculator-mama' ) . '</h1>';
        echo '<div class="cmama-admin-card">';
        echo '<p>' . esc_html__( 'Welcome! Use the Library to activate calculators and Appearance to style them.', 'calculator-mama' ) . '</p>';
        echo '</div>';
        echo '</div>';
    }

    public static function render_library() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $settings = CMama_Settings::get_all();
        $active = isset( $settings['active_calculators'] ) ? $settings['active_calculators'] : [];
        $all = CMama_Registry::all_calculators();
        echo '<div class="wrap" id="cmama">';
        echo '<h1>' . esc_html__( 'Calculator Library', 'calculator-mama' ) . '</h1>';
        echo '<form id="cmama-settings-form" method="post">';
        wp_nonce_field( 'cmama_save', 'cmama_nonce' );
        echo '<input type="hidden" name="cmama_action" value="save_library" />';
        echo '<input type="hidden" name="cmama_active_calculators" value="' . esc_attr( implode( ',', $active ) ) . '" />';
        echo '<div class="cmama-grid">';
        echo '<div class="cmama-admin-card">';
        foreach ( $all as $slug => $meta ) {
            $checked = in_array( $slug, $active, true ) ? 'checked' : '';
            echo '<div class="cmama-toggle">';
            echo '<span>' . esc_html( $meta['name'] ) . ' (' . esc_html( $slug ) . ')</span>';
            echo '<input type="checkbox" ' . $checked . ' data-cmama-toggle="' . esc_attr( $slug ) . '" />';
            echo '</div>';
        }
        echo '</div>';
        echo '<div class="cmama-admin-card">';
        submit_button( __( 'Save', 'calculator-mama' ) );
        echo '</div>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    public static function render_appearance() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $settings = CMama_Settings::get_all();
        $app = isset( $settings['appearance'] ) ? $settings['appearance'] : [];
        echo '<div class="wrap" id="cmama">';
        echo '<h1>' . esc_html__( 'Appearance', 'calculator-mama' ) . '</h1>';
        echo '<form method="post">';
        wp_nonce_field( 'cmama_save', 'cmama_nonce' );
        echo '<input type="hidden" name="cmama_action" value="save_appearance" />';
        echo '<div class="cmama-grid">';
        echo '<div class="cmama-admin-card">';
        echo '<p><label>' . esc_html__( 'Primary Color', 'calculator-mama' ) . '<br /><input class="cmama-color" type="color" name="appearance[primary_color]" value="' . esc_attr( $app['primary_color'] ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Button Color', 'calculator-mama' ) . '<br /><input class="cmama-color" type="color" name="appearance[button_color]" value="' . esc_attr( $app['button_color'] ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Input Color', 'calculator-mama' ) . '<br /><input class="cmama-color" type="color" name="appearance[input_color]" value="' . esc_attr( $app['input_color'] ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Result Color', 'calculator-mama' ) . '<br /><input class="cmama-color" type="color" name="appearance[result_color]" value="' . esc_attr( $app['result_color'] ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Border Radius', 'calculator-mama' ) . '<br /><input type="text" name="appearance[border_radius]" value="' . esc_attr( $app['border_radius'] ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Padding', 'calculator-mama' ) . '<br /><input type="text" name="appearance[padding]" value="' . esc_attr( $app['padding'] ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Font Family', 'calculator-mama' ) . '<br /><input type="text" name="appearance[font_family]" value="' . esc_attr( $app['font_family'] ) . '" /></label></p>';
        echo '</div>';
        echo '<div class="cmama-admin-card">';
        echo '<p><label>' . esc_html__( 'Custom CSS', 'calculator-mama' ) . '<br /><textarea name="custom_css" rows="8" style="width:100%">' . esc_textarea( $settings['custom_css'] ) . '</textarea></label></p>';
        submit_button( __( 'Save', 'calculator-mama' ) );
        echo '</div>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    public static function maybe_handle_post() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        if ( empty( $_POST['cmama_action'] ) ) return; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( empty( $_POST['cmama_nonce'] ) || ! wp_verify_nonce( $_POST['cmama_nonce'], 'cmama_save' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            return;
        }

        $action = sanitize_text_field( wp_unslash( $_POST['cmama_action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $settings = CMama_Settings::get_all();

        if ( 'save_library' === $action ) {
            $list = isset( $_POST['cmama_active_calculators'] ) ? sanitize_text_field( wp_unslash( $_POST['cmama_active_calculators'] ) ) : '';
            $slugs = array_filter( array_map( 'sanitize_key', explode( ',', $list ) ) );
            $settings['active_calculators'] = array_values( array_unique( $slugs ) );
            CMama_Settings::update( $settings );
            add_settings_error( 'cmama', 'saved', __( 'Library saved.', 'calculator-mama' ), 'updated' );
        }

        if ( 'save_appearance' === $action ) {
            $appearance = isset( $_POST['appearance'] ) ? (array) $_POST['appearance'] : [];
            $settings['appearance'] = [
                'primary_color' => isset( $appearance['primary_color'] ) ? sanitize_hex_color( $appearance['primary_color'] ) : '#4F46E5',
                'button_color'  => isset( $appearance['button_color'] ) ? sanitize_hex_color( $appearance['button_color'] ) : '#4338CA',
                'input_color'   => isset( $appearance['input_color'] ) ? sanitize_hex_color( $appearance['input_color'] ) : '#111827',
                'result_color'  => isset( $appearance['result_color'] ) ? sanitize_hex_color( $appearance['result_color'] ) : '#065F46',
                'border_radius' => isset( $appearance['border_radius'] ) ? sanitize_text_field( $appearance['border_radius'] ) : '8px',
                'padding'       => isset( $appearance['padding'] ) ? sanitize_text_field( $appearance['padding'] ) : '12px',
                'font_family'   => isset( $appearance['font_family'] ) ? sanitize_text_field( $appearance['font_family'] ) : 'inherit',
            ];
            $settings['custom_css'] = isset( $_POST['custom_css'] ) ? wp_kses_post( $_POST['custom_css'] ) : '';
            CMama_Settings::update( $settings );
            add_settings_error( 'cmama', 'saved', __( 'Appearance saved.', 'calculator-mama' ), 'updated' );
        }
    }
}
