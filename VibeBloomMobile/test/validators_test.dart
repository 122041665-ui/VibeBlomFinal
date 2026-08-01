import 'package:flutter_test/flutter_test.dart';
import 'package:vibebloom_mobile/core/validation/validators.dart';

void main() {
  test('rechaza correos inválidos', () {
    expect(Validators.email('sin-arroba'), isNotNull);
    expect(Validators.email('usuario@vibebloom.mx'), isNull);
  });

  test('exige contraseñas de al menos ocho caracteres', () {
    expect(Validators.password('1234567'), isNotNull);
    expect(Validators.password('segura123'), isNull);
  });
}
