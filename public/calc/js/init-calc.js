document.addEventListener('DOMContentLoaded', function () {
  if (!window.calcData || !window.calcData.table) {
    console.error('calcData.table отсутствует');
    return;
  }

  new Vue({
    el: '#app',
    render: h => h(window.App)
  });
});
