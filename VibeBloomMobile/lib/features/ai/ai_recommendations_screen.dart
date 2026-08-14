import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:speech_to_text/speech_to_text.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';
import '../places/place.dart';
import '../places/place_detail_screen.dart';

class AiRecommendationsScreen extends StatefulWidget {
  const AiRecommendationsScreen({super.key});

  @override
  State<AiRecommendationsScreen> createState() =>
      _AiRecommendationsScreenState();
}

class _AiRecommendationsScreenState extends State<AiRecommendationsScreen> {
  static const _prompts = [
    'Cafeterías tranquilas en Querétaro para trabajar',
    'Restaurantes bonitos con presupuesto medio',
    'Bares para ir de noche con buen ambiente',
    'Parques o miradores para un plan tranquilo',
  ];

  final _query = TextEditingController();
  final _city = TextEditingController();
  final _speech = SpeechToText();
  bool _loading = false;
  bool _listening = false;
  String? _message;
  String? _error;
  List<String> _filters = const [];
  List<Place> _places = const [];
  final List<Map<String, String>> _conversation = [
    {
      'role': 'assistant',
      'text':
          'Hola. Soy Vibe, tu asistente virtual. Cuéntame qué plan tienes en mente y encontraremos un lugar para ti.'
    }
  ];

  @override
  void dispose() {
    _speech.stop();
    _query.dispose();
    _city.dispose();
    super.dispose();
  }

  Future<void> _toggleVoice() async {
    if (_listening) {
      await _speech.stop();
      if (mounted) setState(() => _listening = false);
      return;
    }
    final ready = await _speech.initialize(
      onStatus: (status) {
        if (mounted && (status == 'done' || status == 'notListening')) {
          setState(() => _listening = false);
        }
      },
      onError: (_) {
        if (mounted) {
          setState(() {
            _listening = false;
            _error =
                'No pude escuchar tu voz. Revisa el permiso del micrófono.';
          });
        }
      },
    );
    if (!ready) {
      setState(() => _error =
          'El reconocimiento de voz no está disponible en este dispositivo.');
      return;
    }
    setState(() {
      _listening = true;
      _error = null;
    });
    await _speech.listen(
      onResult: (result) {
        _query.text = result.recognizedWords;
        _query.selection = TextSelection.collapsed(offset: _query.text.length);
        if (mounted) setState(() {});
      },
      listenOptions: SpeechListenOptions(localeId: 'es_MX'),
    );
  }

  Future<void> _search([String? prompt]) async {
    if (prompt != null) _query.text = prompt;
    if (_query.text.trim().length < 2) {
      setState(() => _error = 'Cuéntame qué tipo de lugar estás buscando.');
      return;
    }
    final userText = _query.text.trim();
    final historyStart =
        _conversation.length > 10 ? _conversation.length - 10 : 0;
    final history = _conversation.sublist(historyStart);
    await _speech.stop();
    if (!mounted) return;
    FocusScope.of(context).unfocus();
    setState(() {
      _loading = true;
      _listening = false;
      _error = null;
      _conversation.add({'role': 'user', 'text': userText});
    });
    try {
      final response = await ApiClient.dio.post<Map<String, dynamic>>(
        '/ai/recommendations',
        data: {
          'text': userText,
          if (_city.text.trim().isNotEmpty) 'city': _city.text.trim(),
          'limit': 20,
          'history': history,
        },
      );
      final data = response.data ?? const {};
      if (!mounted) return;
      setState(() {
        _message = data['assistant_reply']?.toString();
        _conversation.add({
          'role': 'assistant',
          'text': _message ?? 'Estoy aquí para ayudarte a encontrar un lugar.'
        });
        _filters = (data['filtros_aplicados'] as List? ?? const [])
            .map((item) => item.toString())
            .toList();
        _places = (data['resultados'] as List? ?? const [])
            .whereType<Map>()
            .map((item) => Place.fromJson(Map<String, dynamic>.from(item)))
            .toList();
      });
    } catch (error) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(error));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _clear() {
    _query.clear();
    _city.clear();
    setState(() {
      _message = null;
      _error = null;
      _filters = const [];
      _places = const [];
      _conversation
        ..clear()
        ..add({
          'role': 'assistant',
          'text':
              'Hola. Soy Vibe, tu asistente virtual. ¿Qué tipo de plan buscas hoy?'
        });
    });
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
          leading: const BackButton(),
          title: const Text('Vibe IA',
              style: TextStyle(fontWeight: FontWeight.w900)),
          actions: [
            IconButton(
                tooltip: 'Limpiar chat',
                onPressed: _clear,
                icon: const Icon(Icons.delete_sweep_outlined)),
          ],
        ),
        body: ListView(
          padding: const EdgeInsets.fromLTRB(16, 10, 16, 32),
          children: [
            Container(
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border.all(color: VibeColors.border),
                borderRadius: BorderRadius.circular(28),
                boxShadow: const [
                  BoxShadow(color: Color(0x120F172A), blurRadius: 30)
                ],
              ),
              child: const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _StatusPill(),
                  SizedBox(height: 16),
                  Text.rich(
                    TextSpan(children: [
                      TextSpan(text: 'Vibe IA para encontrar '),
                      TextSpan(
                          text: 'lugares',
                          style: TextStyle(color: VibeColors.blue)),
                    ]),
                    style: TextStyle(
                        color: VibeColors.navy,
                        fontSize: 29,
                        height: 1.08,
                        fontWeight: FontWeight.w900),
                  ),
                  SizedBox(height: 10),
                  Text(
                    'Habla o escribe lo que traes en mente. Vibe interpreta intención, ciudad, presupuesto y tipo de plan.',
                    style: TextStyle(color: Color(0xFF64748B), height: 1.5),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 18),
            const Text('Conversación',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
            const SizedBox(height: 4),
            const Text(
              'Pide algo como “cafetería tranquila en Querétaro, máximo \$250”.',
              style: TextStyle(color: Color(0xFF64748B)),
            ),
            const SizedBox(height: 12),
            Container(
              constraints: const BoxConstraints(minHeight: 120),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  border: Border.all(color: VibeColors.border),
                  borderRadius: BorderRadius.circular(22)),
              child: Column(
                children: _conversation
                    .map((message) => _ChatBubble(
                          text: message['text']!,
                          user: message['role'] == 'user',
                        ))
                    .toList(),
              ),
            ),
            const SizedBox(height: 12),
            SizedBox(
              height: 42,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                itemCount: _prompts.length,
                separatorBuilder: (_, __) => const SizedBox(width: 8),
                itemBuilder: (_, index) => ActionChip(
                  label: Text(_prompts[index]),
                  avatar: const Icon(Icons.auto_awesome,
                      size: 17, color: VibeColors.blue),
                  onPressed: _loading ? null : () => _search(_prompts[index]),
                ),
              ),
            ),
            const SizedBox(height: 14),
            TextField(
              controller: _city,
              textCapitalization: TextCapitalization.words,
              decoration: const InputDecoration(
                labelText: 'Ciudad opcional',
                prefixIcon: Icon(Icons.location_city_outlined),
              ),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _query,
              minLines: 2,
              maxLines: 4,
              maxLength: 1000,
              textInputAction: TextInputAction.send,
              onSubmitted: _loading ? null : (_) => _search(),
              decoration: InputDecoration(
                hintText: 'Describe el plan que quieres...',
                suffixIcon: IconButton(
                  tooltip: _listening ? 'Detener' : 'Hablar',
                  onPressed: _loading ? null : _toggleVoice,
                  icon: Icon(_listening ? Icons.stop_circle : Icons.mic,
                      color: _listening ? Colors.red : VibeColors.blue),
                ),
              ),
            ),
            if (_listening)
              const Padding(
                padding: EdgeInsets.only(bottom: 10),
                child: Row(children: [
                  SizedBox(
                      width: 15,
                      height: 15,
                      child: CircularProgressIndicator(strokeWidth: 2)),
                  SizedBox(width: 9),
                  Text('Escuchando...',
                      style: TextStyle(
                          color: VibeColors.blue, fontWeight: FontWeight.w700)),
                ]),
              ),
            FilledButton.icon(
              onPressed: _loading ? null : _search,
              icon: _loading
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(
                          strokeWidth: 2, color: Colors.white))
                  : const Icon(Icons.arrow_forward),
              label: Padding(
                padding: const EdgeInsets.symmetric(vertical: 13),
                child: Text(_loading ? 'Vibe está pensando...' : 'Enviar'),
              ),
            ),
            if (_error != null)
              Container(
                margin: const EdgeInsets.only(top: 14),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                    color: const Color(0xFFFFF1F2),
                    borderRadius: BorderRadius.circular(14)),
                child: Text(_error!,
                    style: const TextStyle(color: Color(0xFFBE123C))),
              ),
            if (_filters.isNotEmpty) ...[
              const SizedBox(height: 14),
              const Text('Lectura de Vibe',
                  style: TextStyle(
                      color: VibeColors.blue, fontWeight: FontWeight.w900)),
              const SizedBox(height: 8),
              Wrap(
                spacing: 7,
                runSpacing: 7,
                children: _filters
                    .map((filter) => Chip(label: Text(filter)))
                    .toList(),
              ),
            ],
            if (_message != null) ...[
              const SizedBox(height: 20),
              Text('Recomendaciones (${_places.length})',
                  style: const TextStyle(
                      fontSize: 20, fontWeight: FontWeight.w900)),
              const SizedBox(height: 10),
              if (_places.isEmpty)
                const Padding(
                  padding: EdgeInsets.all(24),
                  child: Center(
                      child: Text(
                          'No encontré resultados. Prueba otra ciudad, presupuesto o tipo de lugar.')),
                ),
              ..._places.map((place) => _PlaceCard(place: place)),
            ],
          ],
        ),
      );
}

class _ChatBubble extends StatelessWidget {
  const _ChatBubble({required this.text, required this.user});
  final String text;
  final bool user;

  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.only(bottom: 10),
        child: Row(
          mainAxisAlignment:
              user ? MainAxisAlignment.end : MainAxisAlignment.start,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (!user) ...[
              const CircleAvatar(
                  radius: 18,
                  backgroundColor: VibeColors.blue,
                  child: Text('VB',
                      style: TextStyle(
                          color: Colors.white,
                          fontSize: 11,
                          fontWeight: FontWeight.w900))),
              const SizedBox(width: 8),
            ],
            Flexible(
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
                decoration: BoxDecoration(
                  color: user ? VibeColors.blue : Colors.white,
                  border: user ? null : Border.all(color: VibeColors.border),
                  borderRadius: BorderRadius.only(
                    topLeft: const Radius.circular(17),
                    topRight: const Radius.circular(17),
                    bottomLeft: Radius.circular(user ? 17 : 4),
                    bottomRight: Radius.circular(user ? 4 : 17),
                  ),
                ),
                child: Text(text,
                    style: TextStyle(
                        color: user ? Colors.white : VibeColors.navy,
                        height: 1.4,
                        fontWeight: FontWeight.w600)),
              ),
            ),
          ],
        ),
      );
}

class _StatusPill extends StatelessWidget {
  const _StatusPill();

  @override
  Widget build(BuildContext context) => Container(
        padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 7),
        decoration: BoxDecoration(
            color: const Color(0xFFEFF6FF),
            borderRadius: BorderRadius.circular(30),
            border: Border.all(color: const Color(0xFFDBEAFE))),
        child: const Row(mainAxisSize: MainAxisSize.min, children: [
          CircleAvatar(radius: 4, backgroundColor: Color(0xFF10B981)),
          SizedBox(width: 7),
          Text('Vibe IA',
              style: TextStyle(
                  color: VibeColors.blue,
                  fontSize: 12,
                  fontWeight: FontWeight.w900)),
        ]),
      );
}

class _PlaceCard extends StatelessWidget {
  const _PlaceCard({required this.place});

  final Place place;

  @override
  Widget build(BuildContext context) => Card(
        margin: const EdgeInsets.only(bottom: 14),
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => PlaceDetailScreen(placeId: place.id))),
          child:
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            if (place.photoUrl != null)
              CachedNetworkImage(
                  imageUrl: place.photoUrl!,
                  width: double.infinity,
                  height: 170,
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
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(place.name,
                            style: const TextStyle(
                                fontSize: 17, fontWeight: FontWeight.w900)),
                        Text('${place.type} · ${place.city}',
                            style: const TextStyle(color: Color(0xFF64748B))),
                        const SizedBox(height: 4),
                        Row(children: [
                          const Icon(Icons.star, size: 17, color: Colors.amber),
                          Text(' ${place.rating}'),
                          if (place.price != null)
                            Text('   MXN \$${place.price!.toStringAsFixed(0)}'),
                        ]),
                      ]),
                ),
                const Icon(Icons.chevron_right),
              ]),
            ),
          ]),
        ),
      );
}
