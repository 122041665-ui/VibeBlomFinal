import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';
import '../places/place.dart';
import '../places/place_detail_screen.dart';

class UserProfileScreen extends StatefulWidget {
  const UserProfileScreen({required this.userId, super.key});
  final int userId;
  @override
  State<UserProfileScreen> createState() => _UserProfileScreenState();
}

class _UserProfileScreenState extends State<UserProfileScreen> {
  Map<String, dynamic>? user;
  bool loading = true;
  String? error;
  Future<void> _load() async {
    try {
      final r = await ApiClient.dio
          .get<Map<String, dynamic>>('/users/${widget.userId}/public-profile');
      if (mounted) {
        setState(() {
          user = r.data;
          loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          error = ApiClient.messageFrom(e);
          loading = false;
        });
      }
    }
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _toggle() async {
    try {
      final following = user?['is_following'] == true;
      following
          ? await ApiClient.dio.delete('/users/${widget.userId}/follow')
          : await ApiClient.dio.post('/users/${widget.userId}/follow');
      await _load();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(ApiClient.messageFrom(e))));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }
    if (error != null) {
      return Scaffold(appBar: AppBar(), body: Center(child: Text(error!)));
    }
    final data = user!;
    final photo = ApiClient.mediaUrl(data['profile_photo_url']);
    final places = (data['places'] as List? ?? const [])
        .whereType<Map>()
        .map((e) => Place.fromJson(Map<String, dynamic>.from(e)))
        .toList();
    return Scaffold(
        appBar: AppBar(title: const Text('Perfil de la comunidad')),
        body: ListView(padding: const EdgeInsets.all(16), children: [
          Container(
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                  gradient: const LinearGradient(
                      colors: [VibeColors.blue, VibeColors.navy]),
                  borderRadius: BorderRadius.circular(28)),
              child: Column(children: [
                CircleAvatar(
                    radius: 46,
                    backgroundColor: Colors.white24,
                    backgroundImage: photo == null
                        ? null
                        : CachedNetworkImageProvider(photo),
                    child: photo == null
                        ? const Icon(Icons.person,
                            size: 45, color: Colors.white)
                        : null),
                const SizedBox(height: 12),
                Text(data['name']?.toString() ?? 'Usuario',
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.w900)),
                const SizedBox(height: 15),
                Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _Count('${data['followers_count'] ?? 0}', 'Seguidores'),
                      _Count('${data['following_count'] ?? 0}', 'Seguidos'),
                      _Count('${data['places_count'] ?? 0}', 'Lugares')
                    ]),
                const SizedBox(height: 16),
                SizedBox(
                    width: double.infinity,
                    child: data['is_following'] == true
                        ? OutlinedButton.icon(
                            style: OutlinedButton.styleFrom(
                                foregroundColor: Colors.white),
                            onPressed: _toggle,
                            icon: const Icon(Icons.check),
                            label: const Text('Siguiendo'))
                        : FilledButton.icon(
                            style: FilledButton.styleFrom(
                                backgroundColor: Colors.white,
                                foregroundColor: VibeColors.blue),
                            onPressed: _toggle,
                            icon: const Icon(Icons.add),
                            label: const Text('Seguir'))),
              ])),
          const Padding(
              padding: EdgeInsets.fromLTRB(2, 22, 2, 12),
              child: Text('Lugares publicados',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900))),
          if (places.isEmpty)
            const Center(
                child: Padding(
                    padding: EdgeInsets.all(30),
                    child: Text('Este usuario aún no tiene lugares.'))),
          ...places.map((place) => Card(
              margin: const EdgeInsets.only(bottom: 13),
              clipBehavior: Clip.antiAlias,
              child: InkWell(
                  onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) =>
                              PlaceDetailScreen(placeId: place.id))),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (place.photoUrl != null)
                          CachedNetworkImage(
                              imageUrl: place.photoUrl!,
                              height: 165,
                              width: double.infinity,
                              fit: BoxFit.cover),
                        Padding(
                            padding: const EdgeInsets.all(15),
                            child: Row(children: [
                              CircleAvatar(
                                  backgroundColor: const Color(0xFFDBEAFE),
                                  child: Icon(placeTypeIcon(place.type),
                                      color: VibeColors.blue)),
                              const SizedBox(width: 12),
                              Expanded(
                                  child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                    Text(place.name,
                                        style: const TextStyle(
                                            fontSize: 18,
                                            fontWeight: FontWeight.w900)),
                                    Text('${place.type} · ${place.city}')
                                  ]))
                            ]))
                      ])))),
        ]));
  }
}

class _Count extends StatelessWidget {
  const _Count(this.value, this.label);
  final String value, label;
  @override
  Widget build(BuildContext context) => Column(children: [
        Text(value,
            style: const TextStyle(
                color: Colors.white,
                fontSize: 21,
                fontWeight: FontWeight.w900)),
        Text(label,
            style: const TextStyle(color: Color(0xFFBFDBFE), fontSize: 12))
      ]);
}
