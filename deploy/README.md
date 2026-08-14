# Despliegue de producción de VibeBloom

Esta carpeta inicia la preparación para cumplir los requisitos de nube y seguridad del PI. No sustituye el `docker-compose.yml` de desarrollo.

## Responsabilidades del equipo

### Trabajo que puede preparar Codex en el repositorio

- Imágenes de producción para Laravel, FastAPI y Flask.
- Proxy de entrada y redes Docker sin puertos internos publicados.
- Health checks, CORS restringido, cookies seguras y ejecución sin debug.
- Plantillas de variables y validación para impedir secretos débiles.
- Guía de despliegue, pruebas de carga, monitoreo y evidencia.
- Ajustes de la app móvil para consumir el dominio HTTPS definitivo.

### Decisiones y accesos que debe proporcionar el propietario del proyecto

- Proveedor de nube y presupuesto autorizado.
- Cuenta de nube y método de facturación.
- Dominio o subdominios que se utilizarán.
- Autorización para crear servidores, base administrada, firewall y balanceador.
- Correo o canal que recibirá las alertas.

Nunca se deben compartir contraseñas o llaves en el chat ni subirlas a Git. Se cargarán directamente en el gestor de secretos del proveedor.

## Arquitectura objetivo

1. Un balanceador público recibe HTTPS.
2. Dos servidores privados ejecutan el mismo Compose de producción.
3. Laravel, FastAPI y Flask no publican puertos directamente.
4. MySQL acepta conexiones solo por red privada.
5. Redis comparte sesiones y caché.
6. Las fotografías deben migrarse a almacenamiento de objetos antes de activar dos servidores.

## Preparación local

1. Copiar `.env.production.example` como `.env.production`.
2. Sustituir todos los valores de ejemplo.
3. Generar `APP_KEY` con `php artisan key:generate --show`.
4. Generar `JWT_SECRET` y `FLASK_SECRET` distintos, aleatorios y de al menos 32 caracteres.
5. Validar Compose:

   ```bash
   docker compose --env-file .env.production -f docker-compose.production.yml config
   ```

6. Construir las imágenes:

   ```bash
   docker compose --env-file .env.production -f docker-compose.production.yml build
   ```

El despliegue completo no debe ejecutarse contra la base de producción hasta contar con respaldo y una prueba de migraciones.

## Evidencia que se reunirá

- Diagrama de red pública y privada.
- Capturas de dos instancias saludables detrás del balanceador.
- Reglas del firewall.
- Certificado HTTPS y redirección desde HTTP.
- Dashboard y alertas de monitoreo.
- Prueba de continuidad apagando una instancia.
- Prueba JWT y evidencia de bcrypt sin exponer datos sensibles.
- Matriz de pruebas de la app en varios dispositivos.

## Pendientes antes de dos instancias

- Adaptar fotos y archivos a almacenamiento de objetos compatible con S3.
- Probar Laravel y FastAPI contra una copia de MySQL administrado.
- Automatizar migraciones como una tarea única; no ejecutarlas simultáneamente en cada réplica.
- Añadir CI/CD y escaneo de dependencias/imágenes.
