import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import 'user_profile_screen.dart';

class CommunityScreen extends StatefulWidget {
  const CommunityScreen({super.key});
  @override
  State<CommunityScreen> createState() => _CommunityScreenState();
}

class _CommunityScreenState extends State<CommunityScreen> {
  List<Map<String, dynamic>> _users = const [];
  bool _loading = true;
  String _query = '';
  String? _error;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final r = await ApiClient.dio.get<List<dynamic>>('/users/community');
      if (mounted) {
        setState(() => _users = (r.data ?? [])
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList());
      }
    } catch (e) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(e));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _toggle(Map<String, dynamic> u) async {
    final following = u['is_following'] == true;
    following
        ? await ApiClient.dio.delete('/users/${u['id']}/follow')
        : await ApiClient.dio.post('/users/${u['id']}/follow');
    setState(() => u['is_following'] = !following);
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_error != null) {
      return Center(
          child: FilledButton(onPressed: _load, child: Text(_error!)));
    }
    final filtered = _users
        .where((u) => '${u['name']} ${u['email']}'
            .toLowerCase()
            .contains(_query.toLowerCase()))
        .toList();
    return RefreshIndicator(
        onRefresh: _load,
        child: ListView(padding: const EdgeInsets.all(16), children: [
          Container(
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                  gradient: const LinearGradient(
                      colors: [VibeColors.blue, VibeColors.navy],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight),
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: const [
                    BoxShadow(
                        color: Color(0x332563EB),
                        blurRadius: 30,
                        offset: Offset(0, 14))
                  ]),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Comunidad VibeBloom',
                        style: TextStyle(
                            color: Color(0xFFBFDBFE),
                            fontWeight: FontWeight.w800)),
                    const SizedBox(height: 8),
                    const Text('Descubre personas y nuevos lugares',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 28,
                            height: 1.08,
                            fontWeight: FontWeight.w900)),
                    const SizedBox(height: 10),
                    const Text(
                        'Encuentra perfiles públicos y sigue a personas con lugares interesantes.',
                        style:
                            TextStyle(color: Color(0xFFDBEAFE), height: 1.4)),
                    const SizedBox(height: 16),
                    TextField(
                        onChanged: (v) => setState(() => _query = v),
                        decoration: const InputDecoration(
                            fillColor: Colors.white,
                            prefixIcon:
                                Icon(Icons.search, color: VibeColors.blue),
                            hintText: 'Busca por nombre o correo'))
                  ])),
          const SizedBox(height: 18),
          if (filtered.isEmpty)
            const Padding(
                padding: EdgeInsets.all(40),
                child: Center(child: Text('No hay usuarios para mostrar.'))),
          ...filtered.map((u) {
            final photo = ApiClient.mediaUrl(u['profile_photo_url']);
            final following = u['is_following'] == true;
            return Container(
                margin: const EdgeInsets.only(bottom: 14),
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                    gradient: const LinearGradient(
                        colors: [Colors.white, Color(0xFFF0F7FF)]),
                    borderRadius: BorderRadius.circular(26),
                    border: Border.all(color: const Color(0xFFDBEAFE))),
                child: Column(children: [
                  InkWell(
                      onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (_) => UserProfileScreen(
                                  userId: (u['id'] as num).toInt()))),
                      child: Row(children: [
                        Container(
                            width: 64,
                            height: 64,
                            decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(18),
                                color: const Color(0xFFDBEAFE),
                                image: photo != null
                                    ? DecorationImage(
                                        image:
                                            CachedNetworkImageProvider(photo),
                                        fit: BoxFit.cover)
                                    : null),
                            child: photo == null
                                ? const Icon(Icons.person,
                                    size: 32, color: VibeColors.blue)
                                : null),
                        const SizedBox(width: 14),
                        Expanded(
                            child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                              Text(u['name']?.toString() ?? 'Usuario',
                                  style: const TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.w900,
                                      color: Color(0xFF0F172A))),
                              Text('${u['places_count'] ?? 0} lugares',
                                  style: const TextStyle(
                                      color: VibeColors.blue,
                                      fontWeight: FontWeight.w800))
                            ]))
                      ])),
                  const SizedBox(height: 14),
                  SizedBox(
                      width: double.infinity,
                      child: following
                          ? OutlinedButton.icon(
                              onPressed: () => _toggle(u),
                              icon: const Icon(Icons.check),
                              label: const Text('Siguiendo'))
                          : FilledButton.icon(
                              onPressed: () => _toggle(u),
                              icon: const Icon(Icons.add),
                              label: const Text('Seguir')))
                ]));
          })
        ]));
  }
}
