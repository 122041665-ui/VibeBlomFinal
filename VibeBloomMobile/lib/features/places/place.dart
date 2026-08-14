import '../../core/api/api_client.dart';

class Place {
  const Place({
    required this.id,
    required this.name,
    required this.city,
    required this.type,
    this.description,
    this.price,
    this.latitude,
    this.longitude,
    this.photoUrl,
    this.photoUrls = const [],
    this.rating = 0,
    this.address,
    this.reviews = const [],
    this.creatorId,
    this.creatorName,
    this.creatorPhotoUrl,
  });

  final int id;
  final String name;
  final String city;
  final String type;
  final String? description;
  final double? price;
  final double? latitude;
  final double? longitude;
  final String? photoUrl;
  final List<String> photoUrls;
  final int rating;
  final String? address;
  final List<Map<String, dynamic>> reviews;
  final int? creatorId;
  final String? creatorName;
  final String? creatorPhotoUrl;

  factory Place.fromJson(Map<String, dynamic> json) {
    final photos = json['photos_urls'] ?? json['photos'];
    String? photo = json['photo_url']?.toString();
    if (photos is List && photos.isNotEmpty && photos.first is Map) {
      final first = photos.first as Map;
      photo = (first['url'] ?? first['photo_url'])?.toString();
    } else if (photo == null && photos is List && photos.isNotEmpty) {
      photo = photos.first?.toString();
    }
    final photoUrls = <String>[];
    if (photos is List) {
      for (final item in photos) {
        final raw = item is Map ? (item['url'] ?? item['photo_url']) : item;
        final url = ApiClient.mediaUrl(raw);
        if (url != null && !photoUrls.contains(url)) photoUrls.add(url);
      }
    }
    final mainUrl = ApiClient.mediaUrl(photo);
    if (mainUrl != null && !photoUrls.contains(mainUrl)) {
      photoUrls.insert(0, mainUrl);
    }
    final creator = json['user'] is Map ? json['user'] as Map : const {};
    return Place(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? 'Lugar',
      city: json['city']?.toString() ?? 'Sin ciudad',
      type: json['type']?.toString() ?? 'OTRO',
      description: json['description']?.toString(),
      price: (json['price'] as num?)?.toDouble(),
      latitude: (json['lat'] as num?)?.toDouble(),
      longitude: (json['lng'] as num?)?.toDouble(),
      photoUrl: mainUrl,
      photoUrls: photoUrls,
      rating: (json['rating'] as num?)?.toInt() ?? 0,
      address: json['address']?.toString(),
      reviews: (json['reviews'] as List?)
              ?.whereType<Map>()
              .map((item) => Map<String, dynamic>.from(item))
              .toList() ??
          const [],
      creatorId: (creator['id'] as num?)?.toInt() ??
          (json['user_id'] as num?)?.toInt(),
      creatorName: creator['name']?.toString(),
      creatorPhotoUrl: ApiClient.mediaUrl(creator['profile_photo_url']),
    );
  }
}
