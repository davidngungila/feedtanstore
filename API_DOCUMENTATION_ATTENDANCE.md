# Attendance Mobile App – API Documentation (Flutter Integration)

> **Independent File – Flutter Attendance MVP**
> Base URL: `{{base_url}}/api`
> Example: `https://feedtanstore.com/api` or `http://localhost:8000/api`
> Auth: Laravel Sanctum Bearer Token
> MVP Flow: `Login → Dashboard → Check In/Out → Attendance History → Profile`
> Full Flow: `Home | Attendance | Leave | Notifications | Profile` (Bottom Navigation)

---

## Table of Contents
1. [Quick Start MVP](#1-quick-start-mvp)
2. [Base URL & Headers](#2-base-url--headers)
3. [Authentication](#3-authentication)
4. [Dashboard & Today Status](#4-dashboard--today-status)
5. [Check In / Check Out](#5-check-in--check-out)
   - 5.3 [Biometric (Fingerprint/Face)](#53-biometric-authentication-fingerprintface--recommended)
6. [Attendance History & Calendar](#6-attendance-history--calendar)
7. [Profile](#7-profile)
8. [Leave Management](#8-leave-management)
9. [Notifications](#9-notifications)
10. [Error Handling & Status Codes](#10-error-handling)
11. [Data Models](#11-data-models)
12. [Flutter Integration Guide](#12-flutter-integration-guide)
13. [Permissions & Device Setup](#13-permissions)
14. [Postman Quick Test](#14-postman)
15. [Changelog](#15-changelog)

---

## 1. Quick Start MVP

The simplest integration path for MVP. Implement these 5 screens only. All other features (Leave, Calendar advanced, Notifications) are optional extensions.

| # | Screen | Endpoint(s) | Action |
|---|--------|-------------|--------|
| 1 | **Login** | `POST /attendance/login` | Phone/Email + Password → receives `token`. Save to `flutter_secure_storage`. |
| 2 | **Dashboard** | `GET /attendance/dashboard` | Shows `today.status` (present/absent/late/leave), `check_in`, `check_out`, `working_hours_formatted`, `can_check_in`, `can_check_out`. Single **Check In** / **Check Out** button. |
| 3 | **Check In/Out** | `POST /attendance/check-in` <br> `POST /attendance/check-out` | On button tap: **Biometric prompt** (`local_auth`) → get GPS (`Geolocator`) → optional selfie (`image_picker`) → POST as `multipart/form-data` with `biometric_verified=true`. On success reload dashboard. |
| 4 | **Attendance History** | `GET /attendance/history` <br> `GET /attendance/calendar` | List: Date, Check-in, Check-out, Total hours, Status. Calendar: colors Present(Green)/Absent(Red)/Late(Orange)/Leave(Blue). |
| 5 | **Profile** | `GET /attendance/profile` | Name, Employee ID, Department, Position, Phone, Profile photo. `POST /attendance/profile/photo` for update. |

**Bottom Navigation MVP:**

```
BottomNavigationBar
├─ Home (Dashboard)      → GET /attendance/dashboard
├─ Attendance            → GET /attendance/history + /calendar
├─ Leave (optional)      → GET /attendance/leaves
├─ Notifications (opt)   → GET /attendance/notifications
└─ Profile               → GET /attendance/profile
```

---

## 2. Base URL & Headers

### Base URL
```
{{base_url}}/api
```

### Default Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {{token}}   // for all protected routes
```

For file uploads (`check-in`, `check-out`, `profile/photo`) use:
```
Content-Type: multipart/form-data
```

### Bearer Token Flow (Dio Interceptor)
```dart
dio.interceptors.add(InterceptorsWrapper(
  onRequest: (options, handler) {
    if (token != null) options.headers['Authorization'] = 'Bearer $token';
    return handler.next(options);
  },
  onError: (e, handler) {
    if (e.response?.statusCode == 401) {
      // token expired → clear storage → navigate to login
    }
    return handler.next(e);
  }
));
```

---

## 3. Authentication

### 3.1 Login – Phone or Email
**Endpoint:** `POST /attendance/login`

Supports all three payload styles:
```json
// Style A (Recommended)
{ "login": "rider@example.com", "password": "password123" }
{ "login": "255712345678", "password": "password123" }

// Style B (Legacy)
{ "email": "rider@example.com", "password": "password123" }
{ "phone": "255712345678", "password": "password123" }
```

**Success 200 OK:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@feedtan.com",
    "phone": "255712345678",
    "employee_id": "EMP-0001",
    "department": "Sales",
    "position": "Cashier",
    "role": "staff",
    "profile_image": "https://feedtanstore.com/storage/profile-images/xxx.jpg"
  },
  "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890",
  "token_type": "Bearer"
}
```

**Error 422:**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": { "login": ["The provided credentials are incorrect."] }
}
```

**Flutter:**
```dart
Future<AuthResponse> login(String login, String password) async {
  final res = await dio.post('/attendance/login', data: {"login": login, "password": password});
  final token = res.data['token'] as String;
  await storage.write(key: 'token', value: token);
  dio.options.headers['Authorization'] = 'Bearer $token';
  return AuthResponse.fromJson(res.data);
}
```

---

### 3.2 Forgot Password
**Endpoint:** `POST /attendance/forgot-password`

**Request:**
```json
{ "email": "john@feedtan.com" }
```

**Success 200:**
```json
{ "message": "Password reset link sent to your email" }
```

**Error 422:** email not found
```json
{ "message": "The selected email is invalid.", "errors": {"email": ["..."]} }
```

Uses Laravel `Password::sendResetLink` → respects `password_reset_tokens` table and mail config.

---

### 3.3 Logout
**Endpoint:** `POST /attendance/logout`
**Auth:** Required

**Success 200:**
```json
{ "message": "Logged out successfully" }
```

Clears current Sanctum token only (other devices stay logged in).

---

### 3.4 Get Authenticated User (Me)
**Endpoint:** `GET /attendance/me`
**Auth:** Required

**Success 200:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@feedtan.com",
  "phone": "255712345678",
  "employee_id": "EMP-0001",
  "department": "Sales",
  "position": "Cashier",
  "role": "staff",
  "profile_image": "https://.../storage/profile-images/xxx.jpg",
  "email_verified_at": null
}
```

---

## 4. Dashboard & Today Status

### 4.1 Dashboard (Recommended for Home Screen)
**Endpoint:** `GET /attendance/dashboard`
**Auth:** Required

Aggregated data for dashboard cards + check-in/out button state.

**Success 200:**
```json
{
  "today": {
    "date": "2026-09-10",
    "status": "present",
    "check_in": "2026-09-10T08:42:11.000000Z",
    "check_out": null,
    "check_in_photo_url": "https://.../storage/attendance/check-in/xxx.jpg",
    "check_out_photo_url": null,
    "check_in_location": {
      "latitude": "-3.3869000",
      "longitude": "36.6883000",
      "address": "Moshi, Kilimanjaro"
    },
    "check_out_location": null,
    "working_hours": 2.35,
    "working_hours_formatted": "02:21",
    "can_check_in": false,
    "can_check_out": true,
    "check_in_biometric_verified": true,
    "check_out_biometric_verified": false,
    "check_in_biometric_type": "fingerprint",
    "check_out_biometric_type": null
  },
  "month_summary": {
    "month": "2026-09",
    "present": 8,
    "attendance_percentage": 80.0,
    "total_days_so_far": 10
  },
  "user": {
    "id": 1,
    "name": "John Doe",
    "employee_id": "EMP-0001",
    "department": "Sales",
    "position": "Cashier"
  }
}
```

**Status enum:** `present` | `absent` | `late` | `leave` | `half-day`

**UI Logic:**
```dart
if (data.today.canCheckIn) showButton("Check In", Icons.login, Colors.green);
else if (data.today.canCheckOut) showButton("Check Out", Icons.logout, Colors.orange);
else showChip("Completed for today ✓", Colors.grey);
```

Working hours updates live if checked in but not checked out: `now - check_in`.

---

### 4.2 Today Only (Lightweight)
**Endpoint:** `GET /attendance/today`
**Auth:** Required

Returns today attendance row or `status: absent` stub if none.

**Success 200 – with record:**
```json
{
  "id": 12,
  "user_id": 1,
  "date": "2026-09-10",
  "check_in": "2026-09-10T08:42:11.000000Z",
  "check_out": null,
  "status": "present",
  "total_hours": null,
  "check_in_latitude": "-3.3869000",
  "check_in_longitude": "36.6883000",
  "check_in_photo": "attendance/check-in/abc.jpg",
  "check_in_photo_url": "https://.../storage/attendance/check-in/abc.jpg",
  "working_hours_formatted": null
}
```

**Success 200 – no record:**
```json
{
  "date": "2026-09-10",
  "status": "absent",
  "check_in": null,
  "check_out": null,
  "total_hours": null,
  "message": "No attendance record for today"
}
```

---

### 4.3 Stats (For Profile/Analytics Card)
**Endpoint:** `GET /attendance/stats?month=2026-09`
**Auth:** Required

**Query:** `month` optional `YYYY-MM` (default current month)

**Success 200:**
```json
{
  "month": "2026-09",
  "month_name": "September 2026",
  "present": 7,
  "late": 1,
  "half_day": 0,
  "total_present": 8,
  "absent": 1,
  "leave": 1,
  "total_hours": 64.5,
  "avg_hours_per_day": 8.06,
  "attendance_percentage": 80.0,
  "days_in_month": 30,
  "days_so_far": 10
}
```

---

## 5. Check In / Check Out

Both endpoints accept `multipart/form-data` because of optional selfie. Send as `FormData` in Dio.

### 5.1 Check In
**Endpoint:** `POST /attendance/check-in`
**Auth:** Required
**Content-Type:** `multipart/form-data`

**Fields:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `latitude` | decimal | **yes** | `-90` to `90` |
| `longitude` | decimal | **yes** | `-180` to `180` |
| `address` | string | no | Reverse-geocoded address |
| `photo` | file (image) | no | Selfie – jpeg/png/webp, max 4MB |
| `biometric_verified` | boolean | no | `true` if device biometric succeeded. **Recommended `true`** |
| `biometric_type` | string | no | `fingerprint` \| `face` \| `iris` \| `none` |
| `device_info` | object | no | `{ platform: android/ios, model, device_id, app_version }` |
| `device_info.platform` | string | no | `android` / `ios` |
| `device_info.model` | string | no | e.g., `Pixel 7` |
| `device_info.device_id` | string | no | `Android ID` / `identifierForVendor` |
| `device_info.app_version` | string | no | `1.0.0` |

> **No fingerprint template is sent/stored.** Phone OS verifies fingerprint, app only sends `biometric_verified=true`.

**cURL (with biometric):**
```bash
curl -X POST "https://feedtanstore.com/api/attendance/check-in" \
  -H "Authorization: Bearer TOKEN" \
  -F "latitude=-3.3869" \
  -F "longitude=36.6883" \
  -F "address=Moshi, Kilimanjaro" \
  -F "photo=@/path/to/selfie.jpg" \
  -F "biometric_verified=1" \
  -F "biometric_type=fingerprint" \
  -F "device_info[platform]=android" \
  -F "device_info[model]=Pixel 7" \
  -F "device_info[device_id]=abc123" \
  -F "device_info[app_version]=1.0.0"
```

**Success 201:**
```json
{
  "message": "Checked in successfully",
  "attendance": {
    "id": 13,
    "user_id": 1,
    "date": "2026-09-10",
    "check_in": "2026-09-10T08:42:11.000000Z",
    "check_in_latitude": "-3.3869000",
    "check_in_longitude": "36.6883000",
    "check_in_photo": "attendance/check-in/xyz.jpg",
    "check_in_photo_url": "https://.../storage/attendance/check-in/xyz.jpg",
    "status": "present"
  }
}
```
If late (>09:15 per `WorkShift` or default 09:00): `"message": "Checked in successfully (Late)"` and `status: "late"`.

**Error 422:**
```json
{ "message": "Already checked in today", "attendance": { ... } }
{ "message": "You are on approved leave today. Cannot check in." }
{ "message": "The latitude field is required.", "errors": {"latitude": ["..."]} }
```

---

### 5.2 Check Out
**Endpoint:** `POST /attendance/check-out`
**Auth:** Required
**Content-Type:** `multipart/form-data`

Same fields as Check In (including `biometric_verified`, `biometric_type`, `device_info`). Requires prior Check In.

**Success 200:**
```json
{
  "message": "Checked out successfully",
  "attendance": {
    "id": 13,
    "user_id": 1,
    "date": "2026-09-10",
    "check_in": "2026-09-10T08:42:11.000000Z",
    "check_out": "2026-09-10T17:05:33.000000Z",
    "total_hours": "8.38",
    "working_hours_formatted": "08:23",
    "check_out_photo_url": "https://.../storage/attendance/check-out/xyz.jpg"
  }
}
```

**Error 422:**
```json
{ "message": "You have not checked in today" }
{ "message": "Already checked out today" }
```

---

### 5.3 Biometric Authentication (Fingerprint/Face) – Recommended

> **Do NOT store fingerprint images/templates in Laravel.** Use the phone's built-in Secure Enclave / TEE via `local_auth`. Backend only records `biometric_verified=true`.

**Architecture (exactly as you described):**
```
Flutter App
    ↓
local_auth (Biometric API)
    ↓
Android BiometricPrompt / iOS FaceID/TouchID
    ↓
Fingerprint / Face
    ↓
Success → App calls Attendance API with biometric_verified=true
    ↓
Laravel Backend → Attendance DB (boolean, no template)
```

**Strongest combo for attendance fraud prevention:**
```
Fingerprint (local_auth) + GPS (Geolocator) + Employee Account (Bearer token)
→ must be authenticated user + be within workplace + biometric on same device
```

**Flow in app:**
1. Employee logs in first time → `SharedPreferences.setBool('biometric_enabled', false)` → prompt “Enable fingerprint attendance?”
2. Enable → `LocalAuthentication().authenticate(...)` test → save enabled flag.
3. On Check In/Out:
   ```
   Tap [ CHECK IN ]
        ↓
   🔐 Verify your identity
   Touch the fingerprint sensor
        ↓
   Success → get GPS → POST /attendance/check-in (biometric_verified=1)
   Fail/Cancel → show error, do NOT call API
   ```

**UI states for Dashboard (copy your example):**
```
Good Morning, David 👋
────────────────────
Today's Attendance
Check In       08:42
Check Out      --:--
Working Hours  02:21
        [ CHECK IN ]   ← after fingerprint
────────────────────
📍 Location: Verified
🔐 Fingerprint: Required → Verified ✓
```

**Security notes:**
- Backend trusts `biometric_verified` flag; for higher security consider app attestation (Play Integrity / DeviceCheck) – out of MVP scope.
- If device has no biometric enrolled, fallback to PIN or skip biometric (send `biometric_verified=false`). Server still accepts but marks `check_in_biometric_verified=false` visible in dashboard/history.

---

### Check In/Check Out Flutter Implementation (with Biometric)

**Dependencies:**
```yaml
dependencies:
  dio: ^5.4.0
  geolocator: ^12.0.0
  image_picker: ^1.0.7
  flutter_secure_storage: ^9.0.0
  intl: ^0.19.0
  local_auth: ^2.1.8
  shared_preferences: ^2.2.3
  device_info_plus: ^10.1.0 # for device_info
```

**Geolocator helper:**
```dart
import 'package:geolocator/geolocator.dart';

Future<Position> getCurrentPosition() async {
  bool enabled = await Geolocator.isLocationServiceEnabled();
  if (!enabled) throw Exception('Location services disabled');
  LocationPermission p = await Geolocator.checkPermission();
  if (p == LocationPermission.denied) p = await Geolocator.requestPermission();
  if (p == LocationPermission.deniedForever) throw Exception('Permission permanently denied');
  if (p == LocationPermission.denied) throw Exception('Permission denied');
  return Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
}
```

**Biometric helper (`local_auth`):**
```dart
import 'package:local_auth/local_auth.dart';
import 'package:flutter/services.dart';

class BiometricService {
  final LocalAuthentication _auth = LocalAuthentication();

  Future<bool> isAvailable() async {
    final canCheck = await _auth.canCheckBiometrics;
    final isDeviceSupported = await _auth.isDeviceSupported();
    return canCheck && isDeviceSupported;
  }

  Future<List<BiometricType>> availableTypes() => _auth.getAvailableBiometrics();

  // Returns true if verified, false if cancelled/failed
  Future<bool> authenticate({String reason = 'Verify your identity to confirm attendance'}) async {
    try {
      final ok = await _auth.authenticate(
        localizedReason: reason,
        options: const AuthenticationOptions(biometricOnly: true, stickyAuth: true),
      );
      return ok;
    } on PlatformException catch (e) {
      print('Biometric error: $e');
      return false;
    }
  }
}
```

**Device info helper:**
```dart
import 'package:device_info_plus/device_info_plus.dart';
import 'dart:io';

Future<Map<String,String>> getDeviceInfo() async {
  final di = DeviceInfoPlugin();
  if (Platform.isAndroid) {
    final a = await di.androidInfo;
    return {'platform':'android','model': a.model ?? 'android','device_id': a.id ?? '','app_version':'1.0.0'};
  } else {
    final i = await di.iosInfo;
    return {'platform':'ios','model': i.model ?? 'ios','device_id': i.identifierForVendor ?? '','app_version':'1.0.0'};
  }
}
```

**Dio FormData helpers (with biometric):**
```dart
Future<Map<String,dynamic>> checkIn({double? lat, double? lng, String? address, XFile? photo, bool requireBiometric = true}) async {
  // 1. Biometric first – phone does verification
  bool bioOk = false;
  String bioType = 'none';
  if (requireBiometric) {
    final bio = BiometricService();
    if (await bio.isAvailable()) {
      bioOk = await bio.authenticate(reason: 'Touch the fingerprint sensor to confirm attendance');
      if (!bioOk) throw Exception('Biometric verification failed/cancelled');
      final types = await bio.availableTypes();
      bioType = types.contains(BiometricType.face) ? 'face' : 'fingerprint';
    }
  }
  // 2. GPS
  Position pos = await getCurrentPosition(); // if lat/lng not supplied
  final deviceInfo = await getDeviceInfo();
  final form = FormData.fromMap({
    'latitude': lat ?? pos.latitude,
    'longitude': lng ?? pos.longitude,
    if (address != null) 'address': address,
    if (photo != null) 'photo': await MultipartFile.fromFile(photo.path, filename: 'selfie.jpg'),
    'biometric_verified': bioOk ? '1' : '0',
    'biometric_type': bioType,
    'device_info[platform]': deviceInfo['platform'],
    'device_info[model]': deviceInfo['model'],
    'device_info[device_id]': deviceInfo['device_id'],
    'device_info[app_version]': deviceInfo['app_version'],
  });
  final res = await dio.post('/attendance/check-in', data: form);
  return res.data;
}

Future<Map<String,dynamic>> checkOut({XFile? photo, bool requireBiometric = true}) async {
  bool bioOk = false; String bioType = 'none';
  if (requireBiometric) {
    final bio = BiometricService();
    if (await bio.isAvailable()) {
      bioOk = await bio.authenticate(reason: 'Verify to check out');
      if (!bioOk) throw Exception('Biometric cancelled');
      final types = await bio.availableTypes();
      bioType = types.contains(BiometricType.face) ? 'face' : 'fingerprint';
    }
  }
  Position pos = await getCurrentPosition();
  final deviceInfo = await getDeviceInfo();
  final form = FormData.fromMap({
    'latitude': pos.latitude,
    'longitude': pos.longitude,
    if (photo != null) 'photo': await MultipartFile.fromFile(photo.path, filename: 'selfie.jpg'),
    'biometric_verified': bioOk ? '1' : '0',
    'biometric_type': bioType,
    'device_info[platform]': deviceInfo['platform'],
    'device_info[model]': deviceInfo['model'],
    'device_info[device_id]': deviceInfo['device_id'],
  });
  final res = await dio.post('/attendance/check-out', data: form);
  return res.data;
}
```

**Enable prompt on first login (SharedPreferences):**
```dart
Future<void> promptEnableBiometric(BuildContext context) async {
  final prefs = await SharedPreferences.getInstance();
  if (prefs.getBool('biometric_enabled') == true) return;
  final bio = BiometricService();
  if (!await bio.isAvailable()) return;
  final enable = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
    title: const Text('Enable fingerprint attendance?'),
    content: const Text('Use your fingerprint/face to check in faster next time.'),
    actions: [TextButton(onPressed: ()=> Navigator.pop(context,false), child: const Text('Later')), ElevatedButton(onPressed: ()=> Navigator.pop(context,true), child: const Text('Enable'))],
  ));
  if (enable == true) {
    final ok = await bio.authenticate(reason: 'Enable biometric for attendance');
    await prefs.setBool('biometric_enabled', ok);
  }
}
```

**Selfie picker:**
```dart
final picker = ImagePicker();
final XFile? selfie = await picker.pickImage(source: ImageSource.camera, imageQuality: 70, maxWidth: 800);
```

---

## 6. Attendance History & Calendar

### 6.1 Attendance History (Paginated)
**Endpoint:** `GET /attendance/history`
**Auth:** Required

**Query Parameters (all optional):**

| Param | Type | Example | Description |
|-------|------|---------|-------------|
| `month` | `YYYY-MM` | `2026-09` | Filter by month |
| `year` | `YYYY` | `2026` | Filter by year |
| `status` | enum | `late` | `present, absent, late, leave, half-day` |
| `from` | date `YYYY-MM-DD` | `2026-09-01` | Date range start |
| `to` | date `YYYY-MM-DD` | `2026-09-10` | Date range end |
| `per_page` | int 1-100 | `15` | Default 15 |
| `page` | int | `1` | Pagination page |

**Success 200 (Laravel Paginator):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 13,
      "user_id": 1,
      "date": "2026-09-10",
      "check_in": "2026-09-10T08:42:11.000000Z",
      "check_out": "2026-09-10T17:05:33.000000Z",
      "status": "present",
      "total_hours": "8.38",
      "working_hours_formatted": "08:23",
      "check_in_photo_url": "https://.../storage/...",
      "check_out_photo_url": "https://.../storage/...",
      "check_in_latitude": "-3.3869000",
      "check_in_longitude": "36.6883000"
    }
  ],
  "first_page_url": "https://.../api/attendance/history?page=1",
  "from": 1,
  "last_page": 3,
  "last_page_url": "https://.../api/attendance/history?page=3",
  "links": [...],
  "next_page_url": "https://.../api/attendance/history?page=2",
  "path": "https://.../api/attendance/history",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 42
}
```

**Flutter:**
```dart
Future<PaginatedAttendance> getHistory({String? month, int page = 1}) async {
  final res = await dio.get('/attendance/history', queryParameters: {
    if (month != null) 'month': month,
    'page': page,
    'per_page': 20,
  });
  return PaginatedAttendance.fromJson(res.data);
}
```

**Table columns mapping:**
- Date → `date`
- Check-in → `check_in` (format `hh:mm a`)
- Check-out → `check_out`
- Total hours → `total_hours` or `working_hours_formatted` (`02:21`)
- Status → `status` (chip color)

---

### 6.2 Calendar
**Endpoint:** `GET /attendance/calendar?month=2026-09`
**Auth:** Required

Returns every day of the month with computed status. Future days have `status: null` and `is_future: true`.

**Success 200:**
```json
{
  "month": "2026-09",
  "month_name": "September 2026",
  "days": [
    {
      "date": "2026-09-01",
      "day": 1,
      "weekday": "Mon",
      "status": "present",
      "check_in": "08:42:11",
      "check_out": "17:05:33",
      "total_hours": "8.38",
      "is_weekend": false,
      "is_today": false,
      "is_future": false
    },
    {
      "date": "2026-09-02",
      "day": 2,
      "weekday": "Tue",
      "status": "late",
      "check_in": "09:22:00",
      "check_out": "17:00:00",
      "total_hours": "7.63",
      "is_weekend": false,
      "is_today": false,
      "is_future": false
    },
    {
      "date": "2026-09-03",
      "day": 3,
      "weekday": "Wed",
      "status": "leave",
      "check_in": null,
      "check_out": null,
      "total_hours": null,
      "is_weekend": false,
      "is_today": false,
      "is_future": false
    },
    {
      "date": "2026-09-10",
      "day": 10,
      "weekday": "Wed",
      "status": "present",
      "check_in": "08:42:11",
      "check_out": null,
      "total_hours": null,
      "is_weekend": false,
      "is_today": true,
      "is_future": false
    },
    {
      "date": "2026-09-11",
      "day": 11,
      "weekday": "Thu",
      "status": null,
      "is_future": true
    }
  ],
  "summary": {
    "present": 8,
    "absent": 1,
    "late": 1,
    "leave": 1,
    "total_days_in_month": 30,
    "attendance_percentage": 26.7,
    "attendance_percentage_so_far": 80.0
  }
}
```

**Color mapping (suggested):**
```dart
Color statusColor(String? status) {
  switch (status) {
    case 'present': return Colors.green;
    case 'late': return Colors.orange;
    case 'absent': return Colors.red;
    case 'leave': return Colors.blue;
    case 'half-day': return Colors.amber;
    default: return Colors.grey.shade300; // future / null
  }
}
```

**Flutter grid:**
```dart
GridView.builder(
  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 7),
  itemCount: data.days.length,
  itemBuilder: (c,i) {
    final d = data.days[i];
    return Container(
      decoration: BoxDecoration(
        color: statusColor(d.status),
        border: d.isToday ? Border.all(color: Colors.black, width: 2) : null,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(children: [
        Text('${d.day}', style: TextStyle(fontWeight: FontWeight.bold)),
        if (d.status != null) Text(d.status!, style: TextStyle(fontSize: 10)),
      ]),
    );
  }
)
```

---

## 7. Profile

### 7.1 Get Profile
**Endpoint:** `GET /attendance/profile`
**Auth:** Required

**Success 200:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@feedtan.com",
  "phone": "255712345678",
  "employee_id": "EMP-0001",
  "department": "Sales",
  "position": "Cashier",
  "role": "staff",
  "profile_image": "https://.../storage/profile-images/abc.jpg",
  "profile_image_path": "profile-images/abc.jpg",
  "stats": {
    "total_present": 42,
    "total_leaves": 3
  },
  "created_at": "2026-01-10T00:00:00.000000Z"
}
```
If `employee_id` null server returns `EMP-XXXX` padded id.

---

### 7.2 Update Profile
**Endpoint:** `PUT /attendance/profile`
**Auth:** Required

**Request (all optional):**
```json
{
  "name": "John Updated",
  "phone": "255712345678",
  "email": "john@feedtan.com",
  "department": "Marketing",
  "position": "Senior Cashier"
}
```

**Success 200:**
```json
{
  "message": "Profile updated successfully",
  "user": { "id": 1, "name": "John Updated", ... }
}
```

**Error 422:** phone/email already taken.

---

### 7.3 Update Profile Photo
**Endpoint:** `POST /attendance/profile/photo`
**Auth:** Required
**Content-Type:** `multipart/form-data`

**Fields:**
- `photo` (file) OR `image` (file) – image `jpeg,png,jpg,webp` max 4MB
- `remove` (boolean) – `true` to delete photo

**Upload:**
```bash
curl -X POST "https://.../api/attendance/profile/photo" \
  -H "Authorization: Bearer TOKEN" \
  -F "photo=@avatar.jpg"
```

**Remove:**
```json
{ "remove": true }
```

**Success 200 (upload):**
```json
{
  "message": "Profile photo updated",
  "profile_image": "https://.../storage/profile-images/xyz.jpg",
  "path": "profile-images/xyz.jpg"
}
```

**Success 200 (remove):**
```json
{ "message": "Profile photo removed", "profile_image": null }
```

---

### 7.4 Change Password
**Endpoint:** `POST /attendance/profile/change-password`
**Auth:** Required

**Request:**
```json
{
  "current_password": "old12345",
  "new_password": "new123456",
  "new_password_confirmation": "new123456"
}
```

**Success 200:**
```json
{ "message": "Password changed successfully" }
```

**Error 422:**
```json
{ "message": "Current password is incorrect" }
```

---

## 8. Leave Management

### 8.1 List Leaves (Paginated + Summary)
**Endpoint:** `GET /attendance/leaves?status=pending&leave_type=casual&per_page=15&page=1`
**Auth:** Required

**Query:** `status` pending|approved|rejected|cancelled, `leave_type` sick|casual|annual|emergency|maternity|paternity|other

**Success 200:**
```json
{
  "data": [
    {
      "id": 5,
      "user_id": 1,
      "leave_type": "casual",
      "start_date": "2026-09-15",
      "end_date": "2026-09-16",
      "total_days": 2,
      "reason": "Family function",
      "status": "pending",
      "reviewed_by": null,
      "reviewed_at": null,
      "review_notes": null,
      "created_at": "2026-09-10T10:00:00.000000Z",
      "updated_at": "2026-09-10T10:00:00.000000Z"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 },
  "summary": { "pending": 1, "approved": 2, "rejected": 0, "total": 3 },
  "links": { "first": "...", "last": "...", "prev": null, "next": null }
}
```

---

### 8.2 Apply for Leave
**Endpoint:** `POST /attendance/leaves`
**Auth:** Required

**Request:**
```json
{
  "leave_type": "casual",
  "start_date": "2026-09-15",
  "end_date": "2026-09-16",
  "reason": "Family function at hometown"
}
```

**Success 201:**
```json
{
  "message": "Leave application submitted successfully",
  "leave": {
    "id": 6,
    "user_id": 1,
    "leave_type": "casual",
    "start_date": "2026-09-15",
    "end_date": "2026-09-16",
    "total_days": 2,
    "reason": "Family function at hometown",
    "status": "pending"
  }
}
```

**Error 422:**
```json
{ "message": "You already have a leave request overlapping these dates" }
{ "message": "The start date field must be a date after or equal to today." }
```

Leave types: `sick | casual | annual | emergency | maternity | paternity | other`

---

### 8.3 Show Leave Details
**Endpoint:** `GET /attendance/leaves/{id}`
**Auth:** Required

**Success 200:**
```json
{
  "id": 5,
  "user_id": 1,
  "leave_type": "sick",
  "start_date": "2026-09-12",
  "end_date": "2026-09-12",
  "total_days": 1,
  "reason": "Fever",
  "status": "approved",
  "reviewed_by": 2,
  "reviewed_at": "2026-09-11T09:00:00.000000Z",
  "review_notes": "Get well soon",
  "reviewer": { "id": 2, "name": "Admin", "email": "admin@feedtan.com" }
}
```

**Error 404:** Not found or not yours.

---

### 8.4 Cancel Leave (Only `pending`)
**Endpoint:** `POST /attendance/leaves/{id}/cancel`
**Auth:** Required
**Body:** empty

**Success 200:**
```json
{ "message": "Leave cancelled successfully", "leave": { "id": 5, "status": "cancelled" } }
```

**Error 422:**
```json
{ "message": "Only pending leaves can be cancelled" }
```

---

### 8.5 Review Leave (Manager/Admin)
**Endpoint:** `POST /attendance/leaves/{id}/review`
**Auth:** Required

> Note: This is for manager/admin app or web. Regular employee cannot review own leave (403).

**Request:**
```json
{
  "status": "approved",
  "review_notes": "Approved, enjoy"
}
```

**Success 200:**
```json
{ "message": "Leave approved successfully", "leave": { "id": 5, "status": "approved" } }
```

**Error 403:**
```json
{ "message": "You cannot review your own leave" }
```

---

### Leave Flutter Example

```dart
// Apply
Future<Leave> applyLeave(String type, DateTime start, DateTime end, String reason) async {
  final res = await dio.post('/attendance/leaves', data: {
    'leave_type': type,
    'start_date': DateFormat('yyyy-MM-dd').format(start),
    'end_date': DateFormat('yyyy-MM-dd').format(end),
    'reason': reason,
  });
  return Leave.fromJson(res.data['leave']);
}

// List
Future<LeavePaginated> getLeaves({String? status}) async {
  final res = await dio.get('/attendance/leaves', queryParameters: {if (status!=null) 'status': status});
  return LeavePaginated.fromJson(res.data);
}

// Status chip color
Color leaveStatusColor(String s) => s == 'approved' ? Colors.green : s == 'rejected' ? Colors.red : s == 'pending' ? Colors.orange : Colors.grey;
```

---

## 9. Notifications

Types: `attendance | leave | late | reminder | general | system`

Notifications are created automatically on:
- Check-in / Check-out → `attendance` / `late`
- Leave apply / approve / reject → `leave`
- Late check-in → `late`
- Future: cron reminders `reminder` (e.g., "Don't forget to check in")

### 9.1 List Notifications
**Endpoint:** `GET /attendance/notifications?type=leave&is_read=0&per_page=20`
**Auth:** Required

Paginated (latest first). Filter by `type` or `is_read` (0/1).

**Success 200 (Paginator):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 10,
      "user_id": 1,
      "title": "Leave Approved",
      "body": "Your casual leave from 2026-09-15 to 2026-09-16 has been approved.",
      "type": "leave",
      "is_read": false,
      "data": { "leave_id": 5, "action": "approved" },
      "created_at": "2026-09-10T10:05:00.000000Z",
      "updated_at": "2026-09-10T10:05:00.000000Z"
    }
  ],
  "total": 12,
  "...": "..."
}
```

---

### 9.2 Unread Count (for App Badge)
**Endpoint:** `GET /attendance/notifications/unread-count`
**Auth:** Required

**Success 200:**
```json
{ "unread_count": 5 }
```

Poll this endpoint every 60s or on app resume. Or use push via FCM (`/device-token` existing).

---

### 9.3 Mark Single as Read
**Endpoint:** `POST /attendance/notifications/{id}/read`
**Auth:** Required

**Success 200:**
```json
{ "message": "Notification marked as read", "notification": { "id": 10, "is_read": true } }
```

---

### 9.4 Mark All as Read
**Endpoint:** `POST /attendance/notifications/read-all`
**Auth:** Required

**Success 200:**
```json
{ "message": "All notifications marked as read" }
```

---

### 9.5 Delete Notification
**Endpoint:** `DELETE /attendance/notifications/{id}`
**Auth:** Required

**Success 200:**
```json
{ "message": "Notification deleted" }
```

### 9.6 Clear All Read
**Endpoint:** `DELETE /attendance/notifications/clear/read`
**Auth:** Required

**Success 200:**
```json
{ "message": "5 read notifications cleared" }
```

---

### Notification Flutter Example

```dart
// Badge in AppBar
Future<int> fetchUnread() async {
  final res = await dio.get('/attendance/notifications/unread-count');
  return res.data['unread_count'] as int;
}

// List with pull-to-refresh
Future<List<AppNotification>> fetchNotifications() async {
  final res = await dio.get('/attendance/notifications', queryParameters: {'per_page': 30});
  return (res.data['data'] as List).map((e) => AppNotification.fromJson(e)).toList();
}

// On tap
await dio.post('/attendance/notifications/$id/read');

// Icon mapping
IconData notifIcon(String type) {
  switch(type) {
    case 'attendance': return Icons.fact_check;
    case 'late': return Icons.warning_amber;
    case 'leave': return Icons.event_note;
    default: return Icons.notifications;
  }
}
```

---

## 10. Error Handling

### Common HTTP Status Codes

| Code | Meaning | Action |
|------|---------|--------|
| `200` | Success | Use data |
| `201` | Created (check-in, leave apply) | Show success toast |
| `401` | Unauthenticated – token missing/expired | Clear token → navigate to Login |
| `403` | Forbidden – reviewing own leave | Show permission error |
| `404` | Not found – leave/history not found | Show empty state |
| `422` | Validation failed | Parse `errors` map, show under fields |
| `500` | Server error | Show generic retry |

### Validation Error Format (Laravel)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "latitude": ["The latitude field is required."],
    "photo": ["The photo field must be an image."]
  }
}
```

### Global Dio Error Handler (Flutter)
```dart
dio.interceptors.add(InterceptorsWrapper(
  onError: (e, h) {
    if (e.response?.statusCode == 401) {
      storage.delete(key: 'token');
      navigatorKey.currentState?.pushReplacementNamed('/login');
    } else if (e.response?.statusCode == 422) {
      final errors = e.response?.data['errors'] as Map?;
      final msg = errors?.values.first[0] ?? e.response?.data['message'];
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
    }
    return h.next(e);
  }
));
```

---

## 11. Data Models

### Attendance
```json
{
  "id": "integer",
  "user_id": "integer",
  "date": "date YYYY-MM-DD",
  "check_in": "datetime ISO8601 | null",
  "check_out": "datetime ISO8601 | null",
  "check_in_latitude": "decimal(10,7) | null",
  "check_in_longitude": "decimal(10,7) | null",
  "check_out_latitude": "decimal(10,7) | null",
  "check_out_longitude": "decimal(10,7) | null",
  "check_in_address": "string | null",
  "check_out_address": "string | null",
  "check_in_photo": "string path | null",
  "check_out_photo": "string path | null",
  "check_in_photo_url": "string url | null (appended)",
  "check_out_photo_url": "string url | null (appended)",
  "total_hours": "decimal(5,2) | null",
  "working_hours_formatted": "string HH:MM | null (appended)",
  "status": "string: present|absent|late|leave|half-day",
  "notes": "string | null",
  "check_in_biometric_verified": "boolean (true if phone fingerprint/face succeeded)",
  "check_out_biometric_verified": "boolean",
  "check_in_biometric_type": "string fingerprint|face|iris|none | null",
  "check_out_biometric_type": "string | null",
  "device_info": "object | null { platform, model, device_id, app_version }",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### User (Attendance View)
```json
{
  "id": "integer",
  "name": "string",
  "email": "string",
  "phone": "string | null",
  "employee_id": "string | null (e.g., EMP-0001)",
  "department": "string | null",
  "position": "string | null",
  "role": "string: admin|manager|staff|rider|cashier...",
  "profile_image": "string url | null",
  "email_verified_at": "datetime | null"
}
```

### Leave
```json
{
  "id": "integer",
  "user_id": "integer",
  "leave_type": "enum sick|casual|annual|emergency|maternity|paternity|other",
  "start_date": "date YYYY-MM-DD",
  "end_date": "date YYYY-MM-DD",
  "total_days": "integer (auto-calculated inclusive)",
  "reason": "string",
  "status": "enum pending|approved|rejected|cancelled",
  "reviewed_by": "integer user_id | null",
  "reviewed_at": "datetime | null",
  "review_notes": "string | null",
  "reviewer": "User | null (when eager loaded)",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### AppNotification
```json
{
  "id": "integer",
  "user_id": "integer",
  "title": "string",
  "body": "string",
  "type": "enum attendance|leave|late|reminder|general|system",
  "is_read": "boolean",
  "data": "object | null (e.g., {\"attendance_id\": 13, \"action\": \"check_in\"})",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

---

## 12. Flutter Integration Guide

### 12.1 Dependencies (`pubspec.yaml`)

```yaml
dependencies:
  flutter:
    sdk: flutter
  dio: ^5.5.0
  flutter_secure_storage: ^9.0.0
  geolocator: ^12.0.0
  image_picker: ^1.0.7
  intl: ^0.19.0
  provider: ^6.1.2        # or flutter_riverpod
  cached_network_image: ^3.3.0
  local_auth: ^2.1.8
  shared_preferences: ^2.2.3
  device_info_plus: ^10.1.0

dev_dependencies:
  flutter_lints: ^4.0.0
```

### 12.2 Environment Config

Create `lib/config/api_config.dart`:

```dart
class ApiConfig {
  static const String baseUrl = String.fromEnvironment('API_BASE_URL',
      defaultValue: 'https://feedtanstore.com/api'); // or http://10.0.2.2:8000/api for emulator
  static const Duration timeout = Duration(seconds: 30);
}
```

For emulator: `10.0.2.2` maps to host `localhost`. For physical device use `http://192.168.x.x:8000/api`.

### 12.3 Folder Structure (MVP)

```
lib/
├─ config/
│  └─ api_config.dart
├─ services/
│  ├─ api_client.dart          // Dio + interceptors + token persistence
│  ├─ auth_service.dart
│  ├─ attendance_service.dart
│  ├─ leave_service.dart
│  ├─ profile_service.dart
│  └─ notification_service.dart
├─ models/
│  ├─ user.dart
│  ├─ attendance.dart
│  ├─ leave.dart
│  └─ app_notification.dart
├─ providers/
│  ├─ auth_provider.dart
│  ├─ dashboard_provider.dart
│  └─ etc.
├─ screens/
│  ├─ login_screen.dart
│  ├─ dashboard_screen.dart
│  ├─ check_in_screen.dart
│  ├─ history_screen.dart
│  ├─ calendar_screen.dart
│  ├─ leave_screen.dart
│  ├─ notifications_screen.dart
│  └─ profile_screen.dart
├─ widgets/
│  ├─ status_chip.dart
│  ├─ attendance_card.dart
│  └─ bottom_nav.dart
└─ main.dart
```

### 12.4 ApiClient – Complete (copy-paste)

```dart
// lib/services/api_client.dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/api_config.dart';

class ApiClient {
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;

  late final Dio dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  String? _token;

  ApiClient._internal() {
    dio = Dio(BaseOptions(
      baseUrl: ApiConfig.baseUrl,
      connectTimeout: ApiConfig.timeout,
      receiveTimeout: ApiConfig.timeout,
      headers: {'Accept': 'application/json'},
    ));

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        _token ??= await _storage.read(key: 'token');
        if (_token != null) {
          options.headers['Authorization'] = 'Bearer $_token';
        }
        // Debug log
        print('[API] ${options.method} ${options.uri}');
        return handler.next(options);
      },
      onError: (DioException e, handler) async {
        if (e.response?.statusCode == 401) {
          await _storage.delete(key: 'token');
          _token = null;
          // Optionally navigate to login via global navigator key
        }
        print('[API ERROR] ${e.response?.statusCode} ${e.response?.data}');
        return handler.next(e);
      },
    ));

    dio.interceptors.add(LogInterceptor(requestBody: true, responseBody: true));
  }

  Future<void> setToken(String token) async {
    _token = token;
    await _storage.write(key: 'token', value: token);
    dio.options.headers['Authorization'] = 'Bearer $token';
  }

  Future<void> clearToken() async {
    _token = null;
    await _storage.delete(key: 'token');
    dio.options.headers.remove('Authorization');
  }

  Future<String?> getToken() async => _token ?? await _storage.read(key: 'token');
}
```

### 12.5 Auth Service

```dart
// lib/services/auth_service.dart
import 'package:dio/dio.dart';
import 'api_client.dart';

class AuthService {
  final Dio _dio = ApiClient().dio;

  Future<Map<String,dynamic>> login({required String login, required String password}) async {
    final res = await _dio.post('/attendance/login', data: {'login': login, 'password': password});
    await ApiClient().setToken(res.data['token']);
    return res.data;
  }

  Future<void> logout() async {
    try { await _dio.post('/attendance/logout'); } catch (_) {}
    await ApiClient().clearToken();
  }

  Future<Map<String,dynamic>> me() async {
    final res = await _dio.get('/attendance/me');
    return res.data;
  }

  Future<Map<String,dynamic>> forgotPassword(String email) async {
    final res = await _dio.post('/attendance/forgot-password', data: {'email': email});
    return res.data;
  }
}
```

### 12.6 Attendance Service

```dart
// lib/services/attendance_service.dart
import 'package:dio/dio.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'api_client.dart';

class AttendanceService {
  final Dio _dio = ApiClient().dio;

  Future<Map<String,dynamic>> dashboard() async {
    final res = await _dio.get('/attendance/dashboard');
    return res.data;
  }

  Future<Map<String,dynamic>> today() async {
    final res = await _dio.get('/attendance/today');
    return res.data;
  }

  // With biometric (recommended)
  Future<Map<String,dynamic>> checkIn({XFile? photo, String? address, bool requireBiometric = true}) async {
    bool bioOk = false; String bioType = 'none';
    if (requireBiometric) {
      final bio = BiometricService(); // see 5.3
      if (await bio.isAvailable()) {
        bioOk = await bio.authenticate(reason: 'Touch fingerprint sensor to confirm attendance');
        if (!bioOk) throw Exception('Biometric cancelled');
        final types = await bio.availableTypes();
        bioType = types.contains(BiometricType.face) ? 'face' : 'fingerprint';
      }
    }
    final pos = await _getPos();
    final dev = await getDeviceInfo();
    final form = FormData.fromMap({
      'latitude': pos.latitude,
      'longitude': pos.longitude,
      if (address != null) 'address': address,
      if (photo != null) 'photo': await MultipartFile.fromFile(photo.path, filename: photo.name),
      'biometric_verified': bioOk ? '1' : '0',
      'biometric_type': bioType,
      'device_info[platform]': dev['platform'],
      'device_info[model]': dev['model'],
      'device_info[device_id]': dev['device_id'],
    });
    final res = await _dio.post('/attendance/check-in', data: form);
    return res.data;
  }

  Future<Map<String,dynamic>> checkOut({XFile? photo, String? address, bool requireBiometric = true}) async {
    bool bioOk = false; String bioType = 'none';
    if (requireBiometric) {
      final bio = BiometricService();
      if (await bio.isAvailable()) {
        bioOk = await bio.authenticate(reason: 'Verify to check out');
        if (!bioOk) throw Exception('Biometric cancelled');
        final types = await bio.availableTypes();
        bioType = types.contains(BiometricType.face) ? 'face' : 'fingerprint';
      }
    }
    final pos = await _getPos();
    final dev = await getDeviceInfo();
    final form = FormData.fromMap({
      'latitude': pos.latitude,
      'longitude': pos.longitude,
      if (address != null) 'address': address,
      if (photo != null) 'photo': await MultipartFile.fromFile(photo.path, filename: photo.name),
      'biometric_verified': bioOk ? '1' : '0',
      'biometric_type': bioType,
      'device_info[platform]': dev['platform'],
      'device_info[model]': dev['model'],
      'device_info[device_id]': dev['device_id'],
    });
    final res = await _dio.post('/attendance/check-out', data: form);
    return res.data;
  }

  Future<Map<String,dynamic>> history({String? month, int page = 1, int perPage = 15}) async {
    final res = await _dio.get('/attendance/history', queryParameters: {
      if (month != null) 'month': month,
      'page': page,
      'per_page': perPage,
    });
    return res.data;
  }

  Future<Map<String,dynamic>> calendar({String? month}) async {
    final res = await _dio.get('/attendance/calendar', queryParameters: {if (month!=null) 'month': month});
    return res.data;
  }

  Future<Map<String,dynamic>> stats({String? month}) async {
    final res = await _dio.get('/attendance/stats', queryParameters: {if (month!=null) 'month': month});
    return res.data;
  }

  Future<Position> _getPos() async {
    bool enabled = await Geolocator.isLocationServiceEnabled();
    if (!enabled) throw Exception('Location disabled');
    LocationPermission p = await Geolocator.checkPermission();
    if (p == LocationPermission.denied) p = await Geolocator.requestPermission();
    if (p == LocationPermission.denied) throw Exception('Location permission denied');
    if (p == LocationPermission.deniedForever) throw Exception('Location permanently denied');
    return Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
  }
}
```

### 12.7 Leave & Other Services (short)

```dart
// lib/services/leave_service.dart
class LeaveService {
  final Dio _dio = ApiClient().dio;
  Future<Map<String,dynamic>> list({String? status}) async {
    final res = await _dio.get('/attendance/leaves', queryParameters: {if(status!=null) 'status': status});
    return res.data;
  }
  Future<Map<String,dynamic>> apply({required String type, required String start, required String end, required String reason}) async {
    final res = await _dio.post('/attendance/leaves', data: {
      'leave_type': type, 'start_date': start, 'end_date': end, 'reason': reason
    });
    return res.data;
  }
  Future<Map<String,dynamic>> cancel(int id) async {
    final res = await _dio.post('/attendance/leaves/$id/cancel');
    return res.data;
  }
}

// lib/services/profile_service.dart
class ProfileService {
  final Dio _dio = ApiClient().dio;
  Future<Map<String,dynamic>> getProfile() async => (await _dio.get('/attendance/profile')).data;
  Future<Map<String,dynamic>> updateProfile(Map<String,dynamic> data) async => (await _dio.put('/attendance/profile', data: data)).data;
  Future<Map<String,dynamic>> uploadPhoto(XFile file) async {
    final form = FormData.fromMap({'photo': await MultipartFile.fromFile(file.path, filename: file.name)});
    final res = await _dio.post('/attendance/profile/photo', data: form);
    return res.data;
  }
}

// lib/services/notification_service.dart
class NotificationService {
  final Dio _dio = ApiClient().dio;
  Future<Map<String,dynamic>> list() async => (await _dio.get('/attendance/notifications')).data;
  Future<int> unreadCount() async => (await _dio.get('/attendance/notifications/unread-count')).data['unread_count'];
  Future<void> markRead(int id) async => await _dio.post('/attendance/notifications/$id/read');
  Future<void> markAllRead() async => await _dio.post('/attendance/notifications/read-all');
}
```

### 12.8 Model Examples

```dart
// lib/models/attendance.dart
class Attendance {
  final int id;
  final String date;
  final String? checkIn;
  final String? checkOut;
  final String status;
  final String? totalHours;
  final String? workingFormatted;
  final String? checkInPhotoUrl;

  Attendance.fromJson(Map<String,dynamic> j)
    : id = j['id'],
      date = j['date'] is String ? j['date'] : (j['date'] as String),
      checkIn = j['check_in'],
      checkOut = j['check_out'],
      status = j['status'],
      totalHours = j['total_hours']?.toString(),
      workingFormatted = j['working_hours_formatted'],
      checkInPhotoUrl = j['check_in_photo_url'];
}

// lib/models/leave.dart
class Leave {
  final int id;
  final String leaveType;
  final String startDate;
  final String endDate;
  final int totalDays;
  final String reason;
  final String status;
  Leave.fromJson(Map<String,dynamic> j)
    : id=j['id'], leaveType=j['leave_type'], startDate=j['start_date'], endDate=j['end_date'], totalDays=j['total_days'], reason=j['reason'], status=j['status'];
}
```

### 12.9 Screens – Bottom Navigation (MVP)

```dart
// lib/screens/dashboard_screen.dart
class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});
  @override State<DashboardScreen> createState() => _DashboardScreenState();
}
class _DashboardScreenState extends State<DashboardScreen> {
  Map<String,dynamic>? data; bool loading = true;
  @override void initState(){ super.initState(); _load(); }
  Future<void> _load() async {
    final res = await AttendanceService().dashboard();
    setState((){ data=res; loading=false; });
  }
  @override Widget build(BuildContext context) {
    if (loading) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    final today = data!['today'];
    return Scaffold(
      appBar: AppBar(title: const Text('Dashboard')),
      body: RefreshIndicator(
        onRefresh: _load,
        child: ListView(padding: const EdgeInsets.all(16), children: [
          Card(child: ListTile(
            title: Text('Today: ${today['status'].toString().toUpperCase()}'),
            subtitle: Text('Check-in: ${today['check_in'] ?? '-'} | Check-out: ${today['check_out'] ?? '-'}'),
            trailing: Text(today['working_hours_formatted'] ?? '00:00', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          )),
          const SizedBox(height: 16),
          if (today['can_check_in'] == true)
            ElevatedButton.icon(onPressed: () async {
              try { await AttendanceService().checkIn(); _load(); ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checked in'))); }
              catch(e){ ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()))); }
            }, icon: const Icon(Icons.login), label: const Text('Check In'), style: ElevatedButton.styleFrom(backgroundColor: Colors.green, minimumSize: const Size(double.infinity, 50))),
          if (today['can_check_out'] == true)
            ElevatedButton.icon(onPressed: () async {
              try { await AttendanceService().checkOut(); _load(); ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Checked out'))); }
              catch(e){ ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()))); }
            }, icon: const Icon(Icons.logout), label: const Text('Check Out'), style: ElevatedButton.styleFrom(backgroundColor: Colors.orange, minimumSize: const Size(double.infinity, 50))),
          if (today['can_check_in'] == false && today['can_check_out'] == false)
            const Card(child: Padding(padding: EdgeInsets.all(16), child: Text('✓ Attendance completed for today', textAlign: TextAlign.center))),
          const SizedBox(height: 16),
          Card(child: Padding(padding: const EdgeInsets.all(16), child: Column(children: [
            Text('Monthly Attendance: ${data!['month_summary']['attendance_percentage']}%'),
            LinearProgressIndicator(value: (data!['month_summary']['attendance_percentage'] as double)/100),
            Text('${data!['month_summary']['present']} / ${data!['month_summary']['total_days_so_far']} days present'),
          ]))),
        ]),
      ),
    );
  }
}
```

```dart
// lib/main.dart – Bottom Nav
class MainTabs extends StatefulWidget { const MainTabs({super.key}); @override State<MainTabs> createState()=>_MainTabsState();}
class _MainTabsState extends State<MainTabs> {
  int index=0;
  final screens = [const DashboardScreen(), const HistoryScreen(), const LeaveScreen(), const NotificationsScreen(), const ProfileScreen()];
  @override Widget build(BuildContext context) => Scaffold(
    body: screens[index],
    bottomNavigationBar: BottomNavigationBar(
      currentIndex: index, onTap: (i)=> setState(()=> index=i), type: BottomNavigationBarType.fixed,
      items: const [
        BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),
        BottomNavigationBarItem(icon: Icon(Icons.calendar_today), label: 'Attendance'),
        BottomNavigationBarItem(icon: Icon(Icons.event_note), label: 'Leave'),
        BottomNavigationBarItem(icon: Icon(Icons.notifications), label: 'Notifications'),
        BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profile'),
      ],
    ),
  );
}
```

### 12.10 Auth Persistence & Splash

```dart
// lib/screens/splash_screen.dart
class SplashScreen extends StatefulWidget { const SplashScreen({super.key}); @override State<SplashScreen> createState()=>_SplashScreenState();}
class _SplashScreenState extends State<SplashScreen> {
  @override void initState(){ super.initState(); _check(); }
  Future<void> _check() async {
    final token = await const FlutterSecureStorage().read(key: 'token');
    if (!mounted) return;
    if (token != null) {
      ApiClient().dio.options.headers['Authorization'] = 'Bearer $token';
      Navigator.pushReplacement(context, MaterialPageRoute(builder: (_)=> const MainTabs()));
    } else {
      Navigator.pushReplacement(context, MaterialPageRoute(builder: (_)=> const LoginScreen()));
    }
  }
  @override Widget build(BuildContext c) => const Scaffold(body: Center(child: CircularProgressIndicator()));
}
```

---

## 13. Permissions

### Android `android/app/src/main/AndroidManifest.xml`
```xml
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.USE_BIOMETRIC" />
<uses-permission android:name="android.permission.USE_FINGERPRINT" /> <!-- legacy -->
```

### iOS `ios/Runner/Info.plist`
```xml
<key>NSLocationWhenInUseUsageDescription</key>
<string>We need your location to record attendance check-in/out</string>
<key>NSCameraUsageDescription</key>
<string>Camera is used for optional attendance selfie</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>Photo library access for profile photo</string>
<key>NSFaceIDUsageDescription</key>
<string>We use Face ID to securely verify your attendance</string>
```

### Runtime Permission Request (use Geolocator + permission_handler if needed)

---

## 14. Postman

### Collection Variables
```
base_url = https://feedtanstore.com
token = (from login response)
```

### Quick Test Flow

1. **Login**
   ```
   POST {{base_url}}/api/attendance/login
   Body: { "login": "admin@example.com", "password": "password" }
   → Copy token variable
   ```
2. **Dashboard**
   ```
   GET {{base_url}}/api/attendance/dashboard
   Header: Authorization: Bearer {{token}}
   ```
3. **Check In**
   ```
   POST {{base_url}}/api/attendance/check-in
   Header: Authorization: Bearer {{token}}
   Body: form-data
     latitude: -3.3869
     longitude: 36.6883
     address: Moshi
     photo: (file optional)
   ```
4. **History**
   ```
   GET {{base_url}}/api/attendance/history?month=2026-09
   Header: Authorization: Bearer {{token}}
   ```
5. **Leave Apply**
   ```
   POST {{base_url}}/api/attendance/leaves
   Body: { "leave_type": "casual", "start_date": "2026-09-15", "end_date": "2026-09-16", "reason": "Family event" }
   ```

---

## 15. Changelog

- **2026-09-10 (v2)**: **Biometric update** – `check-in/out` now accept `biometric_verified`, `biometric_type`, `device_info`. Backend stores `check_in/out_biometric_verified` (boolean, no template). Dashboard returns biometric flags. Docs add `local_auth` integration (no fingerprint stored on server). Security: phone OS verifies, server trusts flag + GPS + Bearer token.
- **2026-09-10 (v1)**: Initial Attendance MVP API released. Endpoints under `/api/attendance/*` with Sanctum. Covers Login (phone/email), Dashboard, Check In/Out (GPS+photo), History/Calendar/Stats, Profile, Leave, Notifications.

---

## Appendix – Storage Paths

- Check-in photos: `storage/app/public/attendance/check-in/*` → URL `{{base_url}}/storage/attendance/check-in/...`
- Check-out photos: `storage/app/public/attendance/check-out/*`
- Profile images: `storage/app/public/profile-images/*`

Run after deploy:
```bash
php artisan storage:link
php artisan migrate
```

## Appendix – Late Logic

Late is determined server-side by comparing `now` vs `WorkShift.start_time` (if defined, else `09:00:00`) + 15 min grace. Create a WorkShift via `work_shifts` table:

```sql
INSERT INTO work_shifts (name, start_time, end_time, is_active) VALUES ('Day Shift', '09:00:00', '17:00:00', 1);
```

If `WorkShift` not found, fallback to `09:15` threshold.

---

**Support:** For integration help, include `Authorization` header, check `php artisan route:list | grep attendance`, and verify `storage:link`. This doc is standalone – no dependency on Rider API doc.

