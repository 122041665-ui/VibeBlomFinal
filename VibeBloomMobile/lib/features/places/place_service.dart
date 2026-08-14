import 'dart:convert';

import 'package:dio/dio.dart';

import '../../core/api/api_client.dart';
import 'place.dart';

class PlaceService {
  Future<List<Place>> list() async {
    try {
      final response = await ApiClient.dio.get<List<dynamic>>('/places');
      final raw = response.data ?? const [];
      try {
        await ApiClient.storage
            .write(key: 'cached_public_places', value: jsonEncode(raw));
      } catch (_) {
        // La caché es auxiliar: nunca debe bloquear los datos en línea.
      }
      return raw
          .whereType<Map>()
          .map((item) => Place.fromJson(Map<String, dynamic>.from(item)))
          .toList();
    } on DioException {
      final cached = await ApiClient.storage.read(key: 'cached_public_places');
      if (cached == null || cached.isEmpty) rethrow;
      try {
        final raw = jsonDecode(cached) as List<dynamic>;
        return raw
            .whereType<Map>()
            .map((item) => Place.fromJson(Map<String, dynamic>.from(item)))
            .toList();
      } catch (_) {
        rethrow;
      }
    }
  }

  Future<void> toggleFavorite(int placeId) async {
    await ApiClient.dio
        .post<void>('/favorites/toggle', data: {'place_id': placeId});
  }

  Future<Place> detail(int placeId) async {
    final response =
        await ApiClient.dio.get<Map<String, dynamic>>('/places/$placeId');
    return Place.fromJson(response.data!);
  }

  Future<List<Place>> favorites() async {
    final response = await ApiClient.dio.get<List<dynamic>>('/favorites');
    return (response.data ?? const [])
        .whereType<Map>()
        .map((item) => item['place'])
        .whereType<Map>()
        .map((item) => Place.fromJson(Map<String, dynamic>.from(item)))
        .toList();
  }

  Future<void> addReview(int placeId, String body) async {
    await ApiClient.dio
        .post('/reviews', data: {'place_id': placeId, 'body': body.trim()});
  }
}
