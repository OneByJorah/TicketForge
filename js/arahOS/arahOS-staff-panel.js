/* ============================================================
   arahOS Help Desk — Staff Panel enhancements
   1) Collapsible mobile sidebar with backdrop + Escape-to-close
   2) Dark mode toggle (persisted, useful for overnight NOC shifts)

   Written defensively: every selector is optional-chained so this
   never throws if a given osTicket build's markup differs slightly.
   ============================================================ */
(function () {
  "use strict";

  // NOTE: sidebar selector retargeted to include real osTicket SCP #sub_nav.
  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  ready(function () {
    var sidebar = document.querySelector("#sub_nav, #sidebar, .sidebar");
    var topbar = document.querySelector("#staff-header, #header, .staff-header");

    // --- 1) Mobile sidebar toggle + backdrop ---
    if (sidebar && topbar) {
      var backdrop = document.createElement("div");
      backdrop.className = "arahOS-sidebar-backdrop";
      document.body.appendChild(backdrop);

      var toggleBtn = document.createElement("button");
      toggleBtn.className = "arahOS-sidebar-toggle";
      toggleBtn.setAttribute("aria-label", "Toggle department menu");
      toggleBtn.innerHTML = "&#9776;";
      topbar.insertBefore(toggleBtn, topbar.firstChild);

      function closeSidebar() {
        sidebar.classList.remove("arahOS-sidebar-open");
        backdrop.classList.remove("arahOS-sidebar-open");
      }
      function openSidebar() {
        sidebar.classList.add("arahOS-sidebar-open");
        backdrop.classList.add("arahOS-sidebar-open");
      }

      toggleBtn.addEventListener("click", function () {
        sidebar.classList.contains("arahOS-sidebar-open") ? closeSidebar() : openSidebar();
      });
      backdrop.addEventListener("click", closeSidebar);
      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeSidebar();
      });
      // Close sidebar automatically after tapping a nav link (mobile UX expectation)
      sidebar.querySelectorAll("a").forEach(function (a) {
        a.addEventListener("click", closeSidebar);
      });
    }

    // --- 2) Dark mode toggle ---
    var savedTheme = localStorage.getItem("arahOS-theme");
    if (savedTheme) document.documentElement.setAttribute("data-theme", savedTheme);

    if (topbar && !document.querySelector("arahOS-theme-toggle")) {
      var themeBtn = document.createElement("button");
      themeBtn.className = "arahOS-theme-toggle";
      themeBtn.setAttribute("aria-label", "Toggle dark mode");
      themeBtn.textContent = savedTheme === "dark" ? "☀" : "🌙";
      topbar.appendChild(themeBtn);

      themeBtn.addEventListener("click", function () {
        var isDark = document.documentElement.getAttribute("data-theme") === "dark";
        var next = isDark ? "light" : "dark";
        document.documentElement.setAttribute("data-theme", next);
        localStorage.setItem("arahOS-theme", next);
        themeBtn.textContent = next === "dark" ? "☀" : "🌙";
      });
    }
  });
})();
