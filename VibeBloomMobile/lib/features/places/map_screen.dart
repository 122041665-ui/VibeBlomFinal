import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';
import 'place.dart';
import 'place_detail_screen.dart';
import 'place_service.dart';

class PlacesMapScreen extends StatefulWidget {
  const PlacesMapScreen({super.key});
  @override
  State<PlacesMapScreen> createState() => _PlacesMapScreenState();
}

class _PlacesMapScreenState extends State<PlacesMapScreen> {
  List<Place> _places = const [];
  bool _loading = true;
  String? _error;
  String? _mapboxToken;
  final _mapController = MapController();
  String? _type;
  static const _types = [
    'RESTAURANTE',
    'CAFETERIA',
    'BAR',
    'ANTRO',
    'PARQUE',
    'PLAZA',
    'MIRADOR',
    'MUSEO',
    'OTRO'
  ];
  @override
  void initState() {
    super.initState();
    _load();
  }

  List<Place> get _visible => _type == null
      ? _places
      : _places.where((place) {
          final normalized = place.type.toUpperCase().replaceAll('É', 'E');
          return normalized == _type ||
              (_type == 'CAFETERIA' && normalized.contains('CAFE'));
        }).toList();

  void _selectType(String? value) {
    setState(() => _type = value);
    WidgetsBinding.instance.addPostFrameCallback((_) => _fitVisible());
  }

  void _fitVisible() {
    final values = _visible;
    if (values.isEmpty) return;
    final points =
        values.map((p) => LatLng(p.latitude!, p.longitude!)).toList();
    _mapController.move(_centerFor(points), _zoomFor(points));
  }

  LatLng _centerFor(List<LatLng> points) => LatLng(
      points.map((p) => p.latitude).reduce((a, b) => a + b) / points.length,
      points.map((p) => p.longitude).reduce((a, b) => a + b) / points.length);

  double _zoomFor(List<LatLng> points) {
    if (points.length == 1) return 14;
    final latitudes = points.map((p) => p.latitude);
    final longitudes = points.map((p) => p.longitude);
    final span = (latitudes.reduce((a, b) => a > b ? a : b) -
                latitudes.reduce((a, b) => a < b ? a : b))
            .abs() +
        (longitudes.reduce((a, b) => a > b ? a : b) -
                longitudes.reduce((a, b) => a < b ? a : b))
            .abs();
    if (span < .2) return 12;
    if (span < 1) return 9;
    if (span < 3) return 7;
    if (span < 8) return 5.5;
    return 4;
  }

  Future<void> _load() async {
    try {
      final responses = await Future.wait([
        PlaceService().list(),
        ApiClient.dio.get<Map<String, dynamic>>('/mobile/config'),
      ]);
      final values = responses[0] as List<Place>;
      final config = (responses[1] as dynamic).data as Map<String, dynamic>?;
      if (mounted) {
        setState(() {
          _places = values
              .where((p) => p.latitude != null && p.longitude != null)
              .toList();
          _mapboxToken = config?['mapbox_token']?.toString();
        });
      }
    } catch (e) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(e));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_error != null) {
      return Center(
          child: FilledButton(onPressed: _load, child: Text(_error!)));
    }
    final visible = _visible;
    final points =
        visible.map((p) => LatLng(p.latitude!, p.longitude!)).toList();
    final center = points.isNotEmpty
        ? _centerFor(points)
        : const LatLng(23.6345, -102.5528);
    return Stack(children: [
      FlutterMap(
          mapController: _mapController,
          options: MapOptions(
              initialCenter: center,
              initialZoom: points.isEmpty ? 4.5 : _zoomFor(points)),
          children: [
            if (_mapboxToken != null && _mapboxToken!.isNotEmpty)
              TileLayer(
                urlTemplate:
                    'https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/256/{z}/{x}/{y}@2x?access_token=$_mapboxToken',
                userAgentPackageName: 'com.vibebloom.mobile',
              )
            else
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.vibebloom.mobile',
              ),
            MarkerLayer(
                markers: visible
                    .map((place) => Marker(
                        point: LatLng(place.latitude!, place.longitude!),
                        width: 54,
                        height: 62,
                        child: GestureDetector(
                            onTap: () => Navigator.push(
                                context,
                                MaterialPageRoute(
                                    builder: (_) =>
                                        PlaceDetailScreen(placeId: place.id))),
                            child: Column(children: [
                              Container(
                                  width: 42,
                                  height: 42,
                                  decoration: BoxDecoration(
                                      color: VibeColors.blue,
                                      shape: BoxShape.circle,
                                      border: Border.all(
                                          color: Colors.white, width: 3),
                                      boxShadow: const [
                                        BoxShadow(
                                            color: Color(0x4D0F172A),
                                            blurRadius: 8)
                                      ]),
                                  child: Icon(placeTypeIcon(place.type),
                                      color: Colors.white, size: 21)),
                              Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 5, vertical: 2),
                                  decoration: BoxDecoration(
                                      color: Colors.white,
                                      borderRadius: BorderRadius.circular(6)),
                                  child: Text(place.name,
                                      overflow: TextOverflow.ellipsis,
                                      style: const TextStyle(
                                          fontSize: 9,
                                          fontWeight: FontWeight.w800)))
                            ]))))
                    .toList()),
          ]),
      Positioned(
          top: 14,
          left: 14,
          right: 14,
          child: Card(
              child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Row(children: [
                    const CircleAvatar(
                        backgroundColor: Color(0xFFDBEAFE),
                        child: Icon(Icons.map, color: VibeColors.blue)),
                    const SizedBox(width: 12),
                    Expanded(
                        child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                          const Text('Mapa VibeBloom',
                              style: TextStyle(fontWeight: FontWeight.w900)),
                          Text('${visible.length} lugares con ubicación',
                              style: const TextStyle(
                                  fontSize: 12, color: Color(0xFF64748B)))
                        ]))
                  ])))),
      Positioned(
          top: 90,
          left: 10,
          right: 10,
          child: SizedBox(
              height: 43,
              child: ListView(scrollDirection: Axis.horizontal, children: [
                _typeChip(null, 'Todos'),
                ..._types.map((value) => _typeChip(
                    value,
                    value == 'CAFETERIA'
                        ? 'Cafetería'
                        : value[0] + value.substring(1).toLowerCase()))
              ]))),
      Positioned(
          right: 14,
          bottom: 18,
          child: FloatingActionButton.small(
              heroTag: 'fit-map',
              tooltip: 'Centrar lugares',
              onPressed: _fitVisible,
              child: const Icon(Icons.center_focus_strong))),
    ]);
  }

  Widget _typeChip(String? value, String label) => Padding(
      padding: const EdgeInsets.only(right: 7),
      child: FilterChip(
          backgroundColor: Colors.white,
          selectedColor: const Color(0xFFDBEAFE),
          selected: _type == value,
          avatar: Icon(value == null ? Icons.apps : placeTypeIcon(value),
              size: 17, color: VibeColors.blue),
          label:
              Text(label, style: const TextStyle(fontWeight: FontWeight.w800)),
          onSelected: (_) => _selectType(value)));
}
