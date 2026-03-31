document.addEventListener('DOMContentLoaded', () => {
    /* ── Data ── */
    const dashboardDataEl = document.getElementById('dashboard-data');
    const dashboardData   = dashboardDataEl ? JSON.parse(dashboardDataEl.textContent) : {};
    const reportOptions   = dashboardData.report_options || {};

    /* ── Elements ── */
    const reportModule    = document.getElementById('reportModule');
    const reportScope     = document.getElementById('reportScope');
    const reportStartDate = document.getElementById('reportStartDate');
    const reportEndDate   = document.getElementById('reportEndDate');
    const generateBtn     = document.getElementById('generateReportBtn');
    const printBtn        = document.getElementById('printReportBtn');
    const reportSummary   = document.getElementById('reportSummary');
    const reportThead     = document.getElementById('reportThead');
    const reportTbody     = document.getElementById('reportTbody');
    const toggleExport    = document.getElementById('toggleExportMenu');
    const exportMenu      = document.getElementById('exportMenu');
    const exportPdfBtn    = document.getElementById('exportPdfBtn');
    const exportXlsBtn    = document.getElementById('exportXlsBtn');

    /* State: track whether a report has been generated */
    let lastReportData = null;

    /* ── Helpers ── */
    function escapeHtml(value) {
        if (value === null || value === undefined) return '—';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setText(el, text) { if (el) el.textContent = text; }

    function moduleLabel(v) {
        const map = { places: 'Lugares', users: 'Usuarios', reviews: 'Reseñas', approvals: 'Aprobaciones' };
        return map[v] || 'Reporte';
    }

    function defaultHeaders() {
        return ['id', 'nombre', 'accion', 'realizado_por', 'puesto', 'fecha', 'estado'];
    }

    function getFilters() {
        return {
            module:     reportModule     ? reportModule.value     : 'places',
            scope:      reportScope      ? reportScope.value      : 'all',
            start_date: reportStartDate  ? reportStartDate.value  : '',
            end_date:   reportEndDate    ? reportEndDate.value    : '',
        };
    }

    function buildQS(params) {
        const q = new URLSearchParams();
        Object.entries(params).forEach(([k, v]) => { if (v !== '' && v != null) q.append(k, v); });
        return q.toString();
    }

    function scopeLabel(module, scope) {
        const opts = reportOptions[module] || [];
        const found = opts.find(o => o.value === scope);
        return found ? found.label : scope;
    }

    /* ── Scope options ── */
    function loadScopeOptions() {
        if (!reportModule || !reportScope) return;
        const mod = reportModule.value;
        const opts = reportOptions[mod] || [];
        const prev = reportScope.value;
        reportScope.innerHTML = '';

        if (!opts.length) {
            const o = document.createElement('option');
            o.value = 'all'; o.textContent = 'Todas las acciones';
            reportScope.appendChild(o);
            return;
        }
        opts.forEach(opt => {
            const o = document.createElement('option');
            o.value = opt.value; o.textContent = opt.label;
            reportScope.appendChild(o);
        });
        reportScope.value = opts.some(o => o.value === prev) ? prev : opts[0].value;
    }

    /* ── Table render ── */
    function renderTable(headers, rows) {
        if (!reportThead || !reportTbody) return;

        const hdrs = Array.isArray(headers) && headers.length ? headers : defaultHeaders();

        reportThead.innerHTML = `<tr>${hdrs.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr>`;

        if (!Array.isArray(rows) || !rows.length) {
            reportTbody.innerHTML = `<tr><td class="db-empty-cell" colspan="${hdrs.length}">No se encontraron resultados para los filtros seleccionados.</td></tr>`;
            return;
        }

        reportTbody.innerHTML = rows.map(row =>
            `<tr>${hdrs.map(k => `<td>${escapeHtml(row?.[k] ?? '—')}</td>`).join('')}</tr>`
        ).join('');
    }

    function setLoadingState() {
        const hdrs = defaultHeaders();
        if (reportThead) reportThead.innerHTML = `<tr>${hdrs.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr>`;
        if (reportTbody) reportTbody.innerHTML = `<tr><td class="db-empty-cell" colspan="${hdrs.length}">Cargando reporte…</td></tr>`;
        if (printBtn) printBtn.disabled = true;
        lastReportData = null;
    }

    function setErrorState(msg = 'No fue posible generar el reporte.') {
        const hdrs = defaultHeaders();
        if (reportThead) reportThead.innerHTML = `<tr>${hdrs.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr>`;
        if (reportTbody) reportTbody.innerHTML = `<tr><td class="db-empty-cell" colspan="${hdrs.length}">${escapeHtml(msg)}</td></tr>`;
        if (printBtn) printBtn.disabled = true;
        lastReportData = null;
    }

    /* ── Export links ── */
    function updateExportLinks() {
        const qs = buildQS(getFilters());
        if (exportPdfBtn) exportPdfBtn.href = `/dashboard/export/pdf?${qs}`;
        if (exportXlsBtn) exportXlsBtn.href = `/dashboard/export/xls?${qs}`;
    }

    /* ── Generate report ── */
    async function generateReport() {
        const filters = getFilters();
        const fallbackModule = moduleLabel(filters.module);
        const fallbackScope  = scopeLabel(filters.module, filters.scope);
        const start = filters.start_date || 'sin fecha inicial';
        const end   = filters.end_date   || 'sin fecha final';

        setText(reportSummary, `Generando reporte de ${fallbackModule}…`);
        setLoadingState();
        updateExportLinks();

        try {
            const res = await fetch(`/dashboard/report-data?${buildQS(filters)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data   = await res.json();
            const rows   = Array.isArray(data.rows)    ? data.rows    : [];
            const hdrs   = Array.isArray(data.headers) && data.headers.length ? data.headers : defaultHeaders();
            const mLabel = data.module_label || fallbackModule;
            const sLabel = data.scope_label  || fallbackScope;
            const total  = Number.isFinite(data.total) ? data.total : rows.length;

            renderTable(hdrs, rows);

            const summary = `${mLabel} · ${sLabel} · ${start} → ${end} · ${total} registro${total !== 1 ? 's' : ''}`;
            setText(reportSummary, summary);

            lastReportData = { headers: hdrs, rows, total, summary, mLabel, sLabel, start, end };
            if (printBtn) printBtn.disabled = (rows.length === 0);

        } catch (err) {
            console.error('generateReport:', err);
            setErrorState();
            setText(reportSummary, `No fue posible generar el reporte de ${fallbackModule}.`);
        }
    }

    /* ── Print ── */
    function printReport() {
        if (!lastReportData || !lastReportData.rows.length) return;

        const { headers, rows, summary } = lastReportData;

        const tableRows = rows.map(row =>
            `<tr>${headers.map(k => `<td>${escapeHtml(row?.[k] ?? '—')}</td>`).join('')}</tr>`
        ).join('');

        const now = new Date().toLocaleString('es-MX', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte VibeBloom</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11pt; color: #0f172a; padding: 20px; background: #fff; }
    .print-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 14px; }
    .print-header h1 { font-size: 18pt; font-weight: 800; color: #1d4ed8; }
    .print-header .brand { font-size: 9pt; color: #64748b; margin-top: 4px; }
    .print-meta { font-size: 9pt; color: #64748b; text-align: right; }
    .print-summary { font-size: 10pt; font-weight: 600; color: #334155; background: #f1f7ff; border: 1px solid #dbeafe; border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #1d4ed8; color: #fff; font-size: 9pt; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; padding: 8px 10px; text-align: left; }
    tbody td { padding: 7px 10px; font-size: 10pt; border-bottom: 1px solid #e2e8f0; color: #334155; }
    tbody tr:nth-child(even) td { background: #f8fafc; }
    tbody tr:last-child td { border-bottom: none; }
    .print-footer { margin-top: 16px; font-size: 8pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    @page { size: landscape; margin: 12mm 10mm; }
  </style>
</head>
<body>
  <div class="print-header">
    <div>
      <h1>VibeBloom Admin</h1>
      <div class="brand">Panel Administrativo — Reporte generado</div>
    </div>
    <div class="print-meta">${escapeHtml(now)}</div>
  </div>
  <div class="print-summary">${escapeHtml(summary)}</div>
  <table>
    <thead><tr>${headers.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr></thead>
    <tbody>${tableRows}</tbody>
  </table>
  <div class="print-footer">VibeBloom Admin · Documento generado el ${escapeHtml(now)}</div>
</body>
</html>`;

        const win = window.open('', '_blank', 'width=900,height=650');
        if (!win) { alert('Por favor permite ventanas emergentes para imprimir.'); return; }
        win.document.open();
        const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
        const url  = URL.createObjectURL(blob);
        win.location.href = url;
        win.addEventListener('load', () => {
            win.focus();
            win.print();
            URL.revokeObjectURL(url);
        });
    }

    /* ── Charts ── */
    const CHART_COLORS = ['#2563eb','#4f46e5','#7c3aed','#a855f7','#60a5fa','#1d4ed8','#1e40af','#93c5fd'];

    function showEmpty(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('visible');
    }

    function buildChart(canvasId, config) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') return;
        return new Chart(canvas, config);
    }

    function initCharts() {
        const byType  = Array.isArray(dashboardData.places_by_type) ? dashboardData.places_by_type : [];
        const uMonth  = Array.isArray(dashboardData.users_monthly)  ? dashboardData.users_monthly  : [];
        const pMonth  = Array.isArray(dashboardData.places_monthly) ? dashboardData.places_monthly : [];

        const gridColor  = 'rgba(226,232,240,0.60)';
        const tickColor  = '#94a3b8';
        const fontFamily = 'Arial, sans-serif';

        Chart.defaults.font.family = fontFamily;
        Chart.defaults.color       = tickColor;

        /* Donut: places by type */
        if (byType.length) {
            buildChart('placesTypeChart', {
                type: 'doughnut',
                data: {
                    labels: byType.map(i => i.label),
                    datasets: [{ data: byType.map(i => i.total), backgroundColor: CHART_COLORS, borderWidth: 0, hoverOffset: 6 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 16, font: { size: 12, weight: '700' } } },
                        tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
                    },
                    cutout: '62%'
                }
            });
        } else {
            showEmpty('placesTypeEmpty');
        }

        /* Bar: users monthly */
        if (uMonth.length) {
            buildChart('usersMonthlyChart', {
                type: 'bar',
                data: {
                    labels: uMonth.map(i => i.label),
                    datasets: [{ label: 'Usuarios', data: uMonth.map(i => i.total), backgroundColor: '#2563eb', borderRadius: 10, borderSkipped: false }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' } } },
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { size: 11 }, precision: 0 } }
                    }
                }
            });
        } else {
            showEmpty('usersMonthlyEmpty');
        }

        /* Line: places monthly */
        if (pMonth.length) {
            buildChart('placesMonthlyChart', {
                type: 'line',
                data: {
                    labels: pMonth.map(i => i.label),
                    datasets: [{
                        label: 'Lugares',
                        data: pMonth.map(i => i.total),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.10)',
                        fill: true,
                        tension: 0.38,
                        pointRadius: 4,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' } } },
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { size: 11 }, precision: 0 } }
                    }
                }
            });
        } else {
            showEmpty('placesMonthlyEmpty');
        }
    }

    /* ── Init ── */
    function init() {
        loadScopeOptions();
        updateExportLinks();

        if (reportModule) reportModule.addEventListener('change', () => { loadScopeOptions(); updateExportLinks(); });
        if (reportScope)     reportScope.addEventListener('change', updateExportLinks);
        if (reportStartDate) reportStartDate.addEventListener('change', updateExportLinks);
        if (reportEndDate)   reportEndDate.addEventListener('change', updateExportLinks);
        if (generateBtn)     generateBtn.addEventListener('click', generateReport);
        if (printBtn)        printBtn.addEventListener('click', printReport);

        /* Export menu toggle */
        if (toggleExport && exportMenu) {
            toggleExport.addEventListener('click', e => {
                e.preventDefault(); e.stopPropagation();
                exportMenu.classList.toggle('show');
            });
            document.addEventListener('click', e => {
                if (!e.target.closest('.db-export-wrap')) exportMenu.classList.remove('show');
            });
        }

        initCharts();
    }

    init();
});
