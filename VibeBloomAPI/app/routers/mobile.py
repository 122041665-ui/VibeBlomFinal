import json
from urllib import parse, request

from fastapi import APIRouter, HTTPException, Query, Response

from app.core.config import settings

router = APIRouter(prefix="/mobile", tags=["Mobile"])


@router.get("/config")
def mobile_config():
    """Configuración pública necesaria por los clientes oficiales.

    El token de Mapbox es público por diseño; la llave privada de OpenAI nunca
    se entrega al dispositivo y solo se informa si el servicio está disponible.
    """
    return {
        "mapbox_token": settings.MAPBOX_TOKEN,
        "mapbox_style": "mapbox/streets-v12",
        "openai_configured": bool(settings.OPENAI_API_KEY),
    }


@router.get("/mapbox/geocode")
def mapbox_geocode(q: str = Query(min_length=3, max_length=300), state: str | None = Query(default=None, max_length=80), limit: int = Query(default=6, ge=1, le=8)):
    if not settings.MAPBOX_TOKEN:
        raise HTTPException(status_code=503, detail="Mapbox no está configurado.")
    search = f"{q.strip()}, {state}, México" if state else f"{q.strip()}, México"
    params = parse.urlencode({"access_token": settings.MAPBOX_TOKEN, "country": "mx", "language": "es", "autocomplete": "true", "types": "address,poi,place", "limit": limit})
    try:
        with request.urlopen(f"https://api.mapbox.com/geocoding/v5/mapbox.places/{parse.quote(search)}.json?{params}", timeout=20) as result:
            return json.loads(result.read())
    except Exception:
        raise HTTPException(status_code=502, detail="No fue posible consultar direcciones en Mapbox.")


@router.get("/mapbox/tiles/{z}/{x}/{y}")
def mapbox_tile(z: int, x: int, y: int):
    if not settings.MAPBOX_TOKEN:
        raise HTTPException(status_code=503, detail="Mapbox no está configurado.")
    url = f"https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/256/{z}/{x}/{y}@2x?access_token={settings.MAPBOX_TOKEN}"
    try:
        with request.urlopen(url, timeout=20) as result:
            return Response(content=result.read(), media_type="image/png", headers={"Cache-Control": "public, max-age=86400"})
    except Exception:
        raise HTTPException(status_code=502, detail="No fue posible cargar el mapa.")
