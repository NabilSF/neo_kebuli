import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import 'auth_service.dart';

class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final dynamic errors;

  ApiException(this.message, {this.statusCode, this.errors});

  @override
  String toString() => 'ApiException: $message (statusCode: $statusCode)';
}

class ApiClient {
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;
  ApiClient._internal();

  Map<String, String> get _headers {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    final token = AuthService().token;
    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  /// Generic GET request
  Future<dynamic> get(String endpoint, {Map<String, String>? queryParams}) async {
    final Stopwatch stopwatch = Stopwatch()..start();
    final Uri uri = Uri.parse('${ApiConfig.baseUrl}$endpoint').replace(queryParameters: queryParams);

    if (ApiConfig.enableLogging) {
      debugPrint('[API REQUEST] GET -> $uri');
    }

    try {
      final response = await http
          .get(uri, headers: _headers)
          .timeout(ApiConfig.timeout);

      stopwatch.stop();
      if (ApiConfig.enableLogging) {
        debugPrint('[API RESPONSE] Status ${response.statusCode} | Duration ${stopwatch.elapsedMilliseconds}ms -> $endpoint');
      }

      return _processResponse(response);
    } on SocketException catch (e) {
      stopwatch.stop();
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] SocketException: $e');
      throw ApiException('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
    } on TimeoutException {
      stopwatch.stop();
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] TimeoutException on $endpoint');
      throw ApiException('Koneksi ke server tenggat waktu (timeout). Silakan coba lagi.');
    } on FormatException catch (e) {
      stopwatch.stop();
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] FormatException: $e');
      throw ApiException('Respon dari server tidak valid (Format Error).');
    } catch (e) {
      stopwatch.stop();
      if (e is ApiException) rethrow;
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] Unhandled Exception: $e');
      throw ApiException('Terjadi kesalahan jaringan yang tidak terduga.');
    }
  }

  /// Generic POST request
  Future<dynamic> post(String endpoint, {Map<String, dynamic>? body}) async {
    final Stopwatch stopwatch = Stopwatch()..start();
    final Uri uri = Uri.parse('${ApiConfig.baseUrl}$endpoint');

    if (ApiConfig.enableLogging) {
      debugPrint('[API REQUEST] POST -> $uri');
    }

    try {
      final response = await http
          .post(uri, headers: _headers, body: body != null ? jsonEncode(body) : null)
          .timeout(ApiConfig.timeout);

      stopwatch.stop();
      if (ApiConfig.enableLogging) {
        debugPrint('[API RESPONSE] Status ${response.statusCode} | Duration ${stopwatch.elapsedMilliseconds}ms -> $endpoint');
      }

      return _processResponse(response);
    } on SocketException catch (e) {
      stopwatch.stop();
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] SocketException: $e');
      throw ApiException('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
    } on TimeoutException {
      stopwatch.stop();
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] TimeoutException on $endpoint');
      throw ApiException('Koneksi ke server tenggat waktu (timeout). Silakan coba lagi.');
    } on FormatException catch (e) {
      stopwatch.stop();
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] FormatException: $e');
      throw ApiException('Respon dari server tidak valid (Format Error).');
    } catch (e) {
      stopwatch.stop();
      if (e is ApiException) rethrow;
      if (ApiConfig.enableLogging) debugPrint('[API ERROR] Unhandled Exception: $e');
      throw ApiException('Terjadi kesalahan jaringan yang tidak terduga.');
    }
  }

  /// Map HTTP Status Codes & Error Bodies cleanly
  dynamic _processResponse(http.Response response) {
    dynamic body;
    try {
      body = jsonDecode(response.body);
    } catch (_) {
      throw ApiException('Respon server bukan merupakan data JSON yang sah.', statusCode: response.statusCode);
    }

    final int status = response.statusCode;

    // HTTP 200 - 299 Success
    if (status >= 200 && status < 300) {
      if (body is Map<String, dynamic> && body.containsKey('data')) {
        return body['data'];
      }
      return body;
    }

    // 401 Unauthorized handling
    if (status == 401) {
      AuthService().clearSession();
    }

    final String message = (body is Map && body['message'] != null)
        ? body['message'].toString()
        : _getDefaultMessageForStatus(status);

    final dynamic errors = (body is Map && body['errors'] != null) ? body['errors'] : null;

    throw ApiException(message, statusCode: status, errors: errors);
  }

  String _getDefaultMessageForStatus(int status) {
    switch (status) {
      case 400:
        return 'Permintaan tidak valid (Bad Request).';
      case 401:
        return 'Sesi Anda telah berakhir. Silakan login kembali (401).';
      case 403:
        return 'Anda tidak memiliki hak akses untuk tindakan ini (403).';
      case 404:
        return 'Data atau halaman tidak ditemukan (404).';
      case 422:
        return 'Data input tidak memenuhi syarat validasi (422).';
      case 429:
        return 'Terlalu banyak permintaan. Silakan tunggu beberapa saat (429).';
      case 500:
        return 'Terjadi kesalahan internal pada server (500).';
      case 503:
        return 'Layanan server sedang dalam pemeliharaan (503).';
      default:
        return 'Terjadi kesalahan pada server ($status).';
    }
  }
}
