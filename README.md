# Feedtan Store Management System

## Overview
Feedtan Store is a comprehensive multi-module retail management system with the following key features:
- **Point of Sale (POS)**: Cashier interface for in-store sales
- **Inventory Management**: Product, category, brand, and stock management
- **Purchasing & Suppliers**: Purchase orders, goods receipt notes, and supplier payments
- **Online Sales**: E-commerce platform, order management, and delivery tracking
- **Finance**: General ledger, expenses, income, shareholders, and financial reports
- **Delivery Management**: Delivery riders, real-time location tracking, and delivery maps
- **Mobile App API**: For delivery riders with real-time location updates
- **Customers**: Customer management, loyalty points, and credit tracking
- **HR & Security**: Employee management, attendance, roles, and system security
- **Reports & Analytics**: Comprehensive reporting for all modules

## Live System
The system is hosted at: [https://store.feedtancmg.org/](https://store.feedtancmg.org/)

## Tech Stack
- **Framework**: Laravel 11
- **Frontend**: Tailwind CSS, Leaflet (Maps)
- **Database**: MySQL/MariaDB
- **API Authentication**: Laravel Sanctum
- **Payment Integration**: ClickPesa (for online payments)

---

## 1. User Roles & Permissions
The system supports multiple user roles, including:
- **Administrator**: Full system access
- **Cashier**: POS and sales only
- **Manager**: Inventory, purchasing, and sales management
- **Accountant**: Finance module
- **Delivery Rider**: API access for delivery management (requires user account linked to rider profile)

---

## 2. Web Application Features

### 2.1 Authentication
- **Login**: `/login`
- **Logout**: `/logout`

### 2.2 Public Shop (Customer Frontend)
- **Shop Homepage**: `/shop` - Browse products
- **Product Detail**: `/shop/product/{product}`
- **Checkout**: `/shop/checkout`
- **Order Tracking**: `/shop/tracking/{orderNumber}` - Track order with real-time delivery map
- **PDF Receipt**: `/shop/tracking/{orderNumber}/pdf` - Download order receipt

### 2.3 Dashboard
- **Main Dashboard**: `/dashboard` - Overview of sales, inventory, and online orders
- **Sales Dashboard**: `/dashboard/sales`
- **Online Orders Dashboard**: `/dashboard/online-orders`
- **Purchases Dashboard**: `/dashboard/purchases`
- **Inventory Dashboard**: `/dashboard/inventory`

### 2.4 Sales Management
- **New Sale**: `/sales/new`
- **Sales History**: `/sales/history`
- **Returns**: `/sales/returns`
- **Cancelled Sales**: `/sales/cancelled`
- **Discounts**: `/sales/discounts`
- **Receipts**: `/sales/receipts`
- **Shifts**: `/sales/shifts`

### 2.5 Inventory Management
- **Products**: `/inventory/products`
- **Categories**: `/inventory/categories`
- **Brands**: `/inventory/brands`
- **Units**: `/inventory/units`
- **Stock Receiving**: `/inventory/receiving`
- **Stock Adjustments**: `/inventory/adjustments`
- **Stock Transfers**: `/inventory/transfers`
- **Stock Count**: `/inventory/count`
- **Low Stock**: `/inventory/low-stock`
- **Expiry Dates**: `/inventory/expiry`
- **Damaged Goods**: `/inventory/damaged`
- **Barcodes**: `/inventory/barcodes`
- **Reports**: `/inventory/reports`

### 2.6 Purchasing & Suppliers
- **Suppliers**: `/purchasing/suppliers`
- **Purchase Orders**: `/purchasing/orders`
- **Goods Received Notes (GRN)**: `/purchasing/grn`
- **Payments**: `/purchasing/payments`
- **Reports**: `/purchasing/reports`

### 2.7 Customers
- **Customer List**: `/customers/list`
- **Customer Groups**: `/customers/groups`
- **Loyalty Points**: `/customers/loyalty`
- **Credit Management**: `/customers/credit`
- **Purchase History**: `/customers/history`

### 2.8 Finance Module
- **Finance Dashboard**: `/finance`
- **Journal Entries**: `/finance/journal-entries`
- **General Ledger**: `/finance/general-ledger`
- **Expenses**: `/finance/expenses`
- **Income**: `/finance/income`
- **Cash Management**: `/finance/cash`
- **Bank Accounts**: `/finance/bank`
- **Mobile Money**: `/finance/mobile-money`
- **Capital Management**: `/finance/capital`
- **Shareholders**: `/finance/shareholders`
- **Balance Sheet**: `/finance/balance-sheet`
- **Income Statement**: `/finance/income-statement`
- **Mobile Money Reconciliation**: `/finance/mobile-money-reconciliation`
- **Accounts Receivable**: `/finance/accounts-receivable`
- **Accounts Payable**: `/finance/accounts-payable`
- **Transactions**: `/finance/transactions`
- **Tax Management**: `/finance/tax-management`
- **Chart of Accounts**: `/finance/chart-of-accounts`
- **Budgets**: `/finance/budgets`
- **Assets**: `/finance/assets`
- **Reports**: `/finance/reports`

### 2.9 Online Sales
- **Orders**: `/online/orders`
- **Product Catalog**: `/online/catalog`
- **Carousel Management**: `/online/carousel`
- **Delivery Management**: `/online/delivery`
- **Riders**: `/online/riders` - Manage delivery riders, activate/deactivate, update passwords
- **Rider Details**: `/online/riders/{id}/edit` - View rider's location history, device info, and map of current location
- **Payments**: `/online/payments`
- **Order Tracking**: `/online/tracking`
- **Delivery Map**: `/online/delivery-map` - Real-time rider location tracking
- **Customer Locations**: `/online/customer-locations`

### 2.9.1 Rider Management Features
The system provides comprehensive management of delivery riders:
- **Create Rider**: Create a new rider and corresponding user account with email/password
- **Edit Rider**: Update rider details, change password, view location history
- **Activate/Deactivate**: Toggle rider's active status with one click
- **Location History**: View last 10 rider locations with dates
- **Device Tracking**: See devices the rider has logged in from
- **Map View**: Interactive map showing rider's current location and movement route

### 2.10 Store Management
- **Profile & Settings**: `/store/profile`
- **Branches**: `/store/branches`
- **Locations**: `/store/locations`
- **Warehouses**: `/store/warehouses`
- **Settings**: `/store/settings`

### 2.11 HR & Security
- **Employees**: `/hr/employees`
- **Roles & Permissions**: `/hr/roles`
- **Attendance**: `/hr/attendance`
- **Work Shifts**: `/hr/shifts`
- **Activity Logs**: `/hr/activity`
- **Security Users**: `/security/users`
- **Access Control**: `/security/access`
- **Devices**: `/security/devices`
- **Audit Logs**: `/security/audit`
- **Security Settings**: `/security/settings`

---

## 3. Mobile Delivery Rider API

### 3.1 Base URL
`https://store.feedtancmg.org/api`

### 3.2 Authentication
The API uses Laravel Sanctum for authentication. All endpoints except public ones require a Bearer token.

#### Login Rider
**Endpoint**: `POST /api/auth/login`
**Payload**:
```json
{
    "email": "rider@example.com",
    "password": "password123"
}
```
**Response**:
```json
{
    "user": { ...user object... },
    "rider": { ...rider object... },
    "token": "1|your_api_token_here"
}
```

#### Logout
**Endpoint**: `POST /api/auth/logout`
**Headers**: `Authorization: Bearer {token}`
**Response**:
```json
{
    "message": "Logged out successfully"
}
```

### 3.3 Rider Profile
#### Get Rider Profile
**Endpoint**: `GET /api/rider/profile`
**Headers**: `Authorization: Bearer {token}`

#### Update Rider Profile
**Endpoint**: `PUT /api/rider/profile`
**Headers**: `Authorization: Bearer {token}`

### 3.4 Rider Location
#### Update Current Location
**Endpoint**: `POST /api/rider/location`
**Headers**: `Authorization: Bearer {token}`
**Payload**:
```json
{
    "latitude": -1.286389,
    "longitude": 36.817223
}
```

#### Get Rider Location
**Endpoint**: `GET /api/rider/location/{riderId}`

### 3.5 Order Management
#### List Assigned Orders
**Endpoint**: `GET /api/orders`
**Headers**: `Authorization: Bearer {token}`

#### List Available Orders (Unassigned)
**Endpoint**: `GET /api/orders/available`
**Headers**: `Authorization: Bearer {token}`

#### Show Order Details
**Endpoint**: `GET /api/orders/{id}`
**Headers**: `Authorization: Bearer {token}`

#### Update Order Status
**Endpoint**: `PUT /api/orders/{id}/status`
**Headers**: `Authorization: Bearer {token}`
**Payload**:
```json
{
    "status": "out_for_delivery",
    "notes": "On the way to customer"
}
```
**Possible Statuses**: `pending`, `confirmed`, `preparing`, `ready`, `out_for_delivery`, `delivered`, `cancelled`

#### Accept Order Assignment
**Endpoint**: `POST /api/orders/{id}/accept`
**Headers**: `Authorization: Bearer {token}`

### 3.6 Catalog (Public)
#### List Products
**Endpoint**: `GET /api/catalog/products`

#### Get Product Detail
**Endpoint**: `GET /api/catalog/products/{id}`

#### Get Carousel Slides
**Endpoint**: `GET /api/catalog/carousel`

### 3.7 Order Tracking (Public)
#### Track Order
**Endpoint**: `GET /api/tracking/{orderNumber}`

**Response**:
```json
{
    "order": { ...order object... },
    "rider": { ...rider object (if assigned)... },
    "current_location": { ...latest rider location (if available)... },
    "storeLat": <store latitude>,
    "storeLng": <store longitude>
}
```

### 3.8 Real-Time Data (Public)
#### Get All Riders with Latest Location
**Endpoint**: `GET /api/realtime/riders`

#### Get All Orders
**Endpoint**: `GET /api/realtime/orders`

---

## 4. System Architecture
### Key Models
1. **User**: System users (admin, cashier, rider, etc.)
2. **Product**: Inventory items with details
3. **Sale**: In-store sale transaction
4. **OnlineOrder**: E-commerce order
5. **PurchaseOrder**: Order from supplier
6. **DeliveryRider**: Delivery personnel with linked user account
7. **RiderLocation**: Real-time location history of riders
8. **UserDevice**: Tracks devices users have logged in from
9. **Customer**: Customer records with loyalty and credit
10. **Shareholder**: Investor in the business
11. **StoreSetting**: Configuration (store name, location, API keys, etc.)

---

## 5. Installation & Deployment
### Requirements
- PHP 8.2+
- Composer
- MySQL/MariaDB
- Node.js & npm (optional for frontend assets)

### Steps to Deploy
1. **Clone repository**: `git clone https://github.com/...`
2. **Install dependencies**: `composer install`
3. **Environment setup**: Copy `.env.example` to `.env` and configure
4. **Generate key**: `php artisan key:generate`
5. **Run migrations**: `php artisan migrate --seed` (seed initial data)
6. **Symlink storage**: `php artisan storage:link`
7. **Set permissions**: Ensure `storage` and `bootstrap/cache` are writable
8. **Configure web server**: Point to `public` directory

---

## 6. Configuration
### Store Settings
- Go to `/store/settings` to configure store name, address, contact info
- Set store latitude/longitude for delivery map
- Enter OpenRouteService API key for delivery routing
- Configure ClickPesa integration for online payments

---

## 7. Reporting & Analytics
The system includes comprehensive reports for all modules, accessible via the Reports section in each module. Key reports include:
- Sales reports (daily, weekly, monthly, by cashier, by product)
- Inventory reports (stock levels, movements, low stock, expiry)
- Financial reports (balance sheet, income statement, general ledger)
- Purchase reports (by supplier, by product, by date)
- Online order reports (by status, by rider, by date)

---

## 8. Support & Maintenance
For support, contact: [your support email]
- Regularly update dependencies via composer
- Back up database frequently
- Monitor logs in `storage/logs` for errors
- Check system activity logs in security/audit

---

## 9. Security Best Practices
- Use HTTPS in production
- Keep Laravel and dependencies up to date
- Use strong, unique passwords for all users
- Enable 2FA if available
- Restrict API access to trusted devices/IPs
- Regularly review audit logs
- Limit file uploads and sanitize all inputs
