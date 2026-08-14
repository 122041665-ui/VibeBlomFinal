#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
    echo "Uso: $0 DIRECTORIO_SEGURO" >&2
    exit 1
fi

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
output_dir="$1"
source_env="${repo_root}/VibeBloom/.env"

if [[ ! -f "${source_env}" ]]; then
    echo "No existe ${source_env}; faltan las claves locales de la aplicación." >&2
    exit 1
fi

read_env() {
    local key="$1"
    awk -v wanted="${key}" '
        index($0, wanted "=") == 1 {
            value = substr($0, length(wanted) + 2)
            gsub(/^['\''"]|['\''"]$/, "", value)
            print value
            exit
        }
    ' "${source_env}"
}

mapbox_token="$(read_env MAPBOX_TOKEN)"
openai_key="$(read_env OPENAI_API_KEY)"
app_key="$(read_env APP_KEY)"

if [[ -z "${mapbox_token}" || -z "${openai_key}" ]]; then
    echo "MAPBOX_TOKEN y OPENAI_API_KEY deben existir en VibeBloom/.env." >&2
    exit 1
fi

if [[ -z "${app_key}" ]]; then
    app_key="base64:$(openssl rand -base64 32)"
fi

mysql_root_password="$(openssl rand -hex 32)"
mysql_password="$(openssl rand -hex 32)"
redis_password="$(openssl rand -hex 32)"
jwt_secret="$(openssl rand -hex 48)"
flask_secret="$(openssl rand -hex 48)"

public_ip="24.144.88.231"
web_domain="vibe.${public_ip//./-}.nip.io"
api_domain="api.${public_ip//./-}.nip.io"
admin_domain="admin.${public_ip//./-}.nip.io"

umask 077
mkdir -p "${output_dir}"

cat > "${output_dir}/private.env" <<EOF
PRIVATE_VPC_IP=10.124.0.4
MYSQL_ROOT_PASSWORD=${mysql_root_password}
MYSQL_DATABASE=vibebloom
MYSQL_USERNAME=vibebloom_app
MYSQL_PASSWORD=${mysql_password}
REDIS_PASSWORD=${redis_password}
EOF

cat > "${output_dir}/public.env" <<EOF
WEB_DOMAIN=${web_domain}
API_DOMAIN=${api_domain}
ADMIN_DOMAIN=${admin_domain}
WEB_PUBLIC_URL=https://${web_domain}
API_PUBLIC_URL=https://${api_domain}
SESSION_SECURE_COOKIE=true
APP_KEY=${app_key}
JWT_SECRET=${jwt_secret}
FLASK_SECRET=${flask_secret}
MYSQL_HOST=10.124.0.4
MYSQL_PORT=3306
MYSQL_DATABASE=vibebloom
MYSQL_USERNAME=vibebloom_app
MYSQL_PASSWORD=${mysql_password}
REDIS_HOST=10.124.0.4
REDIS_PORT=6379
REDIS_PASSWORD=${redis_password}
OPENAI_API_KEY=${openai_key}
MAPBOX_TOKEN=${mapbox_token}
CORS_ORIGINS=https://${web_domain},https://${admin_domain}
EOF

chmod 600 "${output_dir}/private.env" "${output_dir}/public.env"
echo "Archivos de entorno creados con permisos 600 en ${output_dir}."
