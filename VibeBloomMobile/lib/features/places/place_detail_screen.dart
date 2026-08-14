import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';
import '../community/user_profile_screen.dart';
import 'place.dart';
import 'place_service.dart';

class PlaceDetailScreen extends StatefulWidget {
  const PlaceDetailScreen({required this.placeId, super.key});
  final int placeId;

  @override
  State<PlaceDetailScreen> createState() => _PlaceDetailScreenState();
}

class _PlaceDetailScreenState extends State<PlaceDetailScreen> {
  final _service = PlaceService();
  final _review = TextEditingController();
  Place? _place;
  String? _error;
  String? _reviewError;
  String? _notice;
  bool _loading = true;
  bool _sending = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _review.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final place = await _service.detail(widget.placeId);
      if (mounted) setState(() => _place = place);
    } catch (error) {
      if (mounted) setState(() => _error = ApiClient.messageFrom(error));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _sendReview() async {
    final body = _review.text.trim();
    String? validation;
    if (body.isEmpty) {
      validation = 'Escribe tu reseña antes de publicarla.';
    } else if (body.length < 5) {
      validation = 'La reseña debe tener al menos 5 caracteres.';
    } else if (body.length > 1000) {
      validation = 'La reseña no puede superar 1000 caracteres.';
    }
    if (validation != null) {
      setState(() {
        _reviewError = validation;
        _notice = null;
      });
      return;
    }
    setState(() {
      _sending = true;
      _reviewError = null;
      _notice = null;
    });
    try {
      await _service.addReview(widget.placeId, body);
      _review.clear();
      await _load();
      if (mounted) {
        setState(() => _notice = 'Tu reseña se publicó correctamente.');
      }
    } catch (error) {
      if (mounted) setState(() => _reviewError = ApiClient.messageFrom(error));
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Future<void> _copyAddress() async {
    final address = _place?.address?.trim();
    if (address == null || address.isEmpty) return;
    await Clipboard.setData(ClipboardData(text: address));
    if (mounted) {
      setState(() => _notice =
          'Dirección copiada. Ya puedes pegarla en otro navegador o aplicación.');
    }
  }

  Future<void> _openDirections() async {
    final place = _place!;
    final destination = place.latitude != null && place.longitude != null
        ? '${place.latitude},${place.longitude}'
        : place.address ?? place.name;
    final uri = Uri.https('www.google.com', '/maps/dir/',
        {'api': '1', 'destination': destination});
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication) &&
        mounted) {
      setState(() => _notice = 'No fue posible abrir la aplicación de mapas.');
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        body: _loading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? _ErrorState(message: _error!, retry: _load)
                : RefreshIndicator(
                    onRefresh: _load,
                    child: CustomScrollView(slivers: [
                      SliverAppBar(
                        expandedHeight: _place!.photoUrls.isEmpty ? 130 : 330,
                        pinned: true,
                        title: Text(_place!.name,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style:
                                const TextStyle(fontWeight: FontWeight.w900)),
                        flexibleSpace: _place!.photoUrls.isEmpty
                            ? null
                            : FlexibleSpaceBar(
                                background:
                                    _PhotoGallery(photos: _place!.photoUrls),
                              ),
                      ),
                      SliverPadding(
                        padding: const EdgeInsets.fromLTRB(16, 18, 16, 38),
                        sliver: SliverList.list(children: [
                          Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Container(
                                    width: 52,
                                    height: 52,
                                    decoration: BoxDecoration(
                                        color: const Color(0xFFDBEAFE),
                                        borderRadius:
                                            BorderRadius.circular(17)),
                                    child: Icon(placeTypeIcon(_place!.type),
                                        color: VibeColors.blue)),
                                const SizedBox(width: 12),
                                Expanded(
                                    child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                      Text(_place!.name,
                                          style: const TextStyle(
                                              fontSize: 26,
                                              height: 1.1,
                                              color: VibeColors.navy,
                                              fontWeight: FontWeight.w900)),
                                      const SizedBox(height: 5),
                                      Text('${_place!.type} · ${_place!.city}',
                                          style: const TextStyle(
                                              color: Color(0xFF64748B),
                                              fontWeight: FontWeight.w700)),
                                    ])),
                              ]),
                          const SizedBox(height: 16),
                          Wrap(spacing: 8, runSpacing: 8, children: [
                            _InfoPill(
                                icon: Icons.star,
                                label: '${_place!.rating} de 5',
                                color: const Color(0xFFF59E0B)),
                            _InfoPill(
                                icon: Icons.payments_outlined,
                                label:
                                    'MXN \$${_place!.price?.toStringAsFixed(0) ?? '0'}',
                                color: const Color(0xFF059669)),
                            if (_place!.address?.isNotEmpty == true)
                              _InfoPill(
                                  icon: Icons.location_on_outlined,
                                  label: _place!.address!,
                                  color: VibeColors.blue),
                          ]),
                          if (_place!.address?.trim().isNotEmpty == true) ...[
                            const SizedBox(height: 12),
                            Row(children: [
                              Expanded(
                                  child: OutlinedButton.icon(
                                      onPressed: _copyAddress,
                                      icon: const Icon(Icons.copy_rounded),
                                      label: const Text('Copiar ubicación'))),
                              const SizedBox(width: 9),
                              Expanded(
                                  child: FilledButton.icon(
                                      onPressed: _openDirections,
                                      icon:
                                          const Icon(Icons.directions_outlined),
                                      label: const Text('Cómo llegar'))),
                            ]),
                          ],
                          const SizedBox(height: 22),
                          if (_place!.creatorId != null) ...[
                            _CreatorCard(place: _place!),
                            const SizedBox(height: 18),
                          ],
                          _SectionCard(
                            icon: Icons.auto_stories_outlined,
                            title: 'Acerca de este lugar',
                            child: Text(
                              _place!.description?.trim().isNotEmpty == true
                                  ? _place!.description!
                                  : 'Este lugar todavía no tiene una descripción.',
                              style: const TextStyle(
                                  color: Color(0xFF475569), height: 1.6),
                            ),
                          ),
                          const SizedBox(height: 18),
                          _SectionCard(
                            icon: Icons.rate_review_outlined,
                            title: 'Comparte tu experiencia',
                            child: Column(children: [
                              const Align(
                                  alignment: Alignment.centerLeft,
                                  child: Text(
                                      'La descripción informa sobre el lugar; aquí escribe únicamente tu experiencia personal.',
                                      style: TextStyle(
                                          color: Color(0xFF64748B),
                                          height: 1.4))),
                              const SizedBox(height: 12),
                              TextField(
                                controller: _review,
                                minLines: 3,
                                maxLines: 6,
                                maxLength: 1000,
                                onChanged: (_) {
                                  if (_reviewError != null) {
                                    setState(() => _reviewError = null);
                                  }
                                },
                                decoration: InputDecoration(
                                    hintText:
                                        '¿Qué visitaste, qué te gustó y qué recomendarías?',
                                    errorText: _reviewError,
                                    alignLabelWithHint: true),
                              ),
                              const SizedBox(height: 8),
                              SizedBox(
                                  width: double.infinity,
                                  child: FilledButton.icon(
                                      onPressed: _sending ? null : _sendReview,
                                      icon: _sending
                                          ? const SizedBox(
                                              width: 18,
                                              height: 18,
                                              child: CircularProgressIndicator(
                                                  strokeWidth: 2,
                                                  color: Colors.white))
                                          : const Icon(Icons.send_rounded),
                                      label: Text(_sending
                                          ? 'Publicando...'
                                          : 'Publicar reseña'))),
                              if (_notice != null)
                                _InlineNotice(message: _notice!, success: true),
                            ]),
                          ),
                          const SizedBox(height: 22),
                          Row(children: [
                            const Expanded(
                                child: Text('Reseñas de la comunidad',
                                    style: TextStyle(
                                        fontSize: 21,
                                        color: VibeColors.navy,
                                        fontWeight: FontWeight.w900))),
                            Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 10, vertical: 5),
                                decoration: BoxDecoration(
                                    color: const Color(0xFFDBEAFE),
                                    borderRadius: BorderRadius.circular(20)),
                                child: Text('${_place!.reviews.length}',
                                    style: const TextStyle(
                                        color: VibeColors.blue,
                                        fontWeight: FontWeight.w900))),
                          ]),
                          const SizedBox(height: 12),
                          if (_place!.reviews.isEmpty)
                            const _EmptyReviews()
                          else
                            ..._place!.reviews.map(_ReviewCard.new),
                        ]),
                      ),
                    ]),
                  ),
      );
}

class _PhotoGallery extends StatefulWidget {
  const _PhotoGallery({required this.photos});
  final List<String> photos;

  @override
  State<_PhotoGallery> createState() => _PhotoGalleryState();
}

class _PhotoGalleryState extends State<_PhotoGallery> {
  int _index = 0;
  late final PageController _controller;

  @override
  void initState() {
    super.initState();
    _controller = PageController();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _go(int index) {
    if (index < 0 || index >= widget.photos.length) return;
    _controller.animateToPage(index,
        duration: const Duration(milliseconds: 260),
        curve: Curves.easeOutCubic);
  }

  @override
  Widget build(BuildContext context) => Stack(fit: StackFit.expand, children: [
        PageView.builder(
          controller: _controller,
          itemCount: widget.photos.length,
          onPageChanged: (index) => setState(() => _index = index),
          itemBuilder: (_, index) => GestureDetector(
            onTap: () => showDialog<void>(
              context: context,
              builder: (dialogContext) => Dialog.fullscreen(
                backgroundColor: Colors.black,
                child: Stack(children: [
                  InteractiveViewer(
                    minScale: 1,
                    maxScale: 5,
                    child: Center(
                        child: CachedNetworkImage(
                            imageUrl: widget.photos[index],
                            fit: BoxFit.contain)),
                  ),
                  Positioned(
                    top: 14,
                    left: 10,
                    child: SafeArea(
                        child: IconButton.filledTonal(
                            onPressed: () => Navigator.pop(dialogContext),
                            icon: const Icon(Icons.close))),
                  ),
                ]),
              ),
            ),
            child: CachedNetworkImage(
                imageUrl: widget.photos[index], fit: BoxFit.cover),
          ),
        ),
        const IgnorePointer(
            child: DecoratedBox(
                decoration: BoxDecoration(
                    gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [Color(0x11000000), Color(0x990F172A)])))),
        if (widget.photos.length > 1)
          Positioned.fill(
              child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                IconButton.filledTonal(
                    tooltip: 'Foto anterior',
                    onPressed: _index == 0 ? null : () => _go(_index - 1),
                    icon: const Icon(Icons.chevron_left_rounded)),
                IconButton.filledTonal(
                    tooltip: 'Foto siguiente',
                    onPressed: _index == widget.photos.length - 1
                        ? null
                        : () => _go(_index + 1),
                    icon: const Icon(Icons.chevron_right_rounded)),
              ])),
        if (widget.photos.length > 1)
          Positioned(
            right: 16,
            bottom: 18,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 7),
              decoration: BoxDecoration(
                  color: Colors.black.withValues(alpha: .62),
                  borderRadius: BorderRadius.circular(20)),
              child: Text('${_index + 1} / ${widget.photos.length}',
                  style: const TextStyle(
                      color: Colors.white, fontWeight: FontWeight.w900)),
            ),
          ),
      ]);
}

class _CreatorCard extends StatelessWidget {
  const _CreatorCard({required this.place});
  final Place place;

  @override
  Widget build(BuildContext context) => Material(
        color: Colors.white,
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(22),
            side: const BorderSide(color: VibeColors.border)),
        child: InkWell(
          borderRadius: BorderRadius.circular(22),
          onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => UserProfileScreen(userId: place.creatorId!))),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(children: [
              CircleAvatar(
                radius: 25,
                backgroundColor: const Color(0xFFDBEAFE),
                backgroundImage: place.creatorPhotoUrl == null
                    ? null
                    : CachedNetworkImageProvider(place.creatorPhotoUrl!),
                child: place.creatorPhotoUrl == null
                    ? const Icon(Icons.person, color: VibeColors.blue)
                    : null,
              ),
              const SizedBox(width: 12),
              Expanded(
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                    const Text('Publicado por',
                        style:
                            TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                    Text(place.creatorName ?? 'Usuario de VibeBloom',
                        style: const TextStyle(
                            color: VibeColors.navy,
                            fontWeight: FontWeight.w900)),
                    const Text('Ver perfil y lugares publicados',
                        style: TextStyle(fontSize: 12, color: VibeColors.blue)),
                  ])),
              const Icon(Icons.chevron_right_rounded, color: VibeColors.blue),
            ]),
          ),
        ),
      );
}

class _SectionCard extends StatelessWidget {
  const _SectionCard(
      {required this.icon, required this.title, required this.child});
  final IconData icon;
  final String title;
  final Widget child;
  @override
  Widget build(BuildContext context) => Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
            color: Colors.white,
            border: Border.all(color: VibeColors.border),
            borderRadius: BorderRadius.circular(24)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Icon(icon, color: VibeColors.blue),
            const SizedBox(width: 9),
            Text(title,
                style: const TextStyle(
                    fontSize: 18,
                    color: VibeColors.navy,
                    fontWeight: FontWeight.w900)),
          ]),
          const SizedBox(height: 13),
          child,
        ]),
      );
}

class _ReviewCard extends StatelessWidget {
  const _ReviewCard(this.review);
  final Map<String, dynamic> review;
  @override
  Widget build(BuildContext context) {
    final user = review['user'] is Map ? review['user'] as Map : const {};
    final name = user['name']?.toString() ?? 'Usuario';
    final photo =
        ApiClient.mediaUrl(user['profile_photo_url'] ?? user['photo']);
    final date = review['created_at']?.toString().split('T').first;
    return Container(
      margin: const EdgeInsets.only(bottom: 11),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
          color: Colors.white,
          border: Border.all(color: VibeColors.border),
          borderRadius: BorderRadius.circular(21)),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        CircleAvatar(
          radius: 23,
          backgroundColor: const Color(0xFFDBEAFE),
          backgroundImage:
              photo == null ? null : CachedNetworkImageProvider(photo),
          child: photo == null
              ? Text(name.substring(0, 1).toUpperCase(),
                  style: const TextStyle(
                      color: VibeColors.blue, fontWeight: FontWeight.w900))
              : null,
        ),
        const SizedBox(width: 12),
        Expanded(
            child:
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Expanded(
                child: Text(name,
                    style: const TextStyle(fontWeight: FontWeight.w900))),
            if (date != null)
              Text(date,
                  style:
                      const TextStyle(fontSize: 12, color: Color(0xFF94A3B8))),
          ]),
          const SizedBox(height: 7),
          Text(review['body']?.toString() ?? '',
              style: const TextStyle(color: Color(0xFF475569), height: 1.5)),
        ])),
      ]),
    );
  }
}

class _InfoPill extends StatelessWidget {
  const _InfoPill(
      {required this.icon, required this.label, required this.color});
  final IconData icon;
  final String label;
  final Color color;
  @override
  Widget build(BuildContext context) => Container(
        constraints: const BoxConstraints(maxWidth: 310),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 9),
        decoration: BoxDecoration(
            color: color.withValues(alpha: .09),
            borderRadius: BorderRadius.circular(14)),
        child: Row(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, color: color, size: 18),
          const SizedBox(width: 6),
          Flexible(
              child: Text(label,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(color: color, fontWeight: FontWeight.w800))),
        ]),
      );
}

class _InlineNotice extends StatelessWidget {
  const _InlineNotice({required this.message, this.success = false});
  final String message;
  final bool success;
  @override
  Widget build(BuildContext context) => Container(
      width: double.infinity,
      margin: const EdgeInsets.only(top: 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
          color: success ? const Color(0xFFECFDF5) : const Color(0xFFFFF1F2),
          borderRadius: BorderRadius.circular(14)),
      child: Row(children: [
        Icon(success ? Icons.check_circle : Icons.error_outline,
            color: success ? const Color(0xFF059669) : const Color(0xFFBE123C)),
        const SizedBox(width: 9),
        Expanded(child: Text(message)),
      ]));
}

class _EmptyReviews extends StatelessWidget {
  const _EmptyReviews();
  @override
  Widget build(BuildContext context) => const _SectionCard(
      icon: Icons.forum_outlined,
      title: 'Sé la primera persona en comentar',
      child: Text('Aún no hay reseñas para este lugar.',
          style: TextStyle(color: Color(0xFF64748B))));
}

class _ErrorState extends StatelessWidget {
  const _ErrorState({required this.message, required this.retry});
  final String message;
  final VoidCallback retry;
  @override
  Widget build(BuildContext context) => Center(
      child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            const Icon(Icons.cloud_off_outlined,
                size: 46, color: VibeColors.blue),
            const SizedBox(height: 12),
            Text(message, textAlign: TextAlign.center),
            const SizedBox(height: 14),
            FilledButton(
                onPressed: retry, child: const Text('Intentar de nuevo')),
          ])));
}
