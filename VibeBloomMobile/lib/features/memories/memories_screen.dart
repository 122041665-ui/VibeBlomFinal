import 'package:cached_network_image/cached_network_image.dart';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';

class MemoriesScreen extends StatefulWidget {
  const MemoriesScreen({super.key});
  @override
  State<MemoriesScreen> createState() => _MemoriesScreenState();
}

class _MemoriesScreenState extends State<MemoriesScreen> {
  List<Map<String, dynamic>> _items = const [];
  bool _loading = true;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final r = await ApiClient.dio.get<List<dynamic>>('/memories');
      if (mounted) {
        setState(() => _items = (r.data ?? [])
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList());
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _create() async {
    final title = TextEditingController(),
        description = TextEditingController(),
        location = TextEditingController();
    List<XFile> photos = [];
    final saved = await showModalBottomSheet<bool>(
        context: context,
        isScrollControlled: true,
        backgroundColor: Colors.white,
        shape: const RoundedRectangleBorder(
            borderRadius: BorderRadius.vertical(top: Radius.circular(28))),
        builder: (ctx) => StatefulBuilder(
            builder: (_, setLocal) => Padding(
                padding: EdgeInsets.fromLTRB(
                    20, 20, 20, MediaQuery.viewInsetsOf(ctx).bottom + 20),
                child: SingleChildScrollView(
                    child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                      const Text('Nueva memoria',
                          style: TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.w900,
                              color: VibeColors.navy)),
                      const SizedBox(height: 16),
                      TextField(
                          controller: title,
                          decoration:
                              const InputDecoration(labelText: 'Título')),
                      const SizedBox(height: 12),
                      TextField(
                          controller: location,
                          decoration:
                              const InputDecoration(labelText: 'Lugar')),
                      const SizedBox(height: 12),
                      TextField(
                          controller: description,
                          maxLines: 3,
                          decoration:
                              const InputDecoration(labelText: 'Descripción')),
                      const SizedBox(height: 12),
                      OutlinedButton.icon(
                          onPressed: () async {
                            final p = await ImagePicker().pickMultiImage(
                                imageQuality: 85, maxWidth: 1600);
                            setLocal(() => photos = p.take(3).toList());
                          },
                          icon: const Icon(Icons.photo_library),
                          label: Text(photos.isEmpty
                              ? 'Agregar fotos'
                              : '${photos.length} fotos')),
                      const SizedBox(height: 10),
                      FilledButton(
                          onPressed: () async {
                            if (title.text.trim().isEmpty) return;
                            final files = <MultipartFile>[];
                            for (final p in photos) {
                              files.add(await MultipartFile.fromFile(p.path,
                                  filename: p.name));
                            }
                            await ApiClient.dio.post('/memories',
                                data: FormData.fromMap({
                                  'title': title.text.trim(),
                                  'description': description.text.trim(),
                                  'location': location.text.trim(),
                                  'photos': files
                                }));
                            if (ctx.mounted) Navigator.pop(ctx, true);
                          },
                          child: const Text('Guardar memoria'))
                    ])))));
    if (saved == true) {
      setState(() => _loading = true);
      await _load();
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
      appBar: AppBar(
          title: const Text('Mis memorias',
              style: TextStyle(fontWeight: FontWeight.w900))),
      floatingActionButton: FloatingActionButton.extended(
          onPressed: _create,
          backgroundColor: VibeColors.blue,
          foregroundColor: Colors.white,
          icon: const Icon(Icons.add_photo_alternate),
          label: const Text('Nueva')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(padding: const EdgeInsets.all(16), children: [
                if (_items.isEmpty)
                  const Padding(
                      padding: EdgeInsets.all(40),
                      child: Center(
                          child: Text(
                              'Guarda aquí los momentos que quieres recordar.'))),
                ..._items.map((m) {
                  final photos = (m['photos'] as List? ?? const []);
                  final image = photos.isNotEmpty && photos.first is Map
                      ? ApiClient.mediaUrl((photos.first as Map)['url'])
                      : null;
                  return Card(
                      margin: const EdgeInsets.only(bottom: 14),
                      clipBehavior: Clip.antiAlias,
                      child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            if (image != null)
                              CachedNetworkImage(
                                  imageUrl: image,
                                  height: 180,
                                  width: double.infinity,
                                  fit: BoxFit.cover),
                            Padding(
                                padding: const EdgeInsets.all(18),
                                child: Row(children: [
                                  Expanded(
                                      child: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: [
                                        Text(
                                            m['title']?.toString() ?? 'Memoria',
                                            style: const TextStyle(
                                                fontSize: 18,
                                                fontWeight: FontWeight.w900)),
                                        if (m['location']
                                                ?.toString()
                                                .isNotEmpty ==
                                            true)
                                          Text(m['location'].toString(),
                                              style: const TextStyle(
                                                  color: VibeColors.blue,
                                                  fontWeight: FontWeight.w700)),
                                        if (m['description']
                                                ?.toString()
                                                .isNotEmpty ==
                                            true)
                                          Padding(
                                              padding:
                                                  const EdgeInsets.only(top: 8),
                                              child: Text(
                                                  m['description'].toString()))
                                      ])),
                                  IconButton(
                                      onPressed: () async {
                                        await ApiClient.dio
                                            .delete('/memories/${m['id']}');
                                        await _load();
                                      },
                                      icon: const Icon(Icons.delete_outline,
                                          color: Colors.red))
                                ]))
                          ]));
                })
              ])));
}
