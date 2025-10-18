/**
 * Calculator Mama - Admin JavaScript
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize admin functionality.
	 */
	$(document).ready(function() {
		// Initialize color pickers.
		initColorPickers();

		// Handle calculator toggle switches.
		handleCalculatorToggles();

		// Handle bulk actions.
		handleBulkActions();

		// Handle shortcode copying.
		handleShortcodeCopy();

		// Live preview updates.
		handleLivePreview();
	});

	/**
	 * Initialize WordPress color pickers.
	 */
	function initColorPickers() {
		if ($.fn.wpColorPicker) {
			$('.cmama-color-picker').wpColorPicker({
				change: function() {
					updateLivePreview();
				}
			});
		}
	}

	/**
	 * Handle calculator toggle switches.
	 */
	function handleCalculatorToggles() {
		$(document).on('change', '.cmama-calculator-toggle', function() {
			const $toggle = $(this);
			const slug = $toggle.data('slug');
			const active = $toggle.is(':checked');

			// Show loading state.
			$toggle.prop('disabled', true);

			// Send AJAX request.
			$.ajax({
				url: cmamaAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'cmama_toggle_calculator',
					nonce: cmamaAdmin.nonce,
					slug: slug,
					active: active
				},
				success: function(response) {
					if (response.success) {
						// Show success notification.
						showNotice('success', active ? 
							'Calculator activated successfully.' : 
							'Calculator deactivated successfully.');
					} else {
						// Revert toggle on error.
						$toggle.prop('checked', !active);
						showNotice('error', 'An error occurred. Please try again.');
					}
				},
				error: function() {
					// Revert toggle on error.
					$toggle.prop('checked', !active);
					showNotice('error', 'An error occurred. Please try again.');
				},
				complete: function() {
					$toggle.prop('disabled', false);
				}
			});
		});
	}

	/**
	 * Handle bulk activation/deactivation.
	 */
	function handleBulkActions() {
		// Activate all.
		$('#cmama-activate-all').on('click', function() {
			if (!confirm('Are you sure you want to activate all calculators?')) {
				return;
			}

			const $button = $(this);
			$button.prop('disabled', true).text('Activating...');

			$.ajax({
				url: cmamaAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'cmama_activate_all',
					nonce: cmamaAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						$('.cmama-calculator-toggle').prop('checked', true);
						showNotice('success', 'All calculators activated successfully.');
					} else {
						showNotice('error', 'An error occurred. Please try again.');
					}
				},
				error: function() {
					showNotice('error', 'An error occurred. Please try again.');
				},
				complete: function() {
					$button.prop('disabled', false).text('Activate All');
				}
			});
		});

		// Deactivate all.
		$('#cmama-deactivate-all').on('click', function() {
			if (!confirm('Are you sure you want to deactivate all calculators?')) {
				return;
			}

			const $button = $(this);
			$button.prop('disabled', true).text('Deactivating...');

			$.ajax({
				url: cmamaAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'cmama_deactivate_all',
					nonce: cmamaAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						$('.cmama-calculator-toggle').prop('checked', false);
						showNotice('success', 'All calculators deactivated successfully.');
					} else {
						showNotice('error', 'An error occurred. Please try again.');
					}
				},
				error: function() {
					showNotice('error', 'An error occurred. Please try again.');
				},
				complete: function() {
					$button.prop('disabled', false).text('Deactivate All');
				}
			});
		});
	}

	/**
	 * Handle shortcode copying.
	 */
	function handleShortcodeCopy() {
		$(document).on('click', '.cmama-copy-shortcode', function() {
			const $button = $(this);
			const shortcode = $button.data('shortcode');

			// Create temporary textarea to copy from.
			const $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val(shortcode).select();
			document.execCommand('copy');
			$temp.remove();

			// Show feedback.
			const originalText = $button.html();
			$button.html('<span class="dashicons dashicons-yes"></span> Copied!');
			
			setTimeout(function() {
				$button.html(originalText);
			}, 2000);
		});
	}

	/**
	 * Handle live preview updates.
	 */
	function handleLivePreview() {
		// Update preview when settings change.
		$('#cmama-primary-color, #cmama-button-color, #cmama-input-bg, #cmama-result-bg')
			.on('change', updateLivePreview);
		
		$('#cmama-border-radius, #cmama-padding, #cmama-font-family')
			.on('input change', updateLivePreview);
	}

	/**
	 * Update the live preview with current settings.
	 */
	function updateLivePreview() {
		const $preview = $('.cmama-preview-demo');
		
		if ($preview.length === 0) {
			return;
		}

		const primaryColor = $('#cmama-primary-color').val() || '#2271b1';
		const buttonColor = $('#cmama-button-color').val() || '#2271b1';
		const inputBg = $('#cmama-input-bg').val() || '#ffffff';
		const resultBg = $('#cmama-result-bg').val() || '#f0f0f1';
		const borderRadius = $('#cmama-border-radius').val() || '4';
		const padding = $('#cmama-padding').val() || '20';
		const fontFamily = $('#cmama-font-family').val() || 'inherit';

		$preview.css({
			'--cmama-primary-color': primaryColor,
			'--cmama-button-color': buttonColor,
			'--cmama-input-bg': inputBg,
			'--cmama-result-bg': resultBg,
			'--cmama-border-radius': borderRadius + 'px',
			'--cmama-padding': padding + 'px',
			'font-family': fontFamily
		});
	}

	/**
	 * Show admin notice.
	 *
	 * @param {string} type    Notice type (success, error, warning, info).
	 * @param {string} message Notice message.
	 */
	function showNotice(type, message) {
		const $notice = $('<div>')
			.addClass('notice notice-' + type + ' is-dismissible')
			.html('<p>' + message + '</p>');

		$('.wrap h1').after($notice);

		// Auto-dismiss after 3 seconds.
		setTimeout(function() {
			$notice.fadeOut(function() {
				$(this).remove();
			});
		}, 3000);
	}

	/**
	 * Handle category filter changes.
	 */
	$(document).on('change', '#cmama-category-filter', function() {
		$(this).closest('form').submit();
	});

})(jQuery);
