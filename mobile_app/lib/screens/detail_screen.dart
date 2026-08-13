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
  final String searchQuery;

  const DetailScreen({
    super.key,
    required this.categoryCode,
    required this.categoryTitle,
    this.searchQuery = '',
  });

  @override
  State<DetailScreen> createState() => _DetailScreenState();
}

class _DetailScreenState extends State<DetailScreen>
    with SingleTickerProviderStateMixin {
  final _api = BpsApiService();
  late TabController _tabController;

  KbliItem? _detail;
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
      if (mounted) {
        setState(() {
          _detail = data;
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
                const SnackBar(
                  content: Text('Kode KBLI disalin ke papan klip!'),
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
    final cat = _detail?.kategori ?? (['01', '02', '03'].contains(widget.categoryCode.substring(0, widget.categoryCode.length >= 2 ? 2 : widget.categoryCode.length)) ? 'A' : 'C');

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
              Row(
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
                  const SizedBox(width: 8),
                  CategoryBadgeTag(category: cat, isSmall: true),
                ],
              ),
              const SizedBox(height: 8),
              HighlightedText(
                text: widget.categoryTitle,
                query: widget.searchQuery,
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
        'Deskripsi lengkap untuk kelompok "${widget.categoryTitle}" mencakup standar klasifikasi usaha BPS 2025.';

    final hasProcesses = _detail?.proses.isNotEmpty ?? false;
    final hasKu = _detail?.kegiatanUtama.isNotEmpty ?? false;
    final hasPenjelasan = _detail?.penjelasanKu.isNotEmpty ?? false;

    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Bento Style Information Grid
          _buildBentoInfoGrid(context),

          const SizedBox(height: 16),

          // Kegiatan Utama Card
          if (hasKu) ...[
            _buildCardContainer(
              context,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.work_outline_rounded, color: AppColors.primary, size: 20),
                      const SizedBox(width: 8),
                      Text(
                        'Kegiatan Utama',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          color: AppColors.onSurface(context),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  ..._detail!.kegiatanUtama.map(
                    (ku) => Padding(
                      padding: const EdgeInsets.only(bottom: 6),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Icon(Icons.check_circle_outline_rounded, size: 16, color: AppColors.primary),
                          const SizedBox(width: 8),
                          Expanded(
                            child: HighlightedText(
                              text: ku,
                              query: widget.searchQuery,
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: AppColors.onSurface(context),
                                height: 1.4,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
          ],

          // Process List Card
          if (hasProcesses) ...[
            _buildCardContainer(
              context,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.settings_suggest_rounded, color: AppColors.primary, size: 20),
                      const SizedBox(width: 8),
                      Text(
                        'Proses Produksi / Pengolahan',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          color: AppColors.onSurface(context),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: _detail!.proses.map((p) {
                      return Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.primary.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppColors.primary.withOpacity(0.2)),
                        ),
                        child: Text(
                          p.toUpperCase(),
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: AppColors.primary,
                            letterSpacing: 0.3,
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
          ],

          // Cakupan Kegiatan Card
          _buildCardContainer(
            context,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Deskripsi & Cakupan Klasifikasi',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppColors.onSurface(context),
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  deskripsi,
                  style: TextStyle(
                    fontSize: 13,
                    color: AppColors.onSurfaceVariant(context),
                    height: 1.6,
                  ),
                ),
                if (hasPenjelasan) ...[
                  const SizedBox(height: 12),
                  const Divider(height: 1, thickness: 0.5),
                  const SizedBox(height: 12),
                  Text(
                    'Penjelasan Detail:',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                      color: AppColors.slate900(context),
                    ),
                  ),
                  const SizedBox(height: 6),
                  ..._detail!.penjelasanKu.map(
                    (pj) => Padding(
                      padding: const EdgeInsets.only(bottom: 6),
                      child: Text(
                        pj,
                        style: TextStyle(
                          fontSize: 12,
                          color: AppColors.onSurfaceVariant(context),
                          height: 1.5,
                        ),
                      ),
                    ),
                  ),
                ],
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

  // ─── TAB STRUKTUR (HIERARKI) ──────────────────────────────────────────────
  Widget _buildStrukturTab(BuildContext context) {
    final chain = _api.getHierarchyChain(widget.categoryCode);

    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Hirarki & Struktur KBLI',
            style: TextStyle(
              fontSize: 17,
              fontWeight: FontWeight.w700,
              color: AppColors.onSurface(context),
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'Keterhubungan antar-digit klasifikasi baku BPS RI',
            style: TextStyle(fontSize: 12, color: AppColors.onSurfaceVariant(context)),
          ),
          const SizedBox(height: 24),
          ...chain.asMap().entries.map((entry) {
            final i = entry.key;
            final item = entry.value;
            final isTarget = item['kode'] == widget.categoryCode;
            return _buildHierarchyItem(
              context,
              level: item['level']!,
              code: item['kode']!,
              title: item['judul']!,
              isActive: isTarget,
              isLast: i == chain.length - 1,
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
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Column(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  gradient: isActive
                      ? const LinearGradient(
                          colors: AppColors.heroGradient,
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        )
                      : null,
                  color: isActive ? null : AppColors.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: isActive
                      ? [
                          BoxShadow(
                            color: AppColors.primary.withOpacity(0.3),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ]
                      : null,
                ),
                child: Center(
                  child: Text(
                    code,
                    style: TextStyle(
                      color: isActive ? Colors.white : AppColors.primary,
                      fontSize: code.length > 3 ? 11 : 13,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ),
              ),
              if (!isLast)
                Expanded(
                  child: Container(
                    width: 2.5,
                    margin: const EdgeInsets.symmetric(vertical: 4),
                    decoration: BoxDecoration(
                      color: AppColors.primary.withOpacity(0.3),
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                ),
            ],
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.only(bottom: 20),
              child: Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: isActive
                      ? AppColors.primary.withOpacity(0.06)
                      : AppColors.surfaceContainerLowest(context),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: isActive
                        ? AppColors.primary.withOpacity(0.3)
                        : AppColors.outlineVariant(context).withOpacity(0.2),
                    width: isActive ? 1.5 : 1.0,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          level,
                          style: TextStyle(
                            fontSize: 11,
                            color: isActive ? AppColors.primary : AppColors.slate400(context),
                            fontWeight: FontWeight.w700,
                            letterSpacing: 0.3,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: isActive
                                ? AppColors.primary
                                : AppColors.surfaceContainerHigh(context),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            'Kode $code',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w700,
                              color: isActive ? Colors.white : AppColors.slate700(context),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    HighlightedText(
                      text: title,
                      query: widget.searchQuery,
                      style: TextStyle(
                        fontSize: 13.5,
                        fontWeight: FontWeight.w700,
                        color: AppColors.onSurface(context),
                        height: 1.35,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ─── TAB PRODUK / TURUNAN ─────────────────────────────────────────────────
  Widget _buildProdukTab(BuildContext context) {
    if (_isLoadingChildren && _isLoadingDetail) {
      return ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 5,
        itemBuilder: (_, __) => const SkeletonCard(),
      );
    }

    final hasDirectProducts = _detail != null && _detail!.produk.isNotEmpty;
    final hasChildren = _children.isNotEmpty;

    if (!hasDirectProducts && !hasChildren) {
      return const EmptyStateWidget(
        title: 'Tidak Ada Produk Terdaftar',
        subtitle: 'Tidak ditemukan rincian komoditas atau produk spesifik di bawah kelompok ini.',
      );
    }

    if (hasDirectProducts) {
      final products = _detail!.produk;
      return ListView.builder(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.all(20),
        itemCount: products.length + 1,
        itemBuilder: (context, index) {
          if (index == 0) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 14),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${products.length} Daftar Produk & Komoditas',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.onSurface(context),
                    ),
                  ),
                  CategoryBadgeTag(category: _detail!.kategori, isSmall: true),
                ],
              ),
            );
          }

          final prodName = products[index - 1];
          return Container(
            margin: const EdgeInsets.only(bottom: 8),
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLowest(context),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(
                color: AppColors.outlineVariant(context).withOpacity(0.3),
              ),
            ),
            child: Row(
              children: [
                Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Center(
                    child: Text(
                      '$index',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: AppColors.primary,
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: HighlightedText(
                    text: prodName,
                    query: widget.searchQuery,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: AppColors.onSurface(context),
                      height: 1.3,
                    ),
                  ),
                ),
              ],
            ),
          );
        },
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
              '${_children.length} Kode Sub-Kategori / Items',
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
                    KbliCodeBadge(code: item.kode, size: 40),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          CategoryBadgeTag(category: item.kategori, isSmall: true),
                          const SizedBox(height: 4),
                          Text(
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
                        ],
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
