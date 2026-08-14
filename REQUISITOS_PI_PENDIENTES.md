# Estado de requisitos mínimos del PI

Fecha de actualización: 2026-08-02
Entrega prevista: 2026-08-04

## Resumen

- Requisitos cumplidos y comprobados: **10**.
- Requisitos parcialmente cumplidos o pendientes de evidencia final: **4**.
- Pendientes técnicos prioritarios antes de la entrega: ejecutar la matriz de pruebas en dispositivos y reunir capturas/evidencias.

## 1. Programación móvil

### 🟡 Utilidad real y no solamente copia de la aplicación web

**Estado: parcialmente cumplido; falta evidencia final.**

La aplicación móvil incorpora flujos adaptados al teléfono: exploración de lugares, mapa y ubicación, registro de lugares con fotografías, favoritos, comunidad, perfiles, seguidores, notificaciones y Vibe IA. No se limita a mostrar la web dentro de un WebView.

**Pendiente:** documentar en la presentación qué ventajas móviles aporta cada flujo y realizar una demostración completa usando la API desplegada.

### 🟡 Diseño y estética profesional

**Estado: implementado; falta revisión visual final en dispositivos reales.**

La app conserva la identidad visual de VibeBloom, sus logotipos, colores, tarjetas, iconos de tipos de lugares y navegación adaptada a móvil. El nombre visible y los iconos de inicio de Android e iOS ya fueron unificados como **VibeBloom**.

**Pendiente:** revisar desbordamientos, textos cortados, teclado, orientación y diferentes densidades/tamaños de pantalla.

### 🟡 Aplicación móvil 100 % funcional en varios dispositivos

**Estado: pendiente de validación completa.**

El proyecto pasa `flutter analyze` sin observaciones y la APK Android de producción se compila e instala correctamente. La app también fue compilada, firmada, instalada y ejecutada correctamente en un iPhone 16 Pro físico con iOS 26.5.2. La ejecución más reciente de `flutter test` quedó detenida durante la carga, por lo que la suite debe repetirse antes de usarla como evidencia. Todavía falta completar una matriz formal de todos los flujos en distintos tamaños/dispositivos.

**Pendiente:** probar inicio de sesión, registro, navegación como invitado, mapa, detalles, galería de varias fotos, perfiles, comunidad, seguidores, favoritos, solicitudes, notificaciones, configuración y Vibe IA. Registrar dispositivo, versión de Android, resultado y evidencia.

### ✅ Navegación móvil clara y entendible

**Estado: implementado.**

Existe navegación inferior y rutas adaptadas a móvil, acceso de invitado limitado a exploración, controles de regreso y protección de vistas que requieren autenticación.

**Evidencia pendiente de recopilar:** capturas del flujo invitado, autenticado y retorno entre pantallas.

### 🟡 Validación obligatoria en todas las interfaces que escriben en la BD

**Estado: implementada en los flujos principales; pendiente regresión completa.**

La API utiliza esquemas Pydantic, restricciones de longitud y tipo, validaciones de autenticación y autorización, mensajes en español, validación de fotografías, contraseñas, lugares y reseñas. También existe moderación de contenido mediante OpenAI donde corresponde.

**Pendiente:** ejecutar pruebas negativas de todos los formularios que crean o modifican usuarios, lugares, fotos, reseñas, memorias, favoritos, seguidores, notificaciones y aprobaciones. Confirmar que ningún error borre datos o fotografías ya seleccionadas.

### ✅ API propia y base de datos

**Estado: cumplido y desplegado.**

La app consume una API propia desarrollada con FastAPI. La API usa la base MySQL de VibeBloom alojada en el servidor privado y comparte los datos con el panel Flask. El endpoint de salud comprueba también la conexión a la BD.

- API pública: <https://api.209-38-116-181.nip.io>
- Salud: <https://api.209-38-116-181.nip.io/health>
- Respuesta comprobada: HTTP `200`, `database: ok`.

La configuración móvil predeterminada ya utiliza `https://api.209-38-116-181.nip.io` y permite sobrescribirla mediante `--dart-define` para desarrollo local. Se generó correctamente `app-release.apk` con un tamaño aproximado de 56 MB.

El formulario móvil para proponer lugares sincroniza dirección y mapa: la escritura manual geocodifica y mueve el marcador, mientras que tocar el mapa realiza geocodificación inversa y completa el campo de dirección mediante Mapbox.

### ✅ Web, API y BD alojados en la nube

**Estado: cumplido.**

La solución está desplegada en DigitalOcean, región `sfo3`:

- Web Laravel: <https://vibe.209-38-116-181.nip.io>
- API FastAPI: <https://api.209-38-116-181.nip.io>
- Panel Flask: <https://admin.209-38-116-181.nip.io>
- MySQL y Redis: servidor de datos accesible internamente por la VPC.

Los tres servicios públicos respondieron HTTP `200` a través del balanceador.

## 2. Seguridad informática

### ✅ Métodos de hashing y cifrado

**Estado: cumplido.**

- Contraseñas nuevas protegidas con bcrypt y factor de costo 12.
- Compatibilidad temporal y migración automática de hashes SHA-256 heredados después de un inicio de sesión válido.
- Comparaciones seguras para credenciales heredadas.
- JWT firmado con `HS256` y secreto aleatorio de producción.
- Comunicación pública cifrada mediante TLS/HTTPS.
- Cookies de sesión Laravel marcadas como seguras y sesiones almacenadas en Redis con contraseña.
- Secretos de producción almacenados en archivos de entorno con permisos restrictivos y excluidos de Git.

**Evidencia recomendada:** fragmentos de `security.py`, variables ocultas y captura del certificado; nunca mostrar contraseñas, tokens ni claves completas.

### ✅ Al menos dos servidores, uno público y otro privado

**Estado: cumplido con aislamiento por VPC y firewall.**

- Servidor público `vibebloom-public-01`: ejecuta Caddy, Laravel, FastAPI y Flask Admin.
- Servidor de datos `vibebloom-private-01`: ejecuta MySQL y Redis.
- Comunicación interna mediante VPC: público `10.124.0.3` y privado `10.124.0.4`.
- El servidor de datos conserva una IP pública para actualizaciones salientes, pero el acceso entrante público está bloqueado por firewall de DigitalOcean y UFW. Su administración se realiza mediante el servidor público como bastión.

### ✅ Monitoreo del sistema

**Estado: cumplido.**

El agente oficial `do-agent` está instalado y activo en ambos servidores. DigitalOcean recopila CPU, memoria, disco y carga. Se configuraron alertas por correo:

- CPU superior a 85 % durante 5 minutos.
- Memoria superior a 85 % durante 5 minutos.
- Disco superior a 80 % durante 5 minutos.

**Evidencia pendiente de recopilar:** capturas de Insights, servicios activos y políticas de alertas.

### ✅ Aplicación y monitoreo de firewall

**Estado: cumplido y comprobado externamente.**

- Servidor público: expone SSH `22`, HTTP `80` y HTTPS `443`.
- MySQL `3306` y Redis `6379`: bloqueados desde Internet.
- Servidor privado: SSH, HTTP, HTTPS, MySQL y Redis bloqueados desde su IP pública.
- Por VPC, únicamente el servidor público puede alcanzar SSH, MySQL y Redis del servidor privado.
- UFW, DigitalOcean Cloud Firewall y Fail2ban están activos.

**Mejora posterior recomendada:** limitar los puertos web del Droplet público para aceptar tráfico de aplicación únicamente desde el balanceador, conservando SSH restringido.

### ✅ Protección de la API con JWT

**Estado: cumplido y probado.**

- Autenticación mediante `Authorization: Bearer <token>`.
- Tokens firmados con `HS256` y secreto fuerte de producción.
- Expiración configurada a 60 minutos.
- Validación de firma, expiración, usuario y rol.
- Dependencias separadas para usuario autenticado, personal moderador y administrador.
- Una solicitud sin token a una ruta privada respondió HTTP `401`.
- Documentación Swagger y ReDoc deshabilitada en producción.

### ✅ Certificado SSL para la plataforma

**Estado: cumplido.**

Caddy gestiona certificados públicos de Let’s Encrypt y su renovación automática. HTTP redirige a HTTPS con código `308`. Los certificados de Web, API y Admin fueron comprobados con resultado TLS válido (`ssl_verify_result=0`).

### ✅ Uso de balanceador de carga

**Estado: cumplido.**

Se creó el balanceador regional HTTP `vibebloom-lb-01` en DigitalOcean:

- IP pública: `209.38.116.181`.
- Región: `sfo3`.
- Backend: `vibebloom-public-01`, estado activo.
- Salud: TCP puerto `80`.
- Reglas: HTTP `80 → 80` y HTTPS `443 → 443` con TLS passthrough.
- Los tres dominios resolvieron contra la IP del balanceador y respondieron HTTP `200`.

**Alcance actual:** existe un solo backend público, por lo que se demuestra el uso del balanceador, pero no alta disponibilidad ante la caída completa de ese Droplet. Una segunda instancia sería una mejora futura y aumentaría el costo.

## Pendientes prioritarios antes de la entrega

1. Completar la regresión funcional en el iPhone físico y en el emulador Android contra la infraestructura en la nube.
2. Corregir o aislar el bloqueo de `flutter test` y conservar la salida exitosa como evidencia.
3. Registrar la matriz de pruebas por dispositivo, tamaño y flujo, incluyendo capturas y resultados.
4. Hacer regresión de todas las interfaces que escriben en la BD y documentar resultados.
5. Reunir capturas de arquitectura, VPC, firewall, monitoreo, alertas, JWT, certificados y balanceador.
6. Preparar un diagrama de arquitectura y una demostración breve para la exposición.
7. Configurar un respaldo automático de MySQL y una prueba documentada de restauración.
8. Revisar dependencias reportadas con vulnerabilidades por `npm audit` antes de afirmar que no existen riesgos conocidos.

## Mejoras posteriores a la entrega

- Agregar una segunda instancia pública para alta disponibilidad real.
- Usar un dominio propio en lugar de `nip.io`.
- Incorporar respaldo externo cifrado y política de retención.
- Restringir el acceso directo al Droplet público para obligar el paso por el balanceador.
- Mantener el balanceador solo mientras sea necesario: cuesta aproximadamente USD 0.018 por hora / USD 12 al mes.
- Actualizar el plugin `speech_to_text` cuando publique compatibilidad con el nuevo sistema integrado de Kotlin de Flutter; actualmente solo genera una advertencia y no impide compilar.

Este documento refleja comprobaciones realizadas al 2026-08-02. Cada punto pendiente debe convertirse en una prueba reproducible o evidencia antes de la exposición final.
