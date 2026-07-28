import 'dart:async';
import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/user_model.dart';
import 'api_client.dart';

class AuthService {
  static final AuthService _instance = AuthService._internal();
  factory AuthService() => _instance;
  AuthService._internal();

  final FlutterSecureStorage _storage = const FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );
  final ApiClient _apiClient = ApiClient();

  String? _authToken;
  bool _isInitializing = false;
  final ValueNotifier<UserModel?> currentUserNotifier = ValueNotifier<UserModel?>(null);
  final ValueNotifier<bool> isCheckingSessionNotifier = ValueNotifier<bool>(true);

  String? get token => _authToken;
  bool get isAuthenticated => _authToken != null && _authToken!.trim().isNotEmpty;
  UserModel? get currentUser => currentUserNotifier.value;

  /// Initialize session from secure storage on app launch (Race-condition safe)
  Future<void> initSession() async {
    if (_isInitializing) return;
    _isInitializing = true;
    isCheckingSessionNotifier.value = true;

    try {
      final token = await _storage.read(key: 'auth_token');
      if (token != null && token.trim().isNotEmpty) {
        _authToken = token.trim();
        await refreshCurrentUser();
      } else {
        await clearSession();
      }
    } catch (e) {
      await clearSession();
    } finally {
      isCheckingSessionNotifier.value = false;
      _isInitializing = false;
    }
  }

  /// Refresh current user profile without relogging
  Future<UserModel?> refreshCurrentUser() async {
    if (!isAuthenticated) return null;

    try {
      final response = await _apiClient.get('/auth/me');
      if (response is Map<String, dynamic>) {
        final user = UserModel.fromJson(response);
        currentUserNotifier.value = user;
        return user;
      }
    } catch (e) {
      if (e is ApiException && e.statusCode == 401) {
        await clearSession();
      }
      rethrow;
    }
    return null;
  }

  /// Login user via REST API
  Future<UserModel> login({required String email, required String password}) async {
    final response = await _apiClient.post('/auth/login', body: {
      'email': email,
      'password': password,
    });

    if (response is Map<String, dynamic> && response.containsKey('token')) {
      final token = response['token'].toString().trim();
      final userJson = response['user'] as Map<String, dynamic>;
      final user = UserModel.fromJson(userJson);

      await _storage.write(key: 'auth_token', value: token);
      _authToken = token;
      currentUserNotifier.value = user;

      return user;
    }

    throw ApiException('Format respon login tidak valid.');
  }

  /// Register user via REST API
  Future<UserModel> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    final response = await _apiClient.post('/auth/register', body: {
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': passwordConfirmation,
      if (phone != null && phone.isNotEmpty) 'phone': phone,
    });

    if (response is Map<String, dynamic> && response.containsKey('token')) {
      final token = response['token'].toString().trim();
      final userJson = response['user'] as Map<String, dynamic>;
      final user = UserModel.fromJson(userJson);

      await _storage.write(key: 'auth_token', value: token);
      _authToken = token;
      currentUserNotifier.value = user;

      return user;
    }

    throw ApiException('Format respon registrasi tidak valid.');
  }

  /// Logout current user
  Future<void> logout() async {
    if (isAuthenticated) {
      try {
        await _apiClient.post('/auth/logout', body: {});
      } catch (_) {
        // Continue clearing local session atomically even if network call fails
      }
    }
    await clearSession();
  }

  /// Clear token and reset session atomically
  Future<void> clearSession() async {
    _authToken = null;
    currentUserNotifier.value = null;
    try {
      await _storage.delete(key: 'auth_token');
    } catch (_) {}
  }
}
