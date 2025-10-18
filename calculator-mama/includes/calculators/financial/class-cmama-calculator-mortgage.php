<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once dirname( __DIR__ ) . '/interface/interface-cmama-calculator.php';

class CMama_Calc_Mortgage implements CMama_Calculator_Interface {
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
<div class="cmama-wrap" data-cmama="mortgage">
  <div class="cmama-card">
    <div class="cmama-grid">
      <div class="cmama-field">
        <label class="cmama-label" for="cmama-mortgage-amount"><?php esc_html_e( 'Loan Amount', 'calculator-mama' ); ?></label>
        <input id="cmama-mortgage-amount" class="cmama-input" type="number" min="0" step="1000" />
      </div>
      <div class="cmama-field">
        <label class="cmama-label" for="cmama-mortgage-rate"><?php esc_html_e( 'Interest Rate (%)', 'calculator-mama' ); ?></label>
        <input id="cmama-mortgage-rate" class="cmama-input" type="number" min="0" step="0.01" />
      </div>
      <div class="cmama-field">
        <label class="cmama-label" for="cmama-mortgage-years"><?php esc_html_e( 'Term (years)', 'calculator-mama' ); ?></label>
        <input id="cmama-mortgage-years" class="cmama-input" type="number" min="1" step="1" />
      </div>
    </div>
    <button class="cmama-button" type="button" data-calc="mortgage"><?php esc_html_e( 'Calculate', 'calculator-mama' ); ?></button>
    <div class="cmama-result" id="cmama-mortgage-result" aria-live="polite"></div>
  </div>
</div>
        <?php
        return ob_get_clean();
    }
}
