from io import BytesIO
from datetime import datetime

from flask import Blueprint, render_template, session, redirect, url_for, flash, jsonify, request, send_file
from openpyxl import Workbook
from openpyxl.utils import get_column_letter
from openpyxl.styles import Alignment, Border, Font, PatternFill, Side
from reportlab.lib import colors
from reportlab.lib.pagesizes import landscape, letter
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Spacer, Table, TableStyle, Paragraph
from app.services.api_client import api_get

dashboard_bp = Blueprint("dashboard", __name__, url_prefix="/dashboard")


DEFAULT_REPORT_OPTIONS = {
    "places": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "created", "label": "Agregados"},
        {"value": "updated", "label": "Modificaciones"},
        {"value": "deleted", "label": "Eliminados"},
        {"value": "current", "label": "Solo lugares actuales"},
    ],
    "reviews": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "created", "label": "Agregados"},
        {"value": "updated", "label": "Modificaciones"},
        {"value": "deleted", "label": "Eliminados"},
    ],
    "users": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "created", "label": "Agregados"},
        {"value": "updated", "label": "Modificaciones"},
        {"value": "deleted", "label": "Eliminados"},
    ],
    "approvals": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "approved", "label": "Aprobadas"},
        {"value": "rejected", "label": "Rechazadas"},
    ],
}

DEFAULT_REPORT_HEADERS = [
    "id",
    "nombre",
    "accion",
    "realizado_por",
    "puesto",
    "fecha",
    "estado",
]


def safe_api_json(path: str, default=None, params=None):
    if default is None:
        default = {}

    try:
        response = api_get(path, params=params)
    except Exception:
        return default, None, "connection_error"

    if response.status_code == 401:
        return default, response, "unauthorized"

    if response.status_code == 403:
        return default, response, "forbidden"

    if response.status_code != 200:
        return default, response, "http_error"

    try:
        return response.json(), response, None
    except Exception:
        return default, response, "invalid_json"


def build_dashboard_context(raw_data):
    raw_data = raw_data or {}

    stats = raw_data.get("stats") or {}
    recent = raw_data.get("recent") or {}
    current_user = raw_data.get("current_user") or {}
    recent_activity = raw_data.get("recent_activity") or []

    return {
        "stats": {
            "users": stats.get("users", 0),
            "places": stats.get("places", 0),
            "reviews": stats.get("reviews", 0),
            "favorites": stats.get("favorites", 0),
            "approvals": stats.get("approvals", 0),
        },
        "current_user": {
            "id": current_user.get("id"),
            "name": current_user.get("name", "Usuario"),
            "email": current_user.get("email", "Sin correo"),
            "role": current_user.get("role", "staff"),
        },
        "recent": {
            "users": recent.get("users") or [],
            "places": recent.get("places") or [],
            "reviews": recent.get("reviews") or [],
        },
        "recent_activity": recent_activity,
        "places_by_type": raw_data.get("places_by_type") or [],
        "users_monthly": raw_data.get("users_monthly") or [],
        "places_monthly": raw_data.get("places_monthly") or [],
        "reviews_monthly": raw_data.get("reviews_monthly") or [],
        "favorites_monthly": raw_data.get("favorites_monthly") or [],
        "approvals_by_status": raw_data.get("approvals_by_status") or [],
        "report_options": raw_data.get("report_options") or DEFAULT_REPORT_OPTIONS,
    }


def get_report_filters():
    return {
        "module": request.args.get("module", "places"),
        "scope": request.args.get("scope", "all"),
        "start_date": request.args.get("start_date", ""),
        "end_date": request.args.get("end_date", ""),
    }


def get_module_label(module_value: str) -> str:
    mapping = {
        "places": "Lugares",
        "reviews": "Reseñas",
        "users": "Usuarios",
        "approvals": "Aprobaciones",
    }
    return mapping.get(module_value, "Reporte")


def get_scope_label(module_value: str, scope_value: str, report_options: dict) -> str:
    options = report_options.get(module_value) or []
    for item in options:
        if item.get("value") == scope_value:
            return item.get("label", scope_value)
    return scope_value


@dashboard_bp.route("/")
def dashboard():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    raw_data, response, error = safe_api_json("/admin/dashboard", default={})

    if error == "connection_error":
        flash("No se pudo conectar con la API central", "error")
        return render_template("dashboard.html", data=build_dashboard_context({}))

    if error == "unauthorized":
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if error == "forbidden":
        flash("No autorizado", "error")
        return redirect(url_for("auth.login"))

    if error in ("http_error", "invalid_json"):
        flash("No se pudo cargar el dashboard", "error")
        return render_template("dashboard.html", data=build_dashboard_context({}))

    return render_template("dashboard.html", data=build_dashboard_context(raw_data))


@dashboard_bp.route("/report-data")
def report_data():
    if "access_token" not in session:
        return jsonify({"error": "unauthorized"}), 401

    filters = get_report_filters()

    raw_data, response, error = safe_api_json(
        "/admin/dashboard/report-data",
        default={},
        params=filters,
    )

    if error == "unauthorized":
        session.clear()
        return jsonify({"error": "unauthorized"}), 401

    if error in ("forbidden", "connection_error", "http_error", "invalid_json"):
        return jsonify({
            "headers": DEFAULT_REPORT_HEADERS,
            "rows": [],
            "total": 0,
            "module_label": get_module_label(filters["module"]),
            "scope_label": get_scope_label(filters["module"], filters["scope"], DEFAULT_REPORT_OPTIONS),
        }), 200

    report_options = raw_data.get("report_options") or DEFAULT_REPORT_OPTIONS

    return jsonify({
        "headers": raw_data.get("headers") or DEFAULT_REPORT_HEADERS,
        "rows": raw_data.get("rows") or [],
        "total": raw_data.get("total", 0),
        "module_label": raw_data.get("module_label") or get_module_label(filters["module"]),
        "scope_label": raw_data.get("scope_label") or get_scope_label(filters["module"], filters["scope"], report_options),
    })


def fetch_report_for_export():
    filters = get_report_filters()
    data, response, error = safe_api_json(
        "/admin/dashboard/report-data",
        default={},
        params=filters,
    )

    if error == "unauthorized":
        session.clear()

    return filters, data, error


def export_filename(filters: dict, extension: str) -> str:
    module = filters.get("module", "reporte")
    return f"vibebloom_{module}.{extension}"


@dashboard_bp.route("/export/pdf")
def export_pdf():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    filters, data, error = fetch_report_for_export()
    if error:
        flash("No se pudo generar el reporte PDF", "error")
        return redirect(url_for("dashboard.dashboard"))

    headers = data.get("headers") or DEFAULT_REPORT_HEADERS
    rows = data.get("rows") or []
    styles = getSampleStyleSheet()
    cell_style = ParagraphStyle("ReportCell", parent=styles["BodyText"], fontSize=7, leading=9, textColor=colors.HexColor("#334155"))
    header_style = ParagraphStyle("ReportHeader", parent=cell_style, textColor=colors.white, fontName="Helvetica-Bold", alignment=TA_CENTER)
    table_data = [[Paragraph(str(header).replace("_", " ").title(), header_style) for header in headers]]
    table_data.extend([
        [Paragraph(str(row.get(header, "") if row.get(header) is not None else ""), cell_style) for header in headers]
        for row in rows
    ])

    output = BytesIO()
    document = SimpleDocTemplate(
        output,
        pagesize=landscape(letter),
        rightMargin=0.35 * inch,
        leftMargin=0.35 * inch,
        topMargin=0.35 * inch,
        bottomMargin=0.35 * inch,
    )
    generated_at = datetime.now().strftime("%d/%m/%Y %H:%M")
    date_range = f"{filters.get('start_date') or 'Inicio'} — {filters.get('end_date') or 'Actualidad'}"
    story = [
        Paragraph("VIBEBLOOM · REPORTE ADMINISTRATIVO", ParagraphStyle("Brand", parent=styles["Title"], textColor=colors.HexColor("#1D4ED8"), fontSize=20, leading=24)),
        Paragraph(f"{data.get('module_label', 'Reporte')} · {data.get('scope_label', filters.get('scope', 'all'))}", styles["Heading2"]),
        Paragraph(f"Periodo: {date_range} &nbsp;&nbsp;|&nbsp;&nbsp; Generado: {generated_at} &nbsp;&nbsp;|&nbsp;&nbsp; Registros: {len(rows)}", styles["Normal"]),
        Spacer(1, 0.2 * inch),
    ]
    table = Table(table_data, repeatRows=1)
    table.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#2563EB")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 7),
        ("GRID", (0, 0), (-1, -1), 0.25, colors.HexColor("#CBD5E1")),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
        ("LEFTPADDING", (0, 0), (-1, -1), 4),
        ("RIGHTPADDING", (0, 0), (-1, -1), 4),
        ("TOPPADDING", (0, 0), (-1, -1), 6),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
    ]))
    story.append(table)

    def add_page_number(canvas, doc):
        canvas.saveState()
        canvas.setFont("Helvetica", 8)
        canvas.setFillColor(colors.HexColor("#64748B"))
        canvas.drawString(doc.leftMargin, 0.2 * inch, "VibeBloom · Información administrativa")
        canvas.drawRightString(landscape(letter)[0] - doc.rightMargin, 0.2 * inch, f"Página {doc.page}")
        canvas.restoreState()

    document.build(story, onFirstPage=add_page_number, onLaterPages=add_page_number)
    output.seek(0)

    return send_file(
        output,
        mimetype="application/pdf",
        as_attachment=True,
        download_name=export_filename(filters, "pdf"),
    )


@dashboard_bp.route("/export/xls")
def export_xls():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    filters, data, error = fetch_report_for_export()
    if error:
        flash("No se pudo generar el reporte Excel", "error")
        return redirect(url_for("dashboard.dashboard"))

    headers = data.get("headers") or DEFAULT_REPORT_HEADERS
    rows = data.get("rows") or []
    workbook = Workbook()
    worksheet = workbook.active
    worksheet.title = "Reporte VibeBloom"
    last_column = max(1, len(headers))
    worksheet.merge_cells(start_row=1, start_column=1, end_row=1, end_column=last_column)
    worksheet["A1"] = "VIBEBLOOM · REPORTE ADMINISTRATIVO"
    worksheet["A1"].font = Font(size=18, bold=True, color="FFFFFF")
    worksheet["A1"].fill = PatternFill(fill_type="solid", fgColor="1D4ED8")
    worksheet["A1"].alignment = Alignment(horizontal="left", vertical="center")
    worksheet.row_dimensions[1].height = 34
    worksheet.merge_cells(start_row=2, start_column=1, end_row=2, end_column=last_column)
    worksheet["A2"] = f"{data.get('module_label', 'Reporte')} · {data.get('scope_label', filters.get('scope', 'all'))} · {len(rows)} registros · {datetime.now().strftime('%d/%m/%Y %H:%M')}"
    worksheet["A2"].font = Font(italic=True, color="475569")
    worksheet.append([])
    worksheet.append([str(header).replace("_", " ").title() for header in headers])

    for row in rows:
        worksheet.append([row.get(header, "") for header in headers])

    header_row = 4
    thin_border = Border(bottom=Side(style="thin", color="CBD5E1"))
    for cell in worksheet[header_row]:
        cell.font = Font(bold=True, color="FFFFFF")
        cell.fill = PatternFill(fill_type="solid", fgColor="2563EB")
        cell.alignment = Alignment(horizontal="center", vertical="center")

    for row in worksheet.iter_rows(min_row=header_row + 1):
        for cell in row:
            cell.alignment = Alignment(vertical="top", wrap_text=True)
            cell.border = thin_border

    worksheet.freeze_panes = "A5"
    worksheet.auto_filter.ref = f"A{header_row}:{worksheet.cell(header_row, last_column).coordinate}"
    for column_index in range(1, last_column + 1):
        values = [worksheet.cell(row=row_index, column=column_index).value for row_index in range(header_row, worksheet.max_row + 1)]
        max_length = max((len(str(value or "")) for value in values), default=10)
        worksheet.column_dimensions[get_column_letter(column_index)].width = min(max(max_length + 2, 12), 45)

    output = BytesIO()
    workbook.save(output)
    output.seek(0)

    return send_file(
        output,
        mimetype="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        as_attachment=True,
        download_name=export_filename(filters, "xlsx"),
    )
