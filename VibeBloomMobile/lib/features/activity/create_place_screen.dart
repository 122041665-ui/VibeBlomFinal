import 'dart:async';

import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:image_picker/image_picker.dart';
import 'package:latlong2/latlong.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';

class CreatePlaceScreen extends StatefulWidget {
  const CreatePlaceScreen({super.key});
  @override
  State<CreatePlaceScreen> createState() => _CreatePlaceScreenState();
}

class _CreatePlaceScreenState extends State<CreatePlaceScreen> {
  final _key = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _address = TextEditingController();
  final _description = TextEditingController();
  final _price = TextEditingController(text: '0');
  String? _state;
  String? _type;
  int _rating = 0;
  bool _sending = false;
  String? _formError;
  List<XFile> _photos = const [];
  final _mapController = MapController();
  double? _lat;
  double? _lng;
  String? _cityPlaceId;
  String? _mapboxToken;
  Timer? _addressDebounce;
  List<Map<String, dynamic>> _addressSuggestions = const [];
  bool _resolvingLocation = false;
  bool _updatingAddressFromMap = false;
  int _locationRequest = 0;
  static const states = [
    'Aguascalientes',
    'Baja California',
    'Baja California Sur',
    'Campeche',
    'Chiapas',
    'Chihuahua',
    'Ciudad de México',
    'Coahuila',
    'Colima',
    'Durango',
    'Estado de México',
    'Guanajuato',
    'Guerrero',
    'Hidalgo',
    'Jalisco',
    'Michoacán',
    'Morelos',
    'Nayarit',
    'Nuevo León',
    'Oaxaca',
    'Puebla',
    'Querétaro',
    'Quintana Roo',
    'San Luis Potosí',
    'Sinaloa',
    'Sonora',
    'Tabasco',
    'Tamaulipas',
    'Tlaxcala',
    'Veracruz',
    'Yucatán',
    'Zacatecas'
  ];
  static const types = [
    'Restaurante',
    'Cafetería',
    'Bar',
    'Antro',
    'Museo',
    'Parque',
    'Mirador',
    'Plaza',
    'Centro comercial',
    'Otro'
  ];
  @override
  void initState() {
    super.initState();
    _loadMapConfig();
  }

  Future<void> _loadMapConfig() async {
    try {
      final response =
          await ApiClient.dio.get<Map<String, dynamic>>('/mobile/config');
      if (!mounted) return;
      setState(() {
        _mapboxToken = response.data?['mapbox_token']?.toString();
      });
    } catch (_) {}
  }

  @override
  void dispose() {
    _name.dispose();
    _address.dispose();
    _description.dispose();
    _price.dispose();
    _addressDebounce?.cancel();
    super.dispose();
  }

  Future<void> _pick() async {
    final files =
        await ImagePicker().pickMultiImage(imageQuality: 85, maxWidth: 1800);
    if (files.length > 3) {
      if (mounted) {
        setState(() => _formError = 'Solo puedes seleccionar hasta 3 fotos.');
      }
      return;
    }
    setState(() {
      _photos = files;
      _formError = null;
    });
  }

  Future<void> _send() async {
    if (!_key.currentState!.validate()) return;
    if (_type == null) {
      _showError('Selecciona el tipo de lugar.');
      return;
    }
    if (_state == null) {
      _showError('Selecciona un estado.');
      return;
    }
    if (_rating == 0) {
      _showError('Selecciona de 1 a 5 estrellas para calificar el lugar.');
      return;
    }
    if (_photos.isEmpty) {
      _showError('Agrega al menos una foto del lugar.');
      return;
    }
    if (_lat == null || _lng == null) {
      final located = await _geocodeAddress();
      if (!located) {
        _showError(
            'No encontramos esa dirección. Selecciona la ubicación exacta en el mapa.');
        return;
      }
    }
    setState(() => _sending = true);
    try {
      final uploads = <MultipartFile>[];
      for (final photo in _photos) {
        if (await photo.length() > 5 * 1024 * 1024) {
          throw StateError('Cada foto debe pesar máximo 5 MB.');
        }
        uploads.add(
            await MultipartFile.fromFile(photo.path, filename: photo.name));
      }
      final data = FormData.fromMap({
        'name': _name.text.trim(),
        'type': _type!,
        'rating': _rating,
        'price': double.parse(_price.text),
        'city': _state,
        'city_place_id': _cityPlaceId ?? '',
        'address': _address.text.trim(),
        'lat': _lat,
        'lng': _lng,
        'description': _description.text.trim(),
        'photos': uploads
      });
      await ApiClient.dio.post('/approvals', data: data);
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      if (mounted) {
        _showError(e is StateError ? e.message : ApiClient.messageFrom(e));
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  void _showError(String message) => setState(() => _formError = message);

  Future<bool> _geocodeAddress() async {
    final token = _mapboxToken;
    if (token == null ||
        token.isEmpty ||
        _state == null ||
        _address.text.trim().isEmpty) {
      return false;
    }
    try {
      final response = await ApiClient.dio.get<Map<String, dynamic>>(
          '/mobile/mapbox/geocode',
          queryParameters: {
            'q': _address.text.trim(),
            'state': _state,
            'limit': 1
          });
      final features = response.data?['features'] as List?;
      if (features == null || features.isEmpty) return false;
      final feature = Map<String, dynamic>.from(features.first as Map);
      final center = feature['center'] as List?;
      if (center == null || center.length < 2) return false;
      _setLocation((center[1] as num).toDouble(), (center[0] as num).toDouble(),
          placeId: feature['id']?.toString());
      return true;
    } catch (_) {
      return false;
    }
  }

  void _scheduleAddressSearch(String value) {
    if (_updatingAddressFromMap) return;
    setState(() {
      _lat = null;
      _lng = null;
      _cityPlaceId = null;
    });
    _addressDebounce?.cancel();
    if (value.trim().length < 3 || _state == null) {
      setState(() => _addressSuggestions = const []);
      return;
    }
    _addressDebounce = Timer(
        const Duration(milliseconds: 350), () => _autocompleteAddress(value));
  }

  Future<void> _autocompleteAddress(String value) async {
    final token = _mapboxToken;
    if (token == null || token.isEmpty || _state == null) return;
    try {
      final query = '${value.trim()}, $_state, México';
      final response = await ApiClient.dio.get<Map<String, dynamic>>(
          'https://api.mapbox.com/geocoding/v5/mapbox.places/${Uri.encodeComponent(query)}.json',
          queryParameters: {
            'access_token': token,
            'country': 'mx',
            'language': 'es',
            'types': 'address,poi,place,locality,neighborhood',
            'limit': 6
          });
      if (mounted && value.trim() == _address.text.trim()) {
        final features = (response.data?['features'] as List? ?? const [])
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList();
        setState(() => _addressSuggestions = features);
        // La escritura manual y el mapa representan la misma ubicación:
        // movemos el pin al mejor resultado sin reemplazar lo que escribió.
        if (features.isNotEmpty) {
          final center = features.first['center'] as List?;
          if (center != null && center.length >= 2) {
            _setLocation(
              (center[1] as num).toDouble(),
              (center[0] as num).toDouble(),
              placeId: features.first['id']?.toString(),
            );
          }
        }
      }
    } catch (_) {
      if (mounted) setState(() => _addressSuggestions = const []);
    }
  }

  void _selectAddress(Map<String, dynamic> feature) {
    final center = feature['center'] as List?;
    if (center == null || center.length < 2) return;
    _address.text = feature['place_name']?.toString() ??
        feature['text']?.toString() ??
        _address.text;
    setState(() => _addressSuggestions = const []);
    _setLocation((center[1] as num).toDouble(), (center[0] as num).toDouble(),
        placeId: feature['id']?.toString());
    FocusScope.of(context).unfocus();
  }

  void _setLocation(double lat, double lng, {String? placeId}) {
    setState(() {
      _lat = lat;
      _lng = lng;
      if (placeId != null) _cityPlaceId = placeId;
    });
    _mapController.move(LatLng(lat, lng), 15);
  }

  Future<void> _selectLocationFromMap(LatLng point) async {
    final token = _mapboxToken;
    final requestId = ++_locationRequest;
    _setLocation(point.latitude, point.longitude);
    setState(() {
      _resolvingLocation = true;
      _addressSuggestions = const [];
    });
    if (token == null || token.isEmpty) {
      _showError(
        'El mapa todavía se está preparando. Espera un momento e inténtalo de nuevo.',
      );
      setState(() => _resolvingLocation = false);
      return;
    }
    try {
      final response = await ApiClient.dio.get<Map<String, dynamic>>(
        'https://api.mapbox.com/geocoding/v5/mapbox.places/'
        '${point.longitude.toStringAsFixed(7)},${point.latitude.toStringAsFixed(7)}.json',
        queryParameters: {
          'access_token': token,
          'country': 'mx',
          'language': 'es',
          'types': 'address,poi,place,locality,neighborhood',
          'limit': 1,
        },
      );
      final features = response.data?['features'] as List?;
      if (!mounted ||
          requestId != _locationRequest ||
          features == null ||
          features.isEmpty) {
        return;
      }
      final feature = Map<String, dynamic>.from(features.first as Map);
      final address = feature['place_name']?.toString().trim();
      if (address == null || address.isEmpty) return;
      _updatingAddressFromMap = true;
      _address.value = TextEditingValue(
        text: address,
        selection: TextSelection.collapsed(offset: address.length),
      );
      _updatingAddressFromMap = false;
      setState(() => _cityPlaceId = feature['id']?.toString());
      _key.currentState?.validate();
    } catch (_) {
      if (mounted && requestId == _locationRequest) {
        _showError(
          'El punto quedó seleccionado, pero no pudimos completar la dirección. Puedes escribirla manualmente.',
        );
      }
    } finally {
      if (mounted && requestId == _locationRequest) {
        setState(() => _resolvingLocation = false);
      }
    }
  }

  Future<String?> _choose(
          String title, List<String> options, String? current) =>
      showModalBottomSheet<String>(
          context: context,
          isScrollControlled: true,
          useSafeArea: true,
          builder: (_) =>
              _OptionSheet(title: title, options: options, selected: current));

  String? _required(String? value) =>
      (value?.trim().isEmpty ?? true) ? 'Este campo es obligatorio.' : null;
  @override
  Widget build(BuildContext context) => Scaffold(
      appBar: AppBar(title: const Text('Proponer un lugar')),
      body: Form(
          key: _key,
          child: ListView(padding: const EdgeInsets.all(20), children: [
            if (_formError != null)
              Container(
                  margin: const EdgeInsets.only(bottom: 14),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                      color: const Color(0xFFFFF1F2),
                      border: Border.all(color: const Color(0xFFFECACA)),
                      borderRadius: BorderRadius.circular(16)),
                  child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Icon(Icons.error_outline,
                            color: Color(0xFFBE123C)),
                        const SizedBox(width: 10),
                        Expanded(
                            child: Text(_formError!,
                                style: const TextStyle(
                                    color: Color(0xFF9F1239),
                                    fontWeight: FontWeight.w700))),
                      ])),
            TextFormField(
                controller: _name,
                validator: (value) {
                  final required = _required(value);
                  if (required != null) return 'Escribe el nombre del lugar.';
                  if (value!.trim().length > 255) {
                    return 'El nombre no puede superar 255 caracteres.';
                  }
                  if (value.trim().length < 3) {
                    return 'Escribe al menos 3 caracteres para el nombre.';
                  }
                  if (!RegExp(r'[A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9]').hasMatch(value)) {
                    return 'El nombre debe contener letras o números.';
                  }
                  return null;
                },
                decoration:
                    const InputDecoration(labelText: 'Nombre del lugar')),
            const SizedBox(height: 12),
            _SelectionField(
                label: 'Tipo de lugar',
                hint: 'Selecciona una opción',
                value: _type,
                icon: Icons.category_outlined,
                onTap: () async {
                  final value = await _choose('Tipo de lugar', types, _type);
                  if (value != null) setState(() => _type = value);
                }),
            const SizedBox(height: 12),
            _SelectionField(
                label: 'Estado',
                hint: 'Selecciona una entidad de México',
                value: _state,
                icon: Icons.location_city_outlined,
                onTap: () async {
                  final value =
                      await _choose('Estado de México', states, _state);
                  if (value != null) {
                    setState(() {
                      _state = value;
                      _lat = null;
                      _lng = null;
                      _cityPlaceId = null;
                      _addressSuggestions = const [];
                    });
                    if (_address.text.trim().length >= 3) {
                      _scheduleAddressSearch(_address.text);
                    }
                  }
                }),
            const SizedBox(height: 12),
            TextFormField(
                controller: _address,
                validator: _required,
                onChanged: _scheduleAddressSearch,
                decoration: const InputDecoration(
                    labelText: 'Dirección',
                    hintText: 'Calle, número y colonia',
                    helperText:
                        'Escribe una dirección o selecciónala directamente en el mapa.')),
            if (_addressSuggestions.isNotEmpty)
              Container(
                  margin: const EdgeInsets.only(top: 6),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: VibeColors.border),
                      boxShadow: const [
                        BoxShadow(
                            color: Color(0x140F172A),
                            blurRadius: 18,
                            offset: Offset(0, 8))
                      ]),
                  child: Column(
                      children: _addressSuggestions
                          .map((feature) => ListTile(
                                dense: true,
                                leading: const Icon(Icons.location_on_outlined,
                                    color: VibeColors.blue),
                                title: Text(
                                    feature['text']?.toString() ?? 'Dirección',
                                    style: const TextStyle(
                                        fontWeight: FontWeight.w800)),
                                subtitle: Text(
                                    feature['place_name']?.toString() ?? ''),
                                onTap: () => _selectAddress(feature),
                              ))
                          .toList())),
            const SizedBox(height: 10),
            OutlinedButton.icon(
                onPressed: _geocodeAddress,
                icon: const Icon(Icons.travel_explore),
                label: const Text('Buscar dirección en el mapa')),
            const SizedBox(height: 10),
            ClipRRect(
                borderRadius: BorderRadius.circular(20),
                child: SizedBox(
                    height: 250,
                    child: FlutterMap(
                      mapController: _mapController,
                      options: MapOptions(
                          initialCenter: const LatLng(23.6345, -102.5528),
                          initialZoom: 4.5,
                          onTap: (_, point) => _selectLocationFromMap(point)),
                      children: [
                        if (_mapboxToken != null && _mapboxToken!.isNotEmpty)
                          TileLayer(
                              urlTemplate:
                                  'https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/256/{z}/{x}/{y}@2x?access_token=$_mapboxToken',
                              userAgentPackageName: 'com.vibebloom.mobile')
                        else
                          TileLayer(
                              urlTemplate:
                                  'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                              userAgentPackageName: 'com.vibebloom.mobile'),
                        if (_lat != null && _lng != null)
                          MarkerLayer(markers: [
                            Marker(
                                point: LatLng(_lat!, _lng!),
                                width: 48,
                                height: 48,
                                child: const Icon(Icons.location_on,
                                    color: VibeColors.blue, size: 46))
                          ]),
                      ],
                    ))),
            Padding(
                padding: const EdgeInsets.only(top: 7),
                child: Text(
                    _resolvingLocation
                        ? 'Obteniendo la dirección del punto seleccionado…'
                        : _lat == null
                            ? 'Busca la dirección o toca el mapa para colocar el marcador.'
                            : 'Dirección y ubicación sincronizadas correctamente.',
                    style: TextStyle(
                        fontSize: 12,
                        color: _lat == null
                            ? const Color(0xFF64748B)
                            : Colors.green.shade700,
                        fontWeight: FontWeight.w700))),
            const SizedBox(height: 12),
            TextFormField(
                controller: _price,
                keyboardType: TextInputType.number,
                validator: (v) => double.tryParse(v ?? '') == null
                    ? 'Ingresa un precio válido.'
                    : double.parse(v!) < 0
                        ? 'El precio no puede ser negativo.'
                        : null,
                decoration:
                    const InputDecoration(labelText: 'Precio aproximado')),
            const SizedBox(height: 12),
            TextFormField(
                controller: _description,
                validator: (value) {
                  final required = _required(value);
                  if (required != null) {
                    return required;
                  }
                  if (value!.trim().length < 20) {
                    return 'Escribe al menos 20 caracteres.';
                  }
                  if (value.trim().length > 1000) {
                    return 'La descripción no puede superar 1000 caracteres.';
                  }
                  return null;
                },
                maxLength: 1000,
                maxLines: 4,
                decoration: const InputDecoration(
                    labelText: 'Descripción',
                    hintText:
                        'Describe el ambiente, lo que lo hace especial y qué puede encontrar la comunidad.')),
            const SizedBox(height: 14),
            const Text('Calificación obligatoria'),
            Row(
                children: List.generate(
                    5,
                    (i) => IconButton(
                        onPressed: () => setState(() => _rating = i + 1),
                        icon: Icon(i < _rating ? Icons.star : Icons.star_border,
                            color: Colors.amber)))),
            OutlinedButton.icon(
                onPressed: _pick,
                icon: const Icon(Icons.photo_library),
                label: Text(_photos.isEmpty
                    ? 'Agregar fotografías'
                    : '${_photos.length} fotografía(s) seleccionada(s)')),
            const SizedBox(height: 16),
            FilledButton(
                onPressed: _sending ? null : _send,
                child: Text(_sending ? 'Enviando...' : 'Enviar a aprobación')),
          ])));
}

class _SelectionField extends StatelessWidget {
  const _SelectionField(
      {required this.label,
      required this.hint,
      required this.value,
      required this.icon,
      required this.onTap});
  final String label, hint;
  final String? value;
  final IconData icon;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: InputDecorator(
          decoration: InputDecoration(
              labelText: label,
              prefixIcon: Icon(icon),
              suffixIcon: const Icon(Icons.keyboard_arrow_down_rounded)),
          child: Text(value ?? hint,
              style: TextStyle(
                  fontWeight: value == null ? FontWeight.w500 : FontWeight.w800,
                  color: value == null
                      ? const Color(0xFF64748B)
                      : VibeColors.navy)),
        ),
      );
}

class _OptionSheet extends StatefulWidget {
  const _OptionSheet(
      {required this.title, required this.options, this.selected});
  final String title;
  final List<String> options;
  final String? selected;
  @override
  State<_OptionSheet> createState() => _OptionSheetState();
}

class _OptionSheetState extends State<_OptionSheet> {
  @override
  Widget build(BuildContext context) {
    return FractionallySizedBox(
        heightFactor: .82,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 18, 20, 12),
          child:
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Row(children: [
              Expanded(
                  child: Text(widget.title,
                      style: const TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w900,
                          color: VibeColors.navy))),
              IconButton(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.close),
                  tooltip: 'Cerrar'),
            ]),
            const SizedBox(height: 12),
            Expanded(
                child: ListView.separated(
                    itemCount: widget.options.length,
                    separatorBuilder: (_, __) => const Divider(height: 1),
                    itemBuilder: (_, index) {
                      final item = widget.options[index];
                      final selected = item == widget.selected;
                      return ListTile(
                          contentPadding: const EdgeInsets.symmetric(
                              horizontal: 4, vertical: 3),
                          leading: CircleAvatar(
                              backgroundColor: selected
                                  ? const Color(0xFFDBEAFE)
                                  : const Color(0xFFF1F5F9),
                              child: Icon(
                                  selected ? Icons.check : placeTypeIcon(item),
                                  color: selected
                                      ? VibeColors.blue
                                      : const Color(0xFF64748B))),
                          title: Text(item,
                              style: TextStyle(
                                  fontWeight: selected
                                      ? FontWeight.w900
                                      : FontWeight.w700)),
                          trailing: const Icon(Icons.chevron_right),
                          onTap: () => Navigator.pop(context, item));
                    })),
          ]),
        ));
  }
}
