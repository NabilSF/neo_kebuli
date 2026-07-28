import 'package:flutter/material.dart';

class ThemeManager {
  static final ValueNotifier<ThemeMode> themeModeNotifier =
      ValueNotifier<ThemeMode>(ThemeMode.light);

  static bool get isDark => themeModeNotifier.value == ThemeMode.dark;

  static void toggleTheme() {
    themeModeNotifier.value = isDark ? ThemeMode.light : ThemeMode.dark;
  }
}

class AppColors {
  // Primary - Orange Blaze
  static const primary = Color(0xFFFF7518);
  static const primaryLight = Color(0xFFFF9A52);
  static const primaryDark = Color(0xFFCC5A0E);
  static Color get primarySurface =>
      ThemeManager.isDark ? const Color(0xFF2C1A10) : const Color(0xFFFFF3EB);

  // Dynamically resolved colors depending on theme mode
  static Color get background =>
      ThemeManager.isDark ? const Color(0xFF0F172A) : const Color(0xFFF5F6FA);
  static Color get surface =>
      ThemeManager.isDark ? const Color(0xFF1E293B) : Colors.white;
  static Color get slate900 =>
      ThemeManager.isDark ? const Color(0xFFF8FAFC) : const Color(0xFF0F172A);
  static Color get slate700 =>
      ThemeManager.isDark ? const Color(0xFFCBD5E1) : const Color(0xFF334155);
  static Color get slate400 =>
      ThemeManager.isDark ? const Color(0xFF64748B) : const Color(0xFF94A3B8);
  static Color get slate200 =>
      ThemeManager.isDark ? const Color(0xFF334155) : const Color(0xFFE2E8F0);
  static Color get slate100 =>
      ThemeManager.isDark ? const Color(0xFF1E293B) : const Color(0xFFF1F5F9);

  // Gradient
  static const List<Color> heroGradient = [
    Color(0xFFFF7518),
    Color(0xFFFF9A52),
    Color(0xFFFFB347),
  ];
}

class AppTheme {
  static ThemeData get lightTheme => ThemeData(
        useMaterial3: true,
        brightness: Brightness.light,
        primaryColor: AppColors.primary,
        scaffoldBackgroundColor: const Color(0xFFF5F6FA),
        cardColor: Colors.white,
        colorScheme: ColorScheme.fromSeed(
          seedColor: AppColors.primary,
          primary: AppColors.primary,
          brightness: Brightness.light,
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Colors.white,
          elevation: 0,
          scrolledUnderElevation: 0,
          titleTextStyle: TextStyle(
            color: Color(0xFF0F172A),
            fontSize: 18,
            fontWeight: FontWeight.w700,
            letterSpacing: -0.3,
          ),
          iconTheme: IconThemeData(color: Color(0xFF0F172A)),
        ),
        fontFamily: 'Roboto',
      );

  static ThemeData get darkTheme => ThemeData(
        useMaterial3: true,
        brightness: Brightness.dark,
        primaryColor: AppColors.primary,
        scaffoldBackgroundColor: const Color(0xFF0F172A),
        cardColor: const Color(0xFF1E293B),
        colorScheme: ColorScheme.fromSeed(
          seedColor: AppColors.primary,
          primary: AppColors.primary,
          brightness: Brightness.dark,
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF1E293B),
          elevation: 0,
          scrolledUnderElevation: 0,
          titleTextStyle: TextStyle(
            color: Color(0xFFF8FAFC),
            fontSize: 18,
            fontWeight: FontWeight.w700,
            letterSpacing: -0.3,
          ),
          iconTheme: IconThemeData(color: Color(0xFFF8FAFC)),
        ),
        fontFamily: 'Roboto',
      );
}
