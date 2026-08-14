import 'package:flutter/material.dart';

class VibeColors {
  static const navy = Color(0xFF172554);
  static const blue = Color(0xFF2563EB);
  static const blueDark = Color(0xFF1D4ED8);
  static const sky = Color(0xFF38BDF8);
  static const coral = Color(0xFFF05A47);
  static const soft = Color(0xFFF7F9FE);
  static const border = Color(0xFFE8EEF8);
}

ThemeData vibeTheme() {
  final scheme = ColorScheme.fromSeed(
          seedColor: VibeColors.blue, brightness: Brightness.light)
      .copyWith(
    primary: VibeColors.blue,
    secondary: VibeColors.coral,
    surface: Colors.white,
  );
  return ThemeData(
    colorScheme: scheme,
    scaffoldBackgroundColor: VibeColors.soft,
    useMaterial3: true,
    fontFamily: 'Roboto',
    appBarTheme: const AppBarTheme(
        backgroundColor: Colors.white,
        foregroundColor: Color(0xFF0F172A),
        elevation: 0,
        surfaceTintColor: Colors.transparent),
    snackBarTheme: SnackBarThemeData(
      backgroundColor: Colors.white,
      contentTextStyle:
          const TextStyle(color: VibeColors.navy, fontWeight: FontWeight.w700),
      actionTextColor: VibeColors.blue,
      behavior: SnackBarBehavior.floating,
      elevation: 10,
      insetPadding: const EdgeInsets.all(16),
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: VibeColors.border)),
    ),
    inputDecorationTheme: InputDecorationTheme(
      errorMaxLines: 6,
      helperMaxLines: 4,
      errorStyle: const TextStyle(
          color: Color(0xFFBE123C), fontWeight: FontWeight.w700, height: 1.35),
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: VibeColors.border)),
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: VibeColors.border)),
      focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: VibeColors.blue, width: 1.6)),
    ),
    cardTheme: CardThemeData(
      color: Colors.white,
      elevation: 0,
      margin: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(26),
          side: const BorderSide(color: VibeColors.border)),
    ),
    navigationBarTheme: NavigationBarThemeData(
      backgroundColor: Colors.white,
      indicatorColor: const Color(0xFFDBEAFE),
      labelTextStyle: WidgetStateProperty.resolveWith((states) => TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w700,
          color: states.contains(WidgetState.selected)
              ? VibeColors.blueDark
              : const Color(0xFF64748B))),
      iconTheme: WidgetStateProperty.resolveWith((states) => IconThemeData(
          color: states.contains(WidgetState.selected)
              ? VibeColors.blue
              : const Color(0xFF64748B))),
    ),
    filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
            backgroundColor: VibeColors.blue,
            foregroundColor: Colors.white,
            minimumSize: const Size(0, 48),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            textStyle: const TextStyle(fontWeight: FontWeight.w800))),
  );
}
