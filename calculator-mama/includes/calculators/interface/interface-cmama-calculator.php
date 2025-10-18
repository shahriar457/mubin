<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

interface CMama_Calculator_Interface {
    /**
     * Render calculator HTML
     * @param array $attrs
     * @return string
     */
    public function render( $attrs = [] );
}
