import 'package:flutter/material.dart';

import '../auth/auth_controller.dart';
import '../places/nearby_places_screen.dart';
import '../community/community_screen.dart';
import '../places/map_screen.dart';
import '../profile/my_vibe_screen.dart';
import '../notifications/notifications_screen.dart';
import '../../core/widgets/vibe_logo.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/api/api_client.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({required this.auth, super.key});

  final AuthController auth;

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _index = 0;
  int _unread = 0;
  @override
  void initState() {
    super.initState();
    _loadUnread();
  }

  Future<void> _loadUnread() async {
    if (!widget.auth.isAuthenticated) return;
    try {
      final r = await ApiClient.dio.get<List<dynamic>>('/notifications');
      if (mounted) {
        setState(() => _unread = (r.data ?? [])
            .whereType<Map>()
            .where((e) => e['read_at'] == null)
            .length);
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    if (!widget.auth.isAuthenticated) {
      return Scaffold(
        appBar: AppBar(
          automaticallyImplyLeading: false,
          toolbarHeight: 72,
          title: const VibeLogo(horizontal: true),
          actions: [
            TextButton.icon(
                onPressed: widget.auth.requireLogin,
                icon: const Icon(Icons.login),
                label: const Text('Iniciar sesión')),
            const SizedBox(width: 8),
          ],
        ),
        body:
            NearbyPlacesScreen(guest: true, onLogin: widget.auth.requireLogin),
      );
    }
    final pages = <Widget>[
      const NearbyPlacesScreen(),
      const PlacesMapScreen(),
      const CommunityScreen(),
      MyVibeScreen(auth: widget.auth),
    ];
    const titles = ['Inicio', 'Mapa', 'Comunidad', 'Mi Vibe'];

    return Scaffold(
      appBar: AppBar(
        toolbarHeight: 70,
        title: Row(children: [
          const VibeLogo(horizontal: true),
          const Spacer(),
          Text(titles[_index],
              style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w800,
                  color: VibeColors.blue)),
          if (widget.auth.isAuthenticated) ...[
            const SizedBox(width: 6),
            Badge(
                isLabelVisible: _unread > 0,
                label: Text('$_unread'),
                child: IconButton(
                  tooltip: 'Notificaciones',
                  icon: const Icon(Icons.notifications_outlined,
                      color: VibeColors.navy),
                  onPressed: () async {
                    await Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) =>
                                NotificationsScreen(onChanged: _loadUnread)));
                    await _loadUnread();
                  },
                )),
          ]
        ]),
        bottom: const PreferredSize(
            preferredSize: Size.fromHeight(1),
            child: Divider(height: 1, color: VibeColors.border)),
      ),
      body: IndexedStack(index: _index, children: pages),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (value) => setState(() => _index = value),
        destinations: const [
          NavigationDestination(
              icon: Icon(Icons.home_outlined),
              selectedIcon: Icon(Icons.home),
              label: 'Inicio'),
          NavigationDestination(
              icon: Icon(Icons.map_outlined),
              selectedIcon: Icon(Icons.map),
              label: 'Mapa'),
          NavigationDestination(
              icon: Icon(Icons.people_outline),
              selectedIcon: Icon(Icons.people),
              label: 'Comunidad'),
          NavigationDestination(
              icon: Icon(Icons.person_outline),
              selectedIcon: Icon(Icons.person),
              label: 'Mi Vibe'),
        ],
      ),
    );
  }
}
