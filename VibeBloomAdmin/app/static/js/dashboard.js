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
    const reportDateError = document.getElementById('reportDateError');

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

    function validateDateRange(filters, showMessage = true) {
        const invalid = Boolean(
            filters.start_date && filters.end_date && filters.start_date > filters.end_date
        );

        if (reportDateError) {
            reportDateError.hidden = !invalid || !showMessage;
            reportDateError.textContent = invalid
                ? 'La fecha inicial no puede ser posterior a la fecha final.'
                : '';
        }

        [reportStartDate, reportEndDate].forEach((field) => {
            if (field) field.classList.toggle('field-control-error', invalid);
        });

        return !invalid;
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
        if (!validateDateRange(filters)) {
            setErrorState('Corrige el rango de fechas para generar el reporte.');
            safeSetText(reportSummary, 'No se generó el reporte: el rango de fechas no es válido.');
            return;
        }
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

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || data.detail || `Error HTTP ${response.status}`);
            }
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
            const message = error instanceof Error ? error.message : 'No fue posible generar el reporte.';
            setErrorState(message);
            safeSetText(
                reportSummary,
                message
            );
        }
    }

    function updateExportLinks() {
        const filters = getCurrentFilters();
        const validRange = validateDateRange(filters, false);
        const queryString = buildQueryString(filters);

        if (exportPdfBtn) {
            exportPdfBtn.href = `/dashboard/export/pdf?${queryString}`;
            exportPdfBtn.classList.toggle('disabled', !validRange);
            exportPdfBtn.setAttribute('aria-disabled', String(!validRange));
        }

        if (exportXlsBtn) {
            exportXlsBtn.href = `/dashboard/export/xls?${queryString}`;
            exportXlsBtn.classList.toggle('disabled', !validRange);
            exportXlsBtn.setAttribute('aria-disabled', String(!validRange));
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
            reportStartDate.addEventListener('change', () => {
                validateDateRange(getCurrentFilters());
                updateExportLinks();
            });
        }

        if (reportEndDate) {
            reportEndDate.addEventListener('change', () => {
                validateDateRange(getCurrentFilters());
                updateExportLinks();
            });
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

    function normalizeSeries(items) {
        return Array.isArray(items)
            ? items.filter((item) => item && item.label !== undefined)
            : [];
    }

    function hasChartData(items) {
        return normalizeSeries(items).some((item) => Number(item.total) > 0);
    }

    function getMonthlyLabels(seriesList) {
        const labels = [];

        seriesList.forEach((series) => {
            normalizeSeries(series.items).forEach((item) => {
                if (!labels.includes(item.label)) {
                    labels.push(item.label);
                }
            });
        });

        return labels;
    }

    function getValuesForLabels(items, labels) {
        const totalsByLabel = new Map(
            normalizeSeries(items).map((item) => [item.label, Number(item.total || 0)])
        );

        return labels.map((label) => totalsByLabel.get(label) || 0);
    }

    function buildEmptyState(canvas, message = 'Sin datos suficientes') {
        const shell = canvas ? canvas.closest('.chart-shell') : null;
        if (!shell || shell.querySelector('.chart-empty-state')) return;

        const empty = document.createElement('div');
        empty.className = 'chart-empty-state';
        empty.textContent = message;
        shell.appendChild(empty);
    }

    function initCharts() {
        const stats = dashboardData.stats || {};
        const placesByType = normalizeSeries(dashboardData.places_by_type);
        const usersMonthly = normalizeSeries(dashboardData.users_monthly);
        const placesMonthly = normalizeSeries(dashboardData.places_monthly);
        const reviewsMonthly = normalizeSeries(dashboardData.reviews_monthly);
        const favoritesMonthly = normalizeSeries(dashboardData.favorites_monthly);

        const statsOverview = [
            { label: 'Usuarios', total: Number(stats.users || 0) },
            { label: 'Lugares', total: Number(stats.places || 0) },
            { label: 'Reseñas', total: Number(stats.reviews || 0) },
            { label: 'Favoritos', total: Number(stats.favorites || 0) },
            { label: 'Aprobaciones', total: Number(stats.approvals || 0) }
        ];

        const statsOverviewCanvas = document.getElementById('statsOverviewChart');
        if (statsOverviewCanvas && hasChartData(statsOverview)) {
            buildChart(statsOverviewCanvas, {
                type: 'bar',
                data: {
                    labels: statsOverview.map((item) => item.label),
                    datasets: [{
                        label: 'Total',
                        data: statsOverview.map((item) => item.total),
                        backgroundColor: ['#1f5fbf', '#087a4b', '#b45309', '#7c3aed', '#be123c'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        } else {
            buildEmptyState(statsOverviewCanvas);
        }

        const placesTypeCanvas = document.getElementById('placesTypeChart');
        if (placesTypeCanvas && hasChartData(placesByType)) {
            buildChart(placesTypeCanvas, {
                type: 'doughnut',
                data: {
                    labels: placesByType.map((item) => item.label),
                    datasets: [{
                        data: placesByType.map((item) => item.total),
                        backgroundColor: [
                            '#1f5fbf',
                            '#087a4b',
                            '#b45309',
                            '#7c3aed',
                            '#be123c',
                            '#0f766e',
                            '#475569',
                            '#8db5f5'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10
                            }
                        }
                    }
                }
            });
        } else {
            buildEmptyState(placesTypeCanvas);
        }

        const monthlySeries = [
            {
                label: 'Usuarios',
                items: usersMonthly,
                borderColor: '#1f5fbf',
                backgroundColor: 'rgba(31, 95, 191, 0.12)'
            },
            {
                label: 'Lugares',
                items: placesMonthly,
                borderColor: '#087a4b',
                backgroundColor: 'rgba(8, 122, 75, 0.12)'
            },
            {
                label: 'Reseñas',
                items: reviewsMonthly,
                borderColor: '#b45309',
                backgroundColor: 'rgba(180, 83, 9, 0.12)'
            },
            {
                label: 'Favoritos',
                items: favoritesMonthly,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.12)'
            }
        ];
        const monthlyLabels = getMonthlyLabels(monthlySeries);
        const activityMonthlyCanvas = document.getElementById('activityMonthlyChart');
        const hasMonthlyData = monthlySeries.some((series) => hasChartData(series.items));

        if (activityMonthlyCanvas && monthlyLabels.length && hasMonthlyData) {
            buildChart(activityMonthlyCanvas, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: monthlySeries.map((series) => ({
                        label: series.label,
                        data: getValuesForLabels(series.items, monthlyLabels),
                        borderColor: series.borderColor,
                        backgroundColor: series.backgroundColor,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            buildEmptyState(activityMonthlyCanvas);
        }
    }

    initReportControls();
    initExportMenu();
    initCharts();
});
