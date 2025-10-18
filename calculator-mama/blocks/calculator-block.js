/**
 * Calculator Mama - Gutenberg Block
 *
 * @package CalculatorMama
 * @since 1.0.0
 */

(function() {
    'use strict';

    const { registerBlockType } = wp.blocks;
    const { 
        PanelBody, 
        SelectControl, 
        TextControl, 
        TextareaControl,
        Button,
        Placeholder,
        Spinner,
        Notice
    } = wp.components;
    const { InspectorControls, RichText } = wp.blockEditor;
    const { Fragment, useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { apiFetch } = wp;

    /**
     * FAQ Component
     */
    const FAQComponent = ({ faqs, onChange }) => {
        const addFAQ = () => {
            const newFAQs = [...faqs, { question: '', answer: '' }];
            onChange(newFAQs);
        };

        const removeFAQ = (index) => {
            const newFAQs = faqs.filter((_, i) => i !== index);
            onChange(newFAQs);
        };

        const updateFAQ = (index, field, value) => {
            const newFAQs = [...faqs];
            newFAQs[index][field] = value;
            onChange(newFAQs);
        };

        return (
            <div className="cmama-faq-editor">
                <div className="cmama-faq-header">
                    <h4>{__('Frequently Asked Questions', 'calculator-mama')}</h4>
                    <Button 
                        isSecondary 
                        isSmall 
                        onClick={addFAQ}
                        icon="plus"
                    >
                        {__('Add FAQ', 'calculator-mama')}
                    </Button>
                </div>
                
                {faqs.length === 0 && (
                    <p className="cmama-faq-empty">
                        {__('No FAQs added yet. Click "Add FAQ" to get started.', 'calculator-mama')}
                    </p>
                )}

                {faqs.map((faq, index) => (
                    <div key={index} className="cmama-faq-item">
                        <div className="cmama-faq-item-header">
                            <span className="cmama-faq-number">#{index + 1}</span>
                            <Button 
                                isDestructive 
                                isSmall 
                                onClick={() => removeFAQ(index)}
                                icon="trash"
                            >
                                {__('Remove', 'calculator-mama')}
                            </Button>
                        </div>
                        
                        <TextControl
                            label={__('Question', 'calculator-mama')}
                            value={faq.question}
                            onChange={(value) => updateFAQ(index, 'question', value)}
                            placeholder={__('Enter your question...', 'calculator-mama')}
                        />
                        
                        <TextareaControl
                            label={__('Answer', 'calculator-mama')}
                            value={faq.answer}
                            onChange={(value) => updateFAQ(index, 'answer', value)}
                            placeholder={__('Enter your answer...', 'calculator-mama')}
                            rows={3}
                        />
                    </div>
                ))}
            </div>
        );
    };

    /**
     * Calculator Preview Component
     */
    const CalculatorPreview = ({ calculatorSlug }) => {
        const [preview, setPreview] = useState(null);
        const [loading, setLoading] = useState(false);
        const [error, setError] = useState(null);

        useEffect(() => {
            if (!calculatorSlug) {
                setPreview(null);
                return;
            }

            setLoading(true);
            setError(null);

            apiFetch({
                path: '/calculator-mama/v1/preview/' + calculatorSlug,
                method: 'GET'
            })
            .then((response) => {
                setPreview(response);
                setLoading(false);
            })
            .catch((err) => {
                setError(err.message || __('Failed to load preview', 'calculator-mama'));
                setLoading(false);
            });
        }, [calculatorSlug]);

        if (!calculatorSlug) {
            return null;
        }

        if (loading) {
            return (
                <div className="cmama-preview-loading">
                    <Spinner />
                    <p>{__('Loading calculator preview...', 'calculator-mama')}</p>
                </div>
            );
        }

        if (error) {
            return (
                <Notice status="error" isDismissible={false}>
                    {error}
                </Notice>
            );
        }

        if (!preview) {
            return null;
        }

        return (
            <div 
                className="cmama-calculator-preview"
                dangerouslySetInnerHTML={{ __html: preview.html }}
            />
        );
    };

    /**
     * Register Calculator Block
     */
    registerBlockType('calculator-mama/calculator', {
        title: __('Calculator Mama', 'calculator-mama'),
        description: __('Add an interactive calculator with SEO optimization.', 'calculator-mama'),
        icon: 'calculator',
        category: 'widgets',
        keywords: [
            __('calculator', 'calculator-mama'),
            __('math', 'calculator-mama'),
            __('tool', 'calculator-mama')
        ],

        attributes: {
            calculatorSlug: {
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

        supports: {
            align: ['wide', 'full'],
            html: false
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { calculatorSlug, introduction, instructions, faqs } = attributes;
            
            const [calculators, setCalculators] = useState([]);
            const [loading, setLoading] = useState(true);
            const [error, setError] = useState(null);

            // Load available calculators
            useEffect(() => {
                apiFetch({
                    path: '/calculator-mama/v1/calculators',
                    method: 'GET'
                })
                .then((response) => {
                    const calculatorOptions = [
                        { value: '', label: __('Select a calculator...', 'calculator-mama') }
                    ];
                    
                    response.forEach((calc) => {
                        calculatorOptions.push({
                            value: calc.slug,
                            label: `${calc.name} (${calc.category})`
                        });
                    });
                    
                    setCalculators(calculatorOptions);
                    setLoading(false);
                })
                .catch((err) => {
                    setError(err.message || __('Failed to load calculators', 'calculator-mama'));
                    setLoading(false);
                });
            }, []);

            const onCalculatorChange = (newSlug) => {
                setAttributes({ calculatorSlug: newSlug });
            };

            const onIntroductionChange = (newIntroduction) => {
                setAttributes({ introduction: newIntroduction });
            };

            const onInstructionsChange = (newInstructions) => {
                setAttributes({ instructions: newInstructions });
            };

            const onFAQsChange = (newFAQs) => {
                setAttributes({ faqs: newFAQs });
            };

            // Loading state
            if (loading) {
                return (
                    <Placeholder
                        icon="calculator"
                        label={__('Calculator Mama', 'calculator-mama')}
                    >
                        <Spinner />
                        <p>{__('Loading calculators...', 'calculator-mama')}</p>
                    </Placeholder>
                );
            }

            // Error state
            if (error) {
                return (
                    <Placeholder
                        icon="calculator"
                        label={__('Calculator Mama', 'calculator-mama')}
                    >
                        <Notice status="error" isDismissible={false}>
                            {error}
                        </Notice>
                    </Placeholder>
                );
            }

            // No calculator selected
            if (!calculatorSlug) {
                return (
                    <Fragment>
                        <InspectorControls>
                            <PanelBody title={__('Calculator Settings', 'calculator-mama')}>
                                <SelectControl
                                    label={__('Select Calculator', 'calculator-mama')}
                                    value={calculatorSlug}
                                    options={calculators}
                                    onChange={onCalculatorChange}
                                />
                            </PanelBody>
                        </InspectorControls>

                        <Placeholder
                            icon="calculator"
                            label={__('Calculator Mama', 'calculator-mama')}
                            instructions={__('Select a calculator to display on your page.', 'calculator-mama')}
                        >
                            <SelectControl
                                value={calculatorSlug}
                                options={calculators}
                                onChange={onCalculatorChange}
                            />
                        </Placeholder>
                    </Fragment>
                );
            }

            // Calculator selected - show full editor
            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={__('Calculator Settings', 'calculator-mama')}>
                            <SelectControl
                                label={__('Select Calculator', 'calculator-mama')}
                                value={calculatorSlug}
                                options={calculators}
                                onChange={onCalculatorChange}
                            />
                        </PanelBody>

                        <PanelBody 
                            title={__('SEO Content', 'calculator-mama')} 
                            initialOpen={false}
                        >
                            <p className="components-base-control__help">
                                {__('Add SEO-optimized content around your calculator to improve search rankings.', 'calculator-mama')}
                            </p>
                            
                            <TextareaControl
                                label={__('Introduction', 'calculator-mama')}
                                value={introduction}
                                onChange={onIntroductionChange}
                                placeholder={__('Introduce your calculator and explain its purpose...', 'calculator-mama')}
                                help={__('This text will appear above the calculator.', 'calculator-mama')}
                                rows={3}
                            />
                            
                            <TextareaControl
                                label={__('Usage Instructions', 'calculator-mama')}
                                value={instructions}
                                onChange={onInstructionsChange}
                                placeholder={__('Explain how to use the calculator...', 'calculator-mama')}
                                help={__('This text will appear below the calculator.', 'calculator-mama')}
                                rows={3}
                            />
                        </PanelBody>

                        <PanelBody 
                            title={__('FAQ Section', 'calculator-mama')} 
                            initialOpen={false}
                        >
                            <p className="components-base-control__help">
                                {__('Add frequently asked questions to target long-tail keywords and improve SEO.', 'calculator-mama')}
                            </p>
                            
                            <FAQComponent 
                                faqs={faqs}
                                onChange={onFAQsChange}
                            />
                        </PanelBody>
                    </InspectorControls>

                    <div className="cmama-block-editor">
                        {/* Introduction */}
                        {introduction && (
                            <div className="cmama-introduction-editor">
                                <h4>{__('Introduction', 'calculator-mama')}</h4>
                                <RichText
                                    tagName="div"
                                    value={introduction}
                                    onChange={onIntroductionChange}
                                    placeholder={__('Add an introduction...', 'calculator-mama')}
                                    className="cmama-introduction"
                                />
                            </div>
                        )}

                        {/* Calculator Preview */}
                        <div className="cmama-calculator-editor">
                            <div className="cmama-calculator-header">
                                <h4>{__('Calculator Preview', 'calculator-mama')}</h4>
                                <span className="cmama-calculator-slug">{calculatorSlug}</span>
                            </div>
                            
                            <CalculatorPreview calculatorSlug={calculatorSlug} />
                        </div>

                        {/* Instructions */}
                        {instructions && (
                            <div className="cmama-instructions-editor">
                                <h4>{__('Usage Instructions', 'calculator-mama')}</h4>
                                <RichText
                                    tagName="div"
                                    value={instructions}
                                    onChange={onInstructionsChange}
                                    placeholder={__('Add usage instructions...', 'calculator-mama')}
                                    className="cmama-instructions"
                                />
                            </div>
                        )}

                        {/* FAQ Preview */}
                        {faqs.length > 0 && (
                            <div className="cmama-faq-preview">
                                <h4>{__('FAQ Preview', 'calculator-mama')}</h4>
                                <div className="cmama-faqs">
                                    {faqs.map((faq, index) => (
                                        faq.question && faq.answer && (
                                            <div key={index} className="cmama-faq-item">
                                                <h5 className="cmama-faq-question">{faq.question}</h5>
                                                <div className="cmama-faq-answer">{faq.answer}</div>
                                            </div>
                                        )
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* SEO Tips */}
                        <div className="cmama-seo-tips">
                            <h4>{__('SEO Tips', 'calculator-mama')}</h4>
                            <ul>
                                <li>{__('Add a compelling introduction that explains the calculator\'s value', 'calculator-mama')}</li>
                                <li>{__('Include step-by-step usage instructions', 'calculator-mama')}</li>
                                <li>{__('Add FAQs targeting long-tail keywords related to your calculator', 'calculator-mama')}</li>
                                <li>{__('Use natural language and include relevant keywords', 'calculator-mama')}</li>
                            </ul>
                        </div>
                    </div>
                </Fragment>
            );
        },

        save: function(props) {
            const { attributes } = props;
            const { calculatorSlug, introduction, instructions, faqs } = attributes;

            // Return null - we'll render this server-side
            return null;
        }
    });

})();