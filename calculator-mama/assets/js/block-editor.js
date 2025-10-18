/**
 * Calculator Mama - Gutenberg Block
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

(function (wp) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, RichText } = wp.blockEditor || wp.editor;
	const { PanelBody, SelectControl, Button, TextControl } = wp.components;
	const { Fragment } = wp.element;
	const { __ } = wp.i18n;

	registerBlockType('calculator-mama/calculator', {
		title: __('Calculator Mama', 'calculator-mama'),
		description: __('Embed an interactive calculator', 'calculator-mama'),
		icon: 'calculator',
		category: 'widgets',
		attributes: {
			slug: {
				type: 'string',
				default: ''
			},
			introduction: {
				type: 'string',
				default: ''
			},
			instructions: {
				type: 'string',
				default: ''
			},
			faqs: {
				type: 'array',
				default: []
			}
		},

		edit: function (props) {
			const { attributes, setAttributes } = props;
			const { slug, introduction, instructions, faqs } = attributes;

			// Get calculator data.
			const calculators = window.cmamaBlockData ? window.cmamaBlockData.calculators : [];
			const selectedCalculator = calculators.find(calc => calc.slug === slug);

			// Build calculator options.
			const calculatorOptions = [
				{ label: __('Select a calculator...', 'calculator-mama'), value: '' }
			];

			calculators.forEach(function (calc) {
				calculatorOptions.push({
					label: calc.name + ' (' + calc.category + ')',
					value: calc.slug,
					disabled: !calc.active
				});
			});

			// FAQ management functions.
			const addFAQ = function () {
				const newFAQs = [...faqs, { question: '', answer: '' }];
				setAttributes({ faqs: newFAQs });
			};

			const updateFAQ = function (index, field, value) {
				const newFAQs = [...faqs];
				newFAQs[index][field] = value;
				setAttributes({ faqs: newFAQs });
			};

			const removeFAQ = function (index) {
				const newFAQs = faqs.filter((faq, i) => i !== index);
				setAttributes({ faqs: newFAQs });
			};

			return wp.element.createElement(
				Fragment,
				null,
				// Inspector Controls (Sidebar).
				wp.element.createElement(
					InspectorControls,
					null,
					wp.element.createElement(
						PanelBody,
						{ title: __('Calculator Settings', 'calculator-mama'), initialOpen: true },
						wp.element.createElement(SelectControl, {
							label: __('Select Calculator', 'calculator-mama'),
							value: slug,
							options: calculatorOptions,
							onChange: function (value) {
								setAttributes({ slug: value });
							}
						}),
						slug && selectedCalculator && wp.element.createElement(
							'div',
							{ className: 'cmama-calculator-info' },
							wp.element.createElement('p', null, selectedCalculator.description)
						)
					),
					wp.element.createElement(
						PanelBody,
						{ title: __('FAQ Section', 'calculator-mama'), initialOpen: false },
						faqs.map(function (faq, index) {
							return wp.element.createElement(
								'div',
								{ key: index, className: 'cmama-faq-editor' },
								wp.element.createElement(TextControl, {
									label: __('Question', 'calculator-mama'),
									value: faq.question,
									onChange: function (value) {
										updateFAQ(index, 'question', value);
									}
								}),
								wp.element.createElement(TextControl, {
									label: __('Answer', 'calculator-mama'),
									value: faq.answer,
									onChange: function (value) {
										updateFAQ(index, 'answer', value);
									}
								}),
								wp.element.createElement(Button, {
									isDestructive: true,
									isSmall: true,
									onClick: function () {
										removeFAQ(index);
									}
								}, __('Remove FAQ', 'calculator-mama'))
							);
						}),
						wp.element.createElement(Button, {
							isPrimary: true,
							onClick: addFAQ
						}, __('Add FAQ', 'calculator-mama'))
					)
				),
				// Block content.
				wp.element.createElement(
					'div',
					{ className: 'cmama-block-editor' },
					!slug ? wp.element.createElement(
						'div',
						{ className: 'cmama-placeholder' },
						wp.element.createElement('div', { className: 'dashicons dashicons-calculator' }),
						wp.element.createElement('p', null, __('Select a calculator from the sidebar to get started.', 'calculator-mama'))
					) : wp.element.createElement(
						'div',
						{ className: 'cmama-calculator-preview' },
						wp.element.createElement(
							'div',
							{ className: 'cmama-calculator-header' },
							wp.element.createElement('span', { className: 'dashicons dashicons-calculator' }),
							wp.element.createElement('strong', null, selectedCalculator ? selectedCalculator.name : slug)
						),
						wp.element.createElement(
							'div',
							null,
							wp.element.createElement(RichText, {
								tagName: 'div',
								placeholder: __('Add an introduction explaining what this calculator does...', 'calculator-mama'),
								value: introduction,
								onChange: function (value) {
									setAttributes({ introduction: value });
								},
								className: 'cmama-introduction-editor'
							})
						),
						wp.element.createElement(
							'div',
							{ className: 'cmama-calculator-placeholder' },
							wp.element.createElement('p', null, __('Calculator will appear here on the frontend', 'calculator-mama'))
						),
						wp.element.createElement(
							'div',
							null,
							wp.element.createElement(RichText, {
								tagName: 'div',
								placeholder: __('Add instructions on how to use this calculator...', 'calculator-mama'),
								value: instructions,
								onChange: function (value) {
									setAttributes({ instructions: value });
								},
								className: 'cmama-instructions-editor'
							})
						),
						faqs.length > 0 && wp.element.createElement(
							'div',
							{ className: 'cmama-faq-preview' },
							wp.element.createElement('p', null, faqs.length + ' ' + __('FAQ(s) added', 'calculator-mama'))
						)
					)
				)
			);
		},

		save: function () {
			// Dynamic block - rendered on server side.
			return null;
		}
	});
})(window.wp);
