class KbliCategory {
  final String code;
  final String name;
  final String? description;
  final int sectionsCount;

  KbliCategory({
    required this.code,
    required this.name,
    this.description,
    this.sectionsCount = 0,
  });

  // Backward compatibility getters
  String get kode => code;
  String get judul => name;
  String? get deskripsi => description;

  factory KbliCategory.fromJson(Map<String, dynamic> json) {
    return KbliCategory(
      code: json['code']?.toString() ?? json['kode']?.toString() ?? json['id']?.toString() ?? '',
      name: json['name']?.toString() ?? json['judul']?.toString() ?? json['title']?.toString() ?? '',
      description: json['description']?.toString() ?? json['deskripsi']?.toString(),
      sectionsCount: json['sections_count'] is int
          ? json['sections_count']
          : int.tryParse(json['sections_count']?.toString() ?? '0') ?? 0,
    );
  }
}

class KbliItem {
  final String code;
  final String name;
  final String? description;
  final String? level;
  final String? parentCode;
  final String? parentName;

  KbliItem({
    required this.code,
    required this.name,
    this.description,
    this.level,
    this.parentCode,
    this.parentName,
  });

  // Backward compatibility getters
  String get kode => code;
  String get judul => name;
  String? get deskripsi => description;
  String? get induk => parentCode;

  factory KbliItem.fromJson(Map<String, dynamic> json) {
    String? pCode;
    String? pName;

    if (json['parent'] != null) {
      if (json['parent'] is Map) {
        pCode = json['parent']['code']?.toString();
        pName = json['parent']['name']?.toString();
      } else if (json['parent'] is String) {
        pCode = json['parent'].toString();
      }
    } else if (json['induk'] != null) {
      pCode = json['induk'].toString();
    }

    return KbliItem(
      code: json['code']?.toString() ?? json['kode']?.toString() ?? json['id']?.toString() ?? '',
      name: json['name']?.toString() ?? json['judul']?.toString() ?? json['title']?.toString() ?? '',
      description: json['description']?.toString() ?? json['deskripsi']?.toString(),
      level: json['level']?.toString() ?? (strlen(json['code']?.toString() ?? '') <= 2 ? 'category' : 'item'),
      parentCode: pCode,
      parentName: pName,
    );
  }

  static int strlen(String str) => str.length;
}

class BpsApiResponse {
  final int returnCode;
  final dynamic data;

  BpsApiResponse({required this.returnCode, required this.data});

  factory BpsApiResponse.fromJson(Map<String, dynamic> json) {
    return BpsApiResponse(
      returnCode: json['success'] == true ? 200 : (json['status'] ?? json['returncode'] ?? 0),
      data: json['data'] ?? json['results'],
    );
  }
}
