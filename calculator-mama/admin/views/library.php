<?php
/**
 * Calculator Library View
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap cmama-library">
	<h1><?php esc_html_e( 'Calculator Library', 'calculator-mama' ); ?></h1>

	<div class="cmama-library-header">
		<div class="cmama-library-filters">
			<form method="get" action="">
				<input type="hidden" name="page" value="calculator-mama-library">
				
				<select name="category" id="cmama-category-filter">
					<option value=""><?php esc_html_e( 'All Categories', 'calculator-mama' ); ?></option>
					<?php foreach ( $categories as $slug => $name ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $filter_category, $slug ); ?>>
							<?php echo esc_html( $name ); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search calculators...', 'calculator-mama' ); ?>" value="<?php echo esc_attr( $filter_search ); ?>">
				
				<button type="submit" class="button"><?php esc_html_e( 'Filter', 'calculator-mama' ); ?></button>
			</form>
		</div>

		<div class="cmama-library-actions">
			<button type="button" class="button" id="cmama-activate-all">
				<?php esc_html_e( 'Activate All', 'calculator-mama' ); ?>
			</button>
			<button type="button" class="button" id="cmama-deactivate-all">
				<?php esc_html_e( 'Deactivate All', 'calculator-mama' ); ?>
			</button>
		</div>
	</div>

	<div class="cmama-calculators-grid">
		<?php if ( empty( $calculators ) ) : ?>
			<div class="cmama-no-results">
				<p><?php esc_html_e( 'No calculators found.', 'calculator-mama' ); ?></p>
			</div>
		<?php else : ?>
			<?php foreach ( $calculators as $calculator ) : ?>
				<div class="cmama-calculator-card" data-slug="<?php echo esc_attr( $calculator->get_slug() ); ?>">
					<div class="cmama-calculator-header">
						<h3><?php echo esc_html( $calculator->get_name() ); ?></h3>
						<label class="cmama-toggle">
							<input type="checkbox" 
								   class="cmama-calculator-toggle" 
								   data-slug="<?php echo esc_attr( $calculator->get_slug() ); ?>"
								   <?php checked( $calculator->is_active() ); ?>>
							<span class="cmama-toggle-slider"></span>
						</label>
					</div>
					
					<div class="cmama-calculator-body">
						<p class="cmama-calculator-description">
							<?php echo esc_html( $calculator->get_description() ); ?>
						</p>
						<div class="cmama-calculator-meta">
							<span class="cmama-calculator-category">
								<?php echo esc_html( $categories[ $calculator->get_category() ] ?? $calculator->get_category() ); ?>
							</span>
						</div>
					</div>

					<div class="cmama-calculator-footer">
						<button type="button" 
								class="button button-secondary cmama-copy-shortcode" 
								data-shortcode='[cmama_calculator slug="<?php echo esc_attr( $calculator->get_slug() ); ?>"]'
								title="<?php esc_attr_e( 'Copy shortcode', 'calculator-mama' ); ?>">
							<span class="dashicons dashicons-admin-page"></span>
							<?php esc_html_e( 'Copy Shortcode', 'calculator-mama' ); ?>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
