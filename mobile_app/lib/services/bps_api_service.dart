import 'dart:convert';
import '../models/kbli_model.dart';
import 'kbli_data.dart';

class KbliStats {
  final int totalItems;
  final int countCatA;
  final int countCatC;
  final int totalProducts;

  KbliStats({
    required this.totalItems,
    required this.countCatA,
    required this.countCatC,
    required this.totalProducts,
  });
}

class BpsApiService {
  static final BpsApiService _instance = BpsApiService._internal();
  factory BpsApiService() => _instance;
  BpsApiService._internal();

  Map<String, dynamic>? _cachedData;
  List<KbliCategory>? _cachedCategoryModels;
  List<KbliItem>? _cachedItemModels;

  Map<String, dynamic> _getData() {
    _cachedData ??= jsonDecode(kbliRawJson) as Map<String, dynamic>;
    return _cachedData!;
  }

  List<KbliItem> _getAllItems() {
    if (_cachedItemModels == null) {
      final data = _getData();
      final list = data['items'] as List<dynamic>;
      _cachedItemModels = list.map((e) => KbliItem.fromJson(e as Map<String, dynamic>)).toList();
    }
    return _cachedItemModels!;
  }

  List<KbliCategory> _getAllCategories() {
    if (_cachedCategoryModels == null) {
      final data = _getData();
      final list = data['categories'] as List<dynamic>;
      _cachedCategoryModels = list.map((e) => KbliCategory.fromJson(e as Map<String, dynamic>)).toList();
    }
    return _cachedCategoryModels!;
  }

  // Fetch Kategori KBLI Golongan Pokok (2 digit) - Category C exclusively
  Future<List<KbliCategory>> fetchCategories({String? categoryFilter, int page = 1}) async {
    try {
      final categories = _getAllCategories();
      // Exclude Category A, focus exclusively on Category C (Golongan Pokok 10 - 33)
      return categories.where((cat) => cat.kategori.toUpperCase() == 'C').toList();
    } catch (e) {
      return [];
    }
  }

  // Fetch sub-kategori/item berdasarkan kode induk (2 digit parent)
  Future<List<KbliItem>> fetchByParent({required String parentCode, int page = 1}) async {
    try {
      final items = _getAllItems();
      return items.where((item) => item.induk == parentCode).toList();
    } catch (e) {
      return [];
    }
  }

  // Search KBLI berdasarkan keyword/kode & optional category filter
  Future<List<KbliItem>> search({required String keyword, String? categoryFilter, int page = 1}) async {
    try {
      final items = _getAllItems();
      final query = keyword.toLowerCase().trim();

      Iterable<KbliItem> filtered = items;
      if (categoryFilter != null && categoryFilter.isNotEmpty && categoryFilter != 'ALL') {
        filtered = filtered.where((item) => item.kategori.toUpperCase() == categoryFilter.toUpperCase());
      }

      if (query.isEmpty) {
        return categoryFilter != null && categoryFilter != 'ALL' ? filtered.toList() : [];
      }

      return filtered.where((item) {
        final matchesCode = item.kode.toLowerCase().contains(query);
        final matchesTitle = item.judul.toLowerCase().contains(query);
        final matchesDesc = item.deskripsi?.toLowerCase().contains(query) ?? false;
        final matchesProducts = item.produk.any((p) => p.toLowerCase().contains(query));
        final matchesProses = item.proses.any((p) => p.toLowerCase().contains(query));
        final matchesKu = item.kegiatanUtama.any((k) => k.toLowerCase().contains(query));

        return matchesCode || matchesTitle || matchesDesc || matchesProducts || matchesProses || matchesKu;
      }).toList();
    } catch (e) {
      return [];
    }
  }

  // Fetch detail by kode spesifik (2 atau 5 digit)
  Future<KbliItem?> fetchDetail({required String kode}) async {
    try {
      if (kode.length == 2) {
        final categories = _getAllCategories();
        final match = categories.firstWhere(
          (e) => e.kode == kode,
          orElse: () => KbliCategory(kode: kode, judul: 'Golongan $kode', kategori: ['01', '02', '03'].contains(kode) ? 'A' : 'C'),
        );
        return KbliItem(
          kode: match.kode,
          judul: match.judul,
          induk: '',
          kategori: match.kategori,
          deskripsi: match.deskripsi,
        );
      } else {
        final items = _getAllItems();
        return items.firstWhere((e) => e.kode == kode, orElse: () => items.firstWhere((e) => e.kode.endsWith(kode), orElse: () => items.first));
      }
    } catch (e) {
      // ignore
    }
    return null;
  }

  Map<String, String>? _cachedHierarchyMap;

  Map<String, String> _getHierarchyMap() {
    if (_cachedHierarchyMap == null) {
      final data = _getData();
      final map = data['hierarchy'] as Map<String, dynamic>? ?? {};
      _cachedHierarchyMap = map.map((key, value) => MapEntry(key, value.toString()));
    }
    return _cachedHierarchyMap!;
  }

  List<Map<String, String>> getHierarchyChain(String code) {
    final hierarchy = _getHierarchyMap();
    final List<Map<String, String>> chain = [];

    // 1. Level 1: Kategori
    final String catLetter = ['01', '02', '03'].contains(code.substring(0, code.length >= 2 ? 2 : code.length)) ? 'A' : 'C';
    final String catTitle = hierarchy[catLetter] ?? (catLetter == 'A' ? 'PERTANIAN, KEHUTANAN DAN PERIKANAN' : 'INDUSTRI PENGOLAHAN');
    chain.add({
      'level': 'Kategori (1 Digit)',
      'kode': catLetter,
      'judul': catTitle,
    });

    // 2. Level 2: Golongan Pokok (2 Digit)
    if (code.length >= 2) {
      final g2 = code.substring(0, 2);
      final t2 = hierarchy[g2] ?? 'Golongan Pokok $g2';
      chain.add({
        'level': 'Golongan Pokok (2 Digit)',
        'kode': g2,
        'judul': t2,
      });
    }

    // 3. Level 3: Golongan (3 Digit)
    if (code.length >= 3) {
      final g3 = code.substring(0, 3);
      final t3 = hierarchy[g3] ?? 'Golongan $g3';
      chain.add({
        'level': 'Golongan (3 Digit)',
        'kode': g3,
        'judul': t3,
      });
    }

    // 4. Level 4: Subgolongan (4 Digit)
    if (code.length >= 4) {
      final g4 = code.substring(0, 4);
      final t4 = hierarchy[g4] ?? 'Subgolongan $g4';
      chain.add({
        'level': 'Subgolongan (4 Digit)',
        'kode': g4,
        'judul': t4,
      });
    }

    // 5. Level 5: Kelompok / Komoditas (5 Digit)
    if (code.length >= 5) {
      final g5 = code.substring(0, 5);
      final t5 = hierarchy[g5] ?? 'Kelompok $g5';
      chain.add({
        'level': 'Kelompok (5 Digit)',
        'kode': g5,
        'judul': t5,
      });
    }

    return chain;
  }

  // Fetch Summary Stats
  Future<KbliStats> fetchStats() async {
    final items = _getAllItems();
    int catA = 0;
    int catC = 0;
    int totalProds = 0;

    for (var item in items) {
      if (item.kategori == 'A') {
        catA++;
      } else {
        catC++;
      }
      totalProds += item.produk.length;
    }

    return KbliStats(
      totalItems: items.length,
      countCatA: catA,
      countCatC: catC,
      totalProducts: totalProds,
    );
  }
}

