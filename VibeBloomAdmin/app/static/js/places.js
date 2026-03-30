

(function () {
    function bindPlacesPage() {
        initPlaceSelection();
        initDeleteConfirmation();
    }

    function initPlaceSelection() {
        const rows = document.querySelectorAll('.place-row');
        const selectedPlaceText = document.getElementById('selectedPlaceText');
        const editButton = document.getElementById('editPlaceButton');
        const deleteForm = document.getElementById('deletePlaceForm');
        const deleteButton = document.getElementById('deletePlaceButton');

        if (!rows.length || !selectedPlaceText || !editButton || !deleteForm || !deleteButton) {
            return;
        }

        rows.forEach(function (row) {
            row.addEventListener('click', function (event) {
                const ignoreClick = event.target.closest('form, button, select, option, a, input, textarea, label');
                if (ignoreClick) return;

                selectPlaceRow(row, rows, selectedPlaceText, editButton, deleteForm, deleteButton);
            });

            row.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                event.preventDefault();
                selectPlaceRow(row, rows, selectedPlaceText, editButton, deleteForm, deleteButton);
            });
        });
    }

    function selectPlaceRow(row, rows, selectedPlaceText, editButton, deleteForm, deleteButton) {
        rows.forEach(function (item) {
            item.classList.remove('is-selected');
        });

        row.classList.add('is-selected');

        const placeId = row.dataset.placeId || '';
        const placeName = row.dataset.placeName || 'Lugar';
        const placeCity = row.dataset.placeCity || 'Sin ciudad';
        const editUrl = row.dataset.editUrl || '';
        const deleteUrl = row.dataset.deleteUrl || '';

        selectedPlaceText.textContent = '#' + placeId + ' · ' + placeName + ' · ' + placeCity;

        if (editUrl.trim() !== '') {
            editButton.setAttribute('href', editUrl);
            editButton.classList.remove('is-disabled');
            editButton.setAttribute('aria-disabled', 'false');
        } else {
            editButton.setAttribute('href', '#');
            editButton.classList.add('is-disabled');
            editButton.setAttribute('aria-disabled', 'true');
        }

        if (deleteUrl.trim() !== '') {
            deleteForm.setAttribute('action', deleteUrl);
            deleteButton.disabled = false;
        } else {
            deleteForm.setAttribute('action', '');
            deleteButton.disabled = true;
        }
    }

    function initDeleteConfirmation() {
        const deleteForm = document.getElementById('deletePlaceForm');
        const deleteButton = document.getElementById('deletePlaceButton');

        if (!deleteForm || !deleteButton) return;
        if (deleteForm.dataset.bound === 'true') return;

        deleteForm.addEventListener('submit', function (event) {
            const action = deleteForm.getAttribute('action');

            if (!action || deleteButton.disabled) {
                event.preventDefault();
                return;
            }

            const ok = window.confirm('¿Deseas eliminar este lugar?');
            if (!ok) {
                event.preventDefault();
            }
        });

        deleteForm.dataset.bound = 'true';
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindPlacesPage);
    } else {
        bindPlacesPage();
    }
})();