import '../../core/api/api_client.dart';
import 'place.dart';

class PlaceService {
  Future<List<Place>> list() async {
    final response = await ApiClient.dio.get<List<dynamic>>('/places');
    return (response.data ?? const [])
        .whereType<Map<String, dynamic>>()
        .map(Place.fromJson)
        .toList();
  }

  Future<void> toggleFavorite(int placeId) async {
    await ApiClient.dio.post<void>('/favorites/toggle', data: {'place_id': placeId});
  }
}
