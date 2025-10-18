/**
 * Calculator Mama - Frontend JavaScript
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Calculator Mama Frontend Class
     */
    class CalculatorMamaFrontend {
        constructor() {
            this.calculators = new Map();
            this.init();
        }

        /**
         * Initialize the frontend functionality
         */
        init() {
            this.bindEvents();
            this.initCalculators();
            this.setupFieldDependencies();
        }

        /**
         * Bind event handlers
         */
        bindEvents() {
            // Form submission
            $(document).on('submit', '.cmama-calculator-form', this.handleFormSubmit.bind(this));
            
            // Reset button
            $(document).on('click', '.cmama-reset-btn', this.handleReset.bind(this));
            
            // Field changes for dynamic updates
            $(document).on('change input', '.cmama-field-input', this.handleFieldChange.bind(this));
            
            // Unit system changes (for calculators like BMI)
            $(document).on('change', 'input[name="unit_system"]', this.handleUnitSystemChange.bind(this));
            
            // Calculation type changes (for calculators like percentage)
            $(document).on('change', 'select[name="calculation_type"]', this.handleCalculationTypeChange.bind(this));
        }

        /**
         * Initialize all calculators on the page
         */
        initCalculators() {
            $('.cmama-calculator').each((index, element) => {
                const $calculator = $(element);
                const slug = $calculator.data('calculator');
                
                if (slug) {
                    this.initCalculator($calculator, slug);
                }
            });
        }

        /**
         * Initialize a single calculator
         */
        initCalculator($calculator, slug) {
            const calculatorData = {
                element: $calculator,
                slug: slug,
                form: $calculator.find('.cmama-calculator-form'),
                resultContainer: $calculator.find('.cmama-calculator-result'),
                errorContainer: $calculator.find('.cmama-calculator-error'),
                isCalculating: false
            };

            this.calculators.set(slug, calculatorData);
            
            // Hide result and error containers initially
            calculatorData.resultContainer.hide();
            calculatorData.errorContainer.hide();
            
            // Setup calculator-specific initialization
            this.setupCalculatorSpecific(calculatorData);
        }

        /**
         * Setup calculator-specific functionality
         */
        setupCalculatorSpecific(calculatorData) {
            const slug = calculatorData.slug;
            
            switch (slug) {
                case 'bmi-calculator':
                    this.setupBMICalculator(calculatorData);
                    break;
                case 'age-calculator':
                    this.setupAgeCalculator(calculatorData);
                    break;
                case 'percentage-calculator':
                    this.setupPercentageCalculator(calculatorData);
                    break;
                case 'mortgage-calculator':
                    this.setupMortgageCalculator(calculatorData);
                    break;
            }
        }

        /**
         * Setup BMI calculator specific functionality
         */
        setupBMICalculator(calculatorData) {
            const $form = calculatorData.form;
            
            // Initially show metric fields
            this.toggleUnitFields($form, 'metric');
            
            // Handle unit system toggle
            $form.find('input[name="unit_system"]').on('change', (e) => {
                this.toggleUnitFields($form, e.target.value);
            });
        }

        /**
         * Setup age calculator specific functionality
         */
        setupAgeCalculator(calculatorData) {
            const $form = calculatorData.form;
            
            // Set max date for birth date to today
            $form.find('input[name="birth_date"]').attr('max', new Date().toISOString().split('T')[0]);
            
            // Set default calculation date to today
            const today = new Date().toISOString().split('T')[0];
            $form.find('input[name="calculation_date"]').val(today);
        }

        /**
         * Setup percentage calculator specific functionality
         */
        setupPercentageCalculator(calculatorData) {
            const $form = calculatorData.form;
            
            // Update field labels based on calculation type
            this.updatePercentageFieldLabels($form);
            
            $form.find('select[name="calculation_type"]').on('change', () => {
                this.updatePercentageFieldLabels($form);
            });
        }

        /**
         * Setup mortgage calculator specific functionality
         */
        setupMortgageCalculator(calculatorData) {
            const $form = calculatorData.form;
            
            // Format currency inputs
            $form.find('input[name="loan_amount"], input[name="down_payment"], input[name="property_tax"], input[name="home_insurance"]')
                .on('blur', this.formatCurrencyInput);
        }

        /**
         * Handle form submission
         */
        handleFormSubmit(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const $calculator = $form.closest('.cmama-calculator');
            const slug = $calculator.data('calculator');
            const calculatorData = this.calculators.get(slug);
            
            if (!calculatorData || calculatorData.isCalculating) {
                return;
            }
            
            this.performCalculation(calculatorData);
        }

        /**
         * Handle reset button click
         */
        handleReset(e) {
            e.preventDefault();
            
            const $button = $(e.target);
            const $calculator = $button.closest('.cmama-calculator');
            const slug = $calculator.data('calculator');
            const calculatorData = this.calculators.get(slug);
            
            if (!calculatorData) {
                return;
            }
            
            this.resetCalculator(calculatorData);
        }

        /**
         * Handle field changes
         */
        handleFieldChange(e) {
            const $field = $(e.target);
            const $calculator = $field.closest('.cmama-calculator');
            const slug = $calculator.data('calculator');
            
            // Clear any previous errors for this field
            this.clearFieldError($field);
            
            // Validate field in real-time
            this.validateField($field);
            
            // Handle calculator-specific field changes
            this.handleCalculatorSpecificFieldChange(slug, $field);
        }

        /**
         * Handle unit system changes
         */
        handleUnitSystemChange(e) {
            const $input = $(e.target);
            const $form = $input.closest('.cmama-calculator-form');
            const unitSystem = $input.val();
            
            this.toggleUnitFields($form, unitSystem);
        }

        /**
         * Handle calculation type changes
         */
        handleCalculationTypeChange(e) {
            const $select = $(e.target);
            const $form = $select.closest('.cmama-calculator-form');
            
            this.updatePercentageFieldLabels($form);
        }

        /**
         * Perform calculation
         */
        performCalculation(calculatorData) {
            const formData = this.getFormData(calculatorData.form);
            
            // Validate form
            if (!this.validateForm(calculatorData.form)) {
                return;
            }
            
            // Set calculating state
            this.setCalculatingState(calculatorData, true);
            
            // Prepare AJAX data
            const ajaxData = {
                action: 'cmama_calculate',
                nonce: cmamaFrontend.nonce,
                calculator_slug: calculatorData.slug,
                inputs: formData
            };
            
            // Perform AJAX request
            $.ajax({
                url: cmamaFrontend.ajaxUrl,
                type: 'POST',
                data: ajaxData,
                timeout: 30000
            })
            .done((response) => {
                if (response.success) {
                    this.displayResult(calculatorData, response.data);
                } else {
                    this.displayError(calculatorData, response.data || cmamaFrontend.strings.error);
                }
            })
            .fail((xhr, status, error) => {
                let errorMessage = cmamaFrontend.strings.error;
                
                if (status === 'timeout') {
                    errorMessage = 'Calculation timed out. Please try again.';
                } else if (xhr.responseJSON && xhr.responseJSON.data) {
                    errorMessage = xhr.responseJSON.data;
                }
                
                this.displayError(calculatorData, errorMessage);
            })
            .always(() => {
                this.setCalculatingState(calculatorData, false);
            });
        }

        /**
         * Get form data as object
         */
        getFormData($form) {
            const formData = {};
            
            $form.find('.cmama-field-input').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                let value = $input.val();
                
                if ($input.attr('type') === 'checkbox') {
                    value = $input.is(':checked');
                } else if ($input.attr('type') === 'radio') {
                    if ($input.is(':checked')) {
                        formData[name] = value;
                    }
                    return;
                }
                
                if (name && value !== undefined) {
                    formData[name] = value;
                }
            });
            
            return formData;
        }

        /**
         * Validate entire form
         */
        validateForm($form) {
            let isValid = true;
            
            $form.find('.cmama-field-input').each((index, element) => {
                if (!this.validateField($(element))) {
                    isValid = false;
                }
            });
            
            return isValid;
        }

        /**
         * Validate individual field
         */
        validateField($field) {
            const value = $field.val();
            const type = $field.attr('type');
            const required = $field.prop('required');
            const min = parseFloat($field.attr('min'));
            const max = parseFloat($field.attr('max'));
            
            // Clear previous errors
            this.clearFieldError($field);
            
            // Check required
            if (required && (!value || value.trim() === '')) {
                this.showFieldError($field, cmamaFrontend.strings.required_field);
                return false;
            }
            
            // Skip other validations if field is empty and not required
            if (!value || value.trim() === '') {
                return true;
            }
            
            // Validate numbers
            if (type === 'number') {
                const numValue = parseFloat(value);
                
                if (isNaN(numValue)) {
                    this.showFieldError($field, cmamaFrontend.strings.invalid_number);
                    return false;
                }
                
                if (!isNaN(min) && numValue < min) {
                    this.showFieldError($field, `Value must be at least ${min}.`);
                    return false;
                }
                
                if (!isNaN(max) && numValue > max) {
                    this.showFieldError($field, `Value must be no more than ${max}.`);
                    return false;
                }
            }
            
            // Validate dates
            if (type === 'date') {
                const dateValue = new Date(value);
                const today = new Date();
                
                if (isNaN(dateValue.getTime())) {
                    this.showFieldError($field, 'Please enter a valid date.');
                    return false;
                }
                
                // Check if birth date is in the future
                if ($field.attr('name') === 'birth_date' && dateValue > today) {
                    this.showFieldError($field, 'Birth date cannot be in the future.');
                    return false;
                }
            }
            
            return true;
        }

        /**
         * Show field error
         */
        showFieldError($field, message) {
            $field.addClass('cmama-field-error');
            
            // Remove existing error message
            $field.siblings('.cmama-field-error-message').remove();
            
            // Add error message
            const $errorMsg = $('<div class="cmama-field-error-message">' + message + '</div>');
            $field.after($errorMsg);
        }

        /**
         * Clear field error
         */
        clearFieldError($field) {
            $field.removeClass('cmama-field-error');
            $field.siblings('.cmama-field-error-message').remove();
        }

        /**
         * Set calculating state
         */
        setCalculatingState(calculatorData, isCalculating) {
            calculatorData.isCalculating = isCalculating;
            
            const $form = calculatorData.form;
            const $calculateBtn = $form.find('.cmama-calculate-btn');
            
            if (isCalculating) {
                $form.addClass('cmama-calculating');
                $calculateBtn.prop('disabled', true);
                $calculateBtn.text(cmamaFrontend.strings.calculating);
            } else {
                $form.removeClass('cmama-calculating');
                $calculateBtn.prop('disabled', false);
                $calculateBtn.text($calculateBtn.data('original-text') || 'Calculate');
            }
        }

        /**
         * Display calculation result
         */
        displayResult(calculatorData, result) {
            const $resultContainer = calculatorData.resultContainer;
            const $errorContainer = calculatorData.errorContainer;
            const $resultContent = $resultContainer.find('.cmama-result-content');
            
            // Hide error container
            $errorContainer.hide();
            
            // Update result content
            $resultContent.html(result.formatted_result || result.html || '');
            
            // Show result container with animation
            $resultContainer.slideDown(300);
            
            // Scroll to result
            this.scrollToResult(calculatorData);
            
            // Log usage if enabled
            this.logUsage(calculatorData.slug, result);
        }

        /**
         * Display error message
         */
        displayError(calculatorData, message) {
            const $resultContainer = calculatorData.resultContainer;
            const $errorContainer = calculatorData.errorContainer;
            const $errorMessage = $errorContainer.find('.cmama-error-message');
            
            // Hide result container
            $resultContainer.hide();
            
            // Update error message
            $errorMessage.text(message);
            
            // Show error container
            $errorContainer.slideDown(300);
        }

        /**
         * Reset calculator
         */
        resetCalculator(calculatorData) {
            const $form = calculatorData.form;
            
            // Reset form
            $form[0].reset();
            
            // Clear all field errors
            $form.find('.cmama-field-input').each((index, element) => {
                this.clearFieldError($(element));
            });
            
            // Hide result and error containers
            calculatorData.resultContainer.hide();
            calculatorData.errorContainer.hide();
            
            // Reset calculator-specific states
            this.resetCalculatorSpecific(calculatorData);
        }

        /**
         * Reset calculator-specific states
         */
        resetCalculatorSpecific(calculatorData) {
            const slug = calculatorData.slug;
            
            switch (slug) {
                case 'bmi-calculator':
                    this.toggleUnitFields(calculatorData.form, 'metric');
                    break;
                case 'percentage-calculator':
                    this.updatePercentageFieldLabels(calculatorData.form);
                    break;
            }
        }

        /**
         * Toggle unit fields for BMI calculator
         */
        toggleUnitFields($form, unitSystem) {
            const $metricFields = $form.find('.cmama-metric-field');
            const $imperialFields = $form.find('.cmama-imperial-field');
            
            if (unitSystem === 'metric') {
                $metricFields.closest('.cmama-field').show();
                $imperialFields.closest('.cmama-field').hide();
                
                // Set required attributes
                $metricFields.prop('required', true);
                $imperialFields.prop('required', false);
            } else {
                $metricFields.closest('.cmama-field').hide();
                $imperialFields.closest('.cmama-field').show();
                
                // Set required attributes
                $metricFields.prop('required', false);
                $imperialFields.prop('required', true);
            }
        }

        /**
         * Update percentage calculator field labels
         */
        updatePercentageFieldLabels($form) {
            const calculationType = $form.find('select[name="calculation_type"]').val();
            const $number1Field = $form.find('input[name="number1"]');
            const $number2Field = $form.find('input[name="number2"]');
            const $number1Label = $form.find('label[for="number1"]');
            const $number2Label = $form.find('label[for="number2"]');
            
            const labelMappings = {
                'percentage_of': {
                    number1: 'Percentage (%)',
                    number2: 'Number'
                },
                'what_percent': {
                    number1: 'Number',
                    number2: 'Total'
                },
                'percent_change': {
                    number1: 'Original Value',
                    number2: 'New Value'
                },
                'find_total': {
                    number1: 'Number',
                    number2: 'Percentage (%)'
                },
                'add_percentage': {
                    number1: 'Percentage to Add (%)',
                    number2: 'Original Number'
                },
                'subtract_percentage': {
                    number1: 'Percentage to Subtract (%)',
                    number2: 'Original Number'
                }
            };
            
            if (labelMappings[calculationType]) {
                $number1Label.text(labelMappings[calculationType].number1);
                $number2Label.text(labelMappings[calculationType].number2);
            }
        }

        /**
         * Format currency input
         */
        formatCurrencyInput(e) {
            const $input = $(e.target);
            let value = $input.val().replace(/[^\d.]/g, '');
            
            if (value && !isNaN(parseFloat(value))) {
                const formatted = parseFloat(value).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
                $input.val(formatted);
            }
        }

        /**
         * Handle calculator-specific field changes
         */
        handleCalculatorSpecificFieldChange(slug, $field) {
            switch (slug) {
                case 'mortgage-calculator':
                    if ($field.attr('name') === 'loan_amount' || $field.attr('name') === 'down_payment') {
                        this.updateDownPaymentPercentage($field.closest('.cmama-calculator-form'));
                    }
                    break;
            }
        }

        /**
         * Update down payment percentage display
         */
        updateDownPaymentPercentage($form) {
            const loanAmount = parseFloat($form.find('input[name="loan_amount"]').val().replace(/[^\d.]/g, '')) || 0;
            const downPayment = parseFloat($form.find('input[name="down_payment"]').val().replace(/[^\d.]/g, '')) || 0;
            
            if (loanAmount > 0) {
                const percentage = (downPayment / loanAmount) * 100;
                const $percentageDisplay = $form.find('.cmama-down-payment-percentage');
                
                if ($percentageDisplay.length === 0) {
                    $form.find('input[name="down_payment"]').after(
                        '<div class="cmama-down-payment-percentage">' + percentage.toFixed(1) + '% of loan amount</div>'
                    );
                } else {
                    $percentageDisplay.text(percentage.toFixed(1) + '% of loan amount');
                }
            }
        }

        /**
         * Setup field dependencies
         */
        setupFieldDependencies() {
            // This can be extended for calculators with complex field dependencies
        }

        /**
         * Scroll to result
         */
        scrollToResult(calculatorData) {
            const $resultContainer = calculatorData.resultContainer;
            
            if ($resultContainer.length && $resultContainer.is(':visible')) {
                $('html, body').animate({
                    scrollTop: $resultContainer.offset().top - 50
                }, 500);
            }
        }

        /**
         * Log usage
         */
        logUsage(slug, result) {
            // Only log if analytics is enabled
            if (typeof gtag !== 'undefined') {
                gtag('event', 'calculator_used', {
                    'calculator_type': slug,
                    'custom_parameter': 'calculator_mama'
                });
            }
            
            // Could also send to custom analytics endpoint
            // This is optional and can be configured in settings
        }
    }

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        // Check if we have calculators on the page
        if ($('.cmama-calculator').length > 0) {
            new CalculatorMamaFrontend();
        }
    });

    /**
     * Handle AJAX calculation requests
     */
    $(document).on('wp_ajax_cmama_calculate wp_ajax_nopriv_cmama_calculate', function() {
        // This is handled by the PHP backend
    });

})(jQuery);