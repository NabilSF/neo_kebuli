class MediaModel {
  final int id;
  final String originalName;
  final String? mimeType;
  final int? size;
  final String? createdAt;

  MediaModel({
    required this.id,
    required this.originalName,
    this.mimeType,
    this.size,
    this.createdAt,
  });

  factory MediaModel.fromJson(Map<String, dynamic> json) {
    return MediaModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      originalName: json['original_name']?.toString() ?? '',
      mimeType: json['mime_type']?.toString(),
      size: json['size'] is int ? json['size'] : int.tryParse(json['size']?.toString() ?? '0'),
      createdAt: json['created_at']?.toString(),
    );
  }
}

class ApplicationHistoryModel {
  final int id;
  final String status;
  final String? notes;
  final String? createdAt;

  ApplicationHistoryModel({
    required this.id,
    required this.status,
    this.notes,
    this.createdAt,
  });

  factory ApplicationHistoryModel.fromJson(Map<String, dynamic> json) {
    return ApplicationHistoryModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      status: json['status']?.toString() ?? 'submitted',
      notes: json['notes']?.toString(),
      createdAt: json['created_at']?.toString(),
    );
  }

  String get statusBadgeLabel {
    switch (status.toLowerCase()) {
      case 'submitted':
        return 'Diajukan';
      case 'under_review':
        return 'Sedang Ditinjau';
      case 'need_revision':
        return 'Perlu Perbaikan';
      case 'approved':
        return 'Disetujui';
      case 'rejected':
        return 'Ditolak';
      default:
        return status;
    }
  }
}

class ApplicationModel {
  final int id;
  final String trackingCode;
  final String productName;
  final String? productMaterial;
  final String? productionProcess;
  final String? productDescription;
  final String? sakerAddress;
  final String status;
  final String? adminNotes;
  final String? submittedAt;
  final String? reviewedAt;
  final Map<String, dynamic>? kbliItem;
  final List<ApplicationHistoryModel> histories;
  final List<MediaModel> media;

  ApplicationModel({
    required this.id,
    required this.trackingCode,
    required this.productName,
    this.productMaterial,
    this.productionProcess,
    this.productDescription,
    this.sakerAddress,
    required this.status,
    this.adminNotes,
    this.submittedAt,
    this.reviewedAt,
    this.kbliItem,
    this.histories = const [],
    this.media = const [],
  });

  factory ApplicationModel.fromJson(Map<String, dynamic> json) {
    var historiesList = <ApplicationHistoryModel>[];
    if (json['histories'] is List) {
      historiesList = (json['histories'] as List)
          .map((item) => ApplicationHistoryModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    var mediaList = <MediaModel>[];
    if (json['media'] is List) {
      mediaList = (json['media'] as List)
          .map((item) => MediaModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    return ApplicationModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      trackingCode: json['tracking_code']?.toString() ?? '',
      productName: json['product_name']?.toString() ?? '',
      productMaterial: json['product_material']?.toString(),
      productionProcess: json['production_process']?.toString(),
      productDescription: json['product_description']?.toString(),
      sakerAddress: json['saker_address']?.toString(),
      status: json['status']?.toString() ?? 'submitted',
      adminNotes: json['admin_notes']?.toString(),
      submittedAt: json['submitted_at']?.toString(),
      reviewedAt: json['reviewed_at']?.toString(),
      kbliItem: json['kbli_item'] is Map<String, dynamic> ? json['kbli_item'] : null,
      histories: historiesList,
      media: mediaList,
    );
  }

  String get statusBadgeLabel {
    switch (status.toLowerCase()) {
      case 'submitted':
        return 'Diajukan';
      case 'under_review':
        return 'Sedang Ditinjau';
      case 'need_revision':
        return 'Perlu Perbaikan';
      case 'approved':
        return 'Disetujui';
      case 'rejected':
        return 'Ditolak';
      default:
        return status;
    }
  }
}
