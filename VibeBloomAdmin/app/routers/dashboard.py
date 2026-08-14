from io import BytesIO
from datetime import date, datetime
from pathlib import Path
from zoneinfo import ZoneInfo

from flask import Blueprint, render_template, session, redirect, url_for, flash, jsonify, request, send_file
from openpyxl import Workbook
from openpyxl.drawing.image import Image as ExcelImage
from openpyxl.utils import get_column_letter
from openpyxl.styles import Alignment, Border, Font, PatternFill, Side
from reportlab.lib import colors
from reportlab.lib.pagesizes import landscape, letter
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import Image as PdfImage, SimpleDocTemplate, Spacer, Table, TableStyle, Paragraph
from app.services.api_client import api_get

dashboard_bp = Blueprint("dashboard", __name__, url_prefix="/dashboard")
LOGO_PATH = Path(__file__).resolve().parent.parent / "static" / "images" / "vibebloom.png"
MEXICO_TIMEZONE = ZoneInfo("America/Mexico_City")


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


def validate_report_filters(filters: dict):
    parsed = {}
    for field, label in (("start_date", "fecha inicial"), ("end_date", "fecha final")):
        value = (filters.get(field) or "").strip()
        if not value:
            parsed[field] = None
            continue
        try:
            parsed[field] = date.fromisoformat(value)
        except ValueError:
            return f"La {label} no tiene un formato válido."

    if parsed["start_date"] and parsed["end_date"] and parsed["start_date"] > parsed["end_date"]:
        return "La fecha inicial no puede ser posterior a la fecha final."
    return None


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
    validation_error = validate_report_filters(filters)
    if validation_error:
        return jsonify({"error": validation_error}), 422

    raw_data, response, error = safe_api_json(
        "/admin/dashboard/report-data",
        default={},
        params=filters,
    )

    if error == "unauthorized":
        session.clear()
        return jsonify({"error": "unauthorized"}), 401

    if error in ("forbidden", "connection_error", "http_error", "invalid_json"):
        api_message = "No fue posible consultar el reporte."
        if response is not None:
            try:
                payload = response.json()
                api_message = payload.get("detail") or payload.get("error") or api_message
            except Exception:
                pass
        return jsonify({"error": api_message}), response.status_code if response is not None else 503

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
    validation_error = validate_report_filters(filters)
    if validation_error:
        return filters, {}, validation_error
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
    generated_at = datetime.now(MEXICO_TIMEZONE).strftime("%d/%m/%Y %H:%M")
    date_range = f"{filters.get('start_date') or 'Inicio'} — {filters.get('end_date') or 'Actualidad'}"
    title_style = ParagraphStyle("ReportTitle", parent=styles["Title"], textColor=colors.HexColor("#172554"), fontSize=19, leading=22, alignment=0)
    subtitle_style = ParagraphStyle("ReportSubtitle", parent=styles["Normal"], textColor=colors.HexColor("#475569"), fontSize=9, leading=13)
    logo = PdfImage(str(LOGO_PATH), width=1.2 * inch, height=0.87 * inch) if LOGO_PATH.exists() else Paragraph("VibeBloom", title_style)
    heading = [
        Paragraph("REPORTE ADMINISTRATIVO", title_style),
        Paragraph(f"{data.get('module_label', 'Reporte')} · {data.get('scope_label', filters.get('scope', 'all'))}", ParagraphStyle("ReportSection", parent=subtitle_style, textColor=colors.HexColor("#F05A47"), fontName="Helvetica-Bold", fontSize=11)),
        Paragraph(f"Periodo: {date_range}<br/>Generado: {generated_at} &nbsp; · &nbsp; Registros: {len(rows)}", subtitle_style),
    ]
    brand_header = Table([[logo, heading]], colWidths=[1.45 * inch, 8.45 * inch])
    brand_header.setStyle(TableStyle([
        ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
        ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#F8FAFC")),
        ("BOX", (0, 0), (-1, -1), 0.8, colors.HexColor("#E2E8F0")),
        ("LINEBELOW", (0, 0), (-1, -1), 3, colors.HexColor("#F05A47")),
        ("LEFTPADDING", (0, 0), (-1, -1), 12),
        ("RIGHTPADDING", (0, 0), (-1, -1), 12),
        ("TOPPADDING", (0, 0), (-1, -1), 10),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
    ]))
    story = [brand_header, Spacer(1, 0.24 * inch)]
    table = Table(table_data, repeatRows=1)
    table.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#172554")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 7),
        ("GRID", (0, 0), (-1, -1), 0.25, colors.HexColor("#CBD5E1")),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
        ("LINEBELOW", (0, 0), (-1, 0), 2, colors.HexColor("#F05A47")),
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
    generated_at = datetime.now(MEXICO_TIMEZONE).strftime("%d/%m/%Y %H:%M")
    date_range = f"{filters.get('start_date') or 'Inicio'} — {filters.get('end_date') or 'Actualidad'}"
    text_start = min(3, last_column)
    worksheet.merge_cells(start_row=1, start_column=text_start, end_row=1, end_column=last_column)
    worksheet.cell(1, text_start, "REPORTE ADMINISTRATIVO")
    worksheet.cell(1, text_start).font = Font(size=19, bold=True, color="172554")
    worksheet.merge_cells(start_row=2, start_column=text_start, end_row=2, end_column=last_column)
    worksheet.cell(2, text_start, f"{data.get('module_label', 'Reporte')} · {data.get('scope_label', filters.get('scope', 'all'))}")
    worksheet.cell(2, text_start).font = Font(size=12, bold=True, color="F05A47")
    worksheet.merge_cells(start_row=3, start_column=text_start, end_row=3, end_column=last_column)
    worksheet.cell(3, text_start, f"Periodo: {date_range}  ·  Generado: {generated_at}  ·  Registros: {len(rows)}")
    worksheet.cell(3, text_start).font = Font(size=9, color="475569")
    for row_number in range(1, 4):
        worksheet.row_dimensions[row_number].height = 24
        for cell in worksheet[row_number]:
            cell.fill = PatternFill(fill_type="solid", fgColor="F8FAFC")
            cell.alignment = Alignment(vertical="center")
    if LOGO_PATH.exists():
        logo = ExcelImage(str(LOGO_PATH))
        logo.width = 105
        logo.height = 77
        worksheet.add_image(logo, "A1")
    worksheet["A4"] = ""
    worksheet.row_dimensions[4].height = 8
    worksheet.append([str(header).replace("_", " ").title() for header in headers])

    for row in rows:
        worksheet.append([row.get(header, "") for header in headers])

    header_row = 5
    thin_border = Border(bottom=Side(style="thin", color="CBD5E1"))
    for cell in worksheet[header_row]:
        cell.font = Font(bold=True, color="FFFFFF")
        cell.fill = PatternFill(fill_type="solid", fgColor="172554")
        cell.alignment = Alignment(horizontal="center", vertical="center")

    for row in worksheet.iter_rows(min_row=header_row + 1):
        for cell in row:
            cell.alignment = Alignment(vertical="top", wrap_text=True)
            cell.border = thin_border

    worksheet.freeze_panes = "A6"
    for column_index in range(1, last_column + 1):
        values = [worksheet.cell(row=row_index, column=column_index).value for row_index in range(header_row, worksheet.max_row + 1)]
        max_length = max((len(str(value or "")) for value in values), default=10)
        worksheet.column_dimensions[get_column_letter(column_index)].width = min(max(max_length + 2, 12), 45)
    worksheet.sheet_view.showGridLines = False
    worksheet.auto_filter.ref = f"A{header_row}:{worksheet.cell(worksheet.max_row, last_column).coordinate}"

    output = BytesIO()
    workbook.save(output)
    output.seek(0)

    return send_file(
        output,
        mimetype="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        as_attachment=True,
        download_name=export_filename(filters, "xlsx"),
    )
