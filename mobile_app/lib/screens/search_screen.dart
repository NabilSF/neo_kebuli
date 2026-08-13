// ignore_for_file: prefer_const_constructors

import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../theme/app_theme.dart';
import '../models/kbli_model.dart';
import '../services/bps_api_service.dart';
import '../widgets/common_widgets.dart';
import 'detail_screen.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final _api = BpsApiService();
  final _searchController = TextEditingController();
  final _focusNode = FocusNode();

  List<KbliItem> _results = [];
  bool _isSearching = false;
  bool _hasSearched = false;
  String _selectedCategory = 'C'; // Category C Industri Pengolahan
  List<String> _recentSearches = [];

  @override
  void initState() {
    super.initState();
    _loadRecentSearches();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _focusNode.requestFocus();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    _focusNode.dispose();
    super.dispose();
  }

  Future<void> _loadRecentSearches() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _recentSearches = prefs.getStringList('recent_searches') ?? [];
    });
  }

  Future<void> _saveRecentSearch(String query) async {
    final cleanQuery = query.trim();
    if (cleanQuery.isEmpty) return;

    _recentSearches.remove(cleanQuery);
    _recentSearches.insert(0, cleanQuery);

    if (_recentSearches.length > 5) {
      _recentSearches = _recentSearches.sublist(0, 5);
    }

    final prefs = await SharedPreferences.getInstance();
    await prefs.setStringList('recent_searches', _recentSearches);
    if (mounted) setState(() {});
  }

  Future<void> _deleteRecentSearch(String query) async {
    _recentSearches.remove(query);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setStringList('recent_searches', _recentSearches);
    if (mounted) setState(() {});
  }

  Future<void> _clearAllRecentSearches() async {
    _recentSearches.clear();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('recent_searches');
    if (mounted) setState(() {});
  }

  Future<void> _search(String query, {String? category}) async {
    final targetCat = category ?? _selectedCategory;
    final clean = query.trim();

    setState(() {
      _isSearching = true;
      _hasSearched = true;
      _selectedCategory = targetCat;
    });

    if (clean.isNotEmpty) {
      _saveRecentSearch(clean);
    }

    final results = await _api.search(keyword: clean, categoryFilter: targetCat);

    if (mounted) {
      setState(() {
        _results = results;
        _isSearching = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background(context),
      appBar: AppBar(
        backgroundColor: AppColors.surface(context),
        elevation: 0,
        leadingWidth: 48,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
          onPressed: () => Navigator.pop(context),
        ),
        title: KbliSearchBar(
          controller: _searchController,
          onChanged: (val) {
            if (val.length >= 2 || _selectedCategory != 'ALL') {
              _search(val);
            } else if (val.isEmpty) {
              setState(() {
                _results = [];
                _hasSearched = false;
              });
            }
          },
          onSubmit: () => _search(_searchController.text),
        ),
        titleSpacing: 0,
        actions: const [SizedBox(width: 16)],
      ),
      body: Column(
        children: [
          // Filter Chips (ALL, A, C)
          _buildFilterChips(context),
          Expanded(child: _buildBody(context)),
        ],
      ),
    );
  }

  Widget _buildFilterChips(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      color: AppColors.surface(context),
      child: Row(
        children: [
          _buildChipItem(context, key: 'ALL', label: 'Semua', icon: Icons.grid_view_rounded),
          const SizedBox(width: 8),
          _buildChipItem(context, key: 'A', label: 'Kategori A', icon: Icons.eco_rounded, isCatA: true),
          const SizedBox(width: 8),
          _buildChipItem(context, key: 'C', label: 'Kategori C', icon: Icons.factory_rounded),
        ],
      ),
    );
  }

  Widget _buildChipItem(
    BuildContext context, {
    required String key,
    required String label,
    required IconData icon,
    bool isCatA = false,
  }) {
    final isSelected = _selectedCategory == key;
    final activeColor = isCatA ? const Color(0xFF059669) : AppColors.primary;

    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedCategory = key;
        });
        _search(_searchController.text, category: key);
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
        decoration: BoxDecoration(
          color: isSelected
              ? activeColor
              : AppColors.surfaceContainerLow(context),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isSelected
                ? activeColor
                : AppColors.outlineVariant(context).withOpacity(0.4),
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              size: 13,
              color: isSelected ? Colors.white : activeColor,
            ),
            const SizedBox(width: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w600,
                color: isSelected ? Colors.white : AppColors.slate700(context),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBody(BuildContext context) {
    if (!_hasSearched) {
      return _buildInitialState(context);
    }

    if (_isSearching) {
      return ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 5,
        itemBuilder: (_, __) => const SkeletonCard(),
      );
    }

    if (_results.isEmpty) {
      return const EmptyStateWidget(
        title: 'Hasil Tidak Ditemukan',
        subtitle: 'Coba kata kunci lain atau gunakan kode KBLI yang lebih spesifik.',
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(20),
      itemCount: _results.length,
      itemBuilder: (context, index) {
        final item = _results[index];
        return _buildResultCard(context, item);
      },
    );
  }

  Widget _buildInitialState(BuildContext context) {
    final suggestions = [
      'Pertanian', 'Konstruksi', 'Perdagangan', 'Pendidikan', 'Teknologi',
      'Pariwisata', 'Manufaktur', 'Keuangan',
    ];

    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (_recentSearches.isNotEmpty) ...[
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Pencarian Terbaru',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppColors.slate900(context),
                  ),
                ),
                TextButton(
                  onPressed: _clearAllRecentSearches,
                  style: TextButton.styleFrom(
                    padding: EdgeInsets.zero,
                    minimumSize: Size.zero,
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  ),
                  child: const Text(
                    'Hapus Semua',
                    style: TextStyle(
                      fontSize: 12,
                      color: AppColors.primary,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Column(
              children: _recentSearches.map((query) {
                return Container(
                  margin: const EdgeInsets.only(bottom: 8),
                  decoration: BoxDecoration(
                    color: AppColors.surfaceContainerLow(context),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.slate200(context)),
                  ),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 0),
                    dense: true,
                    leading: Icon(Icons.history_rounded, color: AppColors.slate400(context), size: 18),
                    title: Text(
                      query,
                      style: TextStyle(
                        fontSize: 13,
                        color: AppColors.slate700(context),
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    trailing: IconButton(
                      icon: Icon(Icons.close_rounded, color: AppColors.slate400(context), size: 16),
                      onPressed: () => _deleteRecentSearch(query),
                      padding: EdgeInsets.zero,
                      constraints: const BoxConstraints(),
                    ),
                    onTap: () {
                      _searchController.text = query;
                      _search(query);
                    },
                  ),
                );
              }).toList(),
            ),
            const SizedBox(height: 24),
          ],
          Text(
            'Saran Pencarian',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: AppColors.slate900(context),
            ),
          ),
          const SizedBox(height: 14),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: suggestions.map((s) {
              return GestureDetector(
                onTap: () {
                  _searchController.text = s;
                  _search(s);
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                    color: AppColors.surfaceContainerLow(context),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: AppColors.slate200(context)),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.04),
                        blurRadius: 6,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(Icons.trending_up_rounded,
                          size: 14, color: AppColors.primary),
                      const SizedBox(width: 6),
                      Text(
                        s,
                        style: TextStyle(
                          fontSize: 13,
                          color: AppColors.slate700(context),
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }).toList(),
          ),
          const SizedBox(height: 28),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerHighest(context),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.primary.withOpacity(0.15)),
            ),
            child: Row(
              children: [
                const Icon(Icons.lightbulb_outline_rounded,
                    color: AppColors.primary, size: 22),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Tips: Ketik minimal 3 karakter untuk mulai mencari kode KBLI.',
                    style: TextStyle(
                      fontSize: 13,
                      color: AppColors.slate700(context),
                      height: 1.4,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildResultCard(BuildContext context, KbliItem item) {
    String productsText = '';
    if (item.produk.isNotEmpty) {
      productsText = item.produk.join(', ');
    } else {
      productsText = item.judul;
    }

    // Clean up duplicate commas or extra whitespace
    productsText = productsText
        .replaceAll(RegExp(r',\s*,'), ',')
        .replaceAll(RegExp(r'\s+'), ' ')
        .trim();

    final currentQuery = _searchController.text;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        InkWell(
          onTap: () => Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => DetailScreen(
                categoryCode: item.kode,
                categoryTitle: item.judul,
                searchQuery: currentQuery,
              ),
            ),
          ),
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    HighlightedText(
                      text: item.kode,
                      query: currentQuery,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: AppColors.slate900(context),
                        letterSpacing: -0.2,
                      ),
                    ),
                    const SizedBox(width: 8),
                    CategoryBadgeTag(category: item.kategori, isSmall: true),
                  ],
                ),
                const SizedBox(height: 6),
                HighlightedText(
                  text: productsText,
                  query: currentQuery,
                  style: TextStyle(
                    fontSize: 13.5,
                    fontWeight: FontWeight.w500,
                    color: AppColors.slate700(context),
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
        ),
        Divider(
          height: 1,
          thickness: 1,
          color: AppColors.slate200(context).withOpacity(0.6),
          indent: 20,
          endIndent: 20,
        ),
      ],
    );
  }
}
