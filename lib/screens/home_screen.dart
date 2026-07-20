import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/kbli_model.dart';
import '../services/bps_api_service.dart';
import '../widgets/common_widgets.dart';
import 'search_screen.dart';
import 'detail_screen.dart';

// --- TEMA WARNA REFERENSI BARU (GoBus Style - Terintegrasi Dark Mode) ---
class RefTheme {
  static Color get background => ThemeManager.isDark ? const Color(0xFF0F172A) : const Color(0xFFF8F9FA);
  static Color get primary => const Color(0xFFFF8A00);
  static Color get surface => ThemeManager.isDark ? const Color(0xFF1E293B) : Colors.white;
  static Color get textDark => ThemeManager.isDark ? const Color(0xFFF8FAFC) : const Color(0xFF1A1A1A);
  static Color get textLight => ThemeManager.isDark ? const Color(0xFF94A3B8) : const Color(0xFF8B8B8B);
  static Color get border => ThemeManager.isDark ? const Color(0xFF334155) : const Color(0xFFE5E7EB);
}

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
    return Scaffold(
      backgroundColor: RefTheme.background,
      body: SafeArea(
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            // ─── Bagian Atas (Header, Search, Banner) ─────────────
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildTopBar(),
                    const SizedBox(height: 24),
                    _buildBanner(), // Banner Oranye
                    const SizedBox(height: 20),
                    _buildSearchBar(context),
                    const SizedBox(height: 24),
                    _buildStatsRow(),
                    const SizedBox(height: 24),
                    _buildSectionTitle(),
                  ],
                ),
              ),
            ),

            // ─── Daftar Kategori (List Vertikal) ───
            if (_isLoading)
              SliverPadding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (_, __) => const Padding(
                      padding: EdgeInsets.only(bottom: 12),
                      child: SkeletonCard(),
                    ),
                    childCount: 6,
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
                padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (context, index) {
                      final cat = _categories[index];
                      return _buildCategoryListItem(cat);
                    },
                    childCount: _categories.length,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  // ─── KOMPONEN UI (Gaya Bersih & Oranye) ──────────────────────────────────

  Widget _buildTopBar() {
    final isDark = ThemeManager.isDark;
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        GestureDetector(
          onTap: () {
            ThemeManager.toggleTheme();
          },
          child: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: RefTheme.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: RefTheme.border),
            ),
            child: Icon(
              isDark ? Icons.light_mode_rounded : Icons.dark_mode_rounded,
              color: isDark ? const Color(0xFFFFB347) : RefTheme.textDark,
              size: 22,
            ),
          ),
        ),
        Text(
          'KBLI BPS',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.w800,
            color: RefTheme.textDark,
          ),
        ),
        const SizedBox(width: 40),
      ],
    );
  }

  // Banner bergaya card utama berwarna oranye khas aplikasi referensi
  Widget _buildBanner() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
      decoration: BoxDecoration(
        color: RefTheme.primary,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: RefTheme.primary.withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              'Edisi 2025',
              style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w600),
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'Klasifikasi Baku\nLapangan Usaha',
            style: TextStyle(
              color: Colors.white,
              fontSize: 22,
              fontWeight: FontWeight.w800,
              height: 1.3,
            ),
          ),
        ],
      ),
    );
  }

  // Search bar putih bersih dengan garis pinggir tipis
  Widget _buildSearchBar(BuildContext context) {
    return GestureDetector(
      onTap: () => Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => const SearchScreen()),
      ),
      child: Container(
        height: 52,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        decoration: BoxDecoration(
          color: RefTheme.surface,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: RefTheme.border),
        ),
        child: Row(
          children: [
            Icon(Icons.search_rounded, color: RefTheme.textLight, size: 24),
            const SizedBox(width: 12),
            Text(
              'Cari kategori atau kode...',
              style: TextStyle(color: RefTheme.textLight.withOpacity(0.8), fontSize: 14),
            ),
            const Spacer(),
            // Tombol mini oranye di dalam search bar
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: RefTheme.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                'Cari',
                style: TextStyle(
                  color: RefTheme.primary,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildStatsRow() {
    final stats = [
      {'icon': Icons.account_tree_outlined, 'value': '24', 'label': 'Kategori Utama'},
      {'icon': Icons.format_list_bulleted_rounded, 'value': '464', 'label': 'Komoditas'},
    ];

    return Row(
      children: stats.map((stat) {
        return Expanded(
          child: Container(
            margin: stats.indexOf(stat) == 0 ? const EdgeInsets.only(right: 12) : EdgeInsets.zero,
            padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 16),
            decoration: BoxDecoration(
              color: RefTheme.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: RefTheme.border),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: RefTheme.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(stat['icon'] as IconData, color: RefTheme.primary, size: 20),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        stat['value'] as String,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: RefTheme.textDark,
                        ),
                      ),
                      Text(
                        stat['label'] as String,
                        style: TextStyle(fontSize: 11, color: RefTheme.textLight),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildSectionTitle() {
    return Padding(
      padding: EdgeInsets.only(bottom: 8),
      child: Text(
        'Golongan Pokok',
        style: TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.bold,
          color: RefTheme.textDark,
        ),
      ),
    );
  }

  // ─── Item List (Menggunakan Border abu tipis ala desain tiket) ───
  Widget _buildCategoryListItem(KbliCategory cat) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12), // Jarak antar item
      child: GestureDetector(
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => DetailScreen(
              categoryCode: cat.kode,
              categoryTitle: cat.judul,
            ),
          ),
        ),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: RefTheme.surface,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: RefTheme.border), // Border abu-abu tipis
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // Lingkaran Oranye untuk huruf Kategori (A, B, C...)
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: RefTheme.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: RefTheme.primary.withOpacity(0.3)),
                ),
                child: Center(
                  child: Padding(
                    padding: const EdgeInsets.all(4.0),
                    child: FittedBox(
                      fit: BoxFit.scaleDown,
                      child: Text(
                        cat.kode,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: RefTheme.primary,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              
              // Teks Konten
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Kategori ${cat.kode}',
                      style: TextStyle(
                        fontSize: 12,
                        color: RefTheme.textLight,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      cat.judul,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: RefTheme.textDark,
                        height: 1.4,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              
              // Tombol / Ikon Aksi (Panah ke kanan)
              Icon(
                Icons.chevron_right_rounded,
                size: 24,
                color: RefTheme.textLight,
              ),
            ],
          ),
        ),
      ),
    );
  }
}