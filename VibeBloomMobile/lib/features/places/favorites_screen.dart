import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';

import '../../core/api/api_client.dart';
import 'place.dart';
import 'place_detail_screen.dart';
import 'place_service.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';

class FavoritesScreen extends StatefulWidget {
  const FavoritesScreen({super.key});
  @override
  State<FavoritesScreen> createState() => _FavoritesScreenState();
}

class _FavoritesScreenState extends State<FavoritesScreen> {
  final _service = PlaceService();
  List<Place> _items = const [];
  bool _loading = true;
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
      final data = await _service.favorites();
      if (mounted) setState(() => _items = data);
    } catch (error) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(error));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final Widget body = _loading
        ? const Center(child: CircularProgressIndicator())
        : _error != null
            ? Center(
                child: FilledButton(onPressed: _load, child: Text(_error!)))
            : RefreshIndicator(
                onRefresh: _load,
                child: ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _items.isEmpty ? 1 : _items.length,
                  itemBuilder: (context, index) {
                    if (_items.isEmpty) {
                      return const Padding(
                          padding: EdgeInsets.all(40),
                          child: Center(
                              child: Text('Todavía no guardas lugares.')));
                    }
                    final place = _items[index];
                    return Card(
                        margin: const EdgeInsets.only(bottom: 15),
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
                                        height: 190,
                                        width: double.infinity,
                                        fit: BoxFit.cover),
                                  Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Row(children: [
                                        CircleAvatar(
                                            backgroundColor:
                                                const Color(0xFFDBEAFE),
                                            child: Icon(
                                                placeTypeIcon(place.type),
                                                color: VibeColors.blue)),
                                        const SizedBox(width: 12),
                                        Expanded(
                                            child: Column(
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.start,
                                                children: [
                                              Text(place.name,
                                                  style: const TextStyle(
                                                      fontSize: 19,
                                                      fontWeight:
                                                          FontWeight.w900)),
                                              Text(
                                                  '${place.type} · ${place.city}',
                                                  style: const TextStyle(
                                                      color:
                                                          Color(0xFF64748B))),
                                              Row(children: [
                                                const Icon(Icons.star,
                                                    size: 18,
                                                    color: Colors.amber),
                                                Text(
                                                    ' ${place.rating}   MXN ${r'$'}${place.price?.toStringAsFixed(0) ?? '0'}',
                                                    style: const TextStyle(
                                                        fontWeight:
                                                            FontWeight.w800))
                                              ]),
                                            ])),
                                        IconButton.filledTonal(
                                            icon: const Icon(Icons.favorite,
                                                color: Colors.red),
                                            onPressed: () async {
                                              await _service
                                                  .toggleFavorite(place.id);
                                              await _load();
                                            }),
                                      ])),
                                ])));
                  },
                ),
              );
    return Scaffold(
        appBar: AppBar(
            leading: const BackButton(),
            title: const Text('Mis favoritos',
                style: TextStyle(fontWeight: FontWeight.w900))),
        body: body);
  }
}
