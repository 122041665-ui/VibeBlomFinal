import 'package:flutter/material.dart';

import '../../core/theme/vibe_theme.dart';
import '../../core/validation/validators.dart';
import '../../core/widgets/vibe_logo.dart';
import 'auth_controller.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({required this.auth, super.key});
  final AuthController auth;

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _password = TextEditingController();
  bool _hidePassword = true;
  bool _remember = false;

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;
    await widget.auth.login(_email.text, _password.text);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        body: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 32),
              child: Container(
                constraints: const BoxConstraints(maxWidth: 440),
                padding: const EdgeInsets.all(26),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: .96),
                  borderRadius: BorderRadius.circular(28),
                  border: Border.all(color: Colors.white),
                  boxShadow: const [
                    BoxShadow(
                        color: Color(0x1A0F172A),
                        blurRadius: 40,
                        offset: Offset(0, 20)),
                  ],
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const Center(child: VibeLogo()),
                        const SizedBox(height: 22),
                        const Text('Iniciar sesión',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                fontSize: 25,
                                fontWeight: FontWeight.w900,
                                color: VibeColors.navy)),
                        const SizedBox(height: 6),
                        const Text(
                            'Accede para guardar favoritos, recuerdos y ver detalles.',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                color: Color(0xFF64748B), height: 1.4)),
                        const SizedBox(height: 26),
                        const Text('Correo electrónico',
                            style: TextStyle(fontWeight: FontWeight.w800)),
                        const SizedBox(height: 6),
                        TextFormField(
                          controller: _email,
                          keyboardType: TextInputType.emailAddress,
                          autofillHints: const [AutofillHints.email],
                          validator: Validators.email,
                          decoration: const InputDecoration(
                              hintText: 'tucorreo@ejemplo.com'),
                        ),
                        const Padding(
                            padding: EdgeInsets.only(top: 6),
                            child: Text(
                                'Usa el correo con el que te registraste.',
                                style: TextStyle(
                                    fontSize: 12, color: Color(0xFF64748B)))),
                        const SizedBox(height: 18),
                        const Text('Contraseña',
                            style: TextStyle(fontWeight: FontWeight.w800)),
                        const SizedBox(height: 6),
                        TextFormField(
                          controller: _password,
                          obscureText: _hidePassword,
                          autofillHints: const [AutofillHints.password],
                          validator: Validators.requiredPassword,
                          onFieldSubmitted: (_) => _submit(),
                          decoration: InputDecoration(
                            hintText: '••••••••',
                            suffixIcon: IconButton(
                              onPressed: () => setState(
                                  () => _hidePassword = !_hidePassword),
                              icon: Icon(_hidePassword
                                  ? Icons.visibility_outlined
                                  : Icons.visibility_off_outlined),
                              tooltip: _hidePassword
                                  ? 'Mostrar contraseña'
                                  : 'Ocultar contraseña',
                            ),
                          ),
                        ),
                        const Padding(
                            padding: EdgeInsets.only(top: 6),
                            child: Text(
                                'Puedes mostrar u ocultar tu contraseña.',
                                style: TextStyle(
                                    fontSize: 12, color: Color(0xFF64748B)))),
                        const SizedBox(height: 8),
                        Wrap(
                            alignment: WrapAlignment.spaceBetween,
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              Row(mainAxisSize: MainAxisSize.min, children: [
                                Checkbox(
                                    value: _remember,
                                    onChanged: (v) =>
                                        setState(() => _remember = v ?? false)),
                                const Text('Recuérdame',
                                    style: TextStyle(color: Color(0xFF64748B)))
                              ]),
                              TextButton(
                                  onPressed: () => _showInfo(
                                      context,
                                      'Recuperar contraseña',
                                      'La recuperación segura de contraseña estará disponible desde tu correo registrado.'),
                                  child: const Text('¿Olvidaste tu contraseña?',
                                      style: TextStyle(
                                          decoration:
                                              TextDecoration.underline))),
                            ]),
                        if (widget.auth.error != null)
                          _ErrorBox(widget.auth.error!),
                        const SizedBox(height: 12),
                        Row(children: [
                          Expanded(
                              child: OutlinedButton(
                                  onPressed: widget.auth.isLoading
                                      ? null
                                      : () {
                                          widget.auth.clearError();
                                          Navigator.push(
                                              context,
                                              MaterialPageRoute(
                                                  builder: (_) =>
                                                      RegisterScreen(
                                                          auth: widget.auth)));
                                        },
                                  child: const Text('Crear cuenta'))),
                          const SizedBox(width: 12),
                          Expanded(
                              child: FilledButton(
                                  onPressed:
                                      widget.auth.isLoading ? null : _submit,
                                  child: widget.auth.isLoading
                                      ? const SizedBox.square(
                                          dimension: 20,
                                          child: CircularProgressIndicator(
                                              strokeWidth: 2,
                                              color: Colors.white))
                                      : const Text('Iniciar sesión'))),
                        ]),
                        const SizedBox(height: 8),
                        TextButton(
                          onPressed: widget.auth.isLoading
                              ? null
                              : widget.auth.continueAsGuest,
                          child: const Text('Seguir explorando sin cuenta',
                              style: TextStyle(
                                  decoration: TextDecoration.underline)),
                        ),
                      ]),
                ),
              ),
            ),
          ),
        ),
      );
}

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({required this.auth, super.key});
  final AuthController auth;
  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _key = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _password = TextEditingController();
  final _confirmation = TextEditingController();
  bool _hidePassword = true;
  bool _hideConfirmation = true;
  bool _accepted = false;
  String? _termsError;

  @override
  void initState() {
    super.initState();
    widget.auth.clearError();
  }

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _password.dispose();
    _confirmation.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    final valid = _key.currentState!.validate();
    setState(() => _termsError = _accepted
        ? null
        : 'Debes aceptar los términos, las condiciones del servicio y el aviso de privacidad.');
    if (!valid || !_accepted) return;
    final ok =
        await widget.auth.register(_name.text, _email.text, _password.text);
    if (ok && mounted) Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
            leading: const BackButton(),
            title: const Center(child: VibeLogo(horizontal: true)),
            actions: const [SizedBox(width: 48)]),
        body: Form(
            key: _key,
            child: ListView(
                padding: const EdgeInsets.fromLTRB(22, 18, 22, 40),
                children: [
                  const Text('Crear cuenta',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                          fontSize: 25,
                          fontWeight: FontWeight.w900,
                          color: VibeColors.navy)),
                  const SizedBox(height: 6),
                  const Text(
                      'Únete a VibeBloom y guarda tus lugares favoritos.',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Color(0xFF64748B))),
                  const SizedBox(height: 26),
                  _label('Nombre'),
                  TextFormField(
                      controller: _name,
                      validator: Validators.name,
                      autofillHints: const [AutofillHints.name],
                      decoration: const InputDecoration(
                          hintText: 'Tu nombre completo')),
                  _hint(
                      'Escribe el nombre que usarás dentro de la plataforma.'),
                  _label('Correo electrónico'),
                  TextFormField(
                      controller: _email,
                      validator: Validators.email,
                      keyboardType: TextInputType.emailAddress,
                      autofillHints: const [AutofillHints.email],
                      decoration: const InputDecoration(
                          hintText: 'tucorreo@ejemplo.com')),
                  _hint('Usa un correo válido para acceder después.'),
                  _label('Contraseña'),
                  TextFormField(
                      controller: _password,
                      obscureText: _hidePassword,
                      validator: Validators.strongPassword,
                      decoration: InputDecoration(
                          hintText: '••••••••',
                          suffixIcon: IconButton(
                              tooltip: _hidePassword
                                  ? 'Mostrar contraseña'
                                  : 'Ocultar contraseña',
                              onPressed: () => setState(
                                  () => _hidePassword = !_hidePassword),
                              icon: Icon(_hidePassword
                                  ? Icons.visibility_outlined
                                  : Icons.visibility_off_outlined)))),
                  _hint(
                      'Mínimo 8 caracteres, con mayúscula, minúscula y número.'),
                  _label('Confirmar contraseña'),
                  TextFormField(
                      controller: _confirmation,
                      obscureText: _hideConfirmation,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Confirma tu contraseña.';
                        }
                        if (value != _password.text) {
                          return 'Las contraseñas no coinciden.';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                          hintText: '••••••••',
                          suffixIcon: IconButton(
                              tooltip: _hideConfirmation
                                  ? 'Mostrar confirmación'
                                  : 'Ocultar confirmación',
                              onPressed: () => setState(
                                  () => _hideConfirmation = !_hideConfirmation),
                              icon: Icon(_hideConfirmation
                                  ? Icons.visibility_outlined
                                  : Icons.visibility_off_outlined)))),
                  _hint(
                      'Debe coincidir exactamente con la contraseña anterior.'),
                  Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                          color: const Color(0xFFF8FAFC),
                          borderRadius: BorderRadius.circular(18),
                          border: Border.all(
                              color: _termsError == null
                                  ? VibeColors.border
                                  : Colors.red)),
                      child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Checkbox(
                                value: _accepted,
                                onChanged: (v) => setState(() {
                                      _accepted = v ?? false;
                                      if (_accepted) _termsError = null;
                                    })),
                            Expanded(
                                child: Wrap(children: [
                              const Text('Acepto los '),
                              _legalLink('Términos de uso', _terms),
                              const Text(', las '),
                              _legalLink(
                                  'Condiciones del servicio', _conditions),
                              const Text(' y el '),
                              _legalLink('Aviso de privacidad', _privacy),
                              const Text(
                                  '.\nDebes leerlos y aceptarlos para crear tu cuenta.',
                                  style: TextStyle(
                                      fontSize: 12, color: Color(0xFF64748B))),
                            ])),
                          ])),
                  if (_termsError != null)
                    Padding(
                        padding: const EdgeInsets.only(top: 6),
                        child: Text(_termsError!,
                            style: const TextStyle(
                                color: Colors.red, fontSize: 12))),
                  if (widget.auth.error != null) _ErrorBox(widget.auth.error!),
                  const SizedBox(height: 18),
                  FilledButton(
                      onPressed: widget.auth.isLoading ? null : _submit,
                      child: const Padding(
                          padding: EdgeInsets.symmetric(vertical: 13),
                          child: Text('Crear cuenta'))),
                  TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: const Text('¿Ya tienes cuenta? Inicia sesión',
                          style:
                              TextStyle(decoration: TextDecoration.underline))),
                ])),
      );

  Widget _label(String value) => Padding(
      padding: const EdgeInsets.only(top: 18, bottom: 6),
      child: Text(value, style: const TextStyle(fontWeight: FontWeight.w800)));
  Widget _hint(String value) => Padding(
      padding: const EdgeInsets.only(top: 6),
      child: Text(value,
          style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))));
  Widget _legalLink(String title, String body) => GestureDetector(
      onTap: () => _showLegal(context, title, body),
      child: Text(title,
          style: const TextStyle(
              color: VibeColors.blue,
              fontWeight: FontWeight.w800,
              decoration: TextDecoration.underline)));
}

class _ErrorBox extends StatelessWidget {
  const _ErrorBox(this.message);
  final String message;
  @override
  Widget build(BuildContext context) => Container(
      margin: const EdgeInsets.only(top: 14),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
          color: const Color(0xFFFEF2F2),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFFFECACA))),
      child: Text('Revisa lo siguiente:\n$message',
          style: const TextStyle(color: Color(0xFF991B1B))));
}

Future<void> _showInfo(BuildContext context, String title, String body) =>
    showDialog<void>(
        context: context,
        builder: (_) => AlertDialog(
                title: Text(title),
                content: Text(body),
                actions: [
                  TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: const Text('Cerrar'))
                ]));

Future<void> _showLegal(BuildContext context, String title, String body) =>
    showModalBottomSheet<void>(
        context: context,
        isScrollControlled: true,
        useSafeArea: true,
        builder: (_) => DraggableScrollableSheet(
            expand: false,
            initialChildSize: .88,
            maxChildSize: .95,
            builder: (context, controller) => Container(
                color: const Color(0xFFF8FAFC),
                padding: const EdgeInsets.fromLTRB(22, 12, 22, 16),
                child: Column(children: [
                  Container(
                      width: 42,
                      height: 5,
                      margin: const EdgeInsets.only(bottom: 16),
                      decoration: BoxDecoration(
                          color: const Color(0xFFCBD5E1),
                          borderRadius: BorderRadius.circular(20))),
                  Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                          gradient: const LinearGradient(
                              colors: [VibeColors.blue, VibeColors.navy]),
                          borderRadius: BorderRadius.circular(22)),
                      child: Row(children: [
                        const CircleAvatar(
                            backgroundColor: Colors.white24,
                            child: Icon(Icons.gavel_outlined,
                                color: Colors.white)),
                        const SizedBox(width: 12),
                        Expanded(
                            child: Text(title,
                                style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 20,
                                    fontWeight: FontWeight.w900))),
                        IconButton(
                            onPressed: () => Navigator.pop(context),
                            icon: const Icon(Icons.close, color: Colors.white),
                            tooltip: 'Cerrar documento')
                      ])),
                  const SizedBox(height: 12),
                  Expanded(
                      child: Container(
                          padding: const EdgeInsets.all(18),
                          decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(22),
                              border: Border.all(color: VibeColors.border)),
                          child: ListView(controller: controller, children: [
                            SelectableText(body,
                                style: const TextStyle(
                                    height: 1.6, color: Color(0xFF334155)))
                          ]))),
                  const SizedBox(height: 12),
                  SizedBox(
                      width: double.infinity,
                      child: FilledButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text('Entendido')))
                ]))));

const _terms = '''Términos de uso de VibeBloom

Última actualización: 15 de julio de 2026

Cuenta y seguridad

Debes proporcionar datos correctos, proteger tu contraseña y avisarnos si detectas un acceso no autorizado. Eres responsable de la actividad realizada desde tu cuenta.

Publicaciones y fotografías

Conservas la titularidad del contenido que publicas. Al enviarlo, autorizas a VibeBloom a almacenarlo y mostrarlo dentro de la plataforma para operar el servicio. Solo debes subir material propio o que tengas autorización para utilizar.

Los lugares creados por usuarios pueden permanecer pendientes hasta ser revisados. No se permite contenido ilegal, ofensivo, discriminatorio, engañoso, sexualmente explícito, violento, promocional no solicitado ni que exponga datos personales de terceros.

Reseñas y comunidad

Las reseñas deben reflejar experiencias honestas y expresarse con respeto. VibeBloom puede moderar, rechazar u ocultar publicaciones que incumplan las normas y restringir cuentas ante abuso reiterado.

Mapas e indicaciones

Las rutas, tiempos, precios, horarios y ubicaciones son informativos y pueden cambiar. Mantén atención al entorno y respeta señalización, restricciones y normas locales.''';
const _conditions =
    'Condiciones del servicio\n\nVibeBloom permite descubrir, guardar, reseñar y proponer lugares. La disponibilidad del servicio puede variar. Las propuestas son revisadas antes de publicarse y las decisiones administrativas se reflejan en tu historial de aprobaciones.';
const _privacy = '''Aviso de privacidad de VibeBloom

Última actualización: 15 de julio de 2026

Información tratada

Podemos tratar nombre, correo electrónico, contraseña cifrada, foto de perfil, publicaciones, fotografías, favoritos, reseñas, respuestas, recuerdos e información técnica básica de sesión.

La ubicación precisa se solicita únicamente cuando activas mapas o indicaciones. El dispositivo administra tu permiso y la ubicación se utiliza para calcular y actualizar la ruta solicitada.

Finalidades

Utilizamos la información para crear y proteger tu cuenta, mostrar contenido, guardar preferencias, operar funciones sociales, moderar publicaciones, prevenir abuso y mejorar la experiencia.

Conservación y seguridad

Conservamos la información mientras tu cuenta permanezca activa o sea necesaria para operar el servicio. Aplicamos controles razonables, aunque ningún sistema conectado a internet elimina por completo los riesgos.

Tus derechos y decisiones

Puedes corregir tu perfil, eliminar contenido propio y solicitar la eliminación de tu cuenta desde la configuración.''';
