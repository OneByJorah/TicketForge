/* ============================================================
   arahOS Help Desk — PWA registration + Install banner
   1) Registers /sw.js
   2) Shows a branded "Install the app" banner when the browser
      fires beforeinstallprompt (and on iOS Safari via guidance)
   ============================================================ */
(function () {
  "use strict";

  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  // --- 1) Register service worker ---
  if ("serviceWorker" in navigator) {
    ready(function () {
      navigator.serviceWorker.register("/sw.js").catch(function () {});
    });
  }

  // --- 2) Install banner ---
  var deferredPrompt = null;
  var dismissedKey = "arahOS-install-dismissed";

  window.addEventListener("beforeinstallprompt", function (e) {
    e.preventDefault();
    deferredPrompt = e;
    showBanner();
  });

  function isIOS() {
    return /iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream;
  }
  function isInStandalone() {
    return window.matchMedia("(display-mode: standalone)").matches || window.navigator.standalone === true;
  }
  function wasDismissed() {
    try { return localStorage.getItem(dismissedKey) === "1"; } catch (e) { return false; }
  }

  function showBanner() {
    if (isInStandalone() || wasDismissed()) return;
    if (document.getElementById("arahOS-install-banner")) return;

    var bar = document.createElement("div");
    bar.id = "arahOS-install-banner";
    bar.innerHTML =
      '<img src="/images/arahOS/pwa/icon-192.png" alt="arahOS" class="vib-icon">' +
      '<div class="vib-text">' +
        '<strong>Install arahOS Help Desk</strong>' +
        '<span>Add to your home screen for quick access</span>' +
      '</div>' +
      '<button class="vib-install" type="button">' + (isIOS() ? "How to install" : "Install") + '</button>' +
      '<button class="vib-close" type="button" aria-label="Dismiss">&times;</button>';

    document.body.appendChild(bar);

    bar.querySelector(".vib-close").addEventListener("click", function () {
      try { localStorage.setItem(dismissedKey, "1"); } catch (e) {}
      bar.remove();
    });

    bar.querySelector(".vib-install").addEventListener("click", function () {
      if (isIOS()) {
        showIOSHelp();
        return;
      }
      if (!deferredPrompt) return;
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then(function (choice) {
        if (choice.outcome === "accepted") bar.remove();
        deferredPrompt = null;
      });
    });
  }

  function showIOSHelp() {
    var modal = document.createElement("div");
    modal.className = "arahOS-ios-help";
    modal.innerHTML =
      '<div class="arahOS-ios-card">' +
        '<h3>Install on your iPhone</h3>' +
        '<ol>' +
          '<li>Tap the <strong>Share</strong> button <span class="ios-share">&#8679;</span></li>' +
          '<li>Scroll and tap <strong>Add to Home Screen</strong></li>' +
          '<li>Tap <strong>Add</strong> in the top right</li>' +
        '</ol>' +
        '<button class="vib-install" type="button">Got it</button>' +
      '</div>';
    document.body.appendChild(modal);
    modal.querySelector(".vib-install").addEventListener("click", function () {
      try { localStorage.setItem(dismissedKey, "1"); } catch (e) {}
      modal.remove();
      var b = document.getElementById("arahOS-install-banner"); if (b) b.remove();
    });
  }

  // Show on iOS even without beforeinstallprompt (Safari doesn't fire it)
  ready(function () {
    if (isIOS() && !isInStandalone() && !wasDismissed()) showBanner();
  });

  // Hide banner once installed
  window.addEventListener("appinstalled", function () {
    var b = document.getElementById("arahOS-install-banner"); if (b) b.remove();
  });
})();
