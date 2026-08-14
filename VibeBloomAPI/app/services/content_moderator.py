import json
import re
from urllib import error, request

from app.core.config import settings


def basic_content_violation(text: str) -> str | None:
    value = (text or "").strip()
    if not value:
        return "El contenido es obligatorio."
    if re.search(r"(.)\1{5,}", value, re.IGNORECASE) or re.search(
        r"\b(?:asdf+|qwer+|zxcv+|jaja(?:ja){5,})\b", value, re.IGNORECASE
    ):
        return "Escribe un texto comprensible y relacionado con el lugar."
    if re.search(r"https?://|www\.", value, re.IGNORECASE):
        return "No incluyas enlaces promocionales o externos."
    if re.search(
        r"\b(?:puta|puto|putos|putas|pendej[oa]s?|idiotas?|mierda|cabron(?:a|es|as)?|ching(?:a|ar|ada|ados?))\b",
        value,
        re.IGNORECASE,
    ):
        return "Usa un lenguaje respetuoso y elimina insultos o palabras ofensivas."
    return None


def review_content(text: str, content_type: str) -> tuple[bool, str | None]:
    value = text.strip()
    if not value:
        return False, "El contenido es obligatorio."

    basic_reason = basic_content_violation(value)
    if basic_reason:
        return False, basic_reason
    if not settings.OPENAI_API_KEY:
        return True, None

    moderation_payload = json.dumps({
        "model": "omni-moderation-latest",
        "input": value,
    }).encode()
    moderation_request = request.Request(
        "https://api.openai.com/v1/moderations",
        data=moderation_payload,
        method="POST",
        headers={
            "Authorization": f"Bearer {settings.OPENAI_API_KEY}",
            "Content-Type": "application/json",
        },
    )
    try:
        with request.urlopen(moderation_request, timeout=30) as response:
            moderation_result = json.loads(response.read())
        if bool(moderation_result["results"][0]["flagged"]):
            return False, "El contenido infringe las normas de seguridad de la comunidad."
    except (error.URLError, KeyError, ValueError, json.JSONDecodeError):
        pass

    prompt = (
        "Moderas contenido de una comunidad de lugares. Rechaza amenazas, odio, acoso, "
        "contenido sexual explícito, datos personales, spam, promoción ilegal, insultos graves "
        "y texto incoherente o sin sentido. Acepta texto breve pero comprensible y críticas "
        "respetuosas. Devuelve solo JSON: "
        '{"allowed":true|false,"reason":"explicación breve en español o null"}.'
    )
    payload = json.dumps({
        "model": "gpt-4o-mini",
        "messages": [
            {"role": "system", "content": prompt},
            {"role": "user", "content": f"Tipo: {content_type}\nContenido:\n{value}"},
        ],
        "temperature": 0,
        "max_tokens": 160,
        "response_format": {"type": "json_object"},
    }).encode()
    req = request.Request(
        "https://api.openai.com/v1/chat/completions",
        data=payload,
        method="POST",
        headers={
            "Authorization": f"Bearer {settings.OPENAI_API_KEY}",
            "Content-Type": "application/json",
        },
    )
    try:
        with request.urlopen(req, timeout=30) as response:
            result = json.loads(response.read())
        moderation = json.loads(result["choices"][0]["message"]["content"])
        return bool(moderation.get("allowed")), moderation.get("reason")
    except (error.URLError, KeyError, ValueError, json.JSONDecodeError):
        # Mantiene el mismo comportamiento tolerante de Laravel si OpenAI no responde.
        return True, None
