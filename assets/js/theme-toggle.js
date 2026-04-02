(function () {
  var storageKey = 'bm-theme-preference';
  var root = document.documentElement;
  var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

  function getStoredTheme() {
    try {
      return window.localStorage.getItem(storageKey);
    } catch (error) {
      return null;
    }
  }

  function setStoredTheme(value) {
    try {
      window.localStorage.setItem(storageKey, value);
    } catch (error) {
      return;
    }
  }

  function getResolvedTheme() {
    var storedTheme = getStoredTheme();

    if (storedTheme === 'light' || storedTheme === 'dark') {
      return storedTheme;
    }

    return mediaQuery.matches ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    root.dataset.theme = theme;
    root.style.colorScheme = theme;
    updateToggles(theme);
  }

  function updateToggles(theme) {
    var toggles = document.querySelectorAll('[data-theme-toggle]');
    var nextTheme = theme === 'dark' ? 'light' : 'dark';
    var nextLabel = 'Switch to ' + nextTheme + ' mode';

    toggles.forEach(function (toggle) {
      toggle.setAttribute('aria-pressed', String(theme === 'dark'));
      toggle.setAttribute('aria-label', nextLabel);
      toggle.dataset.themeCurrent = theme;

      var label = toggle.querySelector('[data-theme-toggle-label]');

      if (label) {
        label.textContent = nextLabel;
      }
    });
  }

  function updateHeaderState() {
    document.querySelectorAll('[data-site-header]').forEach(function (header) {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
    });
  }

  function handleToggleClick() {
    var nextTheme = getResolvedTheme() === 'dark' ? 'light' : 'dark';

    setStoredTheme(nextTheme);
    applyTheme(nextTheme);
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyTheme(getResolvedTheme());
    updateHeaderState();

    document.querySelectorAll('[data-theme-toggle]').forEach(function (toggle) {
      toggle.addEventListener('click', handleToggleClick);
    });

    window.addEventListener('scroll', updateHeaderState, { passive: true });
  });

  function handleSystemThemeChange() {
    if (getStoredTheme()) {
      return;
    }

    applyTheme(getResolvedTheme());
  }

  if (typeof mediaQuery.addEventListener === 'function') {
    mediaQuery.addEventListener('change', handleSystemThemeChange);
  } else if (typeof mediaQuery.addListener === 'function') {
    mediaQuery.addListener(handleSystemThemeChange);
  }
}());
