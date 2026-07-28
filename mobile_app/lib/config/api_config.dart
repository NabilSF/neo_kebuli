import 'dart:io';
import 'package:flutter/foundation.dart';

enum ApiEnvironment { development, production }

class ApiConfig {
  static ApiEnvironment environment = ApiEnvironment.development;
  static String? _customBaseUrl;

  /// Get active Base URL dynamically based on Platform and Environment
  static String get baseUrl {
    if (_customBaseUrl != null && _customBaseUrl!.isNotEmpty) {
      return _customBaseUrl!;
    }

    if (environment == ApiEnvironment.production) {
      return 'https://kbli.bps.go.id/api/v1'; // Production domain
    }

    // Development Environment Auto-Detection
    if (kIsWeb) {
      return 'http://127.0.0.1:8000/api/v1';
    }

    try {
      if (Platform.isAndroid) {
        // Android Emulator loopback IP to host machine
        return 'http://10.0.2.2:8000/api/v1';
      } else if (Platform.isIOS || Platform.isMacOS || Platform.isWindows || Platform.isLinux) {
        return 'http://127.0.0.1:8000/api/v1';
      }
    } catch (_) {
      // Fallback
    }

    return 'http://127.0.0.1:8000/api/v1';
  }

  /// Override Base URL at runtime if needed
  static void setBaseUrl(String url) {
    _customBaseUrl = url;
  }

  /// Timeout Duration
  static const Duration timeout = Duration(seconds: 10);

  /// Enable Development Console Logging
  static bool get enableLogging => kDebugMode;
}
