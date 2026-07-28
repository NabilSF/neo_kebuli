import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../theme/app_theme.dart';
import '../widgets/common_widgets.dart';
import 'home_screen.dart';
import 'saved_screen.dart';
import 'help_screen.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;
  final GlobalKey<SavedScreenState> _savedScreenKey = GlobalKey<SavedScreenState>();

  @override
  Widget build(BuildContext context) {
    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));

    return ValueListenableBuilder<ThemeMode>(
      valueListenable: ThemeManager.themeModeNotifier,
      builder: (context, themeMode, _) {
        final screens = [
          const HomeScreen(),
          SavedScreen(key: _savedScreenKey),
          const HelpScreen(),
        ];

        return Scaffold(
          body: IndexedStack(
            key: ValueKey('indexed_stack_$themeMode'),
            index: _selectedIndex,
            children: screens,
          ),
          bottomNavigationBar: _buildBottomNav(context),
        );
      },
    );
  }

  Widget _buildBottomNav(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface(context),
        border: Border(
          top: BorderSide(
            color: AppColors.outlineVariant(context).withOpacity(0.3),
            width: 1,
          ),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 16,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildNavItem(context, 0, Icons.home_rounded, Icons.home_outlined, 'Beranda'),
              _buildNavItem(context, 1, Icons.bookmark_rounded, Icons.bookmark_border_rounded, 'Simpan'),
              _buildNavItem(context, 2, Icons.help_rounded, Icons.help_outline_rounded, 'Bantuan'),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(BuildContext context, int index, IconData activeIcon, IconData inactiveIcon, String label) {
    final isActive = _selectedIndex == index;
    return HoverableBuilder(
      onTap: () {
        setState(() {
          _selectedIndex = index;
        });
        if (index == 1) {
          _savedScreenKey.currentState?.loadSavedItems();
        }
      },
      builder: (context, isHovered, isPressed) {
        return AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
          decoration: BoxDecoration(
            color: isActive
                ? AppColors.primaryContainer.withOpacity(0.12)
                : (isHovered ? AppColors.primary.withOpacity(0.06) : Colors.transparent),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                isActive ? activeIcon : inactiveIcon,
                color: isActive ? AppColors.primary : AppColors.onSurfaceVariant(context),
                size: 22,
              ),
              const SizedBox(height: 4),
              Text(
                label,
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: isActive ? FontWeight.w700 : FontWeight.w500,
                  color: isActive ? AppColors.primary : AppColors.onSurfaceVariant(context),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
