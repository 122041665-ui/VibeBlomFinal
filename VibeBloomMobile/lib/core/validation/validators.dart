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

  static String? requiredPassword(String? value) {
    if (value == null || value.isEmpty) return 'Ingresa tu contraseña.';
    return null;
  }

  static String? name(String? value) {
    final text = value?.trim() ?? '';
    if (text.isEmpty) return 'Ingresa tu nombre.';
    if (text.length > 255) return 'El nombre no puede exceder 255 caracteres.';
    return null;
  }

  static String? strongPassword(String? value) {
    final base = password(value);
    if (base != null) return base;
    if (!RegExp(r'[A-Z]').hasMatch(value!)) {
      return 'Incluye al menos una mayúscula.';
    }
    if (!RegExp(r'[a-z]').hasMatch(value)) {
      return 'Incluye al menos una minúscula.';
    }
    if (!RegExp(r'\d').hasMatch(value)) return 'Incluye al menos un número.';
    return null;
  }
}
