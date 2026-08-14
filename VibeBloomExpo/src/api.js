import AsyncStorage from '@react-native-async-storage/async-storage';

export const API_URL = 'https://api.209-38-116-181.nip.io';

export async function api(path, options = {}) {
  const token = await AsyncStorage.getItem('access_token');
  const response = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });
  const text = await response.text();
  let data;
  try { data = text ? JSON.parse(text) : null; } catch { data = text; }
  if (!response.ok) {
    const detail = data?.detail;
    const message = Array.isArray(detail)
      ? detail.map((item) => item.msg).filter(Boolean).join('\n')
      : detail || 'No fue posible completar la operación.';
    const error = new Error(message);
    error.status = response.status;
    throw error;
  }
  return data;
}

export const post = (path, body) => api(path, { method: 'POST', body: JSON.stringify(body) });
