/* public/profil/js/main.js
   - Reveal animation (IntersectionObserver)
   - Safe on mobile
   - Respects prefers-reduced-motion
*/

(function () {
  "use strict";

  const prefersReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function revealFallbackAll() {
    document.querySelectorAll(".reveal").forEach(el => el.classList.add("is-visible"));
  }

  function initReveal() {
    const els = Array.from(document.querySelectorAll(".reveal"));
    if (!els.length) return;

    if (prefersReduced) {
      revealFallbackAll();
      return;
    }

    if (!("IntersectionObserver" in window)) {
      revealFallbackAll();
      return;
    }

    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          io.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      threshold: 0.12,
      rootMargin: "0px 0px -8% 0px"
    });

    els.forEach(el => io.observe(el));
  }

  // Optional: close mobile nav when click link (if your navbar uses collapse)
  function initNavbarAutoClose() {
    const nav = document.querySelector(".navbar");
    const collapse = document.querySelector(".navbar-collapse");
    if (!nav || !collapse) return;

    nav.addEventListener("click", (e) => {
      const a = e.target.closest("a");
      if (!a) return;

      // Only close if currently shown
      if (collapse.classList.contains("show")) {
        const bsCollapse = bootstrap.Collapse.getInstance(collapse) || new bootstrap.Collapse(collapse, { toggle: false });
        bsCollapse.hide();
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initReveal();
    initNavbarAutoClose();
  });
})();
