import 'package:flutter/foundation.dart';

import '../../core/api/api_client.dart';

class AuthController extends ChangeNotifier {
  AuthController() {
    ApiClient.onSessionExpired = _handleExpiredSession;
  }
  bool _authenticated = false;
  bool _loading = false;
  bool _guest = false;
  String? _error;
  Map<String, dynamic>? _user;
  VoidCallback? onSessionExit;

  bool get isAuthenticated => _authenticated;
  bool get isLoading => _loading;
  bool get isGuest => _guest;
  bool get canEnterApp => _authenticated || _guest;
  String? get error => _error;
  Map<String, dynamic>? get user => _user;

  Future<void> _handleExpiredSession() async {
    _authenticated = false;
    _guest = true;
    _user = null;
    _error = null;
    notifyListeners();
    onSessionExit?.call();
  }

  Future<void> restoreSession() async {
    _authenticated =
        (await ApiClient.storage.read(key: 'access_token'))?.isNotEmpty ??
            false;
    if (_authenticated) await refreshProfile();
    notifyListeners();
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
      _user = response.data?['user'] as Map<String, dynamic>?;
      _authenticated = true;
      _guest = false;
      return true;
    } catch (error) {
      _error = ApiClient.messageFrom(error);
      return false;
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<bool> register(String name, String email, String password) async {
    _loading = true;
    _error = null;
    notifyListeners();
    try {
      final response = await ApiClient.dio
          .post<Map<String, dynamic>>('/auth/register', data: {
        'name': name.trim(),
        'email': email.trim(),
        'password': password,
        'role': 'user'
      });
      final token = response.data?['access_token'] as String?;
      final user = response.data?['user'];
      if (token == null || token.isEmpty || user is! Map) {
        return await login(email, password);
      }
      await ApiClient.storage.write(key: 'access_token', value: token);
      _user = Map<String, dynamic>.from(user);
      _authenticated = true;
      _guest = false;
      return true;
    } catch (error) {
      _error = ApiClient.messageFrom(error);
      return false;
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<void> refreshProfile() async {
    try {
      final response =
          await ApiClient.dio.get<Map<String, dynamic>>('/users/me/profile');
      _user = response.data;
      notifyListeners();
    } catch (_) {}
  }

  Future<void> logout() async {
    await ApiClient.storage.delete(key: 'access_token');
    _authenticated = false;
    _guest = true;
    _user = null;
    _error = null;
    notifyListeners();
    onSessionExit?.call();
  }

  void clearError() {
    if (_error == null) return;
    _error = null;
    notifyListeners();
  }

  void continueAsGuest() {
    _guest = true;
    _error = null;
    notifyListeners();
  }

  void requireLogin() {
    _guest = false;
    _error = null;
    notifyListeners();
  }
}
