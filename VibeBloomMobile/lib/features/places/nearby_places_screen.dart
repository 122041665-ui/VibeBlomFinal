import 'dart:async';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../activity/create_place_screen.dart';
import 'place.dart';
import 'place_detail_screen.dart';
import 'place_service.dart';

class NearbyPlacesScreen extends StatefulWidget {
  const NearbyPlacesScreen({this.guest = false, this.onLogin, super.key});
  final bool guest;
  final VoidCallback? onLogin;
  @override
  State<NearbyPlacesScreen> createState() => _NearbyPlacesScreenState();
}

class _NearbyPlacesScreenState extends State<NearbyPlacesScreen> {
  final _service = PlaceService(), _search = TextEditingController();
  List<Place> _places = const [];
  List<Place> _semanticPlaces = const [];
  final Set<int> _favorites = {};
  bool _loading = true;
  bool _searchingWithAi = false;
  String? _error, _type;
  String _query = '';
  Timer? _searchDebounce;
  int _searchRequest = 0;

  String _normalize(String value) {
    var text = value.toLowerCase();
    const source = 'áéíóúüñ';
    const target = 'aeiouun';
    for (var index = 0; index < source.length; index++) {
      text = text.replaceAll(source[index], target[index]);
    }
    const synonyms = {
      'cafe': 'cafeteria',
      'cafes': 'cafeteria',
      'restaurantes': 'restaurante',
      'bares': 'bar',
      'antros': 'antro',
    };
    for (final entry in synonyms.entries) {
      text = text.replaceAll(RegExp('\\b${entry.key}\\b'), entry.value);
    }
    return text.trim();
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _searchDebounce?.cancel();
    _search.dispose();
    super.dispose();
  }

  void _onSearchChanged(String value) {
    setState(() {
      _query = value;
      if (value.trim().length < 3) _semanticPlaces = const [];
    });
    _searchDebounce?.cancel();
    if (widget.guest ||
        value.trim().length < 3 ||
        _hasLocalMatch(value.trim())) {
      if (_semanticPlaces.isNotEmpty) {
        setState(() => _semanticPlaces = const []);
      }
      return;
    }
    _searchDebounce = Timer(
        const Duration(milliseconds: 550), () => _searchWithAi(value.trim()));
  }

  bool _hasLocalMatch(String query) {
    final normalizedQuery = _normalize(query);
    return _places.any((place) {
      final searchable =
          _normalize('${place.name} ${place.city} ${place.type}');
      final normalizedType = _normalize(place.type).toUpperCase();
      return searchable.contains(normalizedQuery) &&
          (_type == null || normalizedType == _type);
    });
  }

  Future<void> _searchWithAi(String query) async {
    final request = ++_searchRequest;
    if (mounted) setState(() => _searchingWithAi = true);
    try {
      final response = await ApiClient.dio.post<Map<String, dynamic>>(
          '/ai/recommendations',
          data: {'text': query, 'limit': 20, 'history': const []});
      final results = response.data?['resultados'] as List? ?? const [];
      if (!mounted || request != _searchRequest || query != _query.trim()) {
        return;
      }
      setState(() => _semanticPlaces = results
          .whereType<Map>()
          .map((item) => Place.fromJson(Map<String, dynamic>.from(item)))
          .toList());
    } catch (_) {
      if (mounted && request == _searchRequest) {
        setState(() => _semanticPlaces = const []);
      }
    } finally {
      if (mounted && request == _searchRequest) {
        setState(() => _searchingWithAi = false);
      }
    }
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final authenticated =
          (await ApiClient.storage.read(key: 'access_token'))?.isNotEmpty ??
              false;
      final places = await _service.list();
      final favorites = authenticated ? await _service.favorites() : <Place>[];
      if (!widget.guest && await Geolocator.isLocationServiceEnabled()) {
        var permission = await Geolocator.checkPermission();
        if (permission == LocationPermission.denied) {
          permission = await Geolocator.requestPermission();
        }
        if (permission == LocationPermission.whileInUse ||
            permission == LocationPermission.always) {
          final p = await Geolocator.getCurrentPosition();
          places.sort((a, b) => _distance(a, p).compareTo(_distance(b, p)));
        }
      }
      if (mounted) {
        setState(() {
          _places = places;
          _favorites
            ..clear()
            ..addAll(favorites.map((p) => p.id));
        });
      }
    } catch (e) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(e));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  double _distance(
          Place p, Position x) =>
      p.latitude == null || p.longitude == null
          ? double.infinity
          : Geolocator.distanceBetween(
              x.latitude, x.longitude, p.latitude!, p.longitude!);
  @override
  Widget build(BuildContext context) {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_error != null) {
      return Center(
          child: FilledButton(onPressed: _load, child: Text(_error!)));
    }
    final localFiltered = _places.where((p) {
      final q = _normalize(_query);
      final searchable = _normalize('${p.name} ${p.city} ${p.type}');
      final normalizedType = _normalize(p.type).toUpperCase();
      return (q.isEmpty || searchable.contains(q)) &&
          (_type == null || normalizedType == _type);
    }).toList();
    final filtered = localFiltered.isNotEmpty || _query.trim().isEmpty
        ? localFiltered
        : _semanticPlaces.where((p) {
            final normalizedType = _normalize(p.type).toUpperCase();
            return _type == null || normalizedType == _type;
          }).toList();
    return RefreshIndicator(
        onRefresh: _load,
        child: ListView.builder(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
            itemCount: filtered.length + (widget.guest ? 0 : 2),
            itemBuilder: (context, index) {
              if (widget.guest) {
                return _placeCard(context, filtered[index]);
              }
              if (index == 0) {
                return Container(
                    margin: const EdgeInsets.only(bottom: 16),
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(28),
                        border: Border.all(color: VibeColors.border),
                        boxShadow: const [
                          BoxShadow(
                              color: Color(0x120F172A),
                              blurRadius: 28,
                              offset: Offset(0, 12))
                        ]),
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                              width: 44,
                              height: 4,
                              decoration: BoxDecoration(
                                  color: VibeColors.blue,
                                  borderRadius: BorderRadius.circular(4))),
                          const SizedBox(height: 18),
                          const Text('Comunidad VibeBloom',
                              style: TextStyle(
                                  color: VibeColors.blue,
                                  fontWeight: FontWeight.w800,
                                  fontSize: 12)),
                          const SizedBox(height: 7),
                          const Text('Explora lugares con VibeBloom',
                              style: TextStyle(
                                  color: Color(0xFF0F172A),
                                  fontWeight: FontWeight.w900,
                                  fontSize: 29,
                                  height: 1.08)),
                          const SizedBox(height: 10),
                          const Text(
                              'Descubre spots creados por la comunidad y guarda los que quieras visitar.',
                              style: TextStyle(
                                  color: Color(0xFF64748B), height: 1.45)),
                          const SizedBox(height: 16),
                          OutlinedButton.icon(
                              onPressed: widget.guest
                                  ? widget.onLogin
                                  : () async {
                                      final ok = await Navigator.push<bool>(
                                          context,
                                          MaterialPageRoute(
                                              builder: (_) =>
                                                  const CreatePlaceScreen()));
                                      if (ok == true) await _load();
                                    },
                              icon:
                                  Icon(widget.guest ? Icons.login : Icons.add),
                              label: Text(widget.guest
                                  ? 'Iniciar sesión'
                                  : 'Crear lugar'))
                        ]));
              }
              if (index == 1) {
                return Column(children: [
                  TextField(
                      controller: _search,
                      onChanged: _onSearchChanged,
                      decoration: const InputDecoration(
                          prefixIcon:
                              Icon(Icons.search, color: VibeColors.blue),
                          labelText: 'Buscar lugares, tipos o ciudades')),
                  if (_searchingWithAi && localFiltered.isEmpty)
                    const Padding(
                        padding: EdgeInsets.only(top: 8),
                        child: Row(children: [
                          SizedBox.square(
                              dimension: 14,
                              child: CircularProgressIndicator(strokeWidth: 2)),
                          SizedBox(width: 8),
                          Text('Vibe IA está buscando coincidencias…',
                              style: TextStyle(
                                  fontSize: 12, color: Color(0xFF64748B)))
                        ])),
                  const SizedBox(height: 10),
                  SizedBox(
                      height: 42,
                      child:
                          ListView(scrollDirection: Axis.horizontal, children: [
                        _chip(null, 'Todos'),
                        ...[
                          'RESTAURANTE',
                          'CAFETERIA',
                          'BAR',
                          'ANTRO',
                          'PARQUE',
                          'MUSEO',
                          'MIRADOR',
                          'OTRO'
                        ].map((v) => _chip(
                            v,
                            v == 'CAFETERIA'
                                ? 'Cafetería'
                                : v[0] + v.substring(1).toLowerCase()))
                      ])),
                  Padding(
                      padding: const EdgeInsets.fromLTRB(2, 12, 2, 12),
                      child: Row(children: [
                        Expanded(
                            child: Text(
                                '${filtered.length} lugares disponibles',
                                style: const TextStyle(
                                    fontWeight: FontWeight.w900,
                                    color: Color(0xFF0F172A)))),
                        if (_query.isNotEmpty || _type != null)
                          TextButton(
                              onPressed: () => setState(() {
                                    _query = '';
                                    _type = null;
                                    _semanticPlaces = const [];
                                    _search.clear();
                                  }),
                              child: const Text('Limpiar'))
                      ]))
                ]);
              }
              return _placeCard(context, filtered[index - 2]);
            }));
  }

  Widget _placeCard(BuildContext context, Place p) => Card(
        margin: const EdgeInsets.only(bottom: 16),
        clipBehavior: Clip.antiAlias,
        child: InkWell(
            onTap: widget.guest
                ? () => ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                        content: Text(
                            'Inicia sesión para abrir los detalles del lugar.')))
                : () => Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => PlaceDetailScreen(placeId: p.id))),
            child:
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              if (p.photoUrl != null)
                Stack(children: [
                  CachedNetworkImage(
                      imageUrl: p.photoUrl!,
                      height: 215,
                      width: double.infinity,
                      fit: BoxFit.cover),
                  Positioned(
                      left: 14,
                      bottom: 14,
                      child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 11, vertical: 6),
                          decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: .94),
                              borderRadius: BorderRadius.circular(20)),
                          child: Text(p.type,
                              style: const TextStyle(
                                  color: VibeColors.blue,
                                  fontWeight: FontWeight.w800,
                                  fontSize: 12)))),
                ]),
              Padding(
                  padding: const EdgeInsets.all(18),
                  child: Row(children: [
                    Expanded(
                        child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                          Text(p.name,
                              style: const TextStyle(
                                  fontSize: 21,
                                  fontWeight: FontWeight.w900,
                                  color: Color(0xFF0F172A))),
                          const SizedBox(height: 5),
                          Row(children: [
                            const Icon(Icons.location_on_outlined,
                                size: 16, color: VibeColors.blue),
                            const SizedBox(width: 4),
                            Expanded(child: Text(p.city))
                          ]),
                          const SizedBox(height: 9),
                          Row(children: [
                            ...List.generate(
                                5,
                                (i) => Icon(
                                    i < p.rating
                                        ? Icons.star
                                        : Icons.star_border,
                                    size: 18,
                                    color: Colors.amber)),
                            const Spacer(),
                            Text('MXN \$${p.price?.toStringAsFixed(0) ?? '0'}',
                                style: const TextStyle(
                                    fontWeight: FontWeight.w900,
                                    color: VibeColors.navy))
                          ]),
                        ])),
                    IconButton.filledTonal(
                        onPressed: () async {
                          final authenticated = (await ApiClient.storage
                                      .read(key: 'access_token'))
                                  ?.isNotEmpty ??
                              false;
                          if (!authenticated) {
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                      content: Text(
                                          'Inicia sesión para guardar favoritos.')));
                            }
                            return;
                          }
                          await _service.toggleFavorite(p.id);
                          setState(() {
                            _favorites.contains(p.id)
                                ? _favorites.remove(p.id)
                                : _favorites.add(p.id);
                          });
                        },
                        icon: Icon(
                            _favorites.contains(p.id)
                                ? Icons.favorite
                                : Icons.favorite_border,
                            color: _favorites.contains(p.id)
                                ? VibeColors.blue
                                : null)),
                  ])),
            ])),
      );
  Widget _chip(String? value, String label) => Padding(
      padding: const EdgeInsets.only(right: 8),
      child: ChoiceChip(
          label: Text(label),
          selected: _type == value,
          onSelected: (_) => setState(() => _type = value),
          selectedColor: const Color(0xFFDBEAFE),
          labelStyle: TextStyle(
              fontWeight: FontWeight.w700,
              color: _type == value
                  ? VibeColors.blueDark
                  : const Color(0xFF64748B)),
          side: const BorderSide(color: VibeColors.border)));
}
