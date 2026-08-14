import { StatusBar } from 'expo-status-bar';
import * as Location from 'expo-location';
import AsyncStorage from '@react-native-async-storage/async-storage';
import React, { useEffect, useMemo, useState } from 'react';
import {
  ActivityIndicator, Alert, FlatList, Image, KeyboardAvoidingView, Linking,
  Platform, Pressable, RefreshControl, SafeAreaView, ScrollView, StyleSheet,
  Text, TextInput, View,
} from 'react-native';
import { api, post } from './src/api';

const C = { navy: '#172554', blue: '#2563EB', cyan: '#06B6D4', bg: '#F5F8FF', muted: '#64748B', line: '#E2E8F0', white: '#FFF', danger: '#DC2626' };
const tabs = [{ key: 'home', icon: '⌂', label: 'Inicio' }, { key: 'map', icon: '⌖', label: 'Mapa' }, { key: 'community', icon: '♧', label: 'Comunidad' }, { key: 'profile', icon: '☺', label: 'Mi Vibe' }];

function Logo({ large = false }) {
  return <Image
    source={require('./assets/vibebloom-logo.png')}
    resizeMode="contain"
    style={large ? styles.logoLarge : styles.logoHorizontal}
    accessibilityLabel="VibeBloom"
  />;
}
function Button({ title, onPress, outline, disabled, small }) {
  return <Pressable disabled={disabled} onPress={onPress} style={({ pressed }) => [styles.button, outline && styles.buttonOutline, small && styles.buttonSmall, (disabled || pressed) && { opacity: .6 }]}><Text style={[styles.buttonText, outline && { color: C.blue }]}>{title}</Text></Pressable>;
}
function Field({ label, ...props }) {
  return <View style={{ marginBottom: 15 }}><Text style={styles.label}>{label}</Text><TextInput placeholderTextColor="#94A3B8" style={styles.input} {...props} /></View>;
}
function Auth({ onEnter }) {
  const [mode, setMode] = useState('login');
  const [name, setName] = useState(''); const [email, setEmail] = useState(''); const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false); const [error, setError] = useState('');
  const submit = async () => {
    if (!email.includes('@') || password.length < 8 || (mode === 'register' && !name.trim())) return setError('Completa los campos correctamente. La contraseña debe tener 8 caracteres.');
    setLoading(true); setError('');
    try {
      const data = await post(mode === 'login' ? '/auth/login' : '/auth/register', mode === 'login' ? { email: email.trim(), password } : { name: name.trim(), email: email.trim(), password, role: 'user' });
      await AsyncStorage.setItem('access_token', data.access_token);
      onEnter({ token: data.access_token, user: data.user, guest: false });
    } catch (e) { setError(e.message); } finally { setLoading(false); }
  };
  return <SafeAreaView style={styles.authBg}><StatusBar style="dark" /><KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.center}><ScrollView contentContainerStyle={styles.authScroll} keyboardShouldPersistTaps="handled"><View style={styles.authCard}>
    <View style={{ alignItems: 'center' }}><Logo large /><Text style={styles.authTitle}>{mode === 'login' ? 'Iniciar sesión' : 'Crear cuenta'}</Text><Text style={styles.authSubtitle}>{mode === 'login' ? 'Accede para guardar favoritos, recuerdos y ver detalles.' : 'Únete a VibeBloom y guarda tus lugares favoritos.'}</Text></View>
    {mode === 'register' && <Field label="Nombre" value={name} onChangeText={setName} placeholder="Tu nombre" />}
    <Field label="Correo electrónico" value={email} onChangeText={setEmail} placeholder="tucorreo@ejemplo.com" keyboardType="email-address" autoCapitalize="none" />
    <Field label="Contraseña" value={password} onChangeText={setPassword} placeholder="••••••••" secureTextEntry />
    {!!error && <Text style={styles.error}>{error}</Text>}
    {loading ? <ActivityIndicator color={C.blue} style={{ margin: 16 }} /> : <Button title={mode === 'login' ? 'Iniciar sesión' : 'Crear cuenta'} onPress={submit} />}
    <Button outline title={mode === 'login' ? 'Crear cuenta' : 'Ya tengo cuenta'} onPress={() => { setMode(mode === 'login' ? 'register' : 'login'); setError(''); }} />
    <Pressable onPress={() => onEnter({ guest: true, user: null })}><Text style={styles.guest}>Seguir explorando sin cuenta</Text></Pressable>
  </View></ScrollView></KeyboardAvoidingView></SafeAreaView>;
}

function Stars({ value = 0 }) { return <Text style={styles.stars}>{'★'.repeat(Math.max(0, Math.min(5, value)))}<Text style={{ color: '#CBD5E1' }}>{'★'.repeat(5 - Math.max(0, Math.min(5, value)))}</Text></Text>; }
function distance(a, b, c, d) { const r = 6371, p = Math.PI / 180, x = (c - a) * p, y = (d - b) * p; const h = Math.sin(x / 2) ** 2 + Math.cos(a * p) * Math.cos(c * p) * Math.sin(y / 2) ** 2; return 2 * r * Math.asin(Math.sqrt(h)); }
function PlaceCard({ item, location, onOpen }) {
  const km = location && item.lat != null ? distance(location.latitude, location.longitude, item.lat, item.lng) : null;
  return <Pressable onPress={() => onOpen(item)} style={styles.placeCard}>
    {item.photo_url ? <Image source={{ uri: item.photo_url }} style={styles.placeImage} /> : <View style={[styles.placeImage, styles.imageEmpty]}><Text style={{ fontSize: 34 }}>✿</Text></View>}
    <View style={styles.placeBody}><View style={styles.row}><Text style={styles.placeName} numberOfLines={1}>{item.name}</Text><View style={styles.pill}><Text style={styles.pillText}>{item.type}</Text></View></View>
      <Text style={styles.muted}>📍 {item.city}{km != null ? ` · ${km.toFixed(1)} km` : ''}</Text><Stars value={item.rating || 0} />
      <Text style={styles.description} numberOfLines={2}>{item.description || item.address || 'Descubre este lugar.'}</Text><Text style={styles.price}>{Number(item.price || 0) === 0 ? 'Entrada libre' : `$${Number(item.price).toFixed(0)} aprox.`}</Text>
    </View></Pressable>;
}
function Home({ session, onLogin }) {
  const [places, setPlaces] = useState([]); const [loading, setLoading] = useState(true); const [error, setError] = useState(''); const [query, setQuery] = useState(''); const [selected, setSelected] = useState(null); const [location, setLocation] = useState(null);
  const load = async () => { setLoading(true); setError(''); try { const data = await api('/places'); setPlaces(data); await AsyncStorage.setItem('places_cache', JSON.stringify(data)); } catch (e) { const cache = await AsyncStorage.getItem('places_cache'); if (cache) setPlaces(JSON.parse(cache)); else setError(e.message); } finally { setLoading(false); } };
  useEffect(() => { load(); Location.requestForegroundPermissionsAsync().then(async ({ status }) => { if (status === 'granted') setLocation((await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced })).coords); }).catch(() => {}); }, []);
  const shown = useMemo(() => places.filter(p => `${p.name} ${p.city} ${p.type}`.toLowerCase().includes(query.toLowerCase())).sort((a, b) => location ? distance(location.latitude, location.longitude, a.lat, a.lng) - distance(location.latitude, location.longitude, b.lat, b.lng) : 0), [places, query, location]);
  if (selected) return <PlaceDetail place={selected} session={session} onBack={() => setSelected(null)} onLogin={onLogin} />;
  return <View style={styles.page}><View style={styles.hero}><Text style={styles.heroTitle}>Encuentra tu próxima vibra</Text><Text style={styles.heroText}>Lugares reales, experiencias auténticas.</Text><TextInput style={styles.search} value={query} onChangeText={setQuery} placeholder="Buscar lugar, ciudad o categoría…" placeholderTextColor="#94A3B8" /></View>
    {error ? <View style={styles.message}><Text style={styles.error}>{error}</Text><Button small title="Reintentar" onPress={load} /></View> : <FlatList data={shown} keyExtractor={p => String(p.id)} renderItem={({ item }) => <PlaceCard item={item} location={location} onOpen={setSelected} />} contentContainerStyle={styles.list} refreshControl={<RefreshControl refreshing={loading} onRefresh={load} tintColor={C.blue} />} ListEmptyComponent={loading ? <ActivityIndicator color={C.blue} /> : <Text style={styles.empty}>No encontramos lugares.</Text>} />}
  </View>;
}
function PlaceDetail({ place: initial, session, onBack, onLogin }) {
  const [place, setPlace] = useState(initial); const [review, setReview] = useState(''); const [busy, setBusy] = useState(false);
  useEffect(() => { api(`/places/${initial.id}`).then(setPlace).catch(() => {}); }, [initial.id]);
  const favorite = async () => { if (session.guest) return onLogin(); try { await post('/favorites/toggle', { place_id: place.id }); Alert.alert('Listo', 'Actualizamos tus favoritos.'); } catch (e) { Alert.alert('Error', e.message); } };
  const sendReview = async () => { if (session.guest) return onLogin(); if (!review.trim()) return; setBusy(true); try { await post('/reviews', { place_id: place.id, body: review.trim() }); setReview(''); setPlace(await api(`/places/${place.id}`)); } catch (e) { Alert.alert('Error', e.message); } finally { setBusy(false); } };
  const photos = place.photos_urls?.length ? place.photos_urls : place.photo_url ? [place.photo_url] : [];
  return <ScrollView style={styles.page} contentContainerStyle={{ paddingBottom: 30 }}><Pressable onPress={onBack} style={styles.back}><Text style={styles.backText}>‹ Volver</Text></Pressable>{photos[0] && <Image source={{ uri: photos[0] }} style={styles.detailImage} />}
    <View style={styles.detailBody}><View style={styles.row}><Text style={styles.detailTitle}>{place.name}</Text><View style={styles.pill}><Text style={styles.pillText}>{place.type}</Text></View></View><Stars value={place.rating || 0} /><Text style={styles.muted}>📍 {place.address || place.city}</Text><Text style={styles.detailDescription}>{place.description}</Text>
      <View style={styles.actions}><Button small title="♡ Favorito" onPress={favorite} /><Button small outline title="Cómo llegar" onPress={() => Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${place.lat},${place.lng}`)} /></View>
      <Text style={styles.sectionTitle}>Reseñas</Text>{(place.reviews || []).map(r => <View key={r.id} style={styles.review}><Text style={styles.reviewName}>{r.user?.name || 'Usuario'}</Text><Text style={styles.reviewBody}>{r.body}</Text></View>)}
      <TextInput style={[styles.input, { minHeight: 90 }]} multiline value={review} onChangeText={setReview} placeholder="Comparte tu experiencia…" placeholderTextColor="#94A3B8" /><Button title={busy ? 'Publicando…' : 'Publicar reseña'} disabled={busy} onPress={sendReview} />
    </View></ScrollView>;
}
function MapPlaces() { const [places, setPlaces] = useState([]); useEffect(() => { api('/places').then(setPlaces).catch(() => {}); }, []); return <FlatList style={styles.page} contentContainerStyle={styles.list} ListHeaderComponent={<><Text style={styles.pageTitle}>Mapa de lugares</Text><Text style={styles.pageSubtitle}>Toca un lugar para abrirlo en tu aplicación de mapas.</Text></>} data={places} keyExtractor={p => String(p.id)} renderItem={({ item }) => <Pressable style={styles.mapRow} onPress={() => Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${item.lat},${item.lng}`)}><Text style={styles.mapIcon}>⌖</Text><View style={{ flex: 1 }}><Text style={styles.placeName}>{item.name}</Text><Text style={styles.muted}>{item.address || item.city}</Text></View><Text style={{ color: C.blue, fontSize: 22 }}>›</Text></Pressable>} />; }
function Community({ session, onLogin }) { const [users, setUsers] = useState([]); const [error, setError] = useState(''); useEffect(() => { if (session.guest) return; api('/users/community').then(setUsers).catch(e => setError(e.message)); }, [session.guest]); if (session.guest) return <LoginGate onLogin={onLogin} />; return <FlatList style={styles.page} contentContainerStyle={styles.list} ListHeaderComponent={<><Text style={styles.pageTitle}>Comunidad</Text><Text style={styles.pageSubtitle}>Personas que comparten nuevas experiencias.</Text>{!!error && <Text style={styles.error}>{error}</Text>}</>} data={users} keyExtractor={(u, i) => String(u.id || i)} renderItem={({ item }) => <View style={styles.userRow}>{item.profile_photo_url ? <Image source={{ uri: item.profile_photo_url }} style={styles.avatar} /> : <View style={styles.avatarEmpty}><Text style={{ fontSize: 20 }}>☺</Text></View>}<View><Text style={styles.placeName}>{item.name}</Text><Text style={styles.muted}>{item.places_count || 0} lugares compartidos</Text></View></View>} />; }
function LoginGate({ onLogin }) { return <View style={styles.center}><Text style={{ fontSize: 52 }}>✿</Text><Text style={styles.pageTitle}>Únete a VibeBloom</Text><Text style={[styles.pageSubtitle, { textAlign: 'center', marginHorizontal: 35 }]}>Inicia sesión para guardar favoritos, participar y ver tu perfil.</Text><Button title="Iniciar sesión" onPress={onLogin} /></View>; }
function Profile({ session, onLogout, onLogin }) { const [user, setUser] = useState(session.user); useEffect(() => { if (!session.guest) api('/users/me/profile').then(setUser).catch(() => {}); }, [session.guest]); if (session.guest) return <LoginGate onLogin={onLogin} />; return <ScrollView style={styles.page} contentContainerStyle={styles.profile}><View style={styles.bigAvatar}>{user?.profile_photo_url ? <Image source={{ uri: user.profile_photo_url }} style={styles.bigAvatarImage} /> : <Text style={{ fontSize: 44, color: C.blue }}>☺</Text>}</View><Text style={styles.profileName}>{user?.name || 'Mi perfil'}</Text><Text style={styles.muted}>{user?.email}</Text><View style={styles.stats}><View><Text style={styles.statNumber}>{user?.places_count || 0}</Text><Text style={styles.muted}>Lugares</Text></View><View><Text style={styles.statNumber}>{user?.reviews_count || 0}</Text><Text style={styles.muted}>Reseñas</Text></View></View><Button outline title="Cerrar sesión" onPress={onLogout} /></ScrollView>; }

function AppShell({ session, setSession }) { const [tab, setTab] = useState('home'); const login = async () => { await AsyncStorage.removeItem('access_token'); setSession(null); }; const logout = () => Alert.alert('Cerrar sesión', '¿Quieres salir de VibeBloom?', [{ text: 'Cancelar' }, { text: 'Salir', style: 'destructive', onPress: login }]); return <SafeAreaView style={styles.app}><StatusBar style="dark" /><View style={styles.header}><Logo /><View style={styles.headerPill}><Text style={styles.headerPillText}>{tabs.find(t => t.key === tab)?.label}</Text></View></View><View style={{ flex: 1 }}>{tab === 'home' && <Home session={session} onLogin={login} />}{tab === 'map' && <MapPlaces />}{tab === 'community' && <Community session={session} onLogin={login} />}{tab === 'profile' && <Profile session={session} onLogout={logout} onLogin={login} />}</View><View style={styles.tabBar}>{tabs.map(t => <Pressable key={t.key} style={styles.tab} onPress={() => setTab(t.key)}><Text style={[styles.tabIcon, tab === t.key && styles.tabActive]}>{t.icon}</Text><Text style={[styles.tabLabel, tab === t.key && styles.tabActive]}>{t.label}</Text></Pressable>)}</View></SafeAreaView>; }
export default function App() { const [session, setSession] = useState(undefined); useEffect(() => { (async () => { const token = await AsyncStorage.getItem('access_token'); if (!token) return setSession(null); try { const user = await api('/users/me/profile'); setSession({ token, user, guest: false }); } catch { await AsyncStorage.removeItem('access_token'); setSession(null); } })(); }, []); if (session === undefined) return <View style={styles.center}><ActivityIndicator size="large" color={C.blue} /></View>; return session ? <AppShell session={session} setSession={setSession} /> : <Auth onEnter={setSession} />; }

const styles = StyleSheet.create({
  app: { flex: 1, backgroundColor: C.bg }, page: { flex: 1, backgroundColor: C.bg }, center: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: 14 }, row: { flexDirection: 'row', alignItems: 'center', gap: 8 }, muted: { color: C.muted, fontSize: 13, lineHeight: 19 },
  logoHorizontal: { width: 152, height: 55 }, logoLarge: { width: 190, height: 139 },
  authBg: { flex: 1, backgroundColor: '#EEF5FF' }, authScroll: { flexGrow: 1, justifyContent: 'center', padding: 20 }, authCard: { backgroundColor: C.white, borderRadius: 28, padding: 24, shadowColor: C.navy, shadowOpacity: .12, shadowRadius: 24, shadowOffset: { width: 0, height: 12 }, elevation: 6 }, authTitle: { fontSize: 25, fontWeight: '900', color: C.navy, marginTop: 19 }, authSubtitle: { color: C.muted, textAlign: 'center', lineHeight: 20, margin: 8, marginBottom: 24 },
  label: { color: C.navy, fontWeight: '800', marginBottom: 6 }, input: { borderWidth: 1, borderColor: C.line, backgroundColor: '#FAFCFF', color: C.navy, borderRadius: 14, paddingHorizontal: 14, paddingVertical: Platform.OS === 'ios' ? 14 : 11, fontSize: 15 }, error: { color: C.danger, backgroundColor: '#FEF2F2', padding: 11, borderRadius: 10, marginBottom: 10 }, button: { backgroundColor: C.blue, borderRadius: 14, paddingVertical: 14, paddingHorizontal: 20, alignItems: 'center', marginVertical: 5, minWidth: 130 }, buttonOutline: { backgroundColor: C.white, borderWidth: 1.5, borderColor: C.blue }, buttonSmall: { paddingVertical: 10, minWidth: 110 }, buttonText: { color: C.white, fontWeight: '800' }, guest: { color: C.blue, textAlign: 'center', textDecorationLine: 'underline', fontWeight: '700', marginTop: 14 },
  header: { height: 66, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 17, backgroundColor: C.white, borderBottomWidth: 1, borderColor: C.line }, headerPill: { backgroundColor: '#EFF6FF', paddingHorizontal: 11, paddingVertical: 6, borderRadius: 99 }, headerPillText: { color: C.blue, fontSize: 12, fontWeight: '800' },
  hero: { backgroundColor: C.navy, padding: 19, paddingBottom: 24 }, heroTitle: { color: C.white, fontSize: 25, fontWeight: '900' }, heroText: { color: '#BFDBFE', marginTop: 4, marginBottom: 15 }, search: { backgroundColor: C.white, borderRadius: 15, padding: 13, fontSize: 15, color: C.navy }, list: { padding: 15, paddingBottom: 30 }, placeCard: { backgroundColor: C.white, borderRadius: 20, overflow: 'hidden', marginBottom: 15, borderWidth: 1, borderColor: C.line }, placeImage: { height: 165, width: '100%' }, imageEmpty: { backgroundColor: '#DBEAFE', alignItems: 'center', justifyContent: 'center' }, placeBody: { padding: 15 }, placeName: { flex: 1, color: C.navy, fontSize: 16, fontWeight: '900' }, pill: { backgroundColor: '#E0F2FE', paddingHorizontal: 9, paddingVertical: 4, borderRadius: 99 }, pillText: { color: '#0369A1', fontSize: 11, fontWeight: '800' }, stars: { color: '#F59E0B', marginVertical: 5, letterSpacing: 1 }, description: { color: '#475569', lineHeight: 19 }, price: { color: C.blue, fontWeight: '900', marginTop: 8 }, message: { padding: 25, alignItems: 'center' }, empty: { color: C.muted, textAlign: 'center', padding: 30 },
  back: { paddingHorizontal: 17, paddingVertical: 12 }, backText: { color: C.blue, fontWeight: '900', fontSize: 16 }, detailImage: { width: '100%', height: 260 }, detailBody: { padding: 18 }, detailTitle: { flex: 1, fontSize: 25, fontWeight: '900', color: C.navy }, detailDescription: { color: '#334155', lineHeight: 23, marginVertical: 15, fontSize: 15 }, actions: { flexDirection: 'row', gap: 9, marginBottom: 20 }, sectionTitle: { color: C.navy, fontSize: 19, fontWeight: '900', marginVertical: 12 }, review: { backgroundColor: C.white, borderWidth: 1, borderColor: C.line, borderRadius: 13, padding: 13, marginBottom: 9 }, reviewName: { fontWeight: '900', color: C.navy }, reviewBody: { color: '#475569', marginTop: 4 },
  pageTitle: { color: C.navy, fontSize: 26, fontWeight: '900', marginTop: 6 }, pageSubtitle: { color: C.muted, lineHeight: 20, marginTop: 3, marginBottom: 17 }, mapRow: { flexDirection: 'row', alignItems: 'center', gap: 12, backgroundColor: C.white, padding: 14, borderRadius: 15, borderWidth: 1, borderColor: C.line, marginBottom: 10 }, mapIcon: { fontSize: 27, color: C.blue }, userRow: { flexDirection: 'row', gap: 12, alignItems: 'center', backgroundColor: C.white, borderRadius: 15, borderWidth: 1, borderColor: C.line, padding: 13, marginBottom: 10 }, avatar: { width: 48, height: 48, borderRadius: 24 }, avatarEmpty: { width: 48, height: 48, borderRadius: 24, backgroundColor: '#DBEAFE', alignItems: 'center', justifyContent: 'center' },
  profile: { alignItems: 'center', padding: 28 }, bigAvatar: { width: 105, height: 105, borderRadius: 55, backgroundColor: '#DBEAFE', alignItems: 'center', justifyContent: 'center', overflow: 'hidden', marginTop: 15 }, bigAvatarImage: { width: '100%', height: '100%' }, profileName: { fontSize: 26, fontWeight: '900', color: C.navy, marginTop: 15 }, stats: { width: '100%', flexDirection: 'row', justifyContent: 'space-around', backgroundColor: C.white, borderWidth: 1, borderColor: C.line, borderRadius: 17, padding: 20, marginVertical: 24 }, statNumber: { textAlign: 'center', color: C.blue, fontSize: 24, fontWeight: '900' },
  tabBar: { height: 72, backgroundColor: C.white, borderTopWidth: 1, borderColor: C.line, flexDirection: 'row', paddingBottom: Platform.OS === 'ios' ? 7 : 2 }, tab: { flex: 1, alignItems: 'center', justifyContent: 'center' }, tabIcon: { color: '#94A3B8', fontSize: 24 }, tabLabel: { color: '#64748B', fontSize: 11, fontWeight: '700', marginTop: 2 }, tabActive: { color: C.blue },
});
