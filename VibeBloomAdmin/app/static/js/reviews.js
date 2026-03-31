(function () {
  "use strict";

  /* ── Search ── */
  function initSearch() {
    var input = document.getElementById("rvSearch");
    if (!input) return;

    input.addEventListener("input", function () {
      var q = input.value.trim().toLowerCase();
      filterRows(q);
      filterCards(q);
    });
  }

  function filterRows(q) {
    var rows = document.querySelectorAll("#rvTBody .rv-row");
    var noResults = document.getElementById("rvNoResults");
    var visible = 0;

    rows.forEach(function (row) {
      var text = row.dataset.q || "";
      var match = !q || text.indexOf(q) !== -1;
      row.style.display = match ? "" : "none";
      if (match) visible++;
    });

    if (noResults) noResults.hidden = visible > 0 || rows.length === 0;
  }

  function filterCards(q) {
    var cards = document.querySelectorAll("#rvMobileCards .rv-mcard");
    var noResults = document.getElementById("rvNoResultsMobile");
    var visible = 0;

    cards.forEach(function (card) {
      var text = card.dataset.q || "";
      var match = !q || text.indexOf(q) !== -1;
      card.style.display = match ? "" : "none";
      if (match) visible++;
    });

    if (noResults) noResults.hidden = visible > 0 || cards.length === 0;
  }

  /* ── Body expand/collapse ── */
  function initBodyClamp() {
    document.querySelectorAll(".rv-body.rv-clamp").forEach(function (el) {
      requestAnimationFrame(function () {
        if (el.scrollHeight <= el.clientHeight + 4) return;

        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "rv-more-btn";
        btn.textContent = "Ver más";

        btn.addEventListener("click", function (e) {
          e.stopPropagation();
          var expanded = el.classList.contains("expanded");
          if (expanded) {
            el.classList.remove("expanded");
            btn.textContent = "Ver más";
          } else {
            el.classList.add("expanded");
            btn.textContent = "Ver menos";
          }
        });

        el.insertAdjacentElement("afterend", btn);
      });
    });
  }

  /* ── Delete modal ── */
  var overlay   = null;
  var deleteForm = null;
  var modalUser = null;

  function initModal() {
    overlay    = document.getElementById("rvOverlay");
    deleteForm = document.getElementById("rvDeleteForm");
    modalUser  = document.getElementById("rvModalUser");

    if (!overlay) return;

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") rvCloseModal();
    });
  }

  window.rvOpenModal = function (btn) {
    if (!overlay || !deleteForm || !modalUser) return;

    var url      = btn.dataset.url  || "";
    var userName = btn.dataset.user || "este usuario";

    modalUser.textContent = userName;
    deleteForm.action = url;

    overlay.setAttribute("aria-hidden", "false");
    overlay.classList.add("open");
    document.body.style.overflow = "hidden";
  };

  window.rvCloseModal = function () {
    if (!overlay) return;
    overlay.classList.remove("open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

  /* ── Init ── */
  function init() {
    initSearch();
    initBodyClamp();
    initModal();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
