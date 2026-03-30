import os
import requests
from flask import session

FASTAPI_URL = os.getenv("FASTAPI_URL", "http://host.docker.internal:8010").rstrip("/")


def get_auth_headers():
    token = session.get("access_token")
    if not token:
        return {}
    return {"Authorization": f"Bearer {token}"}


def api_get(path: str, params=None):
    return requests.get(
        f"{FASTAPI_URL}{path}",
        params=params,
        headers=get_auth_headers(),
        timeout=15
    )


def api_post(path: str, json_data: dict):
    return requests.post(
        f"{FASTAPI_URL}{path}",
        json=json_data,
        headers=get_auth_headers(),
        timeout=15
    )


def api_put(path: str, json_data: dict):
    return requests.put(
        f"{FASTAPI_URL}{path}",
        json=json_data,
        headers=get_auth_headers(),
        timeout=15
    )


def api_patch(path: str, json_data: dict):
    return requests.patch(
        f"{FASTAPI_URL}{path}",
        json=json_data,
        headers=get_auth_headers(),
        timeout=15
    )


def api_delete(path: str):
    return requests.delete(
        f"{FASTAPI_URL}{path}",
        headers=get_auth_headers(),
        timeout=15
    )