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

  factory Place.fromJson(Map<String, dynamic> json) {
    final photos = json['photos'];
    String? photo;
    if (photos is List && photos.isNotEmpty && photos.first is Map) {
      final first = photos.first as Map;
      photo = (first['url'] ?? first['photo_url'])?.toString();
    }
    return Place(
      id: (json['id'] as num).toInt(),
      name: json['name']?.toString() ?? 'Lugar',
      city: json['city']?.toString() ?? 'Sin ciudad',
      type: json['type']?.toString() ?? 'OTRO',
      description: json['description']?.toString(),
      price: (json['price'] as num?)?.toDouble(),
      latitude: (json['latitude'] as num?)?.toDouble(),
      longitude: (json['longitude'] as num?)?.toDouble(),
      photoUrl: photo,
    );
  }
}
