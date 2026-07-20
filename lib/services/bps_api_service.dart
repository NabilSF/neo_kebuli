import 'dart:convert';
import '../models/kbli_model.dart';
import 'kbli_data.dart';

class BpsApiService {
  static final BpsApiService _instance = BpsApiService._internal();
  factory BpsApiService() => _instance;
  BpsApiService._internal();

  Map<String, dynamic>? _cachedData;

  Map<String, dynamic> _getData() {
    _cachedData ??= jsonDecode(kbliRawJson) as Map<String, dynamic>;
    return _cachedData!;
  }

  // Fetch Kategori KBLI (2 digit)
  Future<List<KbliCategory>> fetchCategories({int page = 1}) async {
    try {
      final data = _getData();
      final list = data['categories'] as List<dynamic>;
      return list
          .map((e) => KbliCategory.fromJson(e as Map<String, dynamic>))
          .toList();
    } catch (e) {
      return [];
    }
  }

  // Fetch sub-kategori/item berdasarkan kode induk (2 digit parent)
  Future<List<KbliItem>> fetchByParent({required String parentCode, int page = 1}) async {
    try {
      final data = _getData();
      final list = data['items'] as List<dynamic>;
      return list
          .map((e) => KbliItem.fromJson(e as Map<String, dynamic>))
          .where((item) => item.induk == parentCode)
          .toList();
    } catch (e) {
      return [];
    }
  }

  // Search KBLI berdasarkan keyword atau kode
  Future<List<KbliItem>> search({required String keyword, int page = 1}) async {
    try {
      final data = _getData();
      final list = data['items'] as List<dynamic>;
      final query = keyword.toLowerCase().trim();
      if (query.isEmpty) return [];

      return list
          .map((e) => KbliItem.fromJson(e as Map<String, dynamic>))
          .where((item) =>
              item.kode.contains(query) ||
              item.judul.toLowerCase().contains(query))
          .toList();
    } catch (e) {
      return [];
    }
  }

  // Fetch detail by kode spesifik (2 atau 5 digit)
  Future<KbliItem?> fetchDetail({required String kode}) async {
    try {
      final data = _getData();
      if (kode.length == 2) {
        final categories = data['categories'] as List<dynamic>;
        final match = categories.firstWhere(
          (e) => e['kode'].toString() == kode,
          orElse: () => null,
        );
        if (match != null) {
          return KbliItem(
            kode: match['kode'].toString(),
            judul: match['judul'].toString(),
            induk: '',
          );
        }
      } else {
        final items = data['items'] as List<dynamic>;
        final match = items.firstWhere(
          (e) => e['kode'].toString() == kode,
          orElse: () => null,
        );
        if (match != null) {
          return KbliItem.fromJson(match as Map<String, dynamic>);
        }
      }
    } catch (e) {
      // ignore
    }
    return null;
  }
}
