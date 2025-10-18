(function(){
  function handleClick(container){
    const h = parseFloat(container.querySelector('#cmama-bmi-height')?.value || '0');
    const w = parseFloat(container.querySelector('#cmama-bmi-weight')?.value || '0');
    const resultEl = container.querySelector('#cmama-bmi-result');

    if (h <= 0 || w <= 0) {
      resultEl.textContent = 'Please enter valid values.';
      return;
    }
    const m = h / 100;
    const bmi = w / (m*m);
    resultEl.textContent = 'BMI: ' + bmi.toFixed(1);
  }

  function init(){
    document.querySelectorAll('[data-cmama="bmi"]').forEach(function(container){
      const btn = container.querySelector('[data-calc="bmi"]');
      if (btn) btn.addEventListener('click', function(){ handleClick(container); });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
