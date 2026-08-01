import 'package:flutter/material.dart';

import 'app.dart';
import 'features/auth/auth_controller.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final auth = AuthController();
  await auth.restoreSession();
  runApp(VibeBloomApp(auth: auth));
}
