import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';

import '../../core/api/api_client.dart';
import 'place.dart';
import 'place_service.dart';

class NearbyPlacesScreen extends StatefulWidget {
  const NearbyPlacesScreen({super.key});

  @override
  State<NearbyPlacesScreen> createState() => _NearbyPlacesScreenState();
}

class _NearbyPlacesScreenState extends State<NearbyPlacesScreen> {
  final _service = PlaceService();
  bool _loading = true;
  String? _error;
  List<Place> _places = const [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final places = await _service.list();
      if (await Geolocator.isLocationServiceEnabled()) {
        var permission = await Geolocator.checkPermission();
        if (permission == LocationPermission.denied) {
          permission = await Geolocator.requestPermission();
        }
        if (permission == LocationPermission.whileInUse || permission == LocationPermission.always) {
          final position = await Geolocator.getCurrentPosition();
          places.sort((a, b) => _distance(a, position).compareTo(_distance(b, position)));
        }
      }
      if (mounted) setState(() => _places = places);
    } catch (error) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(error));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  double _distance(Place place, Position position) {
    if (place.latitude == null || place.longitude == null) return double.infinity;
    return Geolocator.distanceBetween(position.latitude, position.longitude, place.latitude!, place.longitude!);
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_error != null) {
      return Center(child: Padding(padding: const EdgeInsets.all(24), child: Column(mainAxisSize: MainAxisSize.min, children: [Text(_error!, textAlign: TextAlign.center), const SizedBox(height: 12), OutlinedButton(onPressed: _load, child: const Text('Reintentar'))])));
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _places.length,
        itemBuilder: (context, index) {
          final place = _places[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 14),
            clipBehavior: Clip.antiAlias,
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              if (place.photoUrl != null) CachedNetworkImage(imageUrl: place.photoUrl!, height: 160, width: double.infinity, fit: BoxFit.cover, errorWidget: (_, __, ___) => const SizedBox(height: 100, child: Center(child: Icon(Icons.image_not_supported_outlined)))),
              Padding(
                padding: const EdgeInsets.all(16),
                child: Row(children: [
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(place.name, style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800)), const SizedBox(height: 4), Text('${place.type} · ${place.city}') ])),
                  IconButton.filledTonal(
                    tooltip: 'Guardar en favoritos',
                    onPressed: () async {
                      try {
                        await _service.toggleFavorite(place.id);
                        if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Favoritos actualizados.')));
                      } catch (error) {
                        if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(ApiClient.messageFrom(error))));
                      }
                    },
                    icon: const Icon(Icons.favorite_border),
                  ),
                ]),
              ),
            ]),
          );
        },
      ),
    );
  }
}
