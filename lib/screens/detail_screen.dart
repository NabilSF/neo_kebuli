import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../theme/app_theme.dart';
import '../models/kbli_model.dart';
import '../services/bps_api_service.dart';
import '../widgets/common_widgets.dart';

class DetailScreen extends StatefulWidget {
  final String categoryCode;
  final String categoryTitle;

  const DetailScreen({
    super.key,
    required this.categoryCode,
    required this.categoryTitle,
  });

  @override
  State<DetailScreen> createState() => _DetailScreenState();
}

class _DetailScreenState extends State<DetailScreen>
    with SingleTickerProviderStateMixin {
  final _api = BpsApiService();
  late TabController _tabController;

  KbliItem? _detail;
  KbliItem? _parentDetail;
  List<KbliItem> _children = [];
  bool _isLoadingDetail = true;
  bool _isLoadingChildren = true;
  bool _isBookmarked = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadData();
    _checkBookmarkStatus();
  }

  Future<void> _checkBookmarkStatus() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getStringList('saved_kbli_codes') ?? [];
    if (mounted) {
      setState(() {
        _isBookmarked = saved.contains(widget.categoryCode);
      });
    }
  }

  Future<void> _toggleBookmark() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getStringList('saved_kbli_codes') ?? [];
    if (saved.contains(widget.categoryCode)) {
      saved.remove(widget.categoryCode);
      _isBookmarked = false;
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Dihapus dari bookmark!'),
            backgroundColor: AppColors.onSurfaceVariant(context),
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            duration: const Duration(seconds: 1),
          ),
        );
      }
    } else {
      saved.add(widget.categoryCode);
      _isBookmarked = true;
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Disimpan ke bookmark!'),
            backgroundColor: AppColors.primary,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            duration: const Duration(seconds: 1),
          ),
        );
      }
    }
    await prefs.setStringList('saved_kbli_codes', saved);
    if (mounted) {
      setState(() {});
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    await Future.wait([_loadDetail(), _loadChildren()]);
  }

  Future<void> _loadDetail() async {
    try {
      final data = await _api.fetchDetail(kode: widget.categoryCode);
      KbliItem? parent;
      if (widget.categoryCode.length == 5) {
        parent = await _api.fetchDetail(kode: widget.categoryCode.substring(0, 2));
      }
      if (mounted) {
        setState(() {
          _detail = data;
          _parentDetail = parent;
          _isLoadingDetail = false;
        });
      }
    } catch (_) {
      if (mounted) {
        setState(() => _isLoadingDetail = false);
      }
    }
  }

  Future<void> _loadChildren() async {
    try {
      final data = await _api.fetchByParent(parentCode: widget.categoryCode);
      if (mounted) {
        setState(() {
          _children = data;
          _isLoadingChildren = false;
        });
      }
    } catch (_) {
      if (mounted) {
        setState(() => _isLoadingChildren = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background(context),
      body: NestedScrollView(
        physics: const BouncingScrollPhysics(),
        headerSliverBuilder: (context, innerBoxIsScrolled) => [
          _buildSliverAppBar(innerBoxIsScrolled),
        ],
        body: TabBarView(
          controller: _tabController,
          children: [
            _buildDeskripsiTab(context),
            _buildStrukturTab(context),
            _buildProdukTab(context),
          ],
        ),
      ),
    );
  }

  Widget _buildSliverAppBar(bool innerBoxIsScrolled) {
    return SliverAppBar(
      expandedHeight: 230,
      floating: false,
      pinned: true,
      backgroundColor: AppColors.primary,
      elevation: innerBoxIsScrolled ? 2 : 0,
      leading: Padding(
        padding: const EdgeInsets.all(8),
        child: HoverableBuilder(
          onTap: () => Navigator.pop(context),
          builder: (context, isHovered, isPressed) {
            return AnimatedContainer(
              duration: const Duration(milliseconds: 150),
              decoration: BoxDecoration(
                color: isHovered
                    ? Colors.white.withOpacity(0.3)
                    : Colors.white.withOpacity(0.2),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.arrow_back_rounded,
                size: 20,
                color: Colors.white,
              ),
            );
          },
        ),
      ),
      actions: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4),
          child: HoverableBuilder(
            onTap: () {
              Clipboard.setData(ClipboardData(text: widget.categoryCode));
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: const Text('Kode KBLI disalin ke papan klip!'),
                  behavior: SnackBarBehavior.floating,
                  backgroundColor: AppColors.primary,
                ),
              );
            },
            builder: (context, isHovered, isPressed) {
              return Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: isHovered
                      ? Colors.white.withOpacity(0.3)
                      : Colors.transparent,
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.share_outlined,
                  size: 20,
                  color: Colors.white,
                ),
              );
            },
          ),
        ),
        Padding(
          padding: const EdgeInsets.only(right: 12),
          child: HoverableBuilder(
            onTap: _toggleBookmark,
            builder: (context, isHovered, isPressed) {
              return Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: isHovered
                      ? Colors.white.withOpacity(0.3)
                      : Colors.transparent,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  _isBookmarked ? Icons.bookmark_rounded : Icons.bookmark_outline_rounded,
                  size: 22,
                  color: Colors.white,
                ),
              );
            },
          ),
        ),
      ],
      flexibleSpace: FlexibleSpaceBar(
        background: _buildFlexibleHeader(),
      ),
      bottom: TabBar(
        controller: _tabController,
        isScrollable: true,
        indicatorColor: Colors.white,
        indicatorWeight: 3,
        labelColor: Colors.white,
        unselectedLabelColor: Colors.white.withOpacity(0.7),
        labelStyle: const TextStyle(
          fontWeight: FontWeight.w700,
          fontSize: 14,
        ),
        unselectedLabelStyle: const TextStyle(
          fontWeight: FontWeight.w500,
          fontSize: 14,
        ),
        tabs: const [
          Tab(text: 'Deskripsi'),
          Tab(text: 'Struktur'),
          Tab(text: 'Produk'),
        ],
      ),
    );
  }

  Widget _buildFlexibleHeader() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: AppColors.heroGradient,
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 36, 20, 48),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.white.withOpacity(0.3)),
                ),
                child: const Text(
                  'NEO KEBULI',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
              const SizedBox(height: 8),
              Text(
                widget.categoryTitle,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 21,
                  fontWeight: FontWeight.w800,
                  height: 1.25,
                  letterSpacing: -0.4,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ─── TAB DESKRIPSI ────────────────────────────────────────────────────────
  Widget _buildDeskripsiTab(BuildContext context) {
    if (_isLoadingDetail) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.primary),
      );
    }

    final deskripsi = _detail?.deskripsi ??
        'Deskripsi lengkap untuk kelompok "${widget.categoryTitle}" mencakup proses manufaktur, pengolahan, serta distribusi barang/jasa terkait sesuai standar klasifikasi industri BPS 2025.';

    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Bento Style Information Grid
          _buildBentoInfoGrid(context),

          const SizedBox(height: 20),

          // Important Callout Box
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.primaryContainer.withOpacity(0.08),
              borderRadius: BorderRadius.circular(16),
              border: const Border(
                left: BorderSide(color: AppColors.primary, width: 4),
              ),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.info_rounded, color: AppColors.primary, size: 22),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Catatan Penting Edisi 2025',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppColors.primary,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Kode ini telah diperbarui untuk mencakup standar kebersihan industri 4.0. Pastikan sertifikasi usaha Anda masih berlaku.',
                        style: TextStyle(
                          fontSize: 12,
                          color: AppColors.onSurfaceVariant(context),
                          height: 1.4,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          // Cakupan Kegiatan Card
          _buildCardContainer(
            context,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Cakupan Kegiatan',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.onSurface(context),
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  deskripsi,
                  style: TextStyle(
                    fontSize: 13,
                    color: AppColors.onSurfaceVariant(context),
                    height: 1.6,
                  ),
                ),
                const SizedBox(height: 12),
                _buildBulletPoint(context, 'Kegiatan pengolahan dan manufaktur standar industri.'),
                _buildBulletPoint(context, 'Pengemasan, pendistribusian, dan penyediaan produk akhir.'),
                _buildBulletPoint(context, 'Layanan pendukung yang terkait langsung dengan sektor ini.'),
              ],
            ),
          ),

          const SizedBox(height: 16),

          // Pengecualian Card
          _buildCardContainer(
            context,
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Icon(
                  Icons.block_rounded,
                  color: AppColors.outline(context),
                  size: 20,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Pengecualian: Produksi bahan mentah awal dan pertanian dasar tidak termasuk dalam kelompok ini, melainkan masuk ke Kategori Pertanian (01).',
                    style: TextStyle(
                      fontSize: 12,
                      fontStyle: FontStyle.italic,
                      color: AppColors.onSurfaceVariant(context),
                      height: 1.4,
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildBentoInfoGrid(BuildContext context) {
    return _buildCardContainer(
      context,
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Informasi Kode',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppColors.primary,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text(
                  'Aktif',
                  style: TextStyle(
                    color: AppColors.primary,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _buildBentoItem(
                  context,
                  icon: Icons.qr_code_2_rounded,
                  label: 'KODE KBLI',
                  value: widget.categoryCode,
                  isCode: true,
                ),
              ),
              Expanded(
                child: _buildBentoItem(
                  context,
                  icon: Icons.account_tree_rounded,
                  label: 'LEVEL',
                  value: '${widget.categoryCode.length}',
                ),
              ),
            ],
          ),
          const Divider(height: 24, thickness: 0.5),
          _buildBentoItem(
            context,
            icon: Icons.category_rounded,
            label: 'KATEGORI UTAMA',
            value: widget.categoryCode.length >= 2
                ? 'Kategori ${widget.categoryCode.substring(0, 2)} - BPS RI'
                : 'Sektor Utama BPS',
          ),
        ],
      ),
    );
  }

  Widget _buildBentoItem(
    BuildContext context, {
    required IconData icon,
    required String label,
    required String value,
    bool isCode = false,
  }) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppColors.surfaceContainerHigh(context),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: AppColors.primary, size: 20),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: AppColors.onSurfaceVariant(context),
                  letterSpacing: 0.5,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: TextStyle(
                  fontSize: isCode ? 16 : 14,
                  fontWeight: FontWeight.w700,
                  color: AppColors.onSurface(context),
                  fontFamily: isCode ? 'Monospace' : null,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildBulletPoint(BuildContext context, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            margin: const EdgeInsets.only(top: 6),
            width: 6,
            height: 6,
            decoration: const BoxDecoration(
              color: AppColors.primary,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              text,
              style: TextStyle(
                fontSize: 13,
                color: AppColors.onSurfaceVariant(context),
                height: 1.4,
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ─── TAB STRUKTUR (HIERARKI) ──────────────────────────────────────────────
  Widget _buildStrukturTab(BuildContext context) {
    final code = widget.categoryCode;
    List<Map<String, String>> hierarchy = [];

    if (code.length == 2) {
      hierarchy = [
        {
          'level': 'Kategori Utama (2 Digit)',
          'kode': code,
          'judul': widget.categoryTitle,
        },
      ];
    } else if (code.length == 5) {
      hierarchy = [
        {
          'level': 'Komoditas (5 Digit)',
          'kode': code,
          'judul': _detail?.judul ?? widget.categoryTitle,
        },
        {
          'level': 'Kategori Utama (2 Digit)',
          'kode': code.substring(0, 2),
          'judul': _parentDetail?.judul ?? '...',
        },
      ];
    }

    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Struktur KBLI',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: AppColors.onSurface(context),
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'Posisi kode ini dalam klasifikasi baku BPS',
            style: TextStyle(fontSize: 12, color: AppColors.onSurfaceVariant(context)),
          ),
          const SizedBox(height: 20),
          ...hierarchy.asMap().entries.map((entry) {
            final i = entry.key;
            final item = entry.value;
            final isFirst = i == 0;
            return _buildHierarchyItem(
              context,
              level: item['level']!,
              code: item['kode']!,
              title: item['judul']!,
              isActive: isFirst,
              isLast: i == hierarchy.length - 1,
            );
          }),
        ],
      ),
    );
  }

  Widget _buildHierarchyItem(
    BuildContext context, {
    required String level,
    required String code,
    required String title,
    required bool isActive,
    required bool isLast,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                gradient: isActive
                    ? const LinearGradient(
                        colors: AppColors.heroGradient,
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      )
                    : null,
                color: isActive ? null : AppColors.surfaceContainerHigh(context),
                shape: BoxShape.circle,
              ),
              child: Center(
                child: Text(
                  code,
                  style: TextStyle(
                    color: isActive ? Colors.white : AppColors.onSurfaceVariant(context),
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ),
            if (!isLast)
              Container(
                width: 2,
                height: 36,
                color: AppColors.outlineVariant(context).withOpacity(0.5),
              ),
          ],
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Padding(
            padding: const EdgeInsets.only(top: 4, bottom: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  level,
                  style: TextStyle(
                    fontSize: 11,
                    color: isActive ? AppColors.primary : AppColors.onSurfaceVariant(context),
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  'Kode: $code',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.onSurface(context),
                  ),
                ),
                if (title != '—')
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 12,
                      color: AppColors.onSurfaceVariant(context),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  // ─── TAB PRODUK / TURUNAN ─────────────────────────────────────────────────
  Widget _buildProdukTab(BuildContext context) {
    if (_isLoadingChildren) {
      return ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 5,
        itemBuilder: (_, __) => const SkeletonCard(),
      );
    }

    if (_children.isEmpty) {
      return const EmptyStateWidget(
        title: 'Tidak Ada Sub-Produk',
        subtitle: 'Tidak ditemukan rincian komoditas spesifik di bawah kelompok ini.',
      );
    }

    return ListView.builder(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      itemCount: _children.length + 1,
      itemBuilder: (context, index) {
        if (index == 0) {
          return Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: Text(
              '${_children.length} Komoditas Terkait',
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: AppColors.onSurface(context),
              ),
            ),
          );
        }

        final item = _children[index - 1];
        return Padding(
          padding: const EdgeInsets.only(bottom: 10),
          child: HoverableBuilder(
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => DetailScreen(
                  categoryCode: item.kode,
                  categoryTitle: item.judul,
                ),
              ),
            ),
            builder: (context, isHovered, isPressed) {
              return AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: isHovered
                      ? AppColors.surfaceContainerHigh(context)
                      : AppColors.surfaceContainerLowest(context),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: isHovered
                        ? AppColors.primary.withOpacity(0.3)
                        : AppColors.outlineVariant(context).withOpacity(0.2),
                  ),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: AppColors.heroGradient,
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        item.kode,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        item.judul,
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: AppColors.onSurface(context),
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Icon(
                      Icons.chevron_right_rounded,
                      color: AppColors.onSurfaceVariant(context),
                      size: 20,
                    ),
                  ],
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildCardContainer(BuildContext context, {required Widget child}) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surfaceContainerLowest(context),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.outlineVariant(context).withOpacity(0.3)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: child,
    );
  }
}
