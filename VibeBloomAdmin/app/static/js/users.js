(function () {
  "use strict";

  /* ── Search ── */
  function initSearch() {
    var input = document.getElementById("usSearch");
    if (!input) return;

    input.addEventListener("input", function () {
      var q = input.value.trim().toLowerCase();
      filterRows(q);
      filterCards(q);
    });
  }

  function filterRows(q) {
    var rows = document.querySelectorAll("#usTBody .us-row");
    var noResults = document.getElementById("usNoResults");
    var visible = 0;

    rows.forEach(function (row) {
      var match = !q || (row.dataset.q || "").indexOf(q) !== -1;
      row.style.display = match ? "" : "none";
      if (match) visible++;
    });

    if (noResults) noResults.hidden = visible > 0 || rows.length === 0;
  }

  function filterCards(q) {
    var cards = document.querySelectorAll("#usMobileCards .us-mcard");
    var noResults = document.getElementById("usNoResultsMobile");
    var visible = 0;

    cards.forEach(function (card) {
      var match = !q || (card.dataset.q || "").indexOf(q) !== -1;
      card.style.display = match ? "" : "none";
      if (match) visible++;
    });

    if (noResults) noResults.hidden = visible > 0 || cards.length === 0;
  }

  /* ── Delete modal ── */
  var overlay    = null;
  var deleteForm = null;
  var modalUser  = null;

  function initModal() {
    overlay    = document.getElementById("usOverlay");
    deleteForm = document.getElementById("usDeleteForm");
    modalUser  = document.getElementById("usModalUser");

    if (!overlay) return;

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") usCloseModal();
    });
  }

  window.usOpenModal = function (btn) {
    if (!overlay || !deleteForm || !modalUser) return;

    var url      = btn.dataset.url  || "";
    var userName = btn.dataset.user || "este usuario";

    modalUser.textContent = userName;
    deleteForm.action = url;

    overlay.setAttribute("aria-hidden", "false");
    overlay.classList.add("open");
    document.body.style.overflow = "hidden";
  };

  window.usCloseModal = function () {
    if (!overlay) return;
    overlay.classList.remove("open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

  /* ── Init ── */
  function init() {
    initSearch();
    initModal();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
