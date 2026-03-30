

(function () {
    function bindUsersPage() {
        initUserSelection();
        initDeleteConfirmation();
        initRoleFormProtection();
    }

    function initUserSelection() {
        const rows = document.querySelectorAll('.user-row');
        const selectedUserText = document.getElementById('selectedUserText');
        const editButton = document.getElementById('editUserButton');
        const deleteForm = document.getElementById('deleteUserForm');
        const deleteButton = document.getElementById('deleteUserButton');

        if (!rows.length || !selectedUserText || !editButton || !deleteForm || !deleteButton) {
            return;
        }

        rows.forEach(function (row) {
            row.addEventListener('click', function (event) {
                const ignoreClick = event.target.closest('form, button, select, option, a, input, textarea, label');
                if (ignoreClick) return;

                selectUserRow(row, rows, selectedUserText, editButton, deleteForm, deleteButton);
            });

            row.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                event.preventDefault();
                selectUserRow(row, rows, selectedUserText, editButton, deleteForm, deleteButton);
            });
        });
    }

    function selectUserRow(row, rows, selectedUserText, editButton, deleteForm, deleteButton) {
        rows.forEach(function (item) {
            item.classList.remove('is-selected');
        });

        row.classList.add('is-selected');

        const userId = row.dataset.userId || '';
        const userName = row.dataset.userName || 'Usuario';
        const userEmail = row.dataset.userEmail || 'Sin correo';
        const editUrl = row.dataset.editUrl || '';
        const deleteUrl = row.dataset.deleteUrl || '';

        selectedUserText.textContent = '#' + userId + ' · ' + userName + ' · ' + userEmail;

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
        const deleteForm = document.getElementById('deleteUserForm');
        const deleteButton = document.getElementById('deleteUserButton');

        if (!deleteForm || !deleteButton) return;
        if (deleteForm.dataset.bound === 'true') return;

        deleteForm.addEventListener('submit', function (event) {
            const action = deleteForm.getAttribute('action');

            if (!action || deleteButton.disabled) {
                event.preventDefault();
                return;
            }

            const ok = window.confirm('¿Deseas eliminar este usuario?');
            if (!ok) {
                event.preventDefault();
            }
        });

        deleteForm.dataset.bound = 'true';
    }

    function initRoleFormProtection() {
        const roleForms = document.querySelectorAll('.role-form');

        roleForms.forEach(function (form) {
            if (form.dataset.bound === 'true') return;

            form.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            form.addEventListener('keydown', function (event) {
                event.stopPropagation();
            });

            form.dataset.bound = 'true';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindUsersPage);
    } else {
        bindUsersPage();
    }
})();