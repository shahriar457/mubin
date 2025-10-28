/**
 * Gutenberg block JavaScript
 *
 * @package Calculator_Mama
 * @since 1.0
 */

(function(blocks, element, components, i18n, wp) {
    'use strict';

    var el = element.createElement;
    var Fragment = element.Fragment;
    var Component = element.Component;
    var useState = element.useState;
    var useEffect = element.useEffect;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var TextControl = components.TextControl;
    var TextareaControl = components.TextareaControl;
    var Button = components.Button;
    var Placeholder = components.Placeholder;
    var Spinner = components.Spinner;
    var ServerSideRender = wp.serverSideRender;
    var __ = i18n.__;

    // Register the block
    blocks.registerBlockType('calculator-mama/calculator', {
        title: __('Calculator Mama', 'calculator-mama'),
        description: __('Add an interactive calculator to your content.', 'calculator-mama'),
        icon: 'calculator',
        category: 'widgets',
        keywords: [
            __('calculator', 'calculator-mama'),
            __('math', 'calculator-mama'),
            __('financial', 'calculator-mama'),
            __('health', 'calculator-mama')
        ],
        attributes: {
            calculator: {
                type: 'string',
                default: ''
            },
            title: {
                type: 'string',
                default: ''
            },
            description: {
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
            faq: {
                type: 'array',
                default: []
            },
            className: {
                type: 'string',
                default: ''
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var calculators = cmama_gutenberg.calculators || [];
            var isLoading = false;

            // Calculator options for select
            var calculatorOptions = [
                { value: '', label: __('Select a calculator...', 'calculator-mama') }
            ].concat(calculators.map(function(calc) {
                return {
                    value: calc.value,
                    label: calc.label
                };
            }));

            // FAQ management
            var addFAQ = function() {
                var newFAQ = { question: '', answer: '' };
                var faqs = attributes.faq.slice();
                faqs.push(newFAQ);
                setAttributes({ faq: faqs });
            };

            var updateFAQ = function(index, field, value) {
                var faqs = attributes.faq.slice();
                faqs[index][field] = value;
                setAttributes({ faq: faqs });
            };

            var removeFAQ = function(index) {
                var faqs = attributes.faq.slice();
                faqs.splice(index, 1);
                setAttributes({ faq: faqs });
            };

            return el(Fragment, null,
                el(InspectorControls, null,
                    el(PanelBody, { title: __('Calculator Settings', 'calculator-mama'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Calculator', 'calculator-mama'),
                            value: attributes.calculator,
                            options: calculatorOptions,
                            onChange: function(value) {
                                setAttributes({ calculator: value });
                            }
                        }),
                        el(TextControl, {
                            label: __('Title', 'calculator-mama'),
                            value: attributes.title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            },
                            help: __('Optional title to display above the calculator.', 'calculator-mama')
                        }),
                        el(TextareaControl, {
                            label: __('Description', 'calculator-mama'),
                            value: attributes.description,
                            onChange: function(value) {
                                setAttributes({ description: value });
                            },
                            help: __('Optional description to display below the title.', 'calculator-mama')
                        })
                    ),
                    el(PanelBody, { title: __('Content', 'calculator-mama'), initialOpen: false },
                        el(TextareaControl, {
                            label: __('Introduction', 'calculator-mama'),
                            value: attributes.introduction,
                            onChange: function(value) {
                                setAttributes({ introduction: value });
                            },
                            help: __('Introduction text to display before the calculator.', 'calculator-mama')
                        }),
                        el(TextareaControl, {
                            label: __('Instructions', 'calculator-mama'),
                            value: attributes.instructions,
                            onChange: function(value) {
                                setAttributes({ instructions: value });
                            },
                            help: __('Instructions on how to use the calculator.', 'calculator-mama')
                        })
                    ),
                    el(PanelBody, { title: __('FAQ Section', 'calculator-mama'), initialOpen: false },
                        el('div', null,
                            el(Button, {
                                isPrimary: true,
                                onClick: addFAQ
                            }, __('Add FAQ', 'calculator-mama')),
                            attributes.faq.map(function(faq, index) {
                                return el('div', { key: index, style: { marginTop: '10px', padding: '10px', border: '1px solid #ddd', borderRadius: '4px' } },
                                    el(TextControl, {
                                        label: __('Question', 'calculator-mama'),
                                        value: faq.question,
                                        onChange: function(value) {
                                            updateFAQ(index, 'question', value);
                                        }
                                    }),
                                    el(TextareaControl, {
                                        label: __('Answer', 'calculator-mama'),
                                        value: faq.answer,
                                        onChange: function(value) {
                                            updateFAQ(index, 'answer', value);
                                        }
                                    }),
                                    el(Button, {
                                        isDestructive: true,
                                        onClick: function() {
                                            removeFAQ(index);
                                        }
                                    }, __('Remove', 'calculator-mama'))
                                );
                            })
                        )
                    )
                ),
                el('div', { className: 'cmama-gutenberg-block' },
                    !attributes.calculator ? el(Placeholder, {
                        icon: 'calculator',
                        label: __('Calculator Mama', 'calculator-mama'),
                        instructions: __('Select a calculator from the sidebar to get started.', 'calculator-mama')
                    }) : el(ServerSideRender, {
                        block: 'calculator-mama/calculator',
                        attributes: attributes
                    })
                )
            );
        },

        save: function() {
            // Server-side rendering
            return null;
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n,
    window.wp
);