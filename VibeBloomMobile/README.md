# VibeBloom Mobile

Aplicación Flutter independiente de Laravel, Flask y FastAPI. Su utilidad móvil principal es ordenar lugares aprobados por cercanía usando la ubicación del dispositivo, guardar favoritos y, en las siguientes fases, ofrecer mapa e indicaciones en tiempo real.

## Funcionalidades actuales

- Inicio de sesión contra `POST /auth/login`.
- JWT guardado en el almacenamiento seguro del teléfono.
- Validación de correo y contraseña.
- Consulta de lugares desde `GET /places`.
- Orden por cercanía cuando el usuario concede ubicación.
- Registro e inicio de sesión conectados a la API/MySQL.
- Lugares, detalle, fotografías, estrellas y reseñas.
- Favoritos sincronizados mediante `POST /favorites/toggle`.
- Comunidad conectada con los usuarios de la plataforma.
- Notificaciones y seguimiento de solicitudes de aprobación.
- Creación de solicitudes con tipo, estado, estrellas y hasta tres fotografías.
- Perfil, fotografía de hasta 5 MB y cambio seguro de contraseña.
- Navegación inferior para lugares, favoritos, comunidad, actividad y perfil.
- Manejo de carga, errores, reintentos y sesión.
- Pruebas unitarias iniciales de validación.

## Preparar el equipo

1. Instalar Flutter estable: <https://docs.flutter.dev/get-started/install/macos/mobile-android>
2. Instalar Android Studio, Android SDK y crear un emulador.
3. Ejecutar `flutter doctor -v` y resolver los elementos marcados.
4. Desde esta carpeta ejecutar:

```bash
flutter create --platforms=android,ios .
flutter pub get
flutter test
flutter run
```

La configuración predeterminada utiliza la API de producción protegida por HTTPS y accesible mediante el balanceador:

- API: `https://api.209-38-116-181.nip.io`
- Web: `https://vibe.209-38-116-181.nip.io`

Para desarrollo local se pueden sobrescribir las direcciones sin modificar código:

```bash
flutter run \
  --dart-define=API_URL=http://10.0.2.2:8010 \
  --dart-define=WEB_URL=http://10.0.2.2:8000
```

`10.0.2.2` permite que el emulador Android acceda al `localhost` de la Mac. Una compilación destinada a un teléfono físico o a la presentación debe conservar las URL HTTPS predeterminadas.

## Permisos pendientes después de generar Android/iOS

Android, en `android/app/src/main/AndroidManifest.xml`:

```xml
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
<uses-permission android:name="android.permission.INTERNET" />
```

iOS, en `ios/Runner/Info.plist`:

```xml
<key>NSLocationWhenInUseUsageDescription</key>
<string>VibeBloom utiliza tu ubicación para mostrarte lugares cercanos.</string>
```

Todas las operaciones de negocio usan la misma API central y, por lo tanto, la misma base MySQL que Laravel y Flask.
