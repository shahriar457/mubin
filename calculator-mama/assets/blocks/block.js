(function(wp){
  const { __ } = wp.i18n;
  const { registerBlockType } = wp.blocks;
  const { PanelBody, TextControl, SelectControl, Button, TextareaControl } = wp.components;
  const { InspectorControls, RichText } = wp.blockEditor || wp.editor;

  const ACTIVE = wpm && wpm.cmama && wpm.cmama.active ? wpm.cmama.active : [ 'mortgage', 'bmi' ];

  registerBlockType('cmama/calculator', {
    title: __('Calculator Mama', 'calculator-mama'),
    icon: 'calculator',
    category: 'widgets',
    attributes: {
      slug: { type: 'string', default: 'mortgage' },
      intro: { type: 'string', default: '' },
      usage: { type: 'string', default: '' },
      faq: { type: 'array', default: [] },
    },
    edit: (props) => {
      const { attributes, setAttributes } = props;
      const { slug, intro, usage, faq } = attributes;

      const calculatorOptions = ACTIVE.map(s => ({ label: s, value: s }));

      return (
        wp.element.createElement('div', { className: 'cmama-wrap' },
          wp.element.createElement(InspectorControls, null,
            wp.element.createElement(PanelBody, { title: __('Calculator Settings', 'calculator-mama') },
              wp.element.createElement(SelectControl, {
                label: __('Calculator', 'calculator-mama'),
                value: slug,
                options: calculatorOptions,
                onChange: (val)=> setAttributes({ slug: val })
              })
            ),
            wp.element.createElement(PanelBody, { title: __('SEO Content', 'calculator-mama'), initialOpen: false },
              wp.element.createElement(TextareaControl, {
                label: __('Introduction (RichText in front-end)', 'calculator-mama'),
                value: intro,
                onChange: (val)=> setAttributes({ intro: val })
              }),
              wp.element.createElement(TextareaControl, {
                label: __('Usage Instructions (RichText in front-end)', 'calculator-mama'),
                value: usage,
                onChange: (val)=> setAttributes({ usage: val })
              })
            ),
            wp.element.createElement(PanelBody, { title: __('FAQs', 'calculator-mama'), initialOpen: false },
              wp.element.createElement('div', null,
                (faq || []).map((row, idx) => (
                  wp.element.createElement('div', { key: idx, style: { border: '1px solid #e5e7eb', padding: 8, marginBottom: 8 } },
                    wp.element.createElement(TextControl, {
                      label: __('Question', 'calculator-mama'),
                      value: row.q || '',
                      onChange: (v)=>{
                        const next = [ ...faq ];
                        next[idx] = { ...(next[idx]||{}), q: v };
                        setAttributes({ faq: next });
                      }
                    }),
                    wp.element.createElement(TextareaControl, {
                      label: __('Answer', 'calculator-mama'),
                      value: row.a || '',
                      onChange: (v)=>{
                        const next = [ ...faq ];
                        next[idx] = { ...(next[idx]||{}), a: v };
                        setAttributes({ faq: next });
                      }
                    }),
                    wp.element.createElement(Button, { isDestructive: true, onClick: ()=>{
                      const next = [ ...faq ];
                      next.splice(idx, 1);
                      setAttributes({ faq: next });
                    } }, __('Remove', 'calculator-mama'))
                  )
                )),
                wp.element.createElement(Button, { isPrimary: true, onClick: ()=>{
                  const next = [ ...faq, { q: '', a: '' } ];
                  setAttributes({ faq: next });
                } }, __('Add FAQ', 'calculator-mama'))
              )
            )
          ),
          wp.element.createElement('div', { className: 'cmama-card' },
            wp.element.createElement('div', { style: { marginBottom: 8, fontWeight: 600 } }, __('Preview', 'calculator-mama')),
            wp.element.createElement('div', null, __('Live preview appears on front-end. Choose calculator above.', 'calculator-mama'))
          )
        )
      );
    },
    save: () => null,
  });
})(window.wp || {});
