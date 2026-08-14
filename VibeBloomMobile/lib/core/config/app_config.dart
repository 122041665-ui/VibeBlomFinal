class AppConfig {
  const AppConfig._();

  static const apiUrl = String.fromEnvironment(
    'API_URL',
    defaultValue: 'https://api.209-38-116-181.nip.io',
  );
  static const webUrl = String.fromEnvironment(
    'WEB_URL',
    defaultValue: 'https://vibe.209-38-116-181.nip.io',
  );
}
