/* ============================================================
   arahOS Help Desk — Client Portal enhancements
   1) Adds a hamburger toggle for the nav on mobile widths
   2) Auto-labels table cells (data-label) from the header row so
      the mobile "card" CSS can show "Subject: ..." style rows
      without editing every osTicket template by hand.
   ============================================================ */
(function () {
  "use strict";

  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  ready(function () {
    // --- 0) Skip-to-content link (accessibility) ---
    if (!document.querySelector("arahOS-skip-link")) {
      var main = document.querySelector("#content, main, .container");
      if (main && !main.id) main.id = "arahOS-main-content";
      var skip = document.createElement("a");
      skip.className = "arahOS-skip-link";
      skip.href = "#" + (main ? main.id : "arahOS-main-content");
      skip.textContent = "Skip to main content";
      document.body.insertBefore(skip, document.body.firstChild);
    }

    // --- 0b) Dark mode toggle, persisted via localStorage.
    //     Respects OS preference by default; button lets the person override it. ---
    var savedTheme = localStorage.getItem("arahOS-theme");
    if (savedTheme) document.documentElement.setAttribute("data-theme", savedTheme);

    var headerEl = document.querySelector("#header, .header");
    if (headerEl && !document.querySelector("arahOS-theme-toggle")) {
      var themeBtn = document.createElement("button");
      themeBtn.className = "arahOS-theme-toggle";
      themeBtn.setAttribute("aria-label", "Toggle dark mode");
      themeBtn.textContent = savedTheme === "dark" ? "☀" : "🌙";
      themeBtn.addEventListener("click", function () {
        var isDark = document.documentElement.getAttribute("data-theme") === "dark";
        var next = isDark ? "light" : "dark";
        document.documentElement.setAttribute("data-theme", next);
        localStorage.setItem("arahOS-theme", next);
        themeBtn.textContent = next === "dark" ? "☀" : "🌙";
      });
      headerEl.appendChild(themeBtn);
    }

    // --- 1) Mobile nav toggle ---
    var header = document.querySelector("#header, .header");
    var nav = document.querySelector("#nav, ul.nav, .navbar");

    if (header && nav && !document.querySelector("arahOS-nav-toggle")) {
      var btn = document.createElement("button");
      btn.className = "arahOS-nav-toggle";
      btn.setAttribute("aria-label", "Toggle navigation menu");
      btn.innerHTML = "&#9776;"; // hamburger icon
      btn.addEventListener("click", function () {
        nav.classList.toggle("arahOS-nav-open");
      });
      header.appendChild(btn);
    }

    // --- 2) data-label auto-injection for responsive tables ---
    document.querySelectorAll("table.list, table.grid").forEach(function (table) {
      var headers = Array.prototype.map.call(
        table.querySelectorAll("thead th"),
        function (th) { return th.textContent.trim(); }
      );
      if (!headers.length) return;

      table.querySelectorAll("tbody tr").forEach(function (row) {
        Array.prototype.forEach.call(row.children, function (cell, i) {
          if (headers[i]) cell.setAttribute("data-label", headers[i]);
        });
      });
    });
  });

  // --- Optional toast helper — call window.arahOSToast("Ticket submitted")
  //     from anywhere (e.g. after an AJAX form submit) for a small
  //     bottom-of-screen confirmation instead of a full page reload alert. ---
  window.arahOSToast = function (message, duration) {
    var existing = document.querySelector("arahOS-toast");
    if (existing) existing.remove();
    var toast = document.createElement("div");
    toast.className = "arahOS-toast";
    toast.setAttribute("role", "status");
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(function () { toast.remove(); }, duration || 3500);
  };
})();
