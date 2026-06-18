/* GEM-MAGAZINE — フロント側の軽量スクリプト（検索開閉・モバイルメニュー・スクロール表示） */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // 検索バー開閉
    var searchToggle = document.querySelector('[data-search-toggle]');
    var searchBar = document.querySelector('[data-search-bar]');
    var menuToggle = document.querySelector('[data-menu-toggle]');
    var mobileMenu = document.querySelector('[data-mobile-menu]');

    if (searchToggle && searchBar) {
      searchToggle.addEventListener('click', function () {
        searchBar.classList.toggle('is-open');
        if (mobileMenu) mobileMenu.classList.remove('is-open');
        if (searchBar.classList.contains('is-open')) {
          var input = searchBar.querySelector('input');
          if (input) input.focus();
        }
      });
    }

    if (menuToggle && mobileMenu) {
      menuToggle.addEventListener('click', function () {
        mobileMenu.classList.toggle('is-open');
        if (searchBar) searchBar.classList.remove('is-open');
      });
    }

    // スクロールでふわっと表示
    var reveals = document.querySelectorAll('.reveal');
    if (reveals.length && 'IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting) {
            en.target.classList.add('is-visible');
            io.unobserve(en.target);
          }
        });
      }, { threshold: 0.12 });
      reveals.forEach(function (el) { io.observe(el); });
    } else {
      reveals.forEach(function (el) { el.classList.add('is-visible'); });
    }
  });
})();
