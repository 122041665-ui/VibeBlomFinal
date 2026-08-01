import 'package:flutter/material.dart';

import '../auth/auth_controller.dart';
import '../places/nearby_places_screen.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({required this.auth, super.key});

  final AuthController auth;

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _index = 0;

  @override
  Widget build(BuildContext context) {
    final pages = <Widget>[
      const NearbyPlacesScreen(),
      const _PendingFeature(icon: Icons.map_outlined, title: 'Mapa', message: 'El mapa y las indicaciones serán el siguiente módulo.'),
      const _PendingFeature(icon: Icons.favorite_outline, title: 'Favoritos', message: 'Aquí aparecerán tus lugares guardados.'),
      _Profile(auth: widget.auth),
    ];
    const titles = ['Cerca de ti', 'Mapa', 'Favoritos', 'Perfil'];

    return Scaffold(
      appBar: AppBar(title: Text(titles[_index], style: const TextStyle(fontWeight: FontWeight.w800))),
      body: IndexedStack(index: _index, children: pages),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (value) => setState(() => _index = value),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.explore_outlined), selectedIcon: Icon(Icons.explore), label: 'Cerca'),
          NavigationDestination(icon: Icon(Icons.map_outlined), selectedIcon: Icon(Icons.map), label: 'Mapa'),
          NavigationDestination(icon: Icon(Icons.favorite_outline), selectedIcon: Icon(Icons.favorite), label: 'Favoritos'),
          NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Perfil'),
        ],
      ),
    );
  }
}

class _PendingFeature extends StatelessWidget {
  const _PendingFeature({required this.icon, required this.title, required this.message});
  final IconData icon;
  final String title;
  final String message;

  @override
  Widget build(BuildContext context) => Center(child: Padding(padding: const EdgeInsets.all(32), child: Column(mainAxisSize: MainAxisSize.min, children: [Icon(icon, size: 56, color: Theme.of(context).colorScheme.primary), const SizedBox(height: 16), Text(title, style: Theme.of(context).textTheme.titleLarge), const SizedBox(height: 8), Text(message, textAlign: TextAlign.center)])));
}

class _Profile extends StatelessWidget {
  const _Profile({required this.auth});
  final AuthController auth;

  @override
  Widget build(BuildContext context) => ListView(padding: const EdgeInsets.all(20), children: [
    const CircleAvatar(radius: 42, child: Icon(Icons.person, size: 42)),
    const SizedBox(height: 24),
    OutlinedButton.icon(onPressed: auth.logout, icon: const Icon(Icons.logout), label: const Text('Cerrar sesión')),
  ]);
}
