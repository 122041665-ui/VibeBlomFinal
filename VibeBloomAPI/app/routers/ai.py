import json
import re
from urllib import error, request

from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel, Field
from sqlalchemy import func, select
from sqlalchemy.orm import Session

from app.core.config import settings
from app.core.database import get_db
from app.core.security import get_current_user
from app.models.place import Place
from app.models.user import User

router = APIRouter(prefix="/ai", tags=["AI"])
VALID_TYPES = {"RESTAURANTE", "CAFETERIA", "BAR", "ANTRO", "PARQUE", "PLAZA", "MIRADOR", "MUSEO", "OTRO"}


class RecommendationRequest(BaseModel):
    text: str = Field(min_length=2, max_length=1000)
    city: str | None = Field(default=None, max_length=60)
    limit: int = Field(default=12, ge=1, le=50)
    history: list[dict[str, str]] = Field(default_factory=list, max_length=10)


def extract_preferences(text: str, city: str | None, history: list[dict[str, str]] | None = None) -> dict:
    if not settings.OPENAI_API_KEY:
        raise HTTPException(status_code=503, detail="Vibe IA no está configurada en el servidor.")
    prompt = (
        "Extrae preferencias para recomendar lugares. Devuelve SOLO JSON válido con: "
        "type (RESTAURANTE, CAFETERIA, BAR, ANTRO, PARQUE, PLAZA, MIRADOR, MUSEO, OTRO o null), "
        "city, min_price, max_price, rating_min. Interpreta el mensaje actual usando el contexto previo cuando exista. "
        "Contexto: " + json.dumps(history or [], ensure_ascii=False) + ". Mensaje actual: " + text
    )
    if city:
        prompt += f". Ciudad obligatoria: {city}"
    payload = json.dumps({
        "model": "gpt-4o-mini",
        "messages": [
            {"role": "system", "content": "Responde únicamente con JSON válido."},
            {"role": "user", "content": prompt},
        ],
        "temperature": 0.2,
        "max_tokens": 300,
        "response_format": {"type": "json_object"},
    }).encode()
    req = request.Request("https://api.openai.com/v1/chat/completions", data=payload, method="POST", headers={
        "Authorization": f"Bearer {settings.OPENAI_API_KEY}",
        "Content-Type": "application/json",
    })
    try:
        with request.urlopen(req, timeout=45) as response:
            result = json.loads(response.read())
        return json.loads(result["choices"][0]["message"]["content"])
    except (error.URLError, KeyError, ValueError, json.JSONDecodeError):
        raise HTTPException(status_code=502, detail="No fue posible obtener recomendaciones de Vibe IA.")


@router.post("/recommendations")
def recommendations(payload: RecommendationRequest, db: Session = Depends(get_db), current_user: User = Depends(get_current_user)):
    text = payload.text.strip()
    if re.fullmatch(r"(?:hola|holi|buenos d[iías]+|buenas tardes|buenas noches|qu[eé] tal)[!.,?\s]*", text, re.IGNORECASE):
        first_name = (current_user.name or "").strip().split(" ")[0]
        greeting = f"Hola, {first_name}. " if first_name else "Hola. "
        return {
            "transcripcion": text,
            "assistant_reply": greeting + "Soy Vibe, tu asistente para descubrir lugares. Cuéntame qué plan tienes en mente, en qué ciudad y cuánto te gustaría gastar.",
            "preferencias_extraidas": {}, "filtros_aplicados": [], "resultados": [],
        }
    clean_history = [
        {"role": str(item.get("role", "user"))[:12], "text": str(item.get("text", ""))[:500]}
        for item in payload.history[-10:]
        if item.get("text")
    ]
    prefs = extract_preferences(text, payload.city, clean_history)
    query = select(Place)
    place_type = str(prefs.get("type") or "").upper()
    if place_type in VALID_TYPES:
        type_aliases = {
            "CAFETERIA": ["CAFETERIA", "CAFETERÍA", "CAFE", "CAFÉ"],
            "PLAZA": ["PLAZA", "CENTRO COMERCIAL"],
        }
        accepted_types = type_aliases.get(place_type, [place_type])
        query = query.where(func.upper(Place.type).in_(accepted_types))
    chosen_city = str(payload.city or prefs.get("city") or "").strip()
    if chosen_city:
        query = query.where(Place.city.ilike(f"%{chosen_city}%"))
    if isinstance(prefs.get("min_price"), (int, float)):
        query = query.where(Place.price >= prefs["min_price"])
    if isinstance(prefs.get("max_price"), (int, float)):
        query = query.where(Place.price <= prefs["max_price"])
    if isinstance(prefs.get("rating_min"), (int, float)):
        query = query.where(Place.rating >= prefs["rating_min"])
    places = db.execute(query.order_by(Place.rating.desc(), Place.id.desc()).limit(payload.limit)).scalars().all()
    filters = []
    if place_type in VALID_TYPES:
        filters.append(f"Tipo: {place_type.title()}")
    if chosen_city:
        filters.append(f"Ciudad: {chosen_city}")
    if isinstance(prefs.get("max_price"), (int, float)):
        filters.append(f"Hasta ${prefs['max_price']:,.0f} MXN")
    if isinstance(prefs.get("rating_min"), (int, float)):
        filters.append(f"Desde {prefs['rating_min']:g} estrellas")
    first_name = (current_user.name or "").strip().split(" ")[0]
    if places:
        option_word = "opción" if len(places) == 1 else "opciones"
        agreement = "encaja" if len(places) == 1 else "encajan"
        reply = (
            f"{first_name + ', e' if first_name else 'E'}ncontré {len(places)} "
            f"{option_word} que {agreement} con tu plan. "
            "Te las dejo abajo para que puedas compararlas; si quieres, dime algo más específico y afinamos la búsqueda."
        )
    else:
        reply = "No encontré una coincidencia exacta, pero podemos intentarlo juntos. Prueba cambiando la ciudad, el presupuesto o el tipo de lugar."
    return {
        "transcripcion": payload.text.strip(),
        "assistant_reply": reply,
        "preferencias_extraidas": prefs,
        "filtros_aplicados": filters,
        "resultados": [{
            "id": place.id, "name": place.name, "city": place.city, "type": place.type,
            "description": place.description, "price": place.price, "rating": place.rating,
            "lat": place.lat, "lng": place.lng, "address": place.address,
            "photo_url": place.photo_url,
        } for place in places],
    }
