import 'package:flutter/material.dart';

import 'features/auth/auth_controller.dart';
import 'features/auth/login_screen.dart';
import 'features/home/home_shell.dart';
import 'core/theme/vibe_theme.dart';

class VibeBloomApp extends StatelessWidget {
  const VibeBloomApp({required this.auth, super.key});

  final AuthController auth;
  static final navigatorKey = GlobalKey<NavigatorState>();

  @override
  Widget build(BuildContext context) {
    auth.onSessionExit =
        () => WidgetsBinding.instance.addPostFrameCallback((_) {
              navigatorKey.currentState?.popUntil((route) => route.isFirst);
            });
    return MaterialApp(
      navigatorKey: navigatorKey,
      title: 'VibeBloom',
      debugShowCheckedModeBanner: false,
      theme: vibeTheme(),
      home: ListenableBuilder(
        listenable: auth,
        builder: (context, _) =>
            auth.canEnterApp ? HomeShell(auth: auth) : LoginScreen(auth: auth),
      ),
    );
  }
}
