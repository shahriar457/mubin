/**
 * Common calculator JavaScript
 *
 * @package Calculator_Mama
 * @since 1.0
 */

(function($) {
    'use strict';

    // Initialize calculators when document is ready
    $(document).ready(function() {
        initCalculators();
    });

    /**
     * Initialize all calculators on the page
     */
    function initCalculators() {
        $('.cmama-calculator').each(function() {
            var $calculator = $(this);
            var calculatorType = $calculator.data('calculator');
            
            if (calculatorType) {
                initCalculator($calculator, calculatorType);
            }
        });
    }

    /**
     * Initialize a specific calculator
     *
     * @param {jQuery} $calculator
     * @param {string} calculatorType
     */
    function initCalculator($calculator, calculatorType) {
        // Bind form submission
        $calculator.find('.cmama-calculator-form').on('submit', function(e) {
            e.preventDefault();
            handleCalculation($calculator, calculatorType);
        });

        // Bind reset button
        $calculator.find('#cmama-reset').on('click', function() {
            resetCalculator($calculator);
        });

        // Bind input changes for real-time validation
        $calculator.find('.cmama-input').on('input', function() {
            validateInput($(this));
        });
    }

    /**
     * Handle calculation
     *
     * @param {jQuery} $calculator
     * @param {string} calculatorType
     */
    function handleCalculation($calculator, calculatorType) {
        var $form = $calculator.find('.cmama-calculator-form');
        var $results = $calculator.find('.cmama-results');
        var $button = $form.find('button[type="submit"]');
        
        // Show loading state
        $calculator.addClass('loading');
        $button.prop('disabled', true);
        
        // Clear previous errors
        $calculator.find('.cmama-error').remove();
        
        // Get form data
        var formData = getFormData($form);
        
        // Validate data
        if (!validateFormData(formData)) {
            showError($calculator, cmama_public.strings.invalid_input);
            resetLoadingState($calculator, $button);
            return;
        }

        // Send AJAX request
        $.ajax({
            url: cmama_public.ajax_url,
            type: 'POST',
            data: {
                action: 'cmama_calculate',
                nonce: cmama_public.nonce,
                calculator: calculatorType,
                data: formData
            },
            success: function(response) {
                if (response.success) {
                    displayResults($results, response.data);
                    $results.show();
                } else {
                    showError($calculator, response.data.message || cmama_public.strings.error);
                }
            },
            error: function() {
                showError($calculator, cmama_public.strings.error);
            },
            complete: function() {
                resetLoadingState($calculator, $button);
            }
        });
    }

    /**
     * Get form data
     *
     * @param {jQuery} $form
     * @return {Object}
     */
    function getFormData($form) {
        var data = {};
        
        $form.find('.cmama-input').each(function() {
            var $input = $(this);
            var name = $input.attr('name');
            var value = $input.val();
            
            if (name && value !== '') {
                data[name] = value;
            }
        });
        
        return data;
    }

    /**
     * Validate form data
     *
     * @param {Object} data
     * @return {boolean}
     */
    function validateFormData(data) {
        // Check if we have any data
        if (Object.keys(data).length === 0) {
            return false;
        }
        
        // Check for required fields (basic validation)
        var $requiredInputs = $('.cmama-input[required]');
        var allRequired = true;
        
        $requiredInputs.each(function() {
            if (!$(this).val()) {
                allRequired = false;
                return false;
            }
        });
        
        return allRequired;
    }

    /**
     * Validate individual input
     *
     * @param {jQuery} $input
     */
    function validateInput($input) {
        var value = $input.val();
        var type = $input.attr('type');
        var min = $input.attr('min');
        var max = $input.attr('max');
        
        // Remove previous validation classes
        $input.removeClass('cmama-input-error cmama-input-valid');
        
        if (value === '') {
            return;
        }
        
        var isValid = true;
        
        // Check numeric inputs
        if (type === 'number') {
            var numValue = parseFloat(value);
            
            if (isNaN(numValue)) {
                isValid = false;
            } else {
                if (min !== undefined && numValue < parseFloat(min)) {
                    isValid = false;
                }
                if (max !== undefined && numValue > parseFloat(max)) {
                    isValid = false;
                }
            }
        }
        
        // Add validation class
        $input.addClass(isValid ? 'cmama-input-valid' : 'cmama-input-error');
    }

    /**
     * Display calculation results
     *
     * @param {jQuery} $results
     * @param {Object} data
     */
    function displayResults($results, data) {
        // Update result values
        $.each(data, function(key, value) {
            if (key.startsWith('raw_')) {
                return; // Skip raw values
            }
            
            var $result = $results.find('.cmama-' + key.replace(/_/g, '-'));
            if ($result.length) {
                $result.find('.cmama-result-value').text(value);
            }
        });
        
        // Show results with animation
        $results.slideDown();
    }

    /**
     * Show error message
     *
     * @param {jQuery} $calculator
     * @param {string} message
     */
    function showError($calculator, message) {
        var $error = $('<div class="cmama-error">' + message + '</div>');
        $calculator.find('.cmama-calculator-form').after($error);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $error.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Reset calculator
     *
     * @param {jQuery} $calculator
     */
    function resetCalculator($calculator) {
        var $form = $calculator.find('.cmama-calculator-form');
        var $results = $calculator.find('.cmama-results');
        
        // Clear form
        $form[0].reset();
        
        // Clear validation classes
        $form.find('.cmama-input').removeClass('cmama-input-error cmama-input-valid');
        
        // Hide results
        $results.hide();
        
        // Clear errors
        $calculator.find('.cmama-error').remove();
    }

    /**
     * Reset loading state
     *
     * @param {jQuery} $calculator
     * @param {jQuery} $button
     */
    function resetLoadingState($calculator, $button) {
        $calculator.removeClass('loading');
        $button.prop('disabled', false);
    }

    // BMI Calculator specific functionality
    $(document).on('cmama:calculation:complete', function(e, calculatorType, data) {
        if (calculatorType === 'bmi' && data.raw_bmi) {
            highlightBMICategory(data.raw_bmi);
        }
    });

    /**
     * Highlight BMI category
     *
     * @param {number} bmi
     */
    function highlightBMICategory(bmi) {
        $('.cmama-bmi-range').removeClass('active');
        
        if (bmi < 18.5) {
            $('.cmama-bmi-range.underweight').addClass('active');
        } else if (bmi >= 18.5 && bmi < 25) {
            $('.cmama-bmi-range.normal').addClass('active');
        } else if (bmi >= 25 && bmi < 30) {
            $('.cmama-bmi-range.overweight').addClass('active');
        } else {
            $('.cmama-bmi-range.obese').addClass('active');
        }
    }

})(jQuery);