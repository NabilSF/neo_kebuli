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
            content: Text('Dihapus dari bookmark!'),
            backgroundColor: AppColors.slate700,
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
            content: Text('Disimpan ke bookmark!'),
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
      backgroundColor: AppColors.background,
      body: NestedScrollView(
        physics: const BouncingScrollPhysics(),
        headerSliverBuilder: (context, innerBoxIsScrolled) => [
          _buildSliverAppBar(innerBoxIsScrolled),
        ],
        body: TabBarView(
          controller: _tabController,
          children: [
            _buildDeskripsiTab(),
            _buildHierarkiTab(),
            _buildTurunanTab(),
          ],
        ),
      ),
    );
  }

  Widget _buildSliverAppBar(bool innerBoxIsScrolled) {
    return SliverAppBar(
      expandedHeight: 250,
      floating: false,
      pinned: true,
      backgroundColor: AppColors.primary,
      elevation: innerBoxIsScrolled ? 2 : 0,
      leading: Container(
        margin: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(10),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded,
              size: 16, color: AppColors.slate900),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      actions: [
        Container(
          margin: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(10),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.08),
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: IconButton(
            icon: Icon(
              _isBookmarked ? Icons.bookmark_rounded : Icons.bookmark_border_rounded,
              size: 18,
              color: _isBookmarked ? AppColors.primary : AppColors.slate700,
            ),
            onPressed: _toggleBookmark,
          ),
        ),
      ],
      flexibleSpace: FlexibleSpaceBar(
        background: _buildFlexibleHeader(),
      ),
      bottom: TabBar(
        controller: _tabController,
        indicatorColor: Colors.white,
        indicatorWeight: 2.5,
        indicatorSize: TabBarIndicatorSize.label,
        labelColor: Colors.white,
        unselectedLabelColor: Colors.white.withOpacity(0.75),
        labelStyle: TextStyle(
          fontWeight: FontWeight.w700,
          fontSize: 14,
          letterSpacing: -0.2,
        ),
        unselectedLabelStyle: TextStyle(
          fontWeight: FontWeight.w500,
          fontSize: 14,
        ),
        tabs: const [
          Tab(text: 'Deskripsi'),
          Tab(text: 'Hierarki'),
          Tab(text: 'Turunan'),
        ],
      ),
    );
  }

  Widget _buildFlexibleHeader() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFFFF7518), Color(0xFFFFB347)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 48, 24, 48),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              // Code badge
              GestureDetector(
                onTap: () {
                  Clipboard.setData(ClipboardData(text: widget.categoryCode));
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Kode disalin!'),
                      backgroundColor: AppColors.primaryDark,
                      behavior: SnackBarBehavior.floating,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                      duration: const Duration(seconds: 1),
                    ),
                  );
                },
                child: Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.25),
                    borderRadius: BorderRadius.circular(20),
                    border:
                        Border.all(color: Colors.white.withOpacity(0.4)),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.tag_rounded,
                          color: Colors.white, size: 14),
                      const SizedBox(width: 6),
                      Text(
                        'KBLI ${widget.categoryCode}',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 10),
              Text(
                widget.categoryTitle,
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 20,
                  fontWeight: FontWeight.w800,
                  height: 1.2,
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

  Widget _buildDeskripsiTab() {
    if (_isLoadingDetail) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.primary),
      );
    }

    final deskripsi = _detail?.deskripsi ??
        'Deskripsi lengkap untuk golongan "${widget.categoryTitle}" ini mencakup berbagai kegiatan usaha yang dikategorikan berdasarkan KBLI 2025 yang diterbitkan oleh Badan Pusat Statistik Republik Indonesia.';

    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Info Card
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: AppColors.primarySurface,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.primary.withOpacity(0.15)),
            ),
            child: Row(
              children: [
                Icon(Icons.info_outline_rounded,
                    color: AppColors.primary, size: 22),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Data bersumber dari BPS RI — KBLI Edisi 2025',
                    style: TextStyle(
                      fontSize: 12,
                      color: AppColors.slate700,
                      fontWeight: FontWeight.w500,
                      height: 1.4,
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          Text(
            'Deskripsi Golongan',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: AppColors.slate900,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            deskripsi,
            style: TextStyle(
              fontSize: 14,
              color: AppColors.slate700,
              height: 1.7,
            ),
          ),

          const SizedBox(height: 24),

          // Meta info
          _buildMetaCard(),
        ],
      ),
    );
  }

  Widget _buildMetaCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Informasi Kode',
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w700,
              color: AppColors.slate900,
            ),
          ),
          Divider(height: 20, color: AppColors.slate200),
          _buildMetaRow(Icons.tag_rounded, 'Kode', widget.categoryCode),
          const SizedBox(height: 10),
          _buildMetaRow(Icons.layers_rounded, 'Level', 'Golongan Pokok'),
          const SizedBox(height: 10),
          _buildMetaRow(Icons.calendar_today_rounded, 'Edisi', 'KBLI 2025'),
          const SizedBox(height: 10),
          _buildMetaRow(Icons.source_rounded, 'Sumber', 'BPS RI'),
        ],
      ),
    );
  }

  Widget _buildMetaRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: AppColors.primary),
        const SizedBox(width: 10),
        Text(
          '$label: ',
          style: TextStyle(
            fontSize: 13,
            color: AppColors.slate400,
            fontWeight: FontWeight.w500,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontSize: 13,
            color: AppColors.slate900,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _buildHierarkiTab() {
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
            'Struktur Hierarki KBLI',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: AppColors.slate900,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'Posisi kode ini dalam hierarki KBLI',
            style: TextStyle(fontSize: 12, color: AppColors.slate400),
          ),
          const SizedBox(height: 20),
          ...hierarchy.asMap().entries.map((entry) {
            final i = entry.key;
            final item = entry.value;
            final isFirst = i == 0;
            return _buildHierarchyItem(
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

  Widget _buildHierarchyItem({
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
                color: isActive ? null : AppColors.slate200,
                shape: BoxShape.circle,
              ),
              child: Center(
                child: FittedBox(
                  fit: BoxFit.scaleDown,
                  child: Padding(
                    padding: const EdgeInsets.all(4.0),
                    child: Text(
                      code,
                      style: TextStyle(
                        color: isActive ? Colors.white : AppColors.slate400,
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ),
              ),
            ),
            if (!isLast)
              Container(
                width: 2,
                height: 40,
                color: AppColors.slate200,
              ),
          ],
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Padding(
            padding: const EdgeInsets.only(top: 6, bottom: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  level,
                  style: TextStyle(
                    fontSize: 11,
                    color: isActive ? AppColors.primary : AppColors.slate400,
                    fontWeight: FontWeight.w600,
                    letterSpacing: 0.3,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  'Kode: $code',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.slate900,
                  ),
                ),
                if (title != '—')
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 12,
                      color: AppColors.slate700,
                      height: 1.4,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildTurunanTab() {
    if (_isLoadingChildren) {
      return ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 5,
        itemBuilder: (_, __) => const SkeletonCard(),
      );
    }

    if (_children.isEmpty) {
      return const EmptyStateWidget(
        title: 'Tidak Ada Turunan',
        subtitle: 'Tidak ditemukan sub-kode KBLI untuk kategori ini.',
      );
    }

    return ListView.builder(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      itemCount: _children.length + 1,
      itemBuilder: (context, index) {
        if (index == 0) {
          return Padding(
            padding: const EdgeInsets.only(bottom: 14),
            child: Text(
              '${_children.length} Kode Turunan',
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: AppColors.slate900,
              ),
            ),
          );
        }

        final item = _children[index - 1];
        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Material(
            color: Colors.transparent,
            borderRadius: BorderRadius.circular(16),
            child: InkWell(
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => DetailScreen(
                    categoryCode: item.kode,
                    categoryTitle: item.judul,
                  ),
                ),
              ),
              borderRadius: BorderRadius.circular(16),
              child: Padding(
                padding: const EdgeInsets.all(14),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFFFF7518), Color(0xFFFFB347)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        item.kode,
                        style: TextStyle(
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
                          color: AppColors.slate900,
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Icon(Icons.chevron_right_rounded,
                        color: AppColors.slate400, size: 18),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
