import 'package:flutter/material.dart';
import '../theme/vibe_theme.dart';
import '../config/app_config.dart';

class VibeLogo extends StatelessWidget {
  const VibeLogo(
      {this.compact = false,
      this.light = false,
      this.horizontal = false,
      super.key});
  final bool compact;
  final bool light;
  final bool horizontal;
  @override
  Widget build(BuildContext context) {
    if (!light) {
      return Semantics(
          label: 'VibeBloom',
          image: true,
          child: Image.network(
            '${AppConfig.webUrl}/images/vibebloom.png',
            width: compact ? 38 : (horizontal ? 165 : 175),
            height: compact ? 42 : (horizontal ? 58 : 128),
            fit: BoxFit.contain,
            filterQuality: FilterQuality.high,
            errorBuilder: (_, __, ___) => _paintedLogo(),
          ));
    }
    return _paintedLogo();
  }

  Widget _paintedLogo() {
    final mark = CustomPaint(
        size: Size(compact ? 34 : (horizontal ? 46 : 67),
            compact ? 40 : (horizontal ? 54 : 80)),
        painter: _PinPainter());
    if (compact) return mark;
    final name = Row(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('VibeBloom',
              style: TextStyle(
                  color: light ? Colors.white : VibeColors.navy,
                  fontSize: horizontal ? 25 : 31,
                  fontWeight: FontWeight.w900,
                  letterSpacing: -1.5)),
          Text('®',
              style: TextStyle(
                  color: light ? Colors.white : VibeColors.navy,
                  fontSize: 8,
                  fontWeight: FontWeight.w900)),
        ]);
    if (horizontal) {
      return Row(
          mainAxisSize: MainAxisSize.min,
          children: [mark, const SizedBox(width: 8), name]);
    }
    return Column(
        mainAxisSize: MainAxisSize.min,
        children: [mark, const SizedBox(height: 5), name]);
  }
}

class _PinPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final pin = Path()
      ..moveTo(size.width / 2, size.height)
      ..cubicTo(size.width * .42, size.height * .83, 2, size.height * .55, 2,
          size.height * .34)
      ..cubicTo(2, size.height * .05, size.width - 2, size.height * .05,
          size.width - 2, size.height * .34)
      ..cubicTo(size.width - 2, size.height * .55, size.width * .58,
          size.height * .83, size.width / 2, size.height)
      ..close();
    canvas.drawPath(pin, Paint()..color = VibeColors.navy);
    final center = Offset(size.width / 2, size.height * .30);
    canvas.drawCircle(
        center, size.width * .29, Paint()..color = const Color(0xFFFFFBEA));
    Path wave(double y) => Path()
      ..moveTo(size.width * .21, size.height * y)
      ..cubicTo(size.width * .39, size.height * (y - .01), size.width * .45,
          size.height * (y - .16), size.width * .78, size.height * (y - .15));
    for (final entry in <(double, Color)>[
      (.42, const Color(0xFFF0443E)),
      (.36, const Color(0xFFF9734F)),
      (.30, const Color(0xFFF7C84B)),
    ]) {
      canvas.drawPath(
          wave(entry.$1),
          Paint()
            ..color = entry.$2
            ..style = PaintingStyle.stroke
            ..strokeWidth = size.width * .09
            ..strokeCap = StrokeCap.round);
    }
    for (final y in [.13, .23, .33]) {
      canvas.drawLine(
          Offset(size.width * .84, size.height * y),
          Offset(size.width * .98, size.height * (y - .03)),
          Paint()
            ..color = VibeColors.coral
            ..strokeWidth = size.width * .06
            ..strokeCap = StrokeCap.round);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
