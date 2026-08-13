class KbliCategory {
  final String kode;
  final String judul;
  final String? deskripsi;
  final String kategori; // 'A' or 'C'
  final int itemCount;

  KbliCategory({
    required this.kode,
    required this.judul,
    this.deskripsi,
    this.kategori = 'C',
    this.itemCount = 0,
  });

  factory KbliCategory.fromJson(Map<String, dynamic> json) {
    final code = json['kode']?.toString() ?? json['id']?.toString() ?? '';
    final cat = json['kategori']?.toString() ?? (['01', '02', '03'].contains(code) ? 'A' : 'C');
    return KbliCategory(
      kode: code,
      judul: json['judul']?.toString() ?? json['title']?.toString() ?? '',
      deskripsi: json['deskripsi']?.toString() ?? json['description']?.toString(),
      kategori: cat,
      itemCount: json['itemCount'] ?? json['item_count'] ?? 0,
    );
  }
}

class KbliItem {
  final String kode;
  final String judul;
  final String? deskripsi;
  final String? induk;
  final String kategori; // 'A' or 'C'
  final List<String> produk;
  final List<String> proses;
  final List<String> kegiatanUtama;
  final List<String> penjelasanKu;

  KbliItem({
    required this.kode,
    required this.judul,
    this.deskripsi,
    this.induk,
    this.kategori = 'C',
    this.produk = const [],
    this.proses = const [],
    this.kegiatanUtama = const [],
    this.penjelasanKu = const [],
  });

  factory KbliItem.fromJson(Map<String, dynamic> json) {
    final code = json['kode']?.toString() ?? json['id']?.toString() ?? '';
    final parent = json['induk']?.toString() ?? json['parent']?.toString();
    final cat = json['kategori']?.toString() ??
        (['01', '02', '03'].contains(parent) || code.startsWith(RegExp(r'0[1-3]')) ? 'A' : 'C');

    return KbliItem(
      kode: code,
      judul: json['judul']?.toString() ?? json['title']?.toString() ?? '',
      deskripsi: json['deskripsi']?.toString() ?? json['description']?.toString(),
      induk: parent,
      kategori: cat,
      produk: (json['produk'] as List<dynamic>?)?.map((e) => e.toString()).toList() ?? [],
      proses: (json['proses'] as List<dynamic>?)?.map((e) => e.toString()).toList() ?? [],
      kegiatanUtama: (json['kegiatan_utama'] as List<dynamic>?)?.map((e) => e.toString()).toList() ?? [],
      penjelasanKu: (json['penjelasan_ku'] as List<dynamic>?)?.map((e) => e.toString()).toList() ?? [],
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

