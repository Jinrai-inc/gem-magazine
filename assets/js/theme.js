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

/* =========================================================
   お役立ちコラム サブメニュー開閉
   assets/js/theme.js の末尾に貼り付けてください
   （子を持つメニュー項目に開閉ボタン▾を自動で差し込みます）
   ========================================================= */
(function () {
  function initSubmenuToggle() {
    var parents = document.querySelectorAll(
      '.nav-desktop .menu-item-has-children, .mobile-menu .menu-item-has-children'
    );
    parents.forEach(function (li) {
      var sub = li.querySelector(':scope > .sub-menu');
      if (!sub || li.querySelector(':scope > .menu-toggle-sub')) return;

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'menu-toggle-sub';
      btn.setAttribute('aria-label', 'サブメニューを開閉');
      btn.setAttribute('aria-expanded', 'false');
      btn.innerHTML = '<span class="menu-toggle-icon" aria-hidden="true">\u25BE</span>';

      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var open = li.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });

      var link = li.querySelector(':scope > a');
      (link || li).insertAdjacentElement('afterend', btn);
    });
  }
  if (document.readyState !== 'loading') initSubmenuToggle();
  else document.addEventListener('DOMContentLoaded', initSubmenuToggle);
})();
