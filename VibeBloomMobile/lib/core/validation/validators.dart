class Validators {
  const Validators._();

  static String? email(String? value) {
    final text = value?.trim() ?? '';
    if (text.isEmpty) return 'Ingresa tu correo electrónico.';
    if (text.length > 254) return 'El correo es demasiado largo.';
    if (!RegExp(r'^[^\s@]+@[^\s@]+\.[^\s@]+$').hasMatch(text)) {
      return 'Ingresa un correo válido.';
    }
    return null;
  }

  static String? password(String? value) {
    if (value == null || value.isEmpty) return 'Ingresa tu contraseña.';
    if (value.length < 8) return 'Debe tener al menos 8 caracteres.';
    if (value.length > 128) return 'La contraseña es demasiado larga.';
    return null;
  }
}
