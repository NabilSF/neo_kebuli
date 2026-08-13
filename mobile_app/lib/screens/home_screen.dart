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
  List<KbliCategory> _allCategories = [];
  List<KbliCategory> _filteredCategories = [];
  KbliStats? _stats;
  bool _isLoading = true;
  bool _hasError = false;
  String _selectedCategoryFilter = 'ALL'; // 'ALL', 'A', 'C'
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
      _hasError = false;
    });
    try {
      final categoriesData = await _api.fetchCategories();
      final statsData = await _api.fetchStats();
      if (mounted) {
        setState(() {
          _allCategories = categoriesData;
          _stats = statsData;
          _applyFilter(_selectedCategoryFilter);
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _hasError = true;
        });
      }
    }
  }

  void _applyFilter(String filter) {
    _selectedCategoryFilter = filter;
    if (filter == 'ALL') {
      _filteredCategories = List.from(_allCategories);
    } else {
      _filteredCategories = _allCategories
          .where((cat) => cat.kategori.toUpperCase() == filter.toUpperCase())
          .toList();
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

                // Stats Banner Section
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 0, 20, 16),
                    child: _buildStatsBanner(context),
                  ),
                ),

                // Section Title: Golongan Pokok
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 0, 20, 12),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Text(
                                  'Golongan Pokok (2 Digit)',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.w700,
                                    color: AppColors.onSurface(context),
                                    letterSpacing: -0.3,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: AppColors.primary.withOpacity(0.12),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text(
                                    '${_filteredCategories.length}',
                                    style: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w700,
                                      color: AppColors.primary,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 2),
                            Text(
                              'Kategori C - Industri Pengolahan',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                                color: AppColors.slate400(context),
                              ),
                            ),
                          ],
                        ),
                        TextButton(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const SearchScreen()),
                            );
                          },
                          child: const Text(
                            'Cari Semua',
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
                      message: 'Gagal memuat data. Silakan coba lagi.',
                      onRetry: _loadData,
                    ),
                  )
                else if (_filteredCategories.isEmpty)
                  const SliverFillRemaining(
                    child: EmptyStateWidget(
                      title: 'Tidak ada data',
                      subtitle: 'Tidak ada Golongan Pokok untuk kategori ini.',
                    ),
                  )
                else
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(20, 0, 20, 32),
                    sliver: SliverList(
                      delegate: SliverChildBuilderDelegate(
                        (context, index) {
                          final cat = _filteredCategories[index];
                          return CategoryCard(
                            code: cat.kode,
                            title: cat.judul,
                            category: cat.kategori,
                            itemCount: cat.itemCount,
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
                          );
                        },
                        childCount: _filteredCategories.length,
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
                  'Cari Kode, Produk, atau Kegiatan Usaha...',
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

  // ─── STATS BANNER ─────────────────────────────────────────────────────────
  Widget _buildStatsBanner(BuildContext context) {
    final stats = _stats;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: AppColors.surfaceContainerLow(context),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: AppColors.outlineVariant(context).withOpacity(0.3),
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildStatPill(
            context,
            label: 'Total Kode',
            value: stats != null ? '${stats.totalItems}' : '566',
            icon: Icons.tag_rounded,
            color: AppColors.primary,
          ),
          Container(width: 1, height: 32, color: AppColors.outlineVariant(context).withOpacity(0.4)),
          _buildStatPill(
            context,
            label: 'Kategori A',
            value: stats != null ? '${stats.countCatA}' : '102',
            icon: Icons.eco_rounded,
            color: const Color(0xFF059669),
          ),
          Container(width: 1, height: 32, color: AppColors.outlineVariant(context).withOpacity(0.4)),
          _buildStatPill(
            context,
            label: 'Kategori C',
            value: stats != null ? '${stats.countCatC}' : '464',
            icon: Icons.factory_rounded,
            color: AppColors.primary,
          ),
        ],
      ),
    );
  }

  Widget _buildStatPill(
    BuildContext context, {
    required String label,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Column(
      children: [
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 14, color: color),
            const SizedBox(width: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: AppColors.slate900(context),
              ),
            ),
          ],
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w500,
            color: AppColors.slate400(context),
          ),
        ),
      ],
    );
  }
}