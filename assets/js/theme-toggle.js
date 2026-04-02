(function () {
  var storageKey = "bm-theme-preference";
  var root = document.documentElement;
  var mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

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

    if (storedTheme === "light" || storedTheme === "dark") {
      return storedTheme;
    }

    return mediaQuery.matches ? "dark" : "light";
  }

  function applyTheme(theme) {
    root.dataset.theme = theme;
    root.style.colorScheme = theme;
    updateToggles(theme);
  }

  function getToggles() {
    return document.querySelectorAll(".bm-theme-toggle .wp-block-button__link");
  }

  function updateToggles(theme) {
    var toggles = getToggles();
    var isDark = theme === "dark";

    toggles.forEach(function (toggle) {
      var nextLabel = isDark ? "Switch to light mode" : "Switch to dark mode";

      toggle.setAttribute("role", "switch");
      toggle.setAttribute("aria-checked", String(isDark));
      toggle.setAttribute("aria-label", nextLabel);
      toggle.setAttribute("title", nextLabel);
      toggle.dataset.themeCurrent = theme;
    });
  }

  function updateHeaderState() {
    document.querySelectorAll(".bm-site-header").forEach(function (header) {
      header.classList.toggle("is-scrolled", window.scrollY > 8);
    });
  }

  function handleToggleClick(event) {
    if (event) {
      event.preventDefault();
    }

    var nextTheme = getResolvedTheme() === "dark" ? "light" : "dark";

    setStoredTheme(nextTheme);
    applyTheme(nextTheme);
  }

  function initThemeUi() {
    applyTheme(getResolvedTheme());
    updateHeaderState();

    getToggles().forEach(function (toggle) {
      if (toggle.dataset.themeToggleBound === "true") {
        return;
      }

      toggle.addEventListener("click", handleToggleClick);
      toggle.addEventListener("keydown", function (event) {
        if (event.key === " " || event.key === "Enter") {
          handleToggleClick(event);
        }
      });
      toggle.dataset.themeToggleBound = "true";
    });

    window.addEventListener("scroll", updateHeaderState, {passive: true});
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initThemeUi);
  } else {
    initThemeUi();
  }

  function handleSystemThemeChange() {
    if (getStoredTheme()) {
      return;
    }

    applyTheme(getResolvedTheme());
  }

  if (typeof mediaQuery.addEventListener === "function") {
    mediaQuery.addEventListener("change", handleSystemThemeChange);
  } else if (typeof mediaQuery.addListener === "function") {
    mediaQuery.addListener(handleSystemThemeChange);
  }
})();
