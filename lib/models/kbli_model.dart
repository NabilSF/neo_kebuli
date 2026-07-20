class KbliCategory {
  final String kode;
  final String judul;
  final String? deskripsi;

  KbliCategory({
    required this.kode,
    required this.judul,
    this.deskripsi,
  });

  factory KbliCategory.fromJson(Map<String, dynamic> json) {
    return KbliCategory(
      kode: json['kode']?.toString() ?? json['id']?.toString() ?? '',
      judul: json['judul']?.toString() ?? json['title']?.toString() ?? '',
      deskripsi: json['deskripsi']?.toString() ?? json['description']?.toString(),
    );
  }
}

class KbliItem {
  final String kode;
  final String judul;
  final String? deskripsi;
  final String? induk;

  KbliItem({
    required this.kode,
    required this.judul,
    this.deskripsi,
    this.induk,
  });

  factory KbliItem.fromJson(Map<String, dynamic> json) {
    return KbliItem(
      kode: json['kode']?.toString() ?? json['id']?.toString() ?? '',
      judul: json['judul']?.toString() ?? json['title']?.toString() ?? '',
      deskripsi: json['deskripsi']?.toString() ?? json['description']?.toString(),
      induk: json['induk']?.toString() ?? json['parent']?.toString(),
    );
  }
}

class BpsApiResponse {
  final int returnCode;
  final dynamic data;

  BpsApiResponse({required this.returnCode, required this.data});

  factory BpsApiResponse.fromJson(Map<String, dynamic> json) {
    return BpsApiResponse(
      returnCode: json['status'] ?? json['returncode'] ?? 0,
      data: json['data'] ?? json['results'],
    );
  }
}
