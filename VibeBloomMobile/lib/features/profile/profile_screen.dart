import 'package:cached_network_image/cached_network_image.dart';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/validation/validators.dart';
import '../auth/auth_controller.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({required this.auth, super.key});
  final AuthController auth;
  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _key = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  bool _isPublic = true;
  bool _saving = false;
  String? _notice;
  bool _noticeSuccess = false;

  @override
  void initState() {
    super.initState();
    _fill();
  }

  void _fill() {
    _name.text = widget.auth.user?['name']?.toString() ?? '';
    _email.text = widget.auth.user?['email']?.toString() ?? '';
    _isPublic = widget.auth.user?['profile_is_public'] != false;
  }

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    super.dispose();
  }

  void _show(String message, {bool success = false}) => setState(() {
        _notice = message;
        _noticeSuccess = success;
      });

  Future<void> _save() async {
    setState(() => _notice = null);
    if (!_key.currentState!.validate()) return;
    setState(() => _saving = true);
    try {
      await ApiClient.dio.put('/users/me/profile', data: {
        'name': _name.text.trim(),
        'email': _email.text.trim(),
        'profile_is_public': _isPublic,
      });
      await widget.auth.refreshProfile();
      _show('Información del perfil guardada.', success: true);
    } catch (error) {
      _show(ApiClient.messageFrom(error));
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  Future<void> _photo() async {
    final file = await ImagePicker().pickImage(
        source: ImageSource.gallery, imageQuality: 90, maxWidth: 1800);
    if (file == null) return;
    final size = await file.length();
    if (size > 5 * 1024 * 1024) {
      _show('La foto debe pesar como máximo 5 MB.');
      return;
    }
    final extension = file.name.split('.').last.toLowerCase();
    if (!{'jpg', 'jpeg', 'png', 'webp'}.contains(extension)) {
      _show('Selecciona una imagen JPG, PNG o WEBP.');
      return;
    }
    final bytes = await file.readAsBytes();
    if (!mounted) return;
    final confirmed = await showDialog<bool>(
          context: context,
          builder: (dialogContext) => AlertDialog(
            title: const Text('Vista previa de tu foto'),
            content: Column(mainAxisSize: MainAxisSize.min, children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: Image.memory(bytes,
                    width: 220, height: 220, fit: BoxFit.cover),
              ),
              const SizedBox(height: 12),
              const Text('Así se verá tu nueva foto de perfil.',
                  textAlign: TextAlign.center),
            ]),
            actions: [
              TextButton(
                  onPressed: () => Navigator.pop(dialogContext, false),
                  child: const Text('Elegir otra')),
              FilledButton.icon(
                  onPressed: () => Navigator.pop(dialogContext, true),
                  icon: const Icon(Icons.check),
                  label: const Text('Usar esta foto')),
            ],
          ),
        ) ??
        false;
    if (!confirmed) return;
    try {
      await ApiClient.dio.post('/users/me/profile-photo',
          data: FormData.fromMap({
            'photo':
                await MultipartFile.fromFile(file.path, filename: file.name)
          }));
      await widget.auth.refreshProfile();
      if (mounted) {
        setState(() {});
        _show('Foto de perfil actualizada.', success: true);
      }
    } catch (error) {
      _show(ApiClient.messageFrom(error));
    }
  }

  Future<void> _removePhoto() async {
    try {
      await ApiClient.dio.delete('/users/me/profile-photo');
      await widget.auth.refreshProfile();
      if (mounted) {
        setState(() {});
        _show('Foto de perfil eliminada.', success: true);
      }
    } catch (error) {
      _show(ApiClient.messageFrom(error));
    }
  }

  Future<void> _passwordSheet() async {
    final current = TextEditingController();
    final password = TextEditingController();
    final confirmation = TextEditingController();
    final formKey = GlobalKey<FormState>();
    String? serverError;
    bool sending = false;
    bool hideCurrent = true, hidePassword = true, hideConfirmation = true;
    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      builder: (sheetContext) => StatefulBuilder(
        builder: (_, setLocal) => Padding(
          padding: EdgeInsets.fromLTRB(
              20, 18, 20, 20 + MediaQuery.viewInsetsOf(sheetContext).bottom),
          child: SingleChildScrollView(
            child: Form(
              key: formKey,
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(children: [
                      const Expanded(
                          child: Text('Actualizar contraseña',
                              style: TextStyle(
                                  fontSize: 22,
                                  color: VibeColors.navy,
                                  fontWeight: FontWeight.w900))),
                      IconButton(
                          onPressed: () => Navigator.pop(sheetContext),
                          icon: const Icon(Icons.close)),
                    ]),
                    const Text(
                        'Asegúrate de que tu cuenta utiliza una contraseña larga y segura.',
                        style:
                            TextStyle(color: Color(0xFF64748B), height: 1.4)),
                    const SizedBox(height: 18),
                    _PasswordField(
                        controller: current,
                        label: 'Contraseña actual',
                        hidden: hideCurrent,
                        toggle: () =>
                            setLocal(() => hideCurrent = !hideCurrent),
                        validator: (value) => value?.isEmpty == true
                            ? 'Escribe tu contraseña actual.'
                            : null),
                    const SizedBox(height: 13),
                    _PasswordField(
                        controller: password,
                        label: 'Nueva contraseña',
                        hidden: hidePassword,
                        toggle: () =>
                            setLocal(() => hidePassword = !hidePassword),
                        helper:
                            'Mínimo 8 caracteres, una mayúscula, una minúscula y un número.',
                        validator: (value) =>
                            Validators.strongPassword(value ?? '')),
                    const SizedBox(height: 13),
                    _PasswordField(
                        controller: confirmation,
                        label: 'Confirmar contraseña',
                        hidden: hideConfirmation,
                        toggle: () => setLocal(
                            () => hideConfirmation = !hideConfirmation),
                        validator: (value) => value != password.text
                            ? 'Las contraseñas no coinciden.'
                            : null),
                    if (serverError != null)
                      _Notice(message: serverError!, success: false),
                    const SizedBox(height: 16),
                    SizedBox(
                      width: double.infinity,
                      child: FilledButton(
                        onPressed: sending
                            ? null
                            : () async {
                                setLocal(() => serverError = null);
                                if (!formKey.currentState!.validate()) return;
                                setLocal(() => sending = true);
                                try {
                                  await ApiClient.dio
                                      .put('/users/me/password', data: {
                                    'current_password': current.text,
                                    'password': password.text,
                                  });
                                  if (sheetContext.mounted) {
                                    Navigator.pop(sheetContext);
                                  }
                                  if (mounted) {
                                    _show(
                                        'Contraseña actualizada correctamente.',
                                        success: true);
                                  }
                                } catch (error) {
                                  setLocal(() => serverError =
                                      ApiClient.messageFrom(error));
                                } finally {
                                  if (sheetContext.mounted) {
                                    setLocal(() => sending = false);
                                  }
                                }
                              },
                        child: Text(
                            sending ? 'Guardando...' : 'Guardar contraseña'),
                      ),
                    ),
                  ]),
            ),
          ),
        ),
      ),
    );
    current.dispose();
    password.dispose();
    confirmation.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final photo = ApiClient.mediaUrl(widget.auth.user?['profile_photo_url']);
    return Form(
      key: _key,
      child: ListView(padding: const EdgeInsets.all(18), children: [
        const Text('Información del perfil',
            style: TextStyle(
                fontSize: 24,
                color: VibeColors.navy,
                fontWeight: FontWeight.w900)),
        const SizedBox(height: 5),
        const Text(
            'Actualiza tus datos personales y la forma en que apareces en Comunidad.',
            style: TextStyle(color: Color(0xFF64748B), height: 1.4)),
        const SizedBox(height: 18),
        Container(
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
              color: Colors.white,
              border: Border.all(color: VibeColors.border),
              borderRadius: BorderRadius.circular(24)),
          child: Row(children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: Container(
                width: 80,
                height: 80,
                color: const Color(0xFFDBEAFE),
                child: photo == null
                    ? const Icon(Icons.person, size: 42, color: VibeColors.blue)
                    : CachedNetworkImage(imageUrl: photo, fit: BoxFit.cover),
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                  Text(_name.text,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontWeight: FontWeight.w900)),
                  Text(_email.text,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: Color(0xFF64748B))),
                  const SizedBox(height: 8),
                  Wrap(spacing: 6, children: [
                    TextButton(
                        onPressed: _photo, child: const Text('Cambiar foto')),
                    if (photo != null)
                      TextButton(
                          onPressed: _removePhoto,
                          child: const Text('Eliminar',
                              style: TextStyle(color: Color(0xFFBE123C)))),
                  ]),
                ])),
          ]),
        ),
        const Padding(
          padding: EdgeInsets.fromLTRB(2, 8, 2, 16),
          child: Text(
              'JPG, PNG o WEBP de máximo 5 MB. La foto no se deformará.',
              style: TextStyle(fontSize: 12, color: Color(0xFF64748B))),
        ),
        TextFormField(
            controller: _name,
            textCapitalization: TextCapitalization.words,
            validator: Validators.name,
            decoration: const InputDecoration(labelText: 'Nombre')),
        const SizedBox(height: 13),
        TextFormField(
            controller: _email,
            keyboardType: TextInputType.emailAddress,
            validator: Validators.email,
            decoration: const InputDecoration(labelText: 'Correo electrónico')),
        const SizedBox(height: 14),
        SwitchListTile.adaptive(
          value: _isPublic,
          onChanged: (value) => setState(() => _isPublic = value),
          tileColor: Colors.white,
          shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20),
              side: const BorderSide(color: VibeColors.border)),
          title: const Text('Perfil público',
              style: TextStyle(fontWeight: FontWeight.w900)),
          subtitle: const Text(
              'Permite que otras personas vean tu perfil y tus lugares en Comunidad.'),
        ),
        if (_notice != null)
          _Notice(message: _notice!, success: _noticeSuccess),
        const SizedBox(height: 14),
        FilledButton(
            onPressed: _saving ? null : _save,
            child: Text(_saving ? 'Guardando...' : 'Guardar perfil')),
        const SizedBox(height: 22),
        const Text('Seguridad',
            style: TextStyle(
                fontSize: 20,
                color: VibeColors.navy,
                fontWeight: FontWeight.w900)),
        const SizedBox(height: 9),
        OutlinedButton.icon(
            onPressed: _passwordSheet,
            icon: const Icon(Icons.lock_outline),
            label: const Text('Actualizar contraseña')),
        const SizedBox(height: 26),
        OutlinedButton.icon(
            onPressed: () async {
              await widget.auth.logout();
              if (context.mounted) {
                Navigator.of(context).popUntil((route) => route.isFirst);
              }
            },
            icon: const Icon(Icons.logout),
            label: const Text('Cerrar sesión')),
      ]),
    );
  }
}

class _PasswordField extends StatelessWidget {
  const _PasswordField(
      {required this.controller,
      required this.label,
      required this.hidden,
      required this.toggle,
      required this.validator,
      this.helper});
  final TextEditingController controller;
  final String label;
  final bool hidden;
  final VoidCallback toggle;
  final String? Function(String?) validator;
  final String? helper;
  @override
  Widget build(BuildContext context) => TextFormField(
        controller: controller,
        obscureText: hidden,
        validator: validator,
        autovalidateMode: AutovalidateMode.onUserInteraction,
        decoration: InputDecoration(
            labelText: label,
            helperText: helper,
            helperMaxLines: 3,
            errorMaxLines: 5,
            suffixIcon: IconButton(
                onPressed: toggle,
                icon: Icon(hidden
                    ? Icons.visibility_outlined
                    : Icons.visibility_off_outlined))),
      );
}

class _Notice extends StatelessWidget {
  const _Notice({required this.message, required this.success});
  final String message;
  final bool success;
  @override
  Widget build(BuildContext context) => Container(
        width: double.infinity,
        margin: const EdgeInsets.only(top: 13),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
            color: success ? const Color(0xFFECFDF5) : const Color(0xFFFFF1F2),
            borderRadius: BorderRadius.circular(16)),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Icon(success ? Icons.check_circle_outline : Icons.error_outline,
              color:
                  success ? const Color(0xFF047857) : const Color(0xFFBE123C)),
          const SizedBox(width: 9),
          Expanded(
              child: Text(message,
                  style: TextStyle(
                      height: 1.4,
                      color: success
                          ? const Color(0xFF065F46)
                          : const Color(0xFF9F1239),
                      fontWeight: FontWeight.w700))),
        ]),
      );
}
