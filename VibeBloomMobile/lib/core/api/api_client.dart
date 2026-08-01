import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../config/app_config.dart';

class ApiClient {
  ApiClient._();

  static const storage = FlutterSecureStorage();
  static final Dio dio = Dio(
    BaseOptions(
      baseUrl: AppConfig.apiUrl,
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 15),
      headers: const {'Accept': 'application/json'},
    ),
  )..interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await storage.read(key: 'access_token');
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
      ),
    );

  static String messageFrom(Object error) {
    if (error is DioException) {
      final data = error.response?.data;
      if (data is Map && data['detail'] is String) return data['detail'] as String;
      if (error.type == DioExceptionType.connectionError) {
        return 'No fue posible conectar con VibeBloom. Revisa tu conexión.';
      }
    }
    return 'Ocurrió un problema. Intenta nuevamente.';
  }
}
