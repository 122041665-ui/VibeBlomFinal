import 'package:flutter_test/flutter_test.dart';
import 'package:vibebloom_mobile/features/places/place.dart';

void main() {
  test('conserva todas las fotos y el perfil creador del lugar', () {
    final place = Place.fromJson({
      'id': 24,
      'name': 'Maniatica',
      'city': 'Querétaro',
      'type': 'Cafetería',
      'photos_urls': [
        'http://127.0.0.1:8010/storage/uno.jpg',
        'http://127.0.0.1:8010/storage/dos.jpg',
      ],
      'user': {
        'id': 27,
        'name': 'Dulce Montes',
        'profile_photo_url': 'http://127.0.0.1:8010/storage/perfil.jpg',
      },
    });

    expect(place.photoUrls, hasLength(2));
    expect(place.creatorId, 27);
    expect(place.creatorName, 'Dulce Montes');
  });
}
