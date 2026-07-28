import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../theme/app_theme.dart';
import '../models/kbli_model.dart';
import '../services/bps_api_service.dart';
import '../widgets/common_widgets.dart';
import 'detail_screen.dart';
import 'search_screen.dart';

class SavedScreen extends StatefulWidget {
  const SavedScreen({super.key});

  @override
  State<SavedScreen> createState() => SavedScreenState();
}

class SavedScreenState extends State<SavedScreen> {
  final _api = BpsApiService();
  List<KbliItem> _savedItems = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    loadSavedItems();
  }

  Future<void> loadSavedItems() async {
    setState(() {
      _isLoading = true;
    });

    final prefs = await SharedPreferences.getInstance();
    final codes = prefs.getStringList('saved_kbli_codes') ?? [];

    final List<KbliItem> items = [];
    for (final code in codes) {
      final item = await _api.fetchDetail(kode: code);
      if (item != null) {
        items.add(item);
      }
    }

    if (mounted) {
      setState(() {
        _savedItems = items;
        _isLoading = false;
      });
    }
  }

  Future<void> _deleteItem(String code) async {
    final prefs = await SharedPreferences.getInstance();
    final codes = prefs.getStringList('saved_kbli_codes') ?? [];
    codes.remove(code);
    await prefs.setStringList('saved_kbli_codes', codes);

    setState(() {
      _savedItems.removeWhere((item) => item.kode == code);
    });

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
  }

  Future<void> _clearAll() async {
    if (_savedItems.isEmpty) return;

    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Semua Bookmark'),
        content: const Text('Apakah Anda yakin ingin menghapus semua bookmark KBLI Anda?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text('Batal', style: TextStyle(color: AppColors.outline(context))),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Hapus', style: TextStyle(color: AppColors.primary)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('saved_kbli_codes');
      setState(() {
        _savedItems.clear();
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Semua bookmark dihapus!'),
            backgroundColor: AppColors.onSurfaceVariant(context),
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<ThemeMode>(
      valueListenable: ThemeManager.themeModeNotifier,
      builder: (context, themeMode, _) {
        final isDark = themeMode == ThemeMode.dark;
        return Scaffold(
          backgroundColor: AppColors.background(context),
          appBar: AppBar(
            backgroundColor: AppColors.surface(context),
            elevation: 0,
            scrolledUnderElevation: 0,
            title: Row(
              children: [
                Icon(
                  Icons.factory_rounded,
                  color: isDark ? AppColors.primaryFixedDim : AppColors.primary,
                  size: 24,
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
            actions: [
              if (_savedItems.isNotEmpty)
                TextButton(
                  onPressed: _clearAll,
                  child: const Text(
                    'Hapus Semua',
                    style: TextStyle(
                      color: AppColors.primary,
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              HoverableBuilder(
                onTap: () => ThemeManager.toggleTheme(),
                builder: (context, isHovered, isPressed) {
                  return Container(
                    margin: const EdgeInsets.only(right: 12),
                    padding: const EdgeInsets.all(8),
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
          body: _buildBody(),
        );
      },
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.primary),
      );
    }

    if (_savedItems.isEmpty) {
      return SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
        child: _buildEmptyState(context),
      );
    }

    return ListView.builder(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      itemCount: _savedItems.length,
      itemBuilder: (context, index) {
        final item = _savedItems[index];
        return _buildSavedCard(item);
      },
    );
  }

  Widget _buildSavedCard(KbliItem item) {
    final isCategory = item.kode.length == 2;
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: HoverableBuilder(
        onTap: () async {
          await Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => DetailScreen(
                categoryCode: item.kode,
                categoryTitle: item.judul,
              ),
            ),
          );
          loadSavedItems();
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
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: AppColors.savedGradient,
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    item.kode,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                      letterSpacing: 0.3,
                    ),
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        isCategory ? 'Kategori Utama' : 'Komoditas',
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: AppColors.primary,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        item.judul,
                        style: TextStyle(
                          fontSize: 14,
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
                IconButton(
                  icon: Icon(
                    Icons.delete_outline_rounded,
                    color: AppColors.outline(context),
                    size: 22,
                  ),
                  onPressed: () => _deleteItem(item.kode),
                  constraints: const BoxConstraints(),
                  padding: EdgeInsets.zero,
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildEmptyState(BuildContext context) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const SizedBox(height: 10),
        // Hero Bookmark Illustration Container
        HoverableBuilder(
          builder: (context, isHovered, isPressed) {
            return Stack(
              clipBehavior: Clip.none,
              children: [
                AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  width: 140,
                  height: 140,
                  decoration: BoxDecoration(
                    color: AppColors.surfaceContainerLowest(context),
                    borderRadius: BorderRadius.circular(40),
                    border: Border.all(
                      color: AppColors.outlineVariant(context).withOpacity(0.4),
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: AppColors.primaryContainer.withOpacity(
                          isHovered ? 0.25 : 0.15,
                        ),
                        blurRadius: isHovered ? 40 : 25,
                        offset: const Offset(0, 10),
                      ),
                    ],
                  ),
                  child: const Center(
                    child: Icon(
                      Icons.bookmark_outline_rounded,
                      color: AppColors.primary,
                      size: 72,
                    ),
                  ),
                ),
                Positioned(
                  bottom: -6,
                  right: -6,
                  child: Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: AppColors.primaryContainer,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppColors.surface(context), width: 3),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.1),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        ),
                      ],
                    ),
                    child: const Icon(
                      Icons.search_rounded,
                      color: Colors.white,
                      size: 22,
                    ),
                  ),
                ),
              ],
            );
          },
        ),
        const SizedBox(height: 32),
        Text(
          'Simpanan Masih Kosong',
          textAlign: TextAlign.center,
          style: TextStyle(
            fontSize: 22,
            fontWeight: FontWeight.w800,
            color: AppColors.onSurface(context),
            letterSpacing: -0.3,
          ),
        ),
        const SizedBox(height: 10),
        ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 340),
          child: Text(
            'Anda belum menyimpan kode KBLI apa pun. Simpan item penting untuk akses cepat saat mengurus perizinan usaha Anda.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 14,
              color: AppColors.onSurfaceVariant(context),
              height: 1.5,
            ),
          ),
        ),
        const SizedBox(height: 28),
        HoverableBuilder(
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const SearchScreen()),
            );
          },
          builder: (context, isHovered, isPressed) {
            return AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: AppColors.savedGradient,
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(30),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withOpacity(isHovered ? 0.4 : 0.25),
                    blurRadius: isHovered ? 18 : 10,
                    offset: const Offset(0, 5),
                  ),
                ],
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.explore_rounded, color: Colors.white, size: 20),
                  SizedBox(width: 8),
                  Text(
                    'Mulai Cari KBLI',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            );
          },
        ),
        const SizedBox(height: 48),

        // Decorative Card Tips & Riwayat
        Row(
          children: [
            Expanded(
              child: _buildTipCard(
                context,
                icon: Icons.lightbulb_outline_rounded,
                title: 'Tips',
                description:
                    'Gunakan fitur pencarian untuk menemukan kategori usaha yang tepat.',
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildTipCard(
                context,
                icon: Icons.history_rounded,
                title: 'Riwayat',
                description:
                    'Akses kembali pencarian terakhir Anda di halaman Beranda.',
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildTipCard(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String description,
  }) {
    return HoverableBuilder(
      builder: (context, isHovered, isPressed) {
        return AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: AppColors.surfaceContainerLow(context),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: isHovered
                  ? AppColors.primary.withOpacity(0.3)
                  : AppColors.outlineVariant(context).withOpacity(0.2),
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(icon, color: AppColors.primary, size: 18),
                  const SizedBox(width: 6),
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: AppColors.onSurface(context),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                description,
                style: TextStyle(
                  fontSize: 11,
                  color: AppColors.onSurfaceVariant(context),
                  height: 1.35,
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
