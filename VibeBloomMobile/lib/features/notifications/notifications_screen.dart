import 'package:flutter/material.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({this.onChanged, super.key});
  final VoidCallback? onChanged;
  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<Map<String, dynamic>> items = const [];
  bool loading = true;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final response = await ApiClient.dio.get<List<dynamic>>('/notifications');
    if (mounted) {
      setState(() {
        items = (response.data ?? [])
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList();
        loading = false;
      });
    }
  }

  Future<void> _read(Map<String, dynamic> item) async {
    if (item['read_at'] == null) {
      await ApiClient.dio.patch('/notifications/${item['id']}/read');
      await _load();
      widget.onChanged?.call();
    }
  }

  Future<void> _readAll() async {
    await ApiClient.dio.post('/notifications/read-all');
    await _load();
    widget.onChanged?.call();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
            title: const Text('Notificaciones',
                style: TextStyle(fontWeight: FontWeight.w900)),
            actions: [
              TextButton(
                  onPressed:
                      items.any((e) => e['read_at'] == null) ? _readAll : null,
                  child: const Text('Leer todas'))
            ]),
        body: loading
            ? const Center(child: CircularProgressIndicator())
            : RefreshIndicator(
                onRefresh: _load,
                child: ListView(padding: const EdgeInsets.all(16), children: [
                  if (items.isEmpty)
                    const Padding(
                        padding: EdgeInsets.all(50),
                        child:
                            Center(child: Text('No tienes notificaciones.'))),
                  ...items.map((item) {
                    final unread = item['read_at'] == null;
                    return Card(
                      margin: const EdgeInsets.only(bottom: 12),
                      child: InkWell(
                          borderRadius: BorderRadius.circular(26),
                          onTap: () => _read(item),
                          child: Padding(
                              padding: const EdgeInsets.all(17),
                              child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    CircleAvatar(
                                        backgroundColor: unread
                                            ? const Color(0xFFDBEAFE)
                                            : const Color(0xFFF1F5F9),
                                        child: Icon(
                                            unread
                                                ? Icons.notifications_active
                                                : Icons.notifications_none,
                                            color: unread
                                                ? VibeColors.blue
                                                : const Color(0xFF64748B))),
                                    const SizedBox(width: 13),
                                    Expanded(
                                        child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                          Text(
                                              item['title']?.toString() ??
                                                  'Notificación',
                                              style: TextStyle(
                                                  fontWeight: unread
                                                      ? FontWeight.w900
                                                      : FontWeight.w700)),
                                          const SizedBox(height: 4),
                                          Text(item['body']?.toString() ?? '',
                                              style: const TextStyle(
                                                  color: Color(0xFF64748B),
                                                  height: 1.4)),
                                        ])),
                                    if (unread)
                                      const Badge(
                                          backgroundColor: VibeColors.blue),
                                  ]))),
                    );
                  }),
                ])),
      );
}
