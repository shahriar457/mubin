<?php
/**
 * Admin Dashboard View
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap cmama-dashboard">
	<h1><?php esc_html_e( 'Welcome to Calculator Mama', 'calculator-mama' ); ?></h1>
	
	<div class="cmama-welcome-panel">
		<div class="cmama-welcome-header">
			<h2><?php esc_html_e( 'Transform Your Website into an Organic Traffic Powerhouse', 'calculator-mama' ); ?></h2>
			<p class="cmama-welcome-subtitle">
				<?php esc_html_e( 'Over 150 interactive calculators with built-in SEO optimization to drive engagement and rankings.', 'calculator-mama' ); ?>
			</p>
		</div>

		<div class="cmama-stats-grid">
			<div class="cmama-stat-card">
				<div class="cmama-stat-icon">
					<span class="dashicons dashicons-calculator"></span>
				</div>
				<div class="cmama-stat-content">
					<div class="cmama-stat-value"><?php echo esc_html( $total_calculators ); ?></div>
					<div class="cmama-stat-label"><?php esc_html_e( 'Total Calculators', 'calculator-mama' ); ?></div>
				</div>
			</div>

			<div class="cmama-stat-card">
				<div class="cmama-stat-icon cmama-stat-icon-success">
					<span class="dashicons dashicons-yes-alt"></span>
				</div>
				<div class="cmama-stat-content">
					<div class="cmama-stat-value"><?php echo esc_html( $active_calculators ); ?></div>
					<div class="cmama-stat-label"><?php esc_html_e( 'Active Calculators', 'calculator-mama' ); ?></div>
				</div>
			</div>

			<div class="cmama-stat-card">
				<div class="cmama-stat-icon cmama-stat-icon-info">
					<span class="dashicons dashicons-category"></span>
				</div>
				<div class="cmama-stat-content">
					<div class="cmama-stat-value"><?php echo esc_html( count( $categories ) ); ?></div>
					<div class="cmama-stat-label"><?php esc_html_e( 'Categories', 'calculator-mama' ); ?></div>
				</div>
			</div>

			<div class="cmama-stat-card">
				<div class="cmama-stat-icon cmama-stat-icon-warning">
					<span class="dashicons dashicons-search"></span>
				</div>
				<div class="cmama-stat-content">
					<div class="cmama-stat-value"><?php esc_html_e( 'Auto', 'calculator-mama' ); ?></div>
					<div class="cmama-stat-label"><?php esc_html_e( 'SEO Optimization', 'calculator-mama' ); ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="cmama-quick-links">
		<h2><?php esc_html_e( 'Quick Links', 'calculator-mama' ); ?></h2>
		
		<div class="cmama-links-grid">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=calculator-mama-library' ) ); ?>" class="cmama-quick-link">
				<span class="dashicons dashicons-book"></span>
				<h3><?php esc_html_e( 'Calculator Library', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Browse and activate calculators', 'calculator-mama' ); ?></p>
			</a>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=calculator-mama-appearance' ) ); ?>" class="cmama-quick-link">
				<span class="dashicons dashicons-admin-appearance"></span>
				<h3><?php esc_html_e( 'Appearance Settings', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Customize colors, fonts, and styles', 'calculator-mama' ); ?></p>
			</a>

			<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="cmama-quick-link">
				<span class="dashicons dashicons-plus-alt"></span>
				<h3><?php esc_html_e( 'Create Calculator Page', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Add a calculator to your site', 'calculator-mama' ); ?></p>
			</a>

			<a href="#" class="cmama-quick-link" target="_blank" rel="noopener">
				<span class="dashicons dashicons-book-alt"></span>
				<h3><?php esc_html_e( 'Documentation', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Learn how to use Calculator Mama', 'calculator-mama' ); ?></p>
			</a>
		</div>
	</div>

	<div class="cmama-getting-started">
		<h2><?php esc_html_e( 'Getting Started', 'calculator-mama' ); ?></h2>
		
		<div class="cmama-steps">
			<div class="cmama-step">
				<div class="cmama-step-number">1</div>
				<h3><?php esc_html_e( 'Activate Calculators', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Go to the Calculator Library and activate the calculators you want to use.', 'calculator-mama' ); ?></p>
			</div>

			<div class="cmama-step">
				<div class="cmama-step-number">2</div>
				<h3><?php esc_html_e( 'Customize Appearance', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Match your brand by customizing colors, fonts, and styles in Appearance Settings.', 'calculator-mama' ); ?></p>
			</div>

			<div class="cmama-step">
				<div class="cmama-step-number">3</div>
				<h3><?php esc_html_e( 'Embed Calculators', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Use the Calculator Mama block in Gutenberg or shortcodes to add calculators to your pages.', 'calculator-mama' ); ?></p>
			</div>

			<div class="cmama-step">
				<div class="cmama-step-number">4</div>
				<h3><?php esc_html_e( 'Optimize for SEO', 'calculator-mama' ); ?></h3>
				<p><?php esc_html_e( 'Add content, FAQs, and let our SEO engine handle structured data automatically.', 'calculator-mama' ); ?></p>
			</div>
		</div>
	</div>

	<div class="cmama-categories-overview">
		<h2><?php esc_html_e( 'Calculator Categories', 'calculator-mama' ); ?></h2>
		
		<div class="cmama-categories-grid">
			<?php foreach ( $categories as $slug => $name ) : ?>
				<div class="cmama-category-card">
					<h3><?php echo esc_html( $name ); ?></h3>
					<p>
						<?php
						$count = count( CMAMA_Calculator_Registry::instance()->get_by_category( $slug ) );
						/* translators: %d: number of calculators */
						echo esc_html( sprintf( _n( '%d calculator', '%d calculators', $count, 'calculator-mama' ), $count ) );
						?>
					</p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=calculator-mama-library&category=' . $slug ) ); ?>" class="button button-secondary">
						<?php esc_html_e( 'View Calculators', 'calculator-mama' ); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
