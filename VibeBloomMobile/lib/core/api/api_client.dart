import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../config/app_config.dart';

class ApiClient {
  ApiClient._();

  static const storage = FlutterSecureStorage();
  static Future<void> Function()? onSessionExpired;
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
        onError: (error, handler) async {
          final path = error.requestOptions.path;
          final isAuthenticationRequest =
              path.contains('/auth/login') || path.contains('/auth/register');
          if (error.response?.statusCode == 401 && !isAuthenticationRequest) {
            await storage.delete(key: 'access_token');
            await onSessionExpired?.call();
          }
          handler.next(error);
        },
      ),
    );

  static String messageFrom(Object error) {
    if (error is DioException) {
      final data = error.response?.data;
      if (data is Map && data['detail'] is String) {
        return data['detail'] as String;
      }
      if (data is Map && data['detail'] is List) {
        final messages = (data['detail'] as List)
            .whereType<Map>()
            .map((item) => item['msg']?.toString() ?? '')
            .where((message) => message.isNotEmpty)
            .map((message) => message.replaceFirst('Value error, ', ''))
            .toList();
        if (messages.isNotEmpty) return messages.join('\n');
      }
      if (error.type == DioExceptionType.connectionError ||
          error.type == DioExceptionType.connectionTimeout ||
          error.type == DioExceptionType.receiveTimeout) {
        return 'No fue posible conectar con VibeBloom. Revisa tu conexión.';
      }
      if (error.response?.statusCode == 401) {
        return 'Tu sesión venció. Inicia sesión nuevamente para continuar.';
      }
    }
    return 'Ocurrió un problema. Intenta nuevamente.';
  }

  static String? mediaUrl(Object? value) {
    if (value == null || value.toString().trim().isEmpty) return null;
    final source = Uri.tryParse(value.toString());
    final api = Uri.parse(AppConfig.apiUrl);
    if (source == null) return null;
    if (!source.hasScheme) return api.resolve(source.toString()).toString();
    if (source.host == '127.0.0.1' || source.host == 'localhost') {
      return source
          .replace(
              scheme: api.scheme,
              host: api.host,
              port: api.hasPort ? api.port : null)
          .toString();
    }
    return source.toString();
  }
}
