# VibeBloom

VibeBloom es una plataforma para descubrir, recomendar y administrar lugares. El proyecto integra una aplicación web en Laravel, una aplicación móvil Flutter, una API propia en FastAPI, un panel administrativo Flask y una base de datos MySQL compartida.

La aplicación móvil no es un WebView ni una copia directa de la web: usa ubicación, mapa interactivo, selección de fotografías, almacenamiento seguro del JWT y navegación diseñada para teléfono.

## Estado actual

Fecha de revisión: **2 de agosto de 2026**
Entrega prevista: **4 de agosto de 2026**

- Web, API y panel administrativo desplegados en DigitalOcean.
- MySQL y Redis aislados en un servidor privado mediante VPC y firewall.
- Balanceador de carga y HTTPS activos.
- APK Android de producción compilada e instalada en emulador.
- Aplicación compilada, firmada y ejecutada previamente en un iPhone 16 Pro físico.
- `flutter analyze` termina sin observaciones.
- La regresión funcional completa y la recopilación de evidencias todavía están pendientes.

## Servicios desplegados

| Servicio | Tecnología | URL |
| --- | --- | --- |
| Aplicación web | Laravel | <https://vibe.209-38-116-181.nip.io> |
| API propia | FastAPI | <https://api.209-38-116-181.nip.io> |
| Panel administrativo | Flask | <https://admin.209-38-116-181.nip.io> |
| Comprobación de salud | FastAPI + MySQL | <https://api.209-38-116-181.nip.io/health> |

Las URL públicas apuntan al balanceador `vibebloom-lb-01`, con IP `209.38.116.181`.

## Arquitectura

```text
Aplicación Flutter ─┐
Navegador web ──────┼── HTTPS ── Balanceador DigitalOcean
Panel Flask ────────┘                    │
                                        ▼
                              vibebloom-public-01
                         Caddy + Laravel + FastAPI + Flask
                                        │
                                  VPC privada
                                        │
                                        ▼
                              vibebloom-private-01
                                  MySQL + Redis
```

- El servidor público ejecuta los servicios de aplicación en contenedores.
- El servidor privado acepta MySQL, Redis y administración únicamente por la VPC.
- Laravel, Flutter y Flask trabajan sobre la misma información mediante la API y la base central.
- Caddy administra HTTPS y la renovación automática de certificados.

## Funcionalidades principales

### Aplicación móvil Flutter

- Registro, inicio de sesión y navegación limitada como invitado.
- JWT almacenado mediante almacenamiento seguro del dispositivo.
- Exploración de lugares aprobados y ordenamiento por cercanía.
- Mapa Mapbox con marcadores y búsqueda por tipo de lugar.
- Detalles, galería de fotografías, descripción y reseñas separadas.
- Favoritos sincronizados con la base de datos.
- Comunidad, perfiles de otros usuarios, seguidores y seguidos.
- Notificaciones que se marcan como leídas.
- Propuesta de lugares con validaciones en español, estrellas y hasta tres fotos.
- Dirección y mapa sincronizados en ambos sentidos:
  - escribir una dirección mueve el marcador;
  - tocar el mapa completa la dirección mediante Mapbox;
  - si no existe un resultado exacto, se conserva el punto y se permite escribir manualmente.
- Historial de aprobaciones y sección de lugares aprobados del usuario.
- Perfil, fotografía, cambio de contraseña y controles para mostrar u ocultar contraseña.
- Vibe IA conectada con el servicio central.
- Nombre visible e icono oficial unificados como **VibeBloom** en Android e iOS.

### Web Laravel

- Autenticación, perfiles, comunidad, seguidores y notificaciones.
- Exploración, mapa, favoritos, reseñas y creación de propuestas.
- Mis lugares muestra únicamente lugares aprobados.
- Mis aprobaciones conserva el historial sin duplicar solicitudes.
- Validaciones propias de la aplicación y mensajes en español.
- Moderación de contenido mediante OpenAI en los campos correspondientes.

### API FastAPI

- Autenticación JWT y autorización por roles.
- Endpoints compartidos por móvil y panel administrativo.
- Lugares, usuarios, favoritos, seguidores, reseñas, notificaciones y aprobaciones.
- Carga de fotografías con límites y validación.
- Integración de Mapbox y OpenAI sin entregar claves privadas al cliente.
- Esquemas Pydantic y respuestas de error controladas.
- Endpoint de salud que verifica también la base de datos.

### Panel administrativo Flask

- Acceso restringido a moderadores y administradores.
- Jerarquía para impedir que usuarios con menor privilegio modifiquen al administrador.
- Revisión, aprobación o rechazo de solicitudes.
- Edición validada de usuarios y lugares.
- Notificación al creador cuando su lugar es aprobado.
- Dashboard, gráficas y reportes por rango de fechas.
- Exportación de reportes con identidad de VibeBloom.

## Seguridad implementada

- Contraseñas nuevas con bcrypt y factor de costo 12.
- Migración controlada de hashes heredados después de una autenticación válida.
- JWT firmado con `HS256`, secreto fuerte y expiración de 60 minutos.
- Validación de usuario, token y rol en rutas protegidas.
- HTTPS con certificados públicos de Let's Encrypt.
- Cookies seguras y sesiones centralizadas en Redis protegido por contraseña.
- Secretos excluidos del repositorio y almacenados en archivos de entorno restringidos.
- DigitalOcean Cloud Firewall, UFW y Fail2ban activos.
- MySQL `3306` y Redis `6379` bloqueados desde Internet.
- Swagger y ReDoc deshabilitados en producción.
- Monitoreo mediante `do-agent` y alertas de CPU, memoria y disco.

## Estructura del repositorio

```text
VibeBloom/          Aplicación web Laravel
VibeBloomAPI/       API propia FastAPI
VibeBloomAdmin/     Panel administrativo Flask
VibeBloomMobile/    Aplicación Flutter para Android e iOS
deploy/             Proxy, variables de ejemplo y documentación de nube
docker-compose*.yml Definición de contenedores por entorno
```

## Ejecutar el proyecto localmente

### Servicios web

```bash
docker compose up -d --build
docker compose ps
```

No se deben subir archivos `.env`, contraseñas, tokens ni llaves privadas al repositorio.

### Aplicación móvil

```bash
cd VibeBloomMobile
flutter pub get
flutter analyze
flutter test
flutter run
```

La configuración predeterminada consume la API HTTPS de producción. Para Android local:

```bash
flutter run \
  --dart-define=API_URL=http://10.0.2.2:8010 \
  --dart-define=WEB_URL=http://10.0.2.2:8000
```

El APK generado se encuentra en:

```text
VibeBloomMobile/build/app/outputs/flutter-apk/app-release.apk
```

## Cumplimiento de la rúbrica

| Requisito | Estado | Implementación o evidencia |
| --- | --- | --- |
| Utilidad móvil real | Parcial | App nativa con GPS, mapa, fotos, almacenamiento seguro y flujos móviles; falta documentar la demostración. |
| Diseño profesional | Parcial | Identidad VibeBloom, navegación y componentes adaptados; falta revisión visual en varios tamaños. |
| 100 % funcional en varios dispositivos | Parcial | APK Android e instalación física iOS comprobadas; falta matriz completa de regresión. |
| Navegación móvil clara | Cumplido | Navegación inferior, retornos, modo invitado y protección de vistas privadas. |
| Validación de interfaces que escriben en BD | Parcial | Validaciones en móvil, API, Laravel y Flask; falta ejecutar y documentar pruebas negativas de todos los formularios. |
| API propia y base de datos | Cumplido | FastAPI y MySQL compartida, desplegadas y con salud HTTP 200. |
| Web, API y BD en la nube | Cumplido | Infraestructura activa en DigitalOcean. |
| Hashing y cifrado | Cumplido | bcrypt, JWT, TLS, secretos protegidos y sesiones Redis. |
| Dos servidores, público y privado | Cumplido | Droplet público de aplicaciones y Droplet privado de datos comunicados por VPC. |
| Monitoreo | Cumplido | `do-agent` y alertas configuradas; faltan capturas para la exposición. |
| Firewall | Cumplido | Firewall de nube, UFW y Fail2ban; puertos de datos sin acceso público. |
| API protegida con JWT | Cumplido | Bearer token, expiración, validación y roles. |
| Certificado SSL | Cumplido | HTTPS válido y redirección desde HTTP. |
| Balanceador de carga | Cumplido | Balanceador regional activo; actualmente tiene un solo backend. |

El seguimiento detallado se conserva en [REQUISITOS_PI_PENDIENTES.md](REQUISITOS_PI_PENDIENTES.md).

## Pendientes antes de la entrega

1. Ejecutar una matriz de pruebas completa en Android y iPhone con evidencias.
2. Probar todos los formularios que crean o modifican información, incluidos casos inválidos.
3. Revisar visualmente diferentes tamaños, teclado, textos largos, galerías y orientación.
4. Reunir capturas del despliegue, VPC, firewall, monitoreo, alertas, JWT, SSL y balanceador.
5. Preparar el diagrama final y una demostración breve de cada requisito.
6. Configurar y demostrar respaldo automático y restauración de MySQL.
7. Revisar y documentar dependencias con vulnerabilidades conocidas.

## Mejoras posteriores

- Agregar una segunda instancia pública para alta disponibilidad real.
- Migrar fotografías a almacenamiento de objetos compatible con S3.
- Utilizar un dominio propio en lugar de `nip.io`.
- Automatizar respaldos externos cifrados y su retención.
- Incorporar CI/CD y escaneo automático de dependencias e imágenes.
- Restringir el acceso web directo al Droplet para obligar el paso por el balanceador.

## Documentación relacionada

- [Estado detallado de la rúbrica](REQUISITOS_PI_PENDIENTES.md)
- [Aplicación móvil](VibeBloomMobile/README.md)
- [Despliegue y arquitectura](deploy/README.md)
