(function(){
  function formatCurrency(n) {
    if (!isFinite(n)) return '';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(n);
  }

  function calcPayment(P, annualRate, years) {
    if (P <= 0 || annualRate < 0 || years <= 0) return NaN;
    const r = (annualRate/100)/12;
    const n = years*12;
    if (r === 0) return P / n;
    return P * (r * Math.pow(1+r, n)) / (Math.pow(1+r, n) - 1);
  }

  function handleClick(container){
    const amount = parseFloat(container.querySelector('#cmama-mortgage-amount')?.value || '0');
    const rate = parseFloat(container.querySelector('#cmama-mortgage-rate')?.value || '0');
    const years = parseInt(container.querySelector('#cmama-mortgage-years')?.value || '0', 10);
    const resultEl = container.querySelector('#cmama-mortgage-result');

    const payment = calcPayment(amount, rate, years);
    if (!isFinite(payment)) {
      resultEl.textContent = 'Please enter valid values.';
      return;
    }
    resultEl.textContent = 'Estimated monthly payment: ' + formatCurrency(payment);
  }

  function init(){
    document.querySelectorAll('[data-cmama="mortgage"]').forEach(function(container){
      const btn = container.querySelector('[data-calc="mortgage"]');
      if (btn) btn.addEventListener('click', function(){ handleClick(container); });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
