import 'package:flutter/material.dart';

import 'features/auth/auth_controller.dart';
import 'features/auth/login_screen.dart';
import 'features/home/home_shell.dart';

class VibeBloomApp extends StatelessWidget {
  const VibeBloomApp({required this.auth, super.key});

  final AuthController auth;

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VibeBloom',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF665CF6),
          brightness: Brightness.light,
        ),
        useMaterial3: true,
        inputDecorationTheme: const InputDecorationTheme(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.all(Radius.circular(16)),
          ),
        ),
        cardTheme: const CardThemeData(
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(20)),
          ),
        ),
      ),
      home: ListenableBuilder(
        listenable: auth,
        builder: (context, _) => auth.isAuthenticated
            ? HomeShell(auth: auth)
            : LoginScreen(auth: auth),
      ),
    );
  }
}
