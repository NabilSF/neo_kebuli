import 'package:flutter/material.dart';

class ThemeManager {
  static final ValueNotifier<ThemeMode> themeModeNotifier =
      ValueNotifier<ThemeMode>(ThemeMode.light);

  static bool isDark(BuildContext context) {
    return Theme.of(context).brightness == Brightness.dark;
  }

  static void toggleTheme() {
    themeModeNotifier.value =
        themeModeNotifier.value == ThemeMode.dark ? ThemeMode.light : ThemeMode.dark;
  }
}

class AppColors {
  // Primary - KBLI Warm Amber & Orange
  static const primary = Color(0xFF904D00);
  static const primaryContainer = Color(0xFFFF8C00);
  static const onPrimaryContainer = Color(0xFF623200);
  static const primaryFixed = Color(0xFFFFDCC3);
  static const primaryFixedDim = Color(0xFFFFB77D);
  static const tertiaryFixed = Color(0xFFC7E7FF);
  static const tertiary = Color(0xFF00658F);

  // Dynamic colors resolved strictly via BuildContext
  static Color background(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF1B130E) : const Color(0xFFFFF8F5);

  static Color surface(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF241912) : const Color(0xFFFFF8F5);

  static Color surfaceContainerLowest(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF2E221B) : Colors.white;

  static Color surfaceContainerLow(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF2D231C) : const Color(0xFFFFF1E9);

  static Color surfaceContainer(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF362920) : const Color(0xFFFFEADD);

  static Color surfaceContainerHigh(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF403026) : const Color(0xFFF9E4D7);

  static Color surfaceContainerHighest(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF4A382C) : const Color(0xFFF3DFD1);

  static Color onSurface(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFFFFEDE3) : const Color(0xFF241912);

  static Color onSurfaceVariant(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFFDDC1AE) : const Color(0xFF564334);

  static Color outlineVariant(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF564334) : const Color(0xFFDDC1AE);

  static Color outline(BuildContext context) =>
      ThemeManager.isDark(context) ? const Color(0xFF705B4B) : const Color(0xFF897362);

  // Legacy mappings with BuildContext
  static Color slate900(BuildContext context) => onSurface(context);
  static Color slate700(BuildContext context) => onSurfaceVariant(context);
  static Color slate400(BuildContext context) => outline(context);
  static Color slate200(BuildContext context) => outlineVariant(context);
  static Color slate100(BuildContext context) => surfaceContainerLow(context);

  // Gradients
  static const List<Color> heroGradient = [
    Color(0xFF904D00),
    Color(0xFFFF8C00),
  ];

  static const List<Color> savedGradient = [
    Color(0xFFFF8C00),
    Color(0xFF904D00),
  ];
}

class AppTheme {
  static ThemeData get lightTheme => ThemeData(
        useMaterial3: true,
        brightness: Brightness.light,
        primaryColor: AppColors.primary,
        scaffoldBackgroundColor: const Color(0xFFFFF8F5),
        cardColor: Colors.white,
        colorScheme: const ColorScheme.light(
          primary: AppColors.primary,
          primaryContainer: AppColors.primaryContainer,
          surface: Color(0xFFFFF8F5),
          onSurface: Color(0xFF241912),
          onSurfaceVariant: Color(0xFF564334),
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFFFFF8F5),
          elevation: 0,
          scrolledUnderElevation: 0,
          titleTextStyle: TextStyle(
            color: Color(0xFF904D00),
            fontSize: 20,
            fontWeight: FontWeight.w700,
            letterSpacing: -0.3,
          ),
          iconTheme: IconThemeData(color: Color(0xFF904D00)),
        ),
        fontFamily: 'Roboto',
      );

  static ThemeData get darkTheme => ThemeData(
        useMaterial3: true,
        brightness: Brightness.dark,
        primaryColor: AppColors.primaryFixedDim,
        scaffoldBackgroundColor: const Color(0xFF1B130E),
        cardColor: const Color(0xFF2E221B),
        colorScheme: const ColorScheme.dark(
          primary: AppColors.primaryFixedDim,
          primaryContainer: AppColors.primaryContainer,
          surface: Color(0xFF241912),
          onSurface: Color(0xFFFFEDE3),
          onSurfaceVariant: Color(0xFFDDC1AE),
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF241912),
          elevation: 0,
          scrolledUnderElevation: 0,
          titleTextStyle: TextStyle(
            color: Color(0xFFFFB77D),
            fontSize: 20,
            fontWeight: FontWeight.w700,
            letterSpacing: -0.3,
          ),
          iconTheme: IconThemeData(color: Color(0xFFFFB77D)),
        ),
        fontFamily: 'Roboto',
      );
}
