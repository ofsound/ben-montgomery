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
    var nextLabel = nextTheme === 'dark' ? 'Dark mode' : 'Light mode';

    toggles.forEach(function (toggle) {
      toggle.setAttribute('aria-pressed', String(theme === 'dark'));
      toggle.setAttribute('aria-label', 'Switch to ' + nextTheme + ' mode');
      toggle.dataset.themeCurrent = theme;

      var label = toggle.querySelector('[data-theme-toggle-label]');

      if (label) {
        label.textContent = nextLabel;
      }
    });
  }

  function handleToggleClick() {
    var nextTheme = getResolvedTheme() === 'dark' ? 'light' : 'dark';

    setStoredTheme(nextTheme);
    applyTheme(nextTheme);
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyTheme(getResolvedTheme());

    document.querySelectorAll('[data-theme-toggle]').forEach(function (toggle) {
      toggle.addEventListener('click', handleToggleClick);
    });
  });

  mediaQuery.addEventListener('change', function () {
    if (getStoredTheme()) {
      return;
    }

    applyTheme(getResolvedTheme());
  });
}());
