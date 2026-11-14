(function () {
  'use strict';
  var doc = document;
  var body = doc.body;

  /* Menu mobile (tiroir) */
  var drawer = doc.getElementById('snd-drawer');
  var burger = doc.querySelector('.snd-burger');
  var lastFocus = null;
  function openDrawer() {
    if (!drawer) return;
    lastFocus = doc.activeElement;
    drawer.hidden = false;
    requestAnimationFrame(function () { drawer.classList.add('is-open'); });
    body.classList.add('snd-lock');
    if (burger) burger.setAttribute('aria-expanded', 'true');
    var first = drawer.querySelector('input, a, button');
    if (first) setTimeout(function () { first.focus(); }, 60);
  }
  function closeDrawer() {
    if (!drawer || drawer.hidden) return;
    drawer.classList.remove('is-open');
    body.classList.remove('snd-lock');
    if (burger) burger.setAttribute('aria-expanded', 'false');
    setTimeout(function () { drawer.hidden = true; }, 250);
    if (lastFocus) lastFocus.focus();
  }
  if (burger) burger.addEventListener('click', openDrawer);
  if (drawer) {
    drawer.addEventListener('click', function (e) {
      if (e.target.closest('[data-snd-close]')) closeDrawer();
    });
  }

  /* Barre de recherche */
  var bar = doc.getElementById('snd-searchbar');
  var toggles = doc.querySelectorAll('.snd-search-toggle');
  function setSearch(open) {
    if (!bar) return;
    bar.hidden = !open;
    toggles.forEach(function (t) { t.setAttribute('aria-expanded', open ? 'true' : 'false'); });
    if (open) {
      var i = bar.querySelector('input');
      if (i) i.focus();
    }
  }
  toggles.forEach(function (t) {
    t.addEventListener('click', function () { setSearch(bar && bar.hidden); });
  });

  doc.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeDrawer(); setSearch(false); }
  });

  /* Menu collant, barre de progression, retour en haut */
  var nav = doc.querySelector('.snd-nav');
  var progress = doc.querySelector('.snd-progress span');
  var article = doc.querySelector('.snd-prose');
  var totop = doc.querySelector('.snd-totop');
  var ticking = false;
  function onScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (nav) nav.classList.toggle('is-stuck', y > 160);
    body.classList.toggle('snd-scrolled', y > 120);
    if (totop) totop.hidden = y < 900;
    if (progress && article) {
      var r = article.getBoundingClientRect();
      var total = r.height - window.innerHeight * 0.6;
      var done = Math.min(1, Math.max(0, -r.top / (total > 0 ? total : 1)));
      progress.style.transform = 'scaleX(' + done + ')';
    }
    ticking = false;
  }
  window.addEventListener('scroll', function () {
    if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
  }, { passive: true });
  onScroll();
  if (totop) totop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

  /* Copier le lien */
  doc.addEventListener('click', function (e) {
    var b = e.target.closest('[data-snd-copy]');
    if (!b) return;
    var url = b.getAttribute('data-snd-copy');
    var done = function () {
      b.classList.add('is-done');
      b.setAttribute('aria-label', 'Lien copié');
      setTimeout(function () { b.classList.remove('is-done'); b.setAttribute('aria-label', 'Copier le lien'); }, 1800);
    };
    if (navigator.clipboard) navigator.clipboard.writeText(url).then(done, function () {});
  });

  /* Compteur de lectures (fonctionne avec le cache de page) */
  if (window.SND && SND.postId) {
    var data = new FormData();
    data.append('action', 'snd_view');
    data.append('id', SND.postId);
    setTimeout(function () {
      if (navigator.sendBeacon) navigator.sendBeacon(SND.ajax, data);
      else fetch(SND.ajax, { method: 'POST', body: data, credentials: 'same-origin' });
    }, 3000);
  }
})();
