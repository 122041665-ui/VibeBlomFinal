import 'package:flutter/foundation.dart';

import '../../core/api/api_client.dart';

class AuthController extends ChangeNotifier {
  bool _authenticated = false;
  bool _loading = false;
  String? _error;

  bool get isAuthenticated => _authenticated;
  bool get isLoading => _loading;
  String? get error => _error;

  Future<void> restoreSession() async {
    _authenticated = (await ApiClient.storage.read(key: 'access_token'))?.isNotEmpty ?? false;
  }

  Future<bool> login(String email, String password) async {
    _loading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await ApiClient.dio.post<Map<String, dynamic>>(
        '/auth/login',
        data: {'email': email.trim(), 'password': password},
      );
      final token = response.data?['access_token'] as String?;
      if (token == null || token.isEmpty) throw StateError('Token ausente');
      await ApiClient.storage.write(key: 'access_token', value: token);
      _authenticated = true;
      return true;
    } catch (error) {
      _error = ApiClient.messageFrom(error);
      return false;
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<void> logout() async {
    await ApiClient.storage.delete(key: 'access_token');
    _authenticated = false;
    notifyListeners();
  }
}
