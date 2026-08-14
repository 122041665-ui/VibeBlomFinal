import 'package:flutter/material.dart';

IconData placeTypeIcon(String? value) {
  final type = (value ?? '').toUpperCase().replaceAll('É', 'E');
  if (type.contains('RESTAUR')) return Icons.restaurant_outlined;
  if (type.contains('CAFE')) return Icons.local_cafe_outlined;
  if (type == 'BAR') return Icons.local_bar_outlined;
  if (type.contains('ANTRO')) return Icons.music_note_outlined;
  if (type.contains('PARQUE')) return Icons.park_outlined;
  if (type.contains('MIRADOR')) return Icons.landscape_outlined;
  if (type.contains('MUSEO')) return Icons.museum_outlined;
  if (type.contains('PLAZA')) return Icons.account_balance_outlined;
  if (type.contains('CENTRO COMERCIAL')) return Icons.shopping_bag_outlined;
  return Icons.place_outlined;
}
