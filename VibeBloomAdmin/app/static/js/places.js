(function () {
  "use strict";

  function initSearch() {
    var input = document.getElementById("plSearch");
    if (!input) return;
    input.addEventListener("input", function () {
      var q = input.value.trim().toLowerCase();
      filterRows(q);
      filterCards(q);
    });
  }

  function filterRows(q) {
    var rows = document.querySelectorAll("#plTBody .pl-row");
    var noRes = document.getElementById("plNoResults");
    var visible = 0;
    rows.forEach(function (row) {
      var match = !q || (row.dataset.q || "").indexOf(q) !== -1;
      row.style.display = match ? "" : "none";
      if (match) visible++;
    });
    if (noRes) noRes.hidden = visible > 0 || rows.length === 0;
  }

  function filterCards(q) {
    var cards = document.querySelectorAll("#plMobileCards .pl-mcard");
    var noRes = document.getElementById("plNoResultsMobile");
    var visible = 0;
    cards.forEach(function (card) {
      var match = !q || (card.dataset.q || "").indexOf(q) !== -1;
      card.style.display = match ? "" : "none";
      if (match) visible++;
    });
    if (noRes) noRes.hidden = visible > 0 || cards.length === 0;
  }

  var overlay = null;
  var deleteForm = null;
  var modalName = null;

  function initModal() {
    overlay    = document.getElementById("plOverlay");
    deleteForm = document.getElementById("plDeleteForm");
    modalName  = document.getElementById("plModalName");
    if (!overlay) return;
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") plCloseModal();
    });
  }

  window.plOpenModal = function (btn) {
    if (!overlay || !deleteForm || !modalName) return;
    modalName.textContent = btn.dataset.name || "este lugar";
    deleteForm.action = btn.dataset.url || "";
    overlay.setAttribute("aria-hidden", "false");
    overlay.classList.add("open");
    document.body.style.overflow = "hidden";
  };

  window.plCloseModal = function () {
    if (!overlay) return;
    overlay.classList.remove("open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

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
