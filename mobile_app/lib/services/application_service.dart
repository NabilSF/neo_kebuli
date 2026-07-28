import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/application_model.dart';
import 'api_client.dart';
import 'auth_service.dart';

class ApplicationService {
  static final ApplicationService _instance = ApplicationService._internal();
  factory ApplicationService() => _instance;
  ApplicationService._internal();

  final ApiClient _apiClient = ApiClient();

  /// List user applications
  Future<List<ApplicationModel>> getMyApplications() async {
    final response = await _apiClient.get('/applications');
    if (response is List) {
      return response
          .map((item) => ApplicationModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }
    return [];
  }

  /// Get detail of application
  Future<ApplicationModel> getApplicationDetail(int id) async {
    final response = await _apiClient.get('/applications/$id');
    if (response is Map<String, dynamic>) {
      return ApplicationModel.fromJson(response);
    }
    throw ApiException('Format detail pengajuan tidak valid.');
  }

  /// Track application by tracking code (Public, no auth required)
  Future<ApplicationModel> trackApplication(String trackingCode) async {
    final response = await _apiClient.get('/applications/track/${trackingCode.trim()}');
    if (response is Map<String, dynamic>) {
      return ApplicationModel.fromJson(response);
    }
    throw ApiException('Kode tracking tidak ditemukan.');
  }

  /// Submit new application
  Future<ApplicationModel> submitApplication({
    required int kbliItemId,
    required String productName,
    String? productMaterial,
    String? productionProcess,
    String? productDescription,
    String? sakerAddress,
  }) async {
    final response = await _apiClient.post('/applications', body: {
      'kbli_item_id': kbliItemId,
      'product_name': productName,
      if (productMaterial != null) 'product_material': productMaterial,
      if (productionProcess != null) 'production_process': productionProcess,
      if (productDescription != null) 'product_description': productDescription,
      if (sakerAddress != null) 'saker_address': sakerAddress,
    });

    if (response is Map<String, dynamic>) {
      return ApplicationModel.fromJson(response);
    }
    throw ApiException('Format respon pengajuan tidak valid.');
  }

  /// Upload file attachment using MultipartRequest with progress
  Future<MediaModel> uploadAttachment({
    required int applicationId,
    required String filePath,
    required String fileName,
  }) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}/applications/$applicationId/media');
    final request = http.MultipartRequest('POST', uri);

    final token = AuthService().token;
    if (token != null && token.isNotEmpty) {
      request.headers['Authorization'] = 'Bearer $token';
    }
    request.headers['Accept'] = 'application/json';

    final multipartFile = await http.MultipartFile.fromPath('document', filePath, filename: fileName);
    request.files.add(multipartFile);

    final streamedResponse = await request.send().timeout(ApiConfig.timeout);
    final response = await http.Response.fromStream(streamedResponse);

    if (response.statusCode >= 200 && response.statusCode < 300) {
      final body = response.body.isNotEmpty ? (response.body.startsWith('{') ? (http.Response(response.body, response.statusCode).body) : '') : '';
      final json = (body.isNotEmpty) ? (dynamicFromJson(body)) : null;
      if (json is Map<String, dynamic> && json['data'] != null) {
        return MediaModel.fromJson(json['data'] as Map<String, dynamic>);
      }
      throw ApiException('Respon upload tidak valid.');
    } else {
      throw ApiException('Gagal mengunggah dokumen (HTTP ${response.statusCode}).');
    }
  }

  dynamic dynamicFromJson(String body) {
    try {
      return (body.isNotEmpty) ? (jsonDecode(body)) : null;
    } catch (_) {
      return null;
    }
  }
}
