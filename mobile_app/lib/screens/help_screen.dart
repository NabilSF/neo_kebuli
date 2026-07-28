import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class HelpScreen extends StatelessWidget {
  const HelpScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final faqs = [
      {
        'q': 'Apa itu KBLI?',
        'a':
            'KBLI (Klasifikasi Baku Lapangan Usaha Indonesia) adalah panduan resmi dari BPS RI yang mengklasifikasikan jenis-jenis kegiatan ekonomi atau usaha di Indonesia.',
      },
      {
        'q': 'Bagaimana cara mencari kode KBLI?',
        'a':
            'Gunakan fitur pencarian dan ketik minimal 3 karakter berupa nama kegiatan atau kode KBLI. Hasil akan muncul secara otomatis.',
      },
      {
        'q': 'Apa perbedaan kategori utama dan komoditas?',
        'a':
            'Kategori utama ditandai dengan 2 digit angka (Golongan Pokok), sedangkan komoditas/sub-kategori ditandai dengan 5 digit angka.',
      },
      {
        'q': 'Data ini dari sumber mana?',
        'a':
            'Semua data bersumber dari Web API resmi BPS RI (Badan Pusat Statistik) dan disesuaikan dengan data offline KBLI Edisi 2025.',
      },
      {
        'q': 'Bisakah saya menyimpan kode favorit?',
        'a':
            'Ya! Tekan ikon bookmark di halaman detail untuk menyimpan kode KBLI yang sering Anda gunakan.',
      },
    ];

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: Text('Bantuan & FAQ')),
      body: ListView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.all(20),
        children: [
          // Header card
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFFFF7518), Color(0xFFFFB347)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(
                  color: AppColors.primary.withOpacity(0.3),
                  blurRadius: 20,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: Row(
              children: [
                Container(
                  width: 52,
                  height: 52,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.25),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(
                    Icons.help_outline_rounded,
                    color: Colors.white,
                    size: 28,
                  ),
                ),
                const SizedBox(width: 16),
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Pusat Bantuan',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'Temukan jawaban untuk pertanyaan umum Anda.',
                        style: TextStyle(
                          color: Colors.white70,
                          fontSize: 12,
                          height: 1.4,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 24),

          Text(
            'Pertanyaan Umum',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: AppColors.slate900,
            ),
          ),
          const SizedBox(height: 12),

          ...faqs.map((faq) => _buildFaqItem(faq['q']!, faq['a']!)),

          const SizedBox(height: 20),

          // Contact card
          Container(
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
            child: Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: AppColors.primarySurface,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(Icons.mail_outline_rounded,
                      color: AppColors.primary, size: 22),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Masih ada pertanyaan?',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: AppColors.slate900,
                        ),
                      ),
                      SizedBox(height: 2),
                      Text(
                        'Hubungi kami melalui email',
                        style: TextStyle(
                          fontSize: 12,
                          color: AppColors.slate400,
                        ),
                      ),
                    ],
                  ),
                ),
                Icon(Icons.arrow_forward_ios_rounded,
                    size: 14, color: AppColors.slate400),
              ],
            ),
          ),

          const SizedBox(height: 12),

          Center(
            child: Text(
              'New Kebuli v1.0.0 · Powered by BPS RI',
              style: TextStyle(
                fontSize: 11,
                color: AppColors.slate400.withOpacity(0.7),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFaqItem(String question, String answer) {
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
      child: ExpansionTile(
        tilePadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 4),
        childrenPadding:
            const EdgeInsets.fromLTRB(18, 0, 18, 16),
        iconColor: AppColors.primary,
        collapsedIconColor: AppColors.slate400,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        collapsedShape:
            RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        leading: Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            color: AppColors.primarySurface,
            borderRadius: BorderRadius.circular(8),
          ),
          child: const Center(
            child: Icon(Icons.quiz_rounded, size: 16, color: AppColors.primary),
          ),
        ),
        title: Text(
          question,
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: AppColors.slate900,
          ),
        ),
        children: [
          Text(
            answer,
            style: TextStyle(
              fontSize: 13,
              color: AppColors.slate700,
              height: 1.6,
            ),
          ),
        ],
      ),
    );
  }
}
