<?php
/**
 * Uninstall hook for Calculator Mama
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'cmama_settings' );
