(function () {
  "use strict";

  /* ── Search ── */
  function initSearch() {
    var input = document.getElementById("apSearch");
    if (!input) return;
    input.addEventListener("input", function () {
      applyFilters();
    });
  }

  /* ── Status filter buttons ── */
  function initFilters() {
    var btns = document.querySelectorAll(".ap-filter-btn");
    btns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        btns.forEach(function (b) { b.classList.remove("active"); });
        btn.classList.add("active");
        applyFilters();
      });
    });
  }

  function applyFilters() {
    var q = (document.getElementById("apSearch") || {}).value;
    q = (q || "").trim().toLowerCase();

    var activeBtn = document.querySelector(".ap-filter-btn.active");
    var statusFilter = activeBtn ? activeBtn.dataset.filter : "all";

    var cards = document.querySelectorAll("#apGrid .ap-card");
    var noRes = document.getElementById("apNoResults");
    var visible = 0;

    cards.forEach(function (card) {
      var matchQ = !q || (card.dataset.q || "").indexOf(q) !== -1;
      var matchS = statusFilter === "all" || card.dataset.status === statusFilter;
      var show = matchQ && matchS;
      card.style.display = show ? "" : "none";
      if (show) visible++;
    });

    if (noRes) noRes.hidden = visible > 0 || cards.length === 0;
  }

  /* ── Reject modal ── */
  var overlay    = null;
  var rejectForm = null;
  var modalName  = null;

  function initModal() {
    overlay    = document.getElementById("apOverlay");
    rejectForm = document.getElementById("apRejectForm");
    modalName  = document.getElementById("apModalName");
    if (!overlay) return;
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") apCloseModal();
    });
  }

  window.apOpenRejectModal = function (btn) {
    if (!overlay || !rejectForm || !modalName) return;
    modalName.textContent = '"' + (btn.dataset.name || "esta solicitud") + '"';
    rejectForm.action = btn.dataset.url || "";
    // Clear previous reason
    var textarea = rejectForm.querySelector("textarea[name='reason']");
    if (textarea) textarea.value = "";
    overlay.setAttribute("aria-hidden", "false");
    overlay.classList.add("open");
    document.body.style.overflow = "hidden";
    if (textarea) setTimeout(function () { textarea.focus(); }, 100);
  };

  window.apCloseModal = function () {
    if (!overlay) return;
    overlay.classList.remove("open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

  /* ── Init ── */
  function init() {
    initSearch();
    initFilters();
    initModal();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
