(function () {
  var root = document.querySelector('[data-snd-slider]');
  if (!root) return;
  var slides = root.querySelectorAll('.snd-slide');
  var dots = root.querySelectorAll('[data-snd-dot]');
  if (slides.length < 2) return;
  var current = 0;
  var timer = null;

  function show(i) {
    current = (i + slides.length) % slides.length;
    slides.forEach(function (s, k) { s.classList.toggle('is-active', k === current); });
    dots.forEach(function (d, k) { d.classList.toggle('is-active', k === current); });
  }
  function start() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    stop();
    timer = setInterval(function () { show(current + 1); }, 6000);
  }
  function stop() { if (timer) { clearInterval(timer); timer = null; } }

  var prev = root.querySelector('[data-snd-prev]');
  var next = root.querySelector('[data-snd-next]');
  if (prev) prev.addEventListener('click', function () { show(current - 1); start(); });
  if (next) next.addEventListener('click', function () { show(current + 1); start(); });
  dots.forEach(function (d, k) { d.addEventListener('click', function () { show(k); start(); }); });
  root.addEventListener('mouseenter', stop);
  root.addEventListener('mouseleave', start);
  root.addEventListener('focusin', stop);
  root.addEventListener('focusout', start);

  var x0 = null;
  root.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
  root.addEventListener('touchend', function (e) {
    if (x0 === null) return;
    var dx = e.changedTouches[0].clientX - x0;
    if (Math.abs(dx) > 40) { show(current + (dx < 0 ? 1 : -1)); start(); }
    x0 = null;
  }, { passive: true });

  start();
})();
