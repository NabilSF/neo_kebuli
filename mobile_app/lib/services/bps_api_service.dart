import 'dart:convert';
import '../models/kbli_model.dart';
import '../models/news_model.dart';
import 'api_client.dart';
import 'kbli_data.dart';

class BpsApiService {
  static final BpsApiService _instance = BpsApiService._internal();
  factory BpsApiService() => _instance;
  BpsApiService._internal();

  final ApiClient _apiClient = ApiClient();
  Map<String, dynamic>? _cachedLocalData;

  Map<String, dynamic> _getLocalData() {
    _cachedLocalData ??= jsonDecode(kbliRawJson) as Map<String, dynamic>;
    return _cachedLocalData!;
  }

  /// Fetch Kategori KBLI (2 digit) from Laravel REST API with offline fallback
  Future<List<KbliCategory>> fetchCategories({int page = 1}) async {
    try {
      final data = await _apiClient.get('/kbli/categories', queryParams: {'page': page.toString()});
      if (data is List) {
        return data.map((e) => KbliCategory.fromJson(e as Map<String, dynamic>)).toList();
      }
    } catch (e) {
      // Fallback to local JSON if backend is offline/unreachable
      final localData = _getLocalData();
      final list = localData['categories'] as List<dynamic>;
      return list.map((e) => KbliCategory.fromJson(e as Map<String, dynamic>)).toList();
    }
    return [];
  }

  /// Fetch sub-kategori/item berdasarkan kode induk (2 digit parent)
  Future<List<KbliItem>> fetchByParent({required String parentCode, int page = 1}) async {
    try {
      final data = await _apiClient.get('/kbli/search', queryParams: {'q': parentCode, 'page': page.toString()});
      if (data is List) {
        return data.map((e) => KbliItem.fromJson(e as Map<String, dynamic>)).toList();
      }
    } catch (e) {
      // Fallback to local JSON
      final localData = _getLocalData();
      final list = localData['items'] as List<dynamic>;
      return list
          .map((e) => KbliItem.fromJson(e as Map<String, dynamic>))
          .where((item) => item.induk == parentCode)
          .toList();
    }
    return [];
  }

  /// Search KBLI berdasarkan keyword atau kode dari Live API Laravel
  Future<List<KbliItem>> search({required String keyword, int page = 1}) async {
    final query = keyword.trim();
    if (query.isEmpty) return [];

    try {
      final data = await _apiClient.get('/kbli/search', queryParams: {
        'q': query,
        'page': page.toString(),
      });
      if (data is List) {
        return data.map((e) => KbliItem.fromJson(e as Map<String, dynamic>)).toList();
      }
    } catch (e) {
      // Fallback to local search
      final localData = _getLocalData();
      final list = localData['items'] as List<dynamic>;
      final qLower = query.toLowerCase();
      return list
          .map((e) => KbliItem.fromJson(e as Map<String, dynamic>))
          .where((item) =>
              item.kode.contains(qLower) ||
              item.judul.toLowerCase().contains(qLower))
          .toList();
    }
    return [];
  }

  /// Fetch detail by kode spesifik (2 atau 5 digit) dari API Laravel
  Future<KbliItem?> fetchDetail({required String kode}) async {
    try {
      final data = await _apiClient.get('/kbli/$kode');
      if (data is Map<String, dynamic>) {
        return KbliItem.fromJson(data);
      }
    } catch (e) {
      // Fallback to local search
      final localData = _getLocalData();
      if (kode.length == 2) {
        final categories = localData['categories'] as List<dynamic>;
        final match = categories.firstWhere(
          (e) => e['kode'].toString() == kode,
          orElse: () => null,
        );
        if (match != null) {
          return KbliItem(
            code: match['kode'].toString(),
            name: match['judul'].toString(),
            parentCode: '',
          );
        }
      } else {
        final items = localData['items'] as List<dynamic>;
        final match = items.firstWhere(
          (e) => e['kode'].toString() == kode,
          orElse: () => null,
        );
        if (match != null) {
          return KbliItem.fromJson(match as Map<String, dynamic>);
        }
      }
    }
    return null;
  }

  /// Fetch News articles list
  Future<List<NewsModel>> fetchNews({int page = 1, String? query, String? category}) async {
    try {
      final params = <String, String>{'page': page.toString()};
      if (query != null && query.isNotEmpty) params['q'] = query;
      if (category != null && category.isNotEmpty) params['category'] = category;

      final data = await _apiClient.get('/news', queryParams: params);
      if (data is List) {
        return data.map((e) => NewsModel.fromJson(e as Map<String, dynamic>)).toList();
      }
    } catch (e) {
      return [];
    }
    return [];
  }

  /// Fetch News article detail by slug
  Future<NewsModel?> fetchNewsDetail({required String slug}) async {
    try {
      final data = await _apiClient.get('/news/$slug');
      if (data is Map<String, dynamic>) {
        return NewsModel.fromJson(data);
      }
    } catch (e) {
      return null;
    }
    return null;
  }
}
