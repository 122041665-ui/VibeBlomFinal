import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../activity/activity_screen.dart';
import '../ai/ai_recommendations_screen.dart';
import '../auth/auth_controller.dart';
import '../memories/memories_screen.dart';
import '../places/favorites_screen.dart';
import '../places/place.dart';
import '../places/place_detail_screen.dart';
import 'profile_screen.dart';

class MyVibeScreen extends StatelessWidget {
  const MyVibeScreen({required this.auth, super.key});
  final AuthController auth;
  Future<Map<String, dynamic>> _network() async =>
      (await ApiClient.dio.get<Map<String, dynamic>>('/users/me/network'))
          .data ??
      const {};
  void _open(BuildContext context, Widget page, {String? title}) =>
      Navigator.push(
          context,
          MaterialPageRoute(
              builder: (_) => Scaffold(
                    appBar: title == null
                        ? null
                        : AppBar(
                            leading: const BackButton(),
                            title: Text(title,
                                style: const TextStyle(
                                    fontWeight: FontWeight.w900))),
                    body: SafeArea(child: page),
                  )));
  @override
  Widget build(BuildContext context) {
    final user = auth.user ?? const {};
    final photo = ApiClient.mediaUrl(user['profile_photo_url']);
    return ListView(padding: const EdgeInsets.all(16), children: [
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
          child: Row(children: [
            CircleAvatar(
                radius: 34,
                backgroundColor: Colors.white24,
                backgroundImage:
                    photo != null ? CachedNetworkImageProvider(photo) : null,
                child: photo == null
                    ? const Icon(Icons.person, color: Colors.white, size: 32)
                    : null),
            const SizedBox(width: 15),
            Expanded(
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                  const Text('Mi espacio Vibe',
                      style: TextStyle(
                          color: Colors.white70, fontWeight: FontWeight.w700)),
                  Text(user['name']?.toString() ?? 'Usuario',
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 23,
                          fontWeight: FontWeight.w900)),
                  Text(user['email']?.toString() ?? '',
                      style: const TextStyle(
                          color: Color(0xFFBFDBFE), fontSize: 12))
                ]))
          ])),
      const SizedBox(height: 20),
      FutureBuilder<Map<String, dynamic>>(
          future: _network(),
          builder: (context, snapshot) {
            final data = snapshot.data ?? const {};
            return Row(children: [
              Expanded(
                  child: _NetworkCard(
                      title: 'Seguidores',
                      count: data['followers_count'] ?? 0,
                      users: data['followers'] as List? ?? const [])),
              const SizedBox(width: 10),
              Expanded(
                  child: _NetworkCard(
                      title: 'Seguidos',
                      count: data['following_count'] ?? 0,
                      users: data['following'] as List? ?? const [])),
            ]);
          }),
      const SizedBox(height: 20),
      const Text('Tu actividad',
          style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.w900,
              color: Color(0xFF0F172A))),
      const SizedBox(height: 12),
      _Tile(
          icon: Icons.auto_awesome,
          color: VibeColors.blue,
          title: 'Vibe IA',
          subtitle: 'Recomendaciones según lo que buscas',
          onTap: () => _open(context, const AiRecommendationsScreen())),
      _Tile(
          icon: Icons.favorite,
          color: Colors.red,
          title: 'Mis favoritos',
          subtitle: 'Lugares guardados',
          onTap: () => _open(context, const FavoritesScreen())),
      _Tile(
          icon: Icons.auto_stories,
          color: VibeColors.coral,
          title: 'Mis memorias',
          subtitle: 'Momentos y fotografías',
          onTap: () => _open(context, const MemoriesScreen())),
      _Tile(
          icon: Icons.place,
          color: VibeColors.blue,
          title: 'Mis lugares',
          subtitle: 'Lugares aprobados',
          onTap: () => _open(context, const _MyPlacesScreen())),
      _Tile(
          icon: Icons.fact_check,
          color: Colors.orange,
          title: 'Mis aprobaciones',
          subtitle: 'Pendientes, aprobadas y rechazadas',
          onTap: () => _open(context, const ActivityScreen())),
      _Tile(
          icon: Icons.settings,
          color: VibeColors.navy,
          title: 'Configuración del perfil',
          subtitle: 'Foto, datos y contraseña',
          onTap: () => _open(context, ProfileScreen(auth: auth),
              title: 'Configuración del perfil')),
      const SizedBox(height: 10),
      OutlinedButton.icon(
          onPressed: auth.logout,
          icon: const Icon(Icons.logout),
          label: const Text('Cerrar sesión')),
    ]);
  }
}

class _Tile extends StatelessWidget {
  const _Tile(
      {required this.icon,
      required this.color,
      required this.title,
      required this.subtitle,
      required this.onTap});
  final IconData icon;
  final Color color;
  final String title, subtitle;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) => Card(
      margin: const EdgeInsets.only(bottom: 11),
      child: ListTile(
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          onTap: onTap,
          leading: Container(
              width: 46,
              height: 46,
              decoration: BoxDecoration(
                  color: color.withValues(alpha: .1),
                  borderRadius: BorderRadius.circular(14)),
              child: Icon(icon, color: color)),
          title:
              Text(title, style: const TextStyle(fontWeight: FontWeight.w900)),
          subtitle: Text(subtitle),
          trailing: const Icon(Icons.chevron_right)));
}

class _NetworkCard extends StatelessWidget {
  const _NetworkCard(
      {required this.title, required this.count, required this.users});
  final String title;
  final Object count;
  final List users;
  @override
  Widget build(BuildContext context) => InkWell(
      borderRadius: BorderRadius.circular(22),
      onTap: () => showModalBottomSheet(
          context: context,
          useSafeArea: true,
          builder: (_) => Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('$title · $count',
                        style: const TextStyle(
                            fontSize: 22, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 14),
                    Expanded(
                        child: users.isEmpty
                            ? const Center(
                                child: Text('No hay usuarios para mostrar.'))
                            : ListView(
                                children: users
                                    .whereType<Map>()
                                    .map((u) => ListTile(
                                        leading: const CircleAvatar(
                                            child: Icon(Icons.person)),
                                        title: Text(
                                            u['name']?.toString() ?? 'Usuario'),
                                        subtitle:
                                            Text(u['email']?.toString() ?? '')))
                                    .toList()))
                  ]))),
      child: Container(
          padding: const EdgeInsets.all(17),
          decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(22),
              border: Border.all(color: VibeColors.border)),
          child: Column(children: [
            Text('$count',
                style: const TextStyle(
                    fontSize: 25,
                    fontWeight: FontWeight.w900,
                    color: VibeColors.blue)),
            Text(title, style: const TextStyle(fontWeight: FontWeight.w800))
          ])));
}

class _MyPlacesScreen extends StatefulWidget {
  const _MyPlacesScreen();
  @override
  State<_MyPlacesScreen> createState() => _MyPlacesScreenState();
}

class _MyPlacesScreenState extends State<_MyPlacesScreen> {
  List<Place> items = const [];
  bool loading = true;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final r = await ApiClient.dio.get<List<dynamic>>('/places/mine');
    if (mounted) {
      setState(() {
        items = (r.data ?? [])
            .whereType<Map>()
            .map((e) => Place.fromJson(Map<String, dynamic>.from(e)))
            .toList();
        loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
            title: const Text('Mis lugares',
                style: TextStyle(fontWeight: FontWeight.w900))),
        body: loading
            ? const Center(child: CircularProgressIndicator())
            : ListView(padding: const EdgeInsets.all(16), children: [
                if (items.isEmpty)
                  const Padding(
                      padding: EdgeInsets.all(40),
                      child: Center(
                          child: Text('Aún no tienes lugares aprobados.'))),
                ...items.map((p) => Card(
                    margin: const EdgeInsets.only(bottom: 14),
                    clipBehavior: Clip.antiAlias,
                    child: InkWell(
                        onTap: () => Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (_) =>
                                    PlaceDetailScreen(placeId: p.id))),
                        child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              if (p.photoUrl != null)
                                CachedNetworkImage(
                                    imageUrl: p.photoUrl!,
                                    height: 175,
                                    width: double.infinity,
                                    fit: BoxFit.cover),
                              Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Row(children: [
                                    const CircleAvatar(
                                        child: Icon(Icons.place)),
                                    const SizedBox(width: 12),
                                    Expanded(
                                        child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                          Text(p.name,
                                              style: const TextStyle(
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.w900)),
                                          Text('${p.type} · ${p.city}'),
                                          Text(
                                              '${p.rating} estrellas · MXN ${r'$'}${p.price?.toStringAsFixed(0) ?? '0'}',
                                              style: const TextStyle(
                                                  color: VibeColors.blue,
                                                  fontWeight: FontWeight.w800))
                                        ])),
                                    const Icon(Icons.chevron_right)
                                  ]))
                            ])))),
              ]),
      );
}
