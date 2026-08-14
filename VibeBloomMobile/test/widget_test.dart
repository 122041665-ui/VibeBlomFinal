import 'package:flutter_test/flutter_test.dart';
import 'package:vibebloom_mobile/app.dart';
import 'package:vibebloom_mobile/features/auth/auth_controller.dart';

void main() {
  testWidgets('muestra el acceso de VibeBloom', (tester) async {
    await tester.pumpWidget(VibeBloomApp(auth: AuthController()));
    expect(find.bySemanticsLabel('VibeBloom'), findsOneWidget);
    expect(find.text('Iniciar sesión'), findsNWidgets(2));
    expect(find.text('Crear cuenta'), findsOneWidget);
  });
}
