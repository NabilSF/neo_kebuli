import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/kbli_model.dart';
import '../services/bps_api_service.dart';
import '../widgets/common_widgets.dart';
import 'search_screen.dart';
import 'detail_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final _api = BpsApiService();
  List<KbliCategory> _categories = [];
  bool _isLoading = true;
  bool _hasError = false;
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadCategories() async {
    setState(() {
      _isLoading = true;
      _hasError = false;
    });
    try {
      final data = await _api.fetchCategories();
      setState(() {
        _categories = data;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
        _hasError = true;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<ThemeMode>(
      valueListenable: ThemeManager.themeModeNotifier,
      builder: (context, themeMode, _) {
        return Scaffold(
          backgroundColor: AppColors.background(context),
      body: SafeArea(
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            // Top Navigation Bar
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
                child: _buildTopBar(context),
              ),
            ),

            // Hero Section & Floating Search Bar
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  children: [
                    _buildHeroSection(),
                    Transform.translate(
                      offset: const Offset(0, -26),
                      child: _buildFloatingSearchBar(context),
                    ),
                  ],
                ),
              ),
            ),

            // Quick Access / Eksplorasi Section
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 4, 20, 24),
                child: _buildExplorationSection(context),
              ),
            ),

            // Section Title: Golongan Pokok
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 0, 20, 12),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Golongan Pokok Terpopuler',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w700,
                        color: AppColors.onSurface(context),
                        letterSpacing: -0.3,
                      ),
                    ),
                    TextButton(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const SearchScreen()),
                        );
                      },
                      child: const Text(
                        'Lihat Semua',
                        style: TextStyle(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w600,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),

            // List Golongan Pokok
            if (_isLoading)
              SliverPadding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (_, __) => const Padding(
                      padding: EdgeInsets.only(bottom: 12),
                      child: SkeletonCard(),
                    ),
                    childCount: 5,
                  ),
                ),
              )
            else if (_hasError)
              SliverFillRemaining(
                child: ErrorStateWidget(
                  message: 'Gagal memuat data. Pastikan koneksi internet Anda aktif.',
                  onRetry: _loadCategories,
                ),
              )
            else
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(20, 0, 20, 32),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (context, index) {
                      final cat = _categories[index];
                      return _buildCategoryListItem(context, cat);
                    },
                    childCount: _categories.length,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
      },
    );
  }

  // ─── TOP BAR ─────────────────────────────────────────────────────────────
  Widget _buildTopBar(BuildContext context) {
    final isDark = ThemeManager.isDark(context);
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.12),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                Icons.factory_rounded,
                color: isDark ? AppColors.primaryFixedDim : AppColors.primary,
                size: 24,
              ),
            ),
            const SizedBox(width: 10),
            Text(
              'NEO KEBULI',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w800,
                color: isDark ? AppColors.primaryFixedDim : AppColors.primary,
                letterSpacing: -0.3,
              ),
            ),
          ],
        ),
        Row(
          children: [
            HoverableBuilder(
              onTap: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: const Text('Tidak ada notifikasi baru'),
                    behavior: SnackBarBehavior.floating,
                    backgroundColor: AppColors.onSurfaceVariant(context),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                );
              },
              builder: (context, isHovered, isPressed) {
                return Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: isHovered
                        ? AppColors.primary.withOpacity(0.08)
                        : Colors.transparent,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    Icons.notifications_outlined,
                    color: AppColors.onSurfaceVariant(context),
                    size: 22,
                  ),
                );
              },
            ),
            const SizedBox(width: 4),
            HoverableBuilder(
              onTap: () => ThemeManager.toggleTheme(),
              builder: (context, isHovered, isPressed) {
                return Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: isHovered
                        ? AppColors.primary.withOpacity(0.08)
                        : Colors.transparent,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    isDark ? Icons.light_mode_rounded : Icons.dark_mode_outlined,
                    color: isDark ? AppColors.primaryFixedDim : AppColors.primary,
                    size: 22,
                  ),
                );
              },
            ),
          ],
        ),
      ],
    );
  }

  // ─── HERO SECTION ────────────────────────────────────────────────────────
  Widget _buildHeroSection() {
    return Container(
      width: double.infinity,
      constraints: const BoxConstraints(minHeight: 190),
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: AppColors.heroGradient,
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withOpacity(0.3),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            right: -10,
            top: -10,
            child: Icon(
              Icons.analytics_rounded,
              size: 130,
              color: Colors.white.withOpacity(0.12),
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 7,
                      height: 7,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Text(
                      'Versi Terbaru',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                'Klasifikasi Baku Lapangan Usaha',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 23,
                  fontWeight: FontWeight.w800,
                  height: 1.25,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Temukan kode standar usaha Anda dengan data terbaru BPS.',
                style: TextStyle(
                  color: Colors.white.withOpacity(0.85),
                  fontSize: 13,
                  fontWeight: FontWeight.w400,
                ),
              ),
              const SizedBox(height: 14),
            ],
          ),
        ],
      ),
    );
  }

  // ─── FLOATING SEARCH BAR ──────────────────────────────────────────────────
  Widget _buildFloatingSearchBar(BuildContext context) {
    return HoverableBuilder(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const SearchScreen()),
        );
      },
      builder: (context, isHovered, isPressed) {
        return AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          height: 56,
          padding: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(
            color: AppColors.surfaceContainerLowest(context),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: isHovered
                  ? AppColors.primary.withOpacity(0.5)
                  : AppColors.outlineVariant(context).withOpacity(0.4),
              width: isHovered ? 1.5 : 1.0,
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(isHovered ? 0.12 : 0.07),
                blurRadius: isHovered ? 24 : 16,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: Row(
            children: [
              Icon(
                Icons.search_rounded,
                color: isHovered ? AppColors.primary : AppColors.outline(context),
                size: 22,
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  'Cari Kode atau Nama KBLI...',
                  style: TextStyle(
                    color: AppColors.onSurfaceVariant(context).withOpacity(0.7),
                    fontSize: 14,
                  ),
                ),
              ),
              AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 9),
                decoration: BoxDecoration(
                  color: isHovered ? AppColors.primaryContainer : AppColors.primary,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Text(
                  'Cari',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // ─── EKSPLORASI SECTION ───────────────────────────────────────────────────
  Widget _buildExplorationSection(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'Eksplorasi',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w700,
                color: AppColors.onSurface(context),
                letterSpacing: -0.3,
              ),
            ),
            TextButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const SearchScreen()),
                );
              },
              child: const Text(
                'Lihat Semua',
                style: TextStyle(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w600,
                  fontSize: 13,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Row(
          children: [
            Expanded(
              child: _buildExplorationCard(
                icon: Icons.category_rounded,
                iconBg: AppColors.primaryFixed,
                iconColor: AppColors.primary,
                title: 'Kategori Utama',
                subtitle: '21 Sektor Industri Utama',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const SearchScreen()),
                  );
                },
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: _buildExplorationCard(
                icon: Icons.inventory_2_rounded,
                iconBg: AppColors.tertiaryFixed,
                iconColor: AppColors.tertiary,
                title: 'Komoditas',
                subtitle: 'Daftar Produk Terkait',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const SearchScreen()),
                  );
                },
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildExplorationCard({
    required IconData icon,
    required Color iconBg,
    required Color iconColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return HoverableBuilder(
      onTap: onTap,
      builder: (context, isHovered, isPressed) {
        return AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
            color: AppColors.surfaceContainerLow(context),
            borderRadius: BorderRadius.circular(18),
            border: Border.all(
              color: isHovered
                  ? iconColor.withOpacity(0.5)
                  : AppColors.outlineVariant(context).withOpacity(0.2),
              width: isHovered ? 1.5 : 1.0,
            ),
            boxShadow: isHovered
                ? [
                    BoxShadow(
                      color: iconColor.withOpacity(0.12),
                      blurRadius: 16,
                      offset: const Offset(0, 4),
                    ),
                  ]
                : [],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              AnimatedScale(
                scale: isHovered ? 1.1 : 1.0,
                duration: const Duration(milliseconds: 200),
                child: Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: iconBg,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(icon, color: iconColor, size: 22),
                ),
              ),
              const SizedBox(height: 14),
              Text(
                title,
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: AppColors.onSurface(context),
                ),
              ),
              const SizedBox(height: 3),
              Text(
                subtitle,
                style: TextStyle(
                  fontSize: 12,
                  color: AppColors.onSurfaceVariant(context),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // ─── CATEGORY LIST ITEM ──────────────────────────────────────────────────
  Widget _buildCategoryListItem(BuildContext context, KbliCategory cat) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: HoverableBuilder(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => DetailScreen(
                categoryCode: cat.kode,
                categoryTitle: cat.judul,
              ),
            ),
          );
        },
        builder: (context, isHovered, isPressed) {
          return AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: isHovered
                  ? AppColors.surfaceContainerHigh(context)
                  : AppColors.surfaceContainerLowest(context),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: isHovered
                    ? AppColors.primary.withOpacity(0.3)
                    : AppColors.outlineVariant(context).withOpacity(0.2),
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(isHovered ? 0.08 : 0.03),
                  blurRadius: isHovered ? 12 : 6,
                  offset: const Offset(0, 3),
                ),
              ],
            ),
            child: Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: Text(
                      cat.kode,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: AppColors.primary,
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        cat.judul,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: AppColors.onSurface(context),
                        ),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        'Klasifikasi Utama BPS',
                        style: TextStyle(
                          fontSize: 12,
                          color: AppColors.onSurfaceVariant(context),
                        ),
                      ),
                    ],
                  ),
                ),
                Icon(
                  Icons.chevron_right_rounded,
                  size: 22,
                  color: isHovered
                      ? AppColors.primary
                      : AppColors.onSurfaceVariant(context).withOpacity(0.5),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}