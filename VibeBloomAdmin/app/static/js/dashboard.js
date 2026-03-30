document.addEventListener('DOMContentLoaded', () => {
    const dashboardDataEl = document.getElementById('dashboard-data');
    const dashboardData = dashboardDataEl ? JSON.parse(dashboardDataEl.textContent) : {};

    const reportOptions = dashboardData.report_options || {};

    const reportModule = document.getElementById('reportModule');
    const reportScope = document.getElementById('reportScope');
    const reportStartDate = document.getElementById('reportStartDate');
    const reportEndDate = document.getElementById('reportEndDate');
    const generateReportBtn = document.getElementById('generateReportBtn');
    const reportSummary = document.getElementById('reportSummary');
    const reportThead = document.getElementById('reportThead');
    const reportTbody = document.getElementById('reportTbody');

    const toggleExportMenu = document.getElementById('toggleExportMenu');
    const exportMenu = document.getElementById('exportMenu');
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const exportXlsBtn = document.getElementById('exportXlsBtn');

    function safeSetText(element, text) {
        if (element) {
            element.textContent = text;
        }
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '-';

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getModuleLabel(moduleValue) {
        if (moduleValue === 'places') return 'Lugares';
        if (moduleValue === 'users') return 'Usuarios';
        if (moduleValue === 'reviews') return 'Reseñas';
        if (moduleValue === 'approvals') return 'Aprobaciones';
        return 'Reporte';
    }

    function getStandardHeaders() {
        return ['id', 'nombre', 'accion', 'realizado_por', 'puesto', 'fecha', 'estado'];
    }

    function getCurrentFilters() {
        return {
            module: reportModule ? reportModule.value : 'places',
            scope: reportScope ? reportScope.value : 'all',
            start_date: reportStartDate ? reportStartDate.value : '',
            end_date: reportEndDate ? reportEndDate.value : ''
        };
    }

    function buildQueryString(params) {
        const query = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                query.append(key, value);
            }
        });

        return query.toString();
    }

    function getLabelFromScope(moduleValue, scopeValue) {
        const options = reportOptions[moduleValue] || [];
        const found = options.find((item) => item.value === scopeValue);
        return found ? found.label : scopeValue;
    }

    function loadScopeOptions() {
        if (!reportModule || !reportScope) return;

        const moduleValue = reportModule.value;
        const options = reportOptions[moduleValue] || [];
        const previousValue = reportScope.value;

        reportScope.innerHTML = '';

        if (!options.length) {
            const fallback = document.createElement('option');
            fallback.value = 'all';
            fallback.textContent = 'Todas las acciones';
            reportScope.appendChild(fallback);
            return;
        }

        options.forEach((option) => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.label;
            reportScope.appendChild(opt);
        });

        const hasPreviousValue = options.some((option) => option.value === previousValue);
        reportScope.value = hasPreviousValue ? previousValue : options[0].value;
    }

    function renderTable(headers, rows) {
        if (!reportThead || !reportTbody) return;

        const safeHeaders = Array.isArray(headers) && headers.length
            ? headers
            : getStandardHeaders();

        reportThead.innerHTML = `
            <tr>
                ${safeHeaders.map((header) => `<th>${escapeHtml(header)}</th>`).join('')}
            </tr>
        `;

        if (!Array.isArray(rows) || !rows.length) {
            reportTbody.innerHTML = `
                <tr>
                    <td class="empty-cell" colspan="${safeHeaders.length}">
                        No se encontraron resultados para los filtros seleccionados.
                    </td>
                </tr>
            `;
            return;
        }

        reportTbody.innerHTML = rows.map((row) => `
            <tr>
                ${safeHeaders.map((key) => `<td>${escapeHtml(row?.[key] ?? '-')}</td>`).join('')}
            </tr>
        `).join('');
    }

    function setLoadingState() {
        if (!reportThead || !reportTbody) return;

        const headers = getStandardHeaders();

        reportThead.innerHTML = `
            <tr>
                ${headers.map((header) => `<th>${escapeHtml(header)}</th>`).join('')}
            </tr>
        `;

        reportTbody.innerHTML = `
            <tr>
                <td class="empty-cell" colspan="${headers.length}">
                    Cargando reporte...
                </td>
            </tr>
        `;
    }

    function setErrorState(message = 'No fue posible generar el reporte.') {
        if (!reportThead || !reportTbody) return;

        const headers = getStandardHeaders();

        reportThead.innerHTML = `
            <tr>
                ${headers.map((header) => `<th>${escapeHtml(header)}</th>`).join('')}
            </tr>
        `;

        reportTbody.innerHTML = `
            <tr>
                <td class="empty-cell" colspan="${headers.length}">
                    ${escapeHtml(message)}
                </td>
            </tr>
        `;
    }

    async function generateReport() {
        const filters = getCurrentFilters();
        const fallbackModuleLabel = getModuleLabel(filters.module);
        const fallbackScopeLabel = getLabelFromScope(filters.module, filters.scope);

        const start = filters.start_date || 'sin fecha inicial';
        const end = filters.end_date || 'sin fecha final';

        safeSetText(
            reportSummary,
            `Generando reporte de ${fallbackModuleLabel}...`
        );

        setLoadingState();
        updateExportLinks();

        try {
            const queryString = buildQueryString(filters);
            const response = await fetch(`/dashboard/report-data?${queryString}`, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`Error HTTP ${response.status}`);
            }

            const data = await response.json();
            const rows = Array.isArray(data.rows) ? data.rows : [];
            const headers = Array.isArray(data.headers) && data.headers.length
                ? data.headers
                : getStandardHeaders();

            const moduleLabel = data.module_label || fallbackModuleLabel;
            const scopeLabel = data.scope_label || fallbackScopeLabel;
            const total = Number.isFinite(data.total) ? data.total : rows.length;

            renderTable(headers, rows);

            safeSetText(
                reportSummary,
                `Reporte de ${moduleLabel} · ${scopeLabel} · Rango: ${start} a ${end} · Registros encontrados: ${total}`
            );
        } catch (error) {
            console.error('generateReport error:', error);
            setErrorState('No fue posible generar el reporte.');
            safeSetText(
                reportSummary,
                `No fue posible generar el reporte de ${fallbackModuleLabel}.`
            );
        }
    }

    function updateExportLinks() {
        const filters = getCurrentFilters();
        const queryString = buildQueryString(filters);

        if (exportPdfBtn) {
            exportPdfBtn.href = `/dashboard/export/pdf?${queryString}`;
        }

        if (exportXlsBtn) {
            exportXlsBtn.href = `/dashboard/export/xls?${queryString}`;
        }
    }

    function initReportControls() {
        if (reportModule && reportScope) {
            loadScopeOptions();
            updateExportLinks();

            reportModule.addEventListener('change', () => {
                loadScopeOptions();
                updateExportLinks();
            });

            reportScope.addEventListener('change', updateExportLinks);
        }

        if (reportStartDate) {
            reportStartDate.addEventListener('change', updateExportLinks);
        }

        if (reportEndDate) {
            reportEndDate.addEventListener('change', updateExportLinks);
        }

        if (generateReportBtn) {
            generateReportBtn.addEventListener('click', generateReport);
        }
    }

    function initExportMenu() {
        if (!toggleExportMenu || !exportMenu) return;

        toggleExportMenu.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            exportMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.export-dropdown')) {
                exportMenu.classList.remove('show');
            }
        });
    }

    function buildChart(canvas, config) {
        if (!canvas || typeof Chart === 'undefined') return null;
        return new Chart(canvas, config);
    }

    function initCharts() {
        const placesByType = Array.isArray(dashboardData.places_by_type) ? dashboardData.places_by_type : [];
        const usersMonthly = Array.isArray(dashboardData.users_monthly) ? dashboardData.users_monthly : [];
        const placesMonthly = Array.isArray(dashboardData.places_monthly) ? dashboardData.places_monthly : [];

        const placesTypeCanvas = document.getElementById('placesTypeChart');
        if (placesTypeCanvas && placesByType.length) {
            buildChart(placesTypeCanvas, {
                type: 'doughnut',
                data: {
                    labels: placesByType.map((item) => item.label),
                    datasets: [{
                        data: placesByType.map((item) => item.total),
                        backgroundColor: [
                            '#2563eb',
                            '#3b82f6',
                            '#60a5fa',
                            '#93c5fd',
                            '#bfdbfe',
                            '#1d4ed8',
                            '#1e40af',
                            '#dbeafe'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        const usersMonthlyCanvas = document.getElementById('usersMonthlyChart');
        if (usersMonthlyCanvas && usersMonthly.length) {
            buildChart(usersMonthlyCanvas, {
                type: 'bar',
                data: {
                    labels: usersMonthly.map((item) => item.label),
                    datasets: [{
                        label: 'Usuarios',
                        data: usersMonthly.map((item) => item.total),
                        backgroundColor: '#2563eb',
                        borderRadius: 12
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        const placesMonthlyCanvas = document.getElementById('placesMonthlyChart');
        if (placesMonthlyCanvas && placesMonthly.length) {
            buildChart(placesMonthlyCanvas, {
                type: 'line',
                data: {
                    labels: placesMonthly.map((item) => item.label),
                    datasets: [{
                        label: 'Lugares',
                        data: placesMonthly.map((item) => item.total),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    initReportControls();
    initExportMenu();
    initCharts();
});