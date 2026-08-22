/* CRC Vinç — etkileşimler: reveal, sayaç, slider, mobil menü, header gölgesi */
(function () {
  'use strict';

  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Preloader: sayfa yüklenince (en az 400ms göründükten sonra) kapat.
     CSS'teki preloader-autohide animasyonu JS hiç çalışmasa da 4sn sonra kendini kapatır (güvenlik ağı). */
  var preloader = document.getElementById('preloader');
  if (preloader) {
    var preloaderStart = Date.now();
    var minDuration = parseInt(preloader.getAttribute('data-min-duration'), 10);
    if (isNaN(minDuration) || minDuration < 0) minDuration = 400;
    var hidePreloader = function () {
      if (preloader.classList.contains('is-hidden')) return;
      var elapsed = Date.now() - preloaderStart;
      var wait = Math.max(0, minDuration - elapsed);
      setTimeout(function () {
        preloader.classList.add('is-hidden');
        setTimeout(function () {
          if (preloader.parentNode) preloader.parentNode.removeChild(preloader);
        }, 600);
      }, wait);
    };
    window.addEventListener('load', hidePreloader);
    setTimeout(hidePreloader, 3000);
  }

  /* Scroll'da header'a çizgi/gölge */
  var header = document.querySelector('[data-header]');
  if (header) {
    var onScroll = function () {
      header.classList.toggle('is-scrolled', window.scrollY > 10);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* Mobil menü — bağımsız bir eleman (header'ın flex bağlamının dışında, bkz. main.css notu) */
  var navToggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-mobile-menu]');
  if (navToggle && nav) {
    navToggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', String(open));
      document.body.classList.toggle('nav-locked', open);
    });
    nav.addEventListener('click', function (event) {
      if (event.target.tagName === 'A') {
        nav.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-locked');
      }
    });
  }

  /* Reveal animasyonları */
  var revealElements = document.querySelectorAll('[data-reveal]');
  if (reducedMotion || !('IntersectionObserver' in window)) {
    revealElements.forEach(function (el) { el.classList.add('is-visible'); });
  } else {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    revealElements.forEach(function (el) { revealObserver.observe(el); });
  }

  /* Sayaç animasyonu */
  var counters = document.querySelectorAll('[data-counter]');
  var animateCounter = function (el) {
    var target = parseInt(el.getAttribute('data-counter'), 10) || 0;
    if (reducedMotion) {
      el.textContent = String(target);
      return;
    }
    var duration = 1600;
    var start = null;
    var step = function (timestamp) {
      if (start === null) start = timestamp;
      var progress = Math.min((timestamp - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = String(Math.round(target * eased));
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };
  if ('IntersectionObserver' in window) {
    var counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { counterObserver.observe(el); });
  } else {
    counters.forEach(animateCounter);
  }

  /* Proje slider'ı (scroll-snap + ok butonları) */
  document.querySelectorAll('[data-slider]').forEach(function (slider) {
    var track = slider.querySelector('.slider-track');
    var prev = slider.querySelector('[data-slider-prev]');
    var next = slider.querySelector('[data-slider-next]');
    if (!track) return;
    var slideWidth = function () {
      var slide = track.querySelector('.slide');
      return slide ? slide.getBoundingClientRect().width + 22 : 300;
    };
    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -slideWidth(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function () { track.scrollBy({ left: slideWidth(), behavior: 'smooth' }); });
  });
})();
