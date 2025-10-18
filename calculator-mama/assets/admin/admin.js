(function(){
  function q(sel, root){ return (root||document).querySelector(sel); }
  function qa(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }

  function init(){
    const form = q('#cmama-settings-form');
    if (!form) return;
    const toggles = qa('[data-cmama-toggle]');
    toggles.forEach(function(toggle){
      toggle.addEventListener('change', function(){
        const slug = this.getAttribute('data-cmama-toggle');
        const hidden = q('input[name="cmama_active_calculators"]');
        const list = hidden.value ? hidden.value.split(',').filter(Boolean) : [];
        const idx = list.indexOf(slug);
        if (this.checked && idx === -1) list.push(slug);
        if (!this.checked && idx !== -1) list.splice(idx, 1);
        hidden.value = list.join(',');
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
