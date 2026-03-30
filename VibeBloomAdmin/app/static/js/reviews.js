(function () {
    function bindReviewPage() {
        initReviewBodyClamp();
        initReviewSelection();
        initDeleteConfirmation();
    }

    function initReviewBodyClamp() {
        const reviewBodies = document.querySelectorAll(".review-body");

        reviewBodies.forEach(function (body) {
            if (!body) return;

            const td = body.closest("td");
            if (!td) return;

            const oldToggle = td.querySelector(".review-body-toggle");
            if (oldToggle) {
                oldToggle.remove();
            }

            body.classList.remove("is-expanded");
            body.classList.add("review-body--clamp");

            const hasContent = body.textContent && body.textContent.trim().length > 0;
            if (!hasContent) return;

            requestAnimationFrame(function () {
                const needsToggle = body.scrollHeight > body.clientHeight + 5;
                if (!needsToggle) return;

                const toggle = document.createElement("button");
                toggle.type = "button";
                toggle.className = "review-body-toggle";
                toggle.textContent = "Ver más";
                toggle.setAttribute("aria-expanded", "false");

                toggle.addEventListener("click", function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const expanded = body.classList.contains("is-expanded");

                    if (expanded) {
                        body.classList.remove("is-expanded");
                        body.classList.add("review-body--clamp");
                        toggle.textContent = "Ver más";
                        toggle.setAttribute("aria-expanded", "false");
                    } else {
                        body.classList.add("is-expanded");
                        body.classList.remove("review-body--clamp");
                        toggle.textContent = "Ver menos";
                        toggle.setAttribute("aria-expanded", "true");
                    }
                });

                body.insertAdjacentElement("afterend", toggle);
            });
        });
    }

    function initReviewSelection() {
        const rows = document.querySelectorAll(".review-row");
        const selectedReviewText = document.getElementById("selectedReviewText");
        const editButton = document.getElementById("editReviewButton");
        const deleteForm = document.getElementById("deleteReviewForm");
        const deleteButton = document.getElementById("deleteReviewButton");

        if (!rows.length || !selectedReviewText || !editButton || !deleteForm || !deleteButton) {
            return;
        }

        rows.forEach(function (row) {
            row.addEventListener("click", function (event) {
                const clickedToggle = event.target.closest(".review-body-toggle");
                if (clickedToggle) return;

                rows.forEach(function (item) {
                    item.classList.remove("is-selected");
                });

                row.classList.add("is-selected");

                const reviewId = row.dataset.reviewId || "";
                const userName = row.dataset.userName || "Usuario";
                const placeName = row.dataset.placeName || "Lugar";
                const editUrl = row.dataset.editUrl || "";
                const deleteUrl = row.dataset.deleteUrl || "";

                selectedReviewText.textContent = "#" + reviewId + " · " + userName + " · " + placeName;

                if (editUrl.trim() !== "") {
                    editButton.setAttribute("href", editUrl);
                    editButton.classList.remove("is-disabled");
                    editButton.setAttribute("aria-disabled", "false");
                } else {
                    editButton.setAttribute("href", "#");
                    editButton.classList.add("is-disabled");
                    editButton.setAttribute("aria-disabled", "true");
                }

                if (deleteUrl.trim() !== "") {
                    deleteForm.setAttribute("action", deleteUrl);
                    deleteButton.disabled = false;
                } else {
                    deleteForm.setAttribute("action", "");
                    deleteButton.disabled = true;
                }
            });
        });
    }

    function initDeleteConfirmation() {
        const deleteForm = document.getElementById("deleteReviewForm");
        const deleteButton = document.getElementById("deleteReviewButton");

        if (!deleteForm || !deleteButton) return;
        if (deleteForm.dataset.bound === "true") return;

        deleteForm.addEventListener("submit", function (event) {
            const action = deleteForm.getAttribute("action");

            if (!action || deleteButton.disabled) {
                event.preventDefault();
                return;
            }

            const ok = window.confirm("¿Deseas eliminar esta reseña?");
            if (!ok) {
                event.preventDefault();
            }
        });

        deleteForm.dataset.bound = "true";
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", bindReviewPage);
    } else {
        bindReviewPage();
    }
})();