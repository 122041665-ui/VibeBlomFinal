# VibeBloom Mobile

Aplicación Flutter independiente de Laravel, Flask y FastAPI. Su utilidad móvil principal es ordenar lugares aprobados por cercanía usando la ubicación del dispositivo, guardar favoritos y, en las siguientes fases, ofrecer mapa e indicaciones en tiempo real.

## Estado actual

- Inicio de sesión contra `POST /auth/login`.
- JWT guardado en el almacenamiento seguro del teléfono.
- Validación de correo y contraseña.
- Consulta de lugares desde `GET /places`.
- Orden por cercanía cuando el usuario concede ubicación.
- Integración con `POST /favorites/toggle`.
- Navegación móvil inferior clara.
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
flutter run --dart-define=API_URL=http://10.0.2.2:8010
```

`10.0.2.2` permite que el emulador Android acceda al `localhost` de la Mac. En un teléfono físico se debe usar la IP local de la Mac, por ejemplo `http://192.168.1.20:8010`. En producción debe usarse exclusivamente una URL HTTPS.

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

## Próximas fases

1. Pantalla de detalle y mapa con marcadores.
2. Indicaciones en tiempo real.
3. Lista completa de favoritos y notificaciones.
4. Perfil y fotografía.
5. Registro con aceptación de términos y privacidad.
6. Pruebas de widgets e integración en varios tamaños.
7. Despliegue HTTPS y configuración de producción.
