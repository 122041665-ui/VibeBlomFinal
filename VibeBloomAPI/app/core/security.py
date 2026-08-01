from datetime import datetime, timedelta, timezone
from hmac import compare_digest
from hashlib import sha256
from typing import Optional
from jose import jwt, JWTError
from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from sqlalchemy.orm import Session
from sqlalchemy import select
import bcrypt

from app.core.config import settings
from app.core.database import get_db
from app.models.user import User

security = HTTPBearer()
_BCRYPT_ROUNDS = 12


def hash_password(password: str) -> str:
    """Genera un hash bcrypt con un salt aleatorio incluido en el resultado."""
    return bcrypt.hashpw(
        password.encode("utf-8"),
        bcrypt.gensalt(rounds=_BCRYPT_ROUNDS),
    ).decode("utf-8")


def password_needs_rehash(hashed_password: str) -> bool:
    """Indica si un hash heredado SHA-256 debe migrarse a bcrypt."""
    if not hashed_password:
        return False

    value = str(hashed_password).strip()
    return not value.startswith(("$2y$", "$2b$"))


def verify_password(password: str, hashed_password: str) -> bool:
    if not hashed_password:
        return False

    hashed_password = str(hashed_password).strip()

    # Soporte para Laravel bcrypt ($2y$...)
    if hashed_password.startswith("$2y$") or hashed_password.startswith("$2b$"):
        bcrypt_hash = hashed_password
        if bcrypt_hash.startswith("$2y$"):
            bcrypt_hash = "$2b$" + bcrypt_hash[4:]

        try:
            return bcrypt.checkpw(
                password.encode("utf-8"),
                bcrypt_hash.encode("utf-8")
            )
        except Exception:
            return False

    # Compatibilidad temporal con cuentas creadas antes de usar bcrypt.
    # Se compara sin filtrar información por tiempo y se migra al iniciar sesión.
    if len(hashed_password) == 64:
        legacy_hash = sha256(password.encode("utf-8")).hexdigest()
        return compare_digest(legacy_hash, hashed_password.lower())

    return False


def create_access_token(data: dict, expires_delta: Optional[timedelta] = None) -> str:
    to_encode = data.copy()

    expire = datetime.now(timezone.utc) + (
        expires_delta or timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    )

    to_encode.update({"exp": expire})
    return jwt.encode(to_encode, settings.SECRET_KEY, algorithm=settings.ALGORITHM)


def decode_access_token(token: str) -> dict:
    try:
        payload = jwt.decode(
            token,
            settings.SECRET_KEY,
            algorithms=[settings.ALGORITHM]
        )
        return payload
    except JWTError:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido o expirado"
        )


def get_current_user(
    credentials: HTTPAuthorizationCredentials = Depends(security),
    db: Session = Depends(get_db)
) -> User:
    token = credentials.credentials
    payload = decode_access_token(token)

    user_id = payload.get("sub")
    if not user_id:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido"
        )

    user = db.execute(
        select(User).where(User.id == int(user_id))
    ).scalar_one_or_none()

    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Usuario no encontrado"
        )

    return user


def require_admin(current_user: User = Depends(get_current_user)) -> User:
    if current_user.role != "admin":
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado"
        )
    return current_user


def require_staff(current_user: User = Depends(get_current_user)) -> User:
    if current_user.role not in ["admin", "moderator"]:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado"
        )
    return current_user
