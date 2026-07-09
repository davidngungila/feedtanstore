# Rider Mobile App API Documentation (Flutter)

## Base URL
All endpoints are prefixed with:
```
{{base_url}}/api
```
Replace `{{base_url}}` with your actual server address (e.g., `https://api.feedtanstore.com` or `http://localhost:8000`)

---

## Authentication
All protected endpoints require a Bearer Token in the Authorization header:
```
Authorization: Bearer {{token}}
```

---

## 1. Authentication Endpoints

### 1.1 Rider Login
**Endpoint**: `POST /auth/login`

**Description**: Authenticate a rider using their email and password, returns a Bearer token.

**Request Body**:
```json
{
  "email": "rider@example.com",
  "password": "password123"
}
```

**Success Response (200 OK)**:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "rider@example.com",
    "phone": "255712345678",
    "role": "rider",
    "email_verified_at": null,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z"
  },
  "rider": {
    "id": 1,
    "name": "John Doe",
    "phone": "255712345678",
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC 123",
    "is_active": true,
    "user_id": 1,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
}
```

**Error Response (422 Unprocessable Entity)**:
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 1.2 Rider Logout
**Endpoint**: `POST /auth/logout`

**Authentication**: Bearer Token required

**Description**: Invalidate the current access token.

**Success Response (200 OK)**:
```json
{
  "message": "Logged out successfully"
}
```

---

## 2. Rider Profile Endpoints

### 2.1 Get Rider Profile
**Endpoint**: `GET /rider/profile`

**Authentication**: Bearer Token required

**Description**: Retrieve the authenticated rider's profile and latest location.

**Success Response (200 OK)**:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "rider@example.com",
    "phone": "255712345678",
    "role": "rider",
    "email_verified_at": null,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z"
  },
  "rider": {
    "id": 1,
    "name": "John Doe",
    "phone": "255712345678",
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC 123",
    "is_active": true,
    "user_id": 1,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z",
    "locations": [
      {
        "id": 1,
        "delivery_rider_id": 1,
        "latitude": -3.3869,
        "longitude": 36.6883,
        "created_at": "2026-01-01T00:00:00.000000Z",
        "updated_at": "2026-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

---

### 2.2 Update Rider Profile
**Endpoint**: `PUT /rider/profile`

**Authentication**: Bearer Token required

**Description**: Update the authenticated rider's profile details.

**Request Body**:
```json
{
  "name": "John Doe Updated",
  "phone": "255712345678",
  "vehicle_type": "Bike",
  "vehicle_plate": "XYZ 789"
}
```
All fields are optional.

**Success Response (200 OK)**:
```json
{
  "message": "Profile updated",
  "rider": {
    "id": 1,
    "name": "John Doe Updated",
    "phone": "255712345678",
    "vehicle_type": "Bike",
    "vehicle_plate": "XYZ 789",
    "is_active": true,
    "user_id": 1,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-02T00:00:00.000000Z"
  }
}
```

---

## 3. Rider Location Endpoints

### 3.1 Update Rider Location
**Endpoint**: `POST /rider/location`

**Authentication**: Bearer Token required

**Description**: Update the authenticated rider's current GPS location (should be called periodically for real-time tracking).

**Request Body**:
```json
{
  "latitude": -3.3869,
  "longitude": 36.6883
}
```

**Success Response (200 OK)**:
```json
{
  "message": "Location updated"
}
```

---

### 3.2 Get Rider Location
**Endpoint**: `GET /rider/location/{riderId}`

**Authentication**: Bearer Token required

**Description**: Retrieve the latest location of a specific rider.

**Path Parameters**:
- `riderId`: The ID of the rider (integer)

**Success Response (200 OK)**:
```json
{
  "id": 1,
  "delivery_rider_id": 1,
  "latitude": -3.3869,
  "longitude": 36.6883,
  "created_at": "2026-01-01T00:00:00.000000Z",
  "updated_at": "2026-01-01T00:00:00.000000Z"
}
```

---

## 4. Order Endpoints

### 4.1 Get Rider's Assigned Orders
**Endpoint**: `GET /orders`

**Authentication**: Bearer Token required

**Description**: Retrieve all orders assigned to the authenticated rider.

**Success Response (200 OK)**:
```json
[
  {
    "id": 1,
    "order_number": "ORD-ABC123",
    "tracking_token": "abcdef123456",
    "delivery_code": "1234",
    "customer_id": 1,
    "customer_name": "Jane Smith",
    "customer_phone": "255711223344",
    "customer_email": "jane@example.com",
    "delivery_address": "123 Main St, Moshi",
    "delivery_latitude": -3.3600,
    "delivery_longitude": 36.7000,
    "status": "out_for_delivery",
    "payment_status": "pending",
    "payment_method": "cash",
    "payment_transaction_id": null,
    "payment_order_reference": null,
    "clickpesa_status": null,
    "subtotal": 15000,
    "discount": 0,
    "delivery_fee": 2000,
    "total": 17000,
    "delivery_rider_id": 1,
    "user_id": null,
    "notes": "Leave at front door",
    "is_processed": false,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z",
    "items": [
      {
        "id": 1,
        "online_order_id": 1,
        "product_id": 1,
        "quantity": 2,
        "price": 7500,
        "total": 15000,
        "product": {
          "id": 1,
          "name": "Product Name",
          "description": "Product Description",
          "price": 7500,
          "quantity": 50
        }
      }
    ],
    "customer": {
      "id": 1,
      "name": "Jane Smith",
      "phone": "255711223344",
      "email": "jane@example.com"
    }
  }
]
```

---

### 4.2 Get Available Orders
**Endpoint**: `GET /orders/available`

**Authentication**: Bearer Token required

**Description**: Retrieve all orders that are confirmed and not yet assigned to any rider.

**Success Response (200 OK)**:
```json
[
  {
    "id": 2,
    "order_number": "ORD-DEF456",
    "tracking_token": "ghijkl789012",
    "delivery_code": "5678",
    "customer_id": 2,
    "customer_name": "Bob Johnson",
    "customer_phone": "255711334455",
    "customer_email": "bob@example.com",
    "delivery_address": "456 Oak Ave, Arusha",
    "delivery_latitude": -3.3700,
    "delivery_longitude": 36.6900,
    "status": "confirmed",
    "payment_status": "paid",
    "payment_method": "online",
    "payment_transaction_id": "txn_123",
    "payment_order_reference": "ref_456",
    "clickpesa_status": "SUCCESS",
    "subtotal": 20000,
    "discount": 0,
    "delivery_fee": 3000,
    "total": 23000,
    "delivery_rider_id": null,
    "user_id": null,
    "notes": null,
    "is_processed": false,
    "created_at": "2026-01-02T00:00:00.000000Z",
    "updated_at": "2026-01-02T00:00:00.000000Z",
    "items": [
      {
        "id": 2,
        "online_order_id": 2,
        "product_id": 2,
        "quantity": 1,
        "price": 20000,
        "total": 20000,
        "product": {
          "id": 2,
          "name": "Another Product",
          "description": "Another Description",
          "price": 20000,
          "quantity": 30
        }
      }
    ],
    "customer": {
      "id": 2,
      "name": "Bob Johnson",
      "phone": "255711334455",
      "email": "bob@example.com"
    }
  }
]
```

---

### 4.3 Get Order Details
**Endpoint**: `GET /orders/{id}`

**Authentication**: Bearer Token required

**Description**: Retrieve detailed information about a specific order.

**Path Parameters**:
- `id`: The ID of the order (integer)

**Success Response (200 OK)**:
```json
{
  "id": 1,
  "order_number": "ORD-ABC123",
  "tracking_token": "abcdef123456",
  "delivery_code": "1234",
  "customer_id": 1,
  "customer_name": "Jane Smith",
  "customer_phone": "255711223344",
  "customer_email": "jane@example.com",
  "delivery_address": "123 Main St, Moshi",
  "delivery_latitude": -3.3600,
  "delivery_longitude": 36.7000,
  "status": "out_for_delivery",
  "payment_status": "pending",
  "payment_method": "cash",
  "payment_transaction_id": null,
  "payment_order_reference": null,
  "clickpesa_status": null,
  "subtotal": 15000,
  "discount": 0,
  "delivery_fee": 2000,
  "total": 17000,
  "delivery_rider_id": 1,
  "user_id": null,
  "notes": "Leave at front door",
  "is_processed": false,
  "created_at": "2026-01-01T00:00:00.000000Z",
  "updated_at": "2026-01-01T00:00:00.000000Z",
  "items": [
    {
      "id": 1,
      "online_order_id": 1,
      "product_id": 1,
      "quantity": 2,
      "price": 7500,
      "total": 15000,
      "product": {
        "id": 1,
        "name": "Product Name",
        "description": "Product Description",
        "price": 7500,
        "quantity": 50
      }
    }
  ],
  "customer": {
    "id": 1,
    "name": "Jane Smith",
    "phone": "255711223344",
    "email": "jane@example.com"
  },
  "rider": {
    "id": 1,
    "name": "John Doe",
    "phone": "255712345678",
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC 123",
    "is_active": true
  },
  "status_history": [
    {
      "id": 1,
      "online_order_id": 1,
      "old_status": "pending",
      "new_status": "confirmed",
      "notes": null,
      "changed_by": 2,
      "created_at": "2026-01-01T00:00:00.000000Z"
    }
  ]
}
```

---

### 4.4 Accept an Order
**Endpoint**: `POST /orders/{id}/accept`

**Authentication**: Bearer Token required

**Description**: Assign an available order to the authenticated rider and set its status to "out_for_delivery".

**Path Parameters**:
- `id`: The ID of the order (integer)

**Success Response (200 OK)**:
```json
{
  "message": "Order accepted",
  "order": {
    "id": 2,
    "order_number": "ORD-DEF456",
    "status": "out_for_delivery",
    "delivery_rider_id": 1
  }
}
```

**Error Response (400 Bad Request)**:
```json
{
  "message": "Order already assigned"
}
```

---

### 4.5 Update Order Status
**Endpoint**: `PUT /orders/{id}/status`

**Authentication**: Bearer Token required

**Description**: Update the status of an assigned order.

**Path Parameters**:
- `id`: The ID of the order (integer)

**Request Body**:
```json
{
  "status": "delivered",
  "notes": "Delivered successfully to customer"
}
```

**Allowed Status Values**: `pending`, `confirmed`, `processing`, `out_for_delivery`, `delivered`, `cancelled`
`notes` field is optional.

**Success Response (200 OK)**:
```json
{
  "message": "Order status updated",
  "order": {
    "id": 1,
    "status": "delivered"
  }
}
```

**Error Response (500 Internal Server Error)**:
```json
{
  "message": "Failed to update order status"
}
```

---

## 5. Public Real-Time Data Endpoints (No Auth Required)

### 5.1 Get All Active Riders
**Endpoint**: `GET /realtime/riders`

**Description**: Retrieve all delivery riders with their latest locations.

**Success Response (200 OK)**:
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "phone": "255712345678",
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC 123",
    "is_active": true,
    "user_id": 1,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z",
    "latest_location": {
      "id": 1,
      "delivery_rider_id": 1,
      "latitude": -3.3869,
      "longitude": 36.6883,
      "created_at": "2026-01-01T00:00:00.000000Z",
      "updated_at": "2026-01-01T00:00:00.000000Z"
    }
  }
]
```

---

### 5.2 Get All Orders for Map
**Endpoint**: `GET /realtime/orders`

**Description**: Retrieve all orders that have delivery coordinates.

**Success Response (200 OK)**:
```json
[
  {
    "id": 1,
    "order_number": "ORD-ABC123",
    "delivery_latitude": -3.3600,
    "delivery_longitude": 36.7000,
    "status": "out_for_delivery",
    "rider": {
      "id": 1,
      "name": "John Doe",
      "phone": "255712345678"
    },
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Product Name"
        }
      }
    ]
  }
]
```

---

## Data Models

### User Model
```json
{
  "id": "integer",
  "name": "string",
  "email": "string (email)",
  "phone": "string",
  "role": "string",
  "email_verified_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### DeliveryRider Model
```json
{
  "id": "integer",
  "name": "string",
  "phone": "string",
  "vehicle_type": "string",
  "vehicle_plate": "string",
  "is_active": "boolean",
  "user_id": "integer",
  "created_at": "datetime",
  "updated_at": "datetime",
  "latest_location": "RiderLocation|null"
}
```

### RiderLocation Model
```json
{
  "id": "integer",
  "delivery_rider_id": "integer",
  "latitude": "float",
  "longitude": "float",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### OnlineOrder Model
```json
{
  "id": "integer",
  "order_number": "string",
  "tracking_token": "string",
  "delivery_code": "string (4-digit)",
  "customer_id": "integer|null",
  "customer_name": "string",
  "customer_phone": "string",
  "customer_email": "string (email)|null",
  "delivery_address": "string",
  "delivery_latitude": "float|null",
  "delivery_longitude": "float|null",
  "status": "string (pending|confirmed|processing|out_for_delivery|delivered|cancelled)",
  "payment_status": "string (pending|paid|failed)",
  "payment_method": "string (cash|online|bank)|null",
  "payment_transaction_id": "string|null",
  "payment_order_reference": "string|null",
  "clickpesa_status": "string|null",
  "subtotal": "float",
  "discount": "float",
  "delivery_fee": "float",
  "total": "float",
  "delivery_rider_id": "integer|null",
  "user_id": "integer|null",
  "notes": "string|null",
  "is_processed": "boolean",
  "created_at": "datetime",
  "updated_at": "datetime",
  "items": "array of OnlineOrderItem",
  "customer": "Customer|null",
  "rider": "DeliveryRider|null",
  "status_history": "array of OnlineOrderStatusHistory"
}
```

---

## Flutter Implementation Example

### Adding Bearer Token to Requests (Dio)
```dart
import 'package:dio/dio.dart';

class ApiService {
  final Dio _dio = Dio();
  final String baseUrl = 'https://api.feedtanstore.com/api';
  String? _token;

  ApiService() {
    _dio.options.baseUrl = baseUrl;
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) {
        if (_token != null) {
          options.headers['Authorization'] = 'Bearer $_token';
        }
        return handler.next(options);
      },
    ));
  }

  // Login
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await _dio.post(
      '/auth/login',
      data: {'email': email, 'password': password},
    );
    _token = response.data['token'];
    return response.data;
  }

  // Get Profile
  Future<Map<String, dynamic>> getProfile() async {
    final response = await _dio.get('/rider/profile');
    return response.data;
  }

  // Update Location
  Future<void> updateLocation(double latitude, double longitude) async {
    await _dio.post(
      '/rider/location',
      data: {'latitude': latitude, 'longitude': longitude},
    );
  }

  // Get Available Orders
  Future<List<dynamic>> getAvailableOrders() async {
    final response = await _dio.get('/orders/available');
    return response.data;
  }

  // Accept Order
  Future<Map<String, dynamic>> acceptOrder(int orderId) async {
    final response = await _dio.post('/orders/$orderId/accept');
    return response.data;
  }

  // Update Order Status
  Future<Map<String, dynamic>> updateOrderStatus(int orderId, String status, [String? notes]) async {
    final response = await _dio.put(
      '/orders/$orderId/status',
      data: {'status': status, if (notes != null) 'notes': notes},
    );
    return response.data;
  }
}
```
