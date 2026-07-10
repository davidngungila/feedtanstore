# Rider Mobile App API Documentation (Flutter)

## Base URL
All endpoints are prefixed with:
```
{{base_url}}/api
```
Replace `{{base_url}}` with your actual server address (e.g., `https://feedtanstore.com` or `http://localhost:8000`)

---

## Location Permissions & OpenStreetMap Integration
- **IMPORTANT**: The rider app must request location permissions when the app is opened.
- **OpenStreetMap (OSM)**: The app uses OpenStreetMap for live navigation, route planning, and map display.
- **Real-time Location Updates**: The app should periodically update the rider's location using the `/rider/location` endpoint.
- **Map Packages (Flutter)**: Use packages like `flutter_map` or `osm_flutter` for OSM integration, and `geolocator` for location access.

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
    "latest_location": {
      "id": 1,
      "delivery_rider_id": 1,
      "latitude": -3.3869,
      "longitude": 36.6883,
      "created_at": "2026-01-01T00:00:00.000000Z",
      "updated_at": "2026-01-01T00:00:00.000000Z"
    }
  }
}
```

---

### 2.2 Update Personal Information
**Endpoint**: `PUT /rider/personal-info`

**Authentication**: Bearer Token required

**Description**: Update the rider's personal details.

**Request Body**:
```json
{
  "name": "John Doe Updated",
  "phone": "255712345678",
  "date_of_birth": "1990-01-01",
  "gender": "Male",
  "address": "123 Main St, Moshi"
}
```
All fields are optional.

**Success Response (200 OK)**:
```json
{
  "message": "Personal info updated",
  "rider": {
    "id": 1,
    "name": "John Doe Updated",
    "phone": "255712345678",
    "date_of_birth": "1990-01-01",
    "gender": "Male",
    "address": "123 Main St, Moshi"
  }
}
```

---

### 2.3 Get Vehicle Details
**Endpoint**: `GET /rider/vehicle`

**Authentication**: Bearer Token required

**Description**: Retrieve the rider's vehicle information.

**Success Response (200 OK)**:
```json
{
  "vehicle_type": "Motorcycle",
  "vehicle_plate": "ABC 123",
  "vehicle_model": "Honda CB 125",
  "vehicle_color": "Red",
  "vehicle_year": "2020"
}
```

---

### 2.4 Update Vehicle Details
**Endpoint**: `PUT /rider/vehicle`

**Authentication**: Bearer Token required

**Description**: Update the rider's vehicle information.

**Request Body**:
```json
{
  "vehicle_type": "Motorcycle",
  "vehicle_plate": "ABC 123",
  "vehicle_model": "Honda CB 125",
  "vehicle_color": "Red",
  "vehicle_year": "2020"
}
```
All fields are optional.

**Success Response (200 OK)**:
```json
{
  "message": "Vehicle details updated",
  "rider": {
    "id": 1,
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC 123",
    "vehicle_model": "Honda CB 125",
    "vehicle_color": "Red",
    "vehicle_year": "2020"
  }
}
```

---

### 2.5 Get Documents
**Endpoint**: `GET /rider/documents`

**Authentication**: Bearer Token required

**Description**: Retrieve the rider's document information.

**Success Response (200 OK)**:
```json
{
  "nid_number": "1234567890123456",
  "driving_license_number": "DL-123456",
  "license_expiry_date": "2030-12-31",
  "insurance_number": "INS-789012",
  "insurance_expiry_date": "2027-06-30"
}
```

---

### 2.6 Update Documents
**Endpoint**: `PUT /rider/documents`

**Authentication**: Bearer Token required

**Description**: Update the rider's document information.

**Request Body**:
```json
{
  "nid_number": "1234567890123456",
  "driving_license_number": "DL-123456",
  "license_expiry_date": "2030-12-31",
  "insurance_number": "INS-789012",
  "insurance_expiry_date": "2027-06-30"
}
```
All fields are optional.

**Success Response (200 OK)**:
```json
{
  "message": "Documents updated",
  "rider": {
    "id": 1,
    "nid_number": "1234567890123456",
    "driving_license_number": "DL-123456",
    "license_expiry_date": "2030-12-31",
    "insurance_number": "INS-789012",
    "insurance_expiry_date": "2027-06-30"
  }
}
```

---

### 2.7 Get Bank Details
**Endpoint**: `GET /rider/bank-details`

**Authentication**: Bearer Token required

**Description**: Retrieve the rider's bank and mobile money information.

**Success Response (200 OK)**:
```json
{
  "bank_name": "CRDB Bank",
  "bank_account_number": "0123456789012345",
  "bank_account_name": "John Doe",
  "bank_branch": "Moshi Main",
  "mobile_money_number": "255712345678",
  "mobile_money_provider": "M-Pesa"
}
```

---

### 2.8 Update Bank Details
**Endpoint**: `PUT /rider/bank-details`

**Authentication**: Bearer Token required

**Description**: Update the rider's bank and mobile money information.

**Request Body**:
```json
{
  "bank_name": "CRDB Bank",
  "bank_account_number": "0123456789012345",
  "bank_account_name": "John Doe",
  "bank_branch": "Moshi Main",
  "mobile_money_number": "255712345678",
  "mobile_money_provider": "M-Pesa"
}
```
All fields are optional.

**Success Response (200 OK)**:
```json
{
  "message": "Bank details updated",
  "rider": {
    "id": 1,
    "bank_name": "CRDB Bank",
    "bank_account_number": "0123456789012345",
    "bank_account_name": "John Doe",
    "bank_branch": "Moshi Main",
    "mobile_money_number": "255712345678",
    "mobile_money_provider": "M-Pesa"
  }
}
```

---

### 2.9 Get Performance Statistics
**Endpoint**: `GET /rider/performance`

**Authentication**: Bearer Token required

**Description**: Retrieve the rider's performance statistics.

**Success Response (200 OK)**:
```json
{
  "total_deliveries": 150,
  "total_earnings": 300000,
  "rating": 4.8,
  "total_reviews": 45,
  "today_deliveries": 5,
  "this_week_deliveries": 25,
  "this_month_deliveries": 60
}
```

---

### 2.10 Get Customer Reviews
**Endpoint**: `GET /rider/reviews`

**Authentication**: Bearer Token required

**Description**: Retrieve paginated list of customer reviews for the rider.

**Success Response (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "delivery_rider_id": 1,
      "online_order_id": 1,
      "customer_name": "Jane Smith",
      "customer_email": "jane@example.com",
      "rating": 5,
      "comment": "Excellent service, on time!",
      "created_at": "2026-07-01T10:30:00.000000Z",
      "updated_at": "2026-07-01T10:30:00.000000Z"
    }
  ],
  "links": {},
  "meta": {}
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
**Endpoint**: `GET /rider/orders`

**Authentication**: Bearer Token required

**Description**: Retrieve all orders assigned to the authenticated rider, including pending acceptance, accepted, and in transit orders.

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
    "status": "confirmed",
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
    "rider_acceptance_status": "pending",
    "rider_accepted_at": null,
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
**Endpoint**: `GET /rider/orders/available`

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
    "rider_acceptance_status": null,
    "rider_accepted_at": null,
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
**Endpoint**: `GET /rider/orders/{id}`

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
  "status": "confirmed",
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
  "rider_acceptance_status": "pending",
  "rider_accepted_at": null,
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
      "notes": "Rider assigned and acceptance pending",
      "changed_by": 2,
      "created_at": "2026-01-01T00:00:00.000000Z"
    }
  ]
}
```

---

### 4.4 Accept an Order
**Endpoint**: `POST /rider/orders/{id}/accept`

**Authentication**: Bearer Token required

**Description**: Accept an assigned order, set `rider_acceptance_status` to "accepted", and update order status to "out_for_delivery". Can also be used to accept an available unassigned order.

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
    "delivery_rider_id": 1,
    "rider_acceptance_status": "accepted",
    "rider_accepted_at": "2026-07-10T10:30:00.000000Z"
  }
}
```

**Error Response (400 Bad Request)**:
```json
{
  "message": "Order already assigned to another rider"
}
```
OR
```json
{
  "message": "Order already accepted"
}
```

---

### 4.5 Reject an Order
**Endpoint**: `POST /rider/orders/{id}/reject`

**Authentication**: Bearer Token required

**Description**: Reject an assigned order, set `rider_acceptance_status` to "rejected", unassign the rider, and set order status back to "confirmed".

**Path Parameters**:
- `id`: The ID of the order (integer)

**Success Response (200 OK)**:
```json
{
  "message": "Order rejected",
  "order": {
    "id": 1,
    "order_number": "ORD-ABC123",
    "status": "confirmed",
    "delivery_rider_id": null,
    "rider_acceptance_status": "rejected"
  }
}
```

**Error Response (400 Bad Request)**:
```json
{
  "message": "Order not assigned to you"
}
```
OR
```json
{
  "message": "Cannot reject accepted order"
}
```

---

### 4.6 Update Order Status
**Endpoint**: `PUT /rider/orders/{id}/status`

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

## 5. Public Endpoints (No Auth Required)

### 5.1 Get Terms & Policies
**Endpoint**: `GET /terms-policies`

**Description**: Retrieve terms of service and privacy policy for riders.

**Success Response (200 OK)**:
```json
{
  "terms_of_service": "Terms of service text here...",
  "privacy_policy": "Privacy policy text here...",
  "rider_terms": "Rider-specific terms here...",
  "rider_privacy_policy": "Rider-specific privacy policy here..."
}
```

---

### 5.2 Get Rider Support Information
**Endpoint**: `GET /rider-support`

**Description**: Retrieve support contact information for riders.

**Success Response (200 OK)**:
```json
{
  "support_email": "support@feedtanstore.com",
  "support_phone": "255712345678",
  "support_address": "123 Main St, Moshi, Tanzania"
}
```

---

## 6. Public Real-Time Data Endpoints (No Auth Required)

### 6.1 Get All Active Riders
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

### 6.2 Get All Orders for Map
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
  "date_of_birth": "date|null",
  "gender": "string|null",
  "address": "string|null",
  "vehicle_type": "string|null",
  "vehicle_plate": "string|null",
  "vehicle_model": "string|null",
  "vehicle_color": "string|null",
  "vehicle_year": "string|null",
  "nid_number": "string|null",
  "driving_license_number": "string|null",
  "license_expiry_date": "date|null",
  "insurance_number": "string|null",
  "insurance_expiry_date": "date|null",
  "bank_name": "string|null",
  "bank_account_number": "string|null",
  "bank_account_name": "string|null",
  "bank_branch": "string|null",
  "mobile_money_number": "string|null",
  "mobile_money_provider": "string|null",
  "total_deliveries": "integer",
  "total_earnings": "integer",
  "rating": "integer",
  "total_reviews": "integer",
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

### RiderReview Model
```json
{
  "id": "integer",
  "delivery_rider_id": "integer",
  "online_order_id": "integer|null",
  "customer_name": "string|null",
  "customer_email": "string (email)|null",
  "rating": "integer",
  "comment": "string|null",
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
  "status": "string (pending|confirmed|processing|ready|out_for_delivery|delivered|cancelled)",
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
  "rider_acceptance_status": "string (pending|accepted|rejected)|null",
  "rider_accepted_at": "datetime|null",
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
  final String baseUrl = 'https://feedtanstore.com/api';
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

  // Update Personal Info
  Future<Map<String, dynamic>> updatePersonalInfo(Map<String, dynamic> data) async {
    final response = await _dio.put('/rider/personal-info', data: data);
    return response.data;
  }

  // Get Vehicle Details
  Future<Map<String, dynamic>> getVehicleDetails() async {
    final response = await _dio.get('/rider/vehicle');
    return response.data;
  }

  // Update Vehicle Details
  Future<Map<String, dynamic>> updateVehicleDetails(Map<String, dynamic> data) async {
    final response = await _dio.put('/rider/vehicle', data: data);
    return response.data;
  }

  // Get Documents
  Future<Map<String, dynamic>> getDocuments() async {
    final response = await _dio.get('/rider/documents');
    return response.data;
  }

  // Update Documents
  Future<Map<String, dynamic>> updateDocuments(Map<String, dynamic> data) async {
    final response = await _dio.put('/rider/documents', data: data);
    return response.data;
  }

  // Get Bank Details
  Future<Map<String, dynamic>> getBankDetails() async {
    final response = await _dio.get('/rider/bank-details');
    return response.data;
  }

  // Update Bank Details
  Future<Map<String, dynamic>> updateBankDetails(Map<String, dynamic> data) async {
    final response = await _dio.put('/rider/bank-details', data: data);
    return response.data;
  }

  // Get Performance Stats
  Future<Map<String, dynamic>> getPerformanceStats() async {
    final response = await _dio.get('/rider/performance');
    return response.data;
  }

  // Get Reviews
  Future<Map<String, dynamic>> getReviews() async {
    final response = await _dio.get('/rider/reviews');
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
    final response = await _dio.get('/rider/orders/available');
    return response.data;
  }

  // Accept Order
  Future<Map<String, dynamic>> acceptOrder(int orderId) async {
    final response = await _dio.post('/rider/orders/$orderId/accept');
    return response.data;
  }

  // Reject Order
  Future<Map<String, dynamic>> rejectOrder(int orderId) async {
    final response = await _dio.post('/rider/orders/$orderId/reject');
    return response.data;
  }

  // Update Order Status
  Future<Map<String, dynamic>> updateOrderStatus(int orderId, String status, [String? notes]) async {
    final response = await _dio.put(
      '/rider/orders/$orderId/status',
      data: {'status': status, if (notes != null) 'notes': notes},
    );
    return response.data;
  }

  // Get Rider's Orders
  Future<List<dynamic>> getRiderOrders() async {
    final response = await _dio.get('/rider/orders');
    return response.data;
  }

  // Get Order Details
  Future<Map<String, dynamic>> getOrderDetails(int orderId) async {
    final response = await _dio.get('/rider/orders/$orderId');
    return response.data;
  }
}
```

### Location Permissions & OSM in Flutter
```dart
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';

class MapScreen extends StatefulWidget {
  const MapScreen({super.key});

  @override
  State<MapScreen> createState() => _MapScreenState();
}

class _MapScreenState extends State<MapScreen> {
  LatLng? _currentPosition;

  @override
  void initState() {
    super.initState();
    _getLocationPermission();
  }

  Future<void> _getLocationPermission() async {
    bool serviceEnabled;
    LocationPermission permission;

    // Check if location services are enabled
    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      // Location services are not enabled
      return;
    }

    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        // Permissions are denied
        return;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      // Permissions are permanently denied
      return;
    }

    // Get current location
    Position position = await Geolocator.getCurrentPosition();
    setState(() {
      _currentPosition = LatLng(position.latitude, position.longitude);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('OpenStreetMap Navigation')),
      body: _currentPosition == null
          ? const Center(child: CircularProgressIndicator())
          : FlutterMap(
              options: MapOptions(
                initialCenter: _currentPosition!,
                initialZoom: 15,
              ),
              children: [
                TileLayer(
                  urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                  userAgentPackageName: 'com.feedtanstore.rider',
                ),
                MarkerLayer(
                  markers: [
                    Marker(
                      point: _currentPosition!,
                      width: 80,
                      height: 80,
                      child: const Icon(Icons.location_on, color: Colors.red, size: 40),
                    ),
                  ],
                ),
              ],
            ),
    );
  }
}
```
