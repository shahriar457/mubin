/**
 * Calculator Mama - Base JavaScript
 *
 * Common functionality for all calculators.
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize calculator functionality.
	 */
	$(document).ready(function() {
		// Add smooth scroll animations.
		$('.cmama-calculate-btn').on('click', function() {
			const $calculator = $(this).closest('.cmama-calculator');
			const $result = $calculator.find('.cmama-result');
			
			if ($result.is(':visible')) {
				// Smooth scroll to results.
				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $result.offset().top - 100
					}, 500);
				}, 100);
			}
		});

		// Add loading state to buttons.
		$('.cmama-calculate-btn').on('click', function() {
			const $btn = $(this);
			$btn.addClass('cmama-calculating');
			
			setTimeout(function() {
				$btn.removeClass('cmama-calculating');
			}, 300);
		});

		// Initialize tooltips if needed.
		if ($.fn.tooltip) {
			$('[data-cmama-tooltip]').tooltip();
		}

		// Format number inputs.
		$('.cmama-input[type="number"]').on('input', function() {
			// Remove non-numeric characters except decimal point and minus.
			const value = $(this).val();
			const cleaned = value.replace(/[^0-9.-]/g, '');
			if (value !== cleaned) {
				$(this).val(cleaned);
			}
		});

		// Auto-expand textareas.
		$('textarea.cmama-input').on('input', function() {
			this.style.height = 'auto';
			this.style.height = (this.scrollHeight) + 'px';
		});

		// FAQ accordion functionality.
		$('.cmama-faq-question').on('click', function() {
			$(this).toggleClass('active');
			$(this).next('.cmama-faq-answer').slideToggle(300);
		});
	});

	/**
	 * Format number with commas.
	 *
	 * @param {number} num Number to format.
	 * @param {number} decimals Number of decimal places.
	 * @return {string} Formatted number.
	 */
	window.cmamaFormatNumber = function(num, decimals) {
		decimals = decimals || 2;
		return num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	};

	/**
	 * Validate email address.
	 *
	 * @param {string} email Email address to validate.
	 * @return {boolean} Whether email is valid.
	 */
	window.cmamaValidateEmail = function(email) {
		const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return re.test(email);
	};

	/**
	 * Show error message.
	 *
	 * @param {jQuery} $calculator Calculator element.
	 * @param {string} message Error message.
	 */
	window.cmamaShowError = function($calculator, message) {
		let $error = $calculator.find('.cmama-error-message');
		
		if ($error.length === 0) {
			$error = $('<div class="cmama-error-message"></div>');
			$calculator.prepend($error);
		}
		
		$error.text(message).slideDown();
		
		setTimeout(function() {
			$error.slideUp();
		}, 3000);
	};

})(jQuery);
