<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once dirname( __DIR__ ) . '/interface/interface-cmama-calculator.php';

class CMama_Calc_BMI implements CMama_Calculator_Interface {
    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render( $attrs = [] ) {
        ob_start();
        ?>
<div class="cmama-wrap" data-cmama="bmi">
  <div class="cmama-card">
    <div class="cmama-grid">
      <div class="cmama-field">
        <label class="cmama-label" for="cmama-bmi-height"><?php esc_html_e( 'Height (cm)', 'calculator-mama' ); ?></label>
        <input id="cmama-bmi-height" class="cmama-input" type="number" min="0" step="0.1" />
      </div>
      <div class="cmama-field">
        <label class="cmama-label" for="cmama-bmi-weight"><?php esc_html_e( 'Weight (kg)', 'calculator-mama' ); ?></label>
        <input id="cmama-bmi-weight" class="cmama-input" type="number" min="0" step="0.1" />
      </div>
    </div>
    <button class="cmama-button" type="button" data-calc="bmi"><?php esc_html_e( 'Calculate', 'calculator-mama' ); ?></button>
    <div class="cmama-result" id="cmama-bmi-result" aria-live="polite"></div>
  </div>
</div>
        <?php
        return ob_get_clean();
    }
}
