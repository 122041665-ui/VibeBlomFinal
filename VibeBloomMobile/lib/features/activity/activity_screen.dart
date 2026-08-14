import 'package:flutter/material.dart';

import '../../core/api/api_client.dart';
import '../../core/theme/vibe_theme.dart';
import '../../core/widgets/place_type_icon.dart';
import 'create_place_screen.dart';

class ActivityScreen extends StatefulWidget {
  const ActivityScreen({super.key});
  @override
  State<ActivityScreen> createState() => _ActivityScreenState();
}

class _ActivityScreenState extends State<ActivityScreen> {
  List<Map<String, dynamic>> approvals = const [];
  bool loading = true;
  String? error;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      error = null;
    });
    try {
      final r = await ApiClient.dio.get<List<dynamic>>('/approvals/mine');
      if (mounted) {
        setState(() => approvals = (r.data ?? [])
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList());
      }
    } catch (e) {
      if (mounted) setState(() => error = ApiClient.messageFrom(e));
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
            title: const Text('Mis aprobaciones',
                style: TextStyle(fontWeight: FontWeight.w900))),
        floatingActionButton: FloatingActionButton.extended(
            onPressed: () async {
              final ok = await Navigator.push<bool>(context,
                  MaterialPageRoute(builder: (_) => const CreatePlaceScreen()));
              if (ok == true) _load();
            },
            icon: const Icon(Icons.add_location_alt),
            label: const Text('Proponer lugar')),
        body: loading
            ? const Center(child: CircularProgressIndicator())
            : error != null
                ? Center(child: Text(error!))
                : RefreshIndicator(
                    onRefresh: _load,
                    child: ListView(
                        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                        children: [
                          Container(
                              padding: const EdgeInsets.all(20),
                              decoration: BoxDecoration(
                                  color: const Color(0xFFEFF6FF),
                                  borderRadius: BorderRadius.circular(24),
                                  border: Border.all(
                                      color: const Color(0xFFBFDBFE))),
                              child: Row(children: [
                                const CircleAvatar(
                                    backgroundColor: Color(0xFFDBEAFE),
                                    child: Icon(Icons.fact_check,
                                        color: VibeColors.blue)),
                                const SizedBox(width: 13),
                                Expanded(
                                    child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                      const Text('Historial de solicitudes',
                                          style: TextStyle(
                                              fontSize: 19,
                                              fontWeight: FontWeight.w900)),
                                      Text(
                                          '${approvals.where((a) => (a['status'] ?? 'pending') == 'pending').length} pendientes · '
                                          '${approvals.where((a) => a['status'] == 'approved').length} aprobadas · '
                                          '${approvals.where((a) => a['status'] == 'rejected').length} rechazadas')
                                    ]))
                              ])),
                          const SizedBox(height: 16),
                          if (approvals.isEmpty)
                            const Padding(
                                padding: EdgeInsets.all(45),
                                child: Center(
                                    child: Text('Aún no tienes solicitudes.'))),
                          ...approvals.map((a) => Card(
                              margin: const EdgeInsets.only(bottom: 13),
                              child: Padding(
                                  padding: const EdgeInsets.all(17),
                                  child: Row(children: [
                                    CircleAvatar(
                                        backgroundColor:
                                            const Color(0xFFDBEAFE),
                                        child: Icon(
                                            placeTypeIcon(
                                                a['type']?.toString()),
                                            color: VibeColors.blue)),
                                    const SizedBox(width: 13),
                                    Expanded(
                                        child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                          Text(a['name']?.toString() ?? 'Lugar',
                                              style: const TextStyle(
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.w900)),
                                          Text(
                                              '${a['type'] ?? 'Lugar'} · ${a['city'] ?? ''}'),
                                          const SizedBox(height: 7),
                                          _StatusChip(a['status']?.toString())
                                        ]))
                                  ])))),
                        ])),
      );
}

class _StatusChip extends StatelessWidget {
  const _StatusChip(this.status);
  final String? status;

  @override
  Widget build(BuildContext context) {
    final value = status ?? 'pending';
    final approved = value == 'approved';
    final rejected = value == 'rejected';
    return Chip(
      avatar: Icon(
          approved
              ? Icons.check_circle_outline
              : rejected
                  ? Icons.cancel_outlined
                  : Icons.schedule,
          size: 16),
      label: Text(approved
          ? 'Aprobada'
          : rejected
              ? 'Rechazada'
              : 'Pendiente'),
      backgroundColor: approved
          ? const Color(0xFFDCFCE7)
          : rejected
              ? const Color(0xFFFEE2E2)
              : const Color(0xFFFEF3C7),
    );
  }
}
