# 📡 API Documentation - Laundry Digital

Base URL: `http://127.0.0.1:8000/api`

## Authentication

All protected endpoints require Bearer token in header:
```
Authorization: Bearer {your_token}
```

---

## 🔐 Auth Endpoints

### Register
```http
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "081234567890",
  "address": "Jl. Example No. 123",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": { ... },
    "token": "1|xxxxx..."
  }
}
```

### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "admin@laundry.com",
  "password": "password"
}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

### Get Profile
```http
GET /auth/profile
Authorization: Bearer {token}
```

### Update Profile
```http
PUT /auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Name",
  "phone": "081234567890",
  "address": "New Address",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

## 🧺 Services Endpoints

### Get All Services
```http
GET /services
```

### Get Service by ID
```http
GET /services/{id}
```

### Create Service (Admin only)
```http
POST /services
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Cuci Setrika Premium",
  "price_per_kg": 10000,
  "estimated_hours": 24,
  "description": "Layanan cuci setrika premium"
}
```

### Update Service (Admin only)
```http
PUT /services/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Updated Name",
  "price_per_kg": 12000,
  "is_active": true
}
```

### Delete Service (Admin only)
```http
DELETE /services/{id}
Authorization: Bearer {admin_token}
```

---

## 💳 Payment Methods Endpoints

### Get All Payment Methods
```http
GET /payment-methods
```

### Create Payment Method (Admin only)
```http
POST /payment-methods
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Credit Card",
  "description": "Pembayaran dengan kartu kredit"
}
```

---

## 📊 Status Endpoints

### Get All Statuses
```http
GET /statuses
```

---

## 📝 Transaction Endpoints

### Get All Transactions
```http
GET /transactions
Authorization: Bearer {token}

Query Parameters:
- status_id: Filter by status
- payment_status: pending | paid
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD
- search: Search by code or customer name
```

### Create Transaction
```http
POST /transactions
Authorization: Bearer {token}
Content-Type: application/json

{
  "customer_id": 3,
  "service_id": 2,
  "weight_kg": 3.5,
  "notes": "Pisahkan pakaian putih"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Transaksi berhasil dibuat",
  "data": {
    "id": 1,
    "transaction_code": "LDR250115ABCD",
    "customer_id": 3,
    "service_id": 2,
    "weight_kg": 3.5,
    "price_per_kg": 8000,
    "total_price": 28000,
    "status_id": 1,
    "payment_status": "pending",
    ...
  }
}
```

### Get Transaction Detail
```http
GET /transactions/{id}
Authorization: Bearer {token}
```

### Update Transaction (Admin/Kasir only)
```http
PUT /transactions/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "service_id": 3,
  "weight_kg": 4.0,
  "notes": "Updated notes"
}
```

### Update Status (Admin/Kasir only)
```http
PATCH /transactions/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status_id": 2
}
```

### Process Payment (Admin/Kasir only)
```http
PATCH /transactions/{id}/payment
Authorization: Bearer {token}
Content-Type: application/json

{
  "payment_method_id": 1
}
```

### Track Transaction (Public)
```http
GET /transactions/track/{transaction_code}

Example: GET /transactions/track/LDR250115ABCD
```

---

## 📈 Reports Endpoints (Admin only)

### Dashboard Summary
```http
GET /reports/summary
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_customers": 50,
    "total_transactions": 200,
    "pending_transactions": 15,
    "today_transactions": 10,
    "today_revenue": 500000,
    "month_revenue": 15000000,
    "total_revenue": 50000000
  }
}
```

### Transaction Report
```http
GET /reports/transactions?start_date=2025-01-01&end_date=2025-01-31
Authorization: Bearer {admin_token}
```

### Revenue Report
```http
GET /reports/revenue?start_date=2025-01-01&end_date=2025-01-31&group_by=day
Authorization: Bearer {admin_token}

Query Parameters:
- start_date: YYYY-MM-DD (required)
- end_date: YYYY-MM-DD (required)
- group_by: day | month (optional, default: day)
```

---

## 👥 Users Endpoints (Admin only)

### Get All Users
```http
GET /users
Authorization: Bearer {admin_token}

Query Parameters:
- role: admin | kasir | pelanggan
- search: Search by name, email, or phone
```

### Create User
```http
POST /users
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "New User",
  "email": "newuser@example.com",
  "phone": "081234567890",
  "address": "Address",
  "password": "password123",
  "role": "kasir"
}
```

### Update User
```http
PUT /users/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Updated Name",
  "role": "admin",
  "is_active": false
}
```

### Delete User
```http
DELETE /users/{id}
Authorization: Bearer {admin_token}
```

---

## 🔒 Role-Based Access Control

| Endpoint | Admin | Kasir | Pelanggan |
|----------|:-----:|:-----:|:---------:|
| Auth (all) | ✅ | ✅ | ✅ |
| Services (read) | ✅ | ✅ | ✅ |
| Services (write) | ✅ | ❌ | ❌ |
| Transactions (read own) | ✅ | ✅ | ✅ |
| Transactions (read all) | ✅ | ✅ | ❌ |
| Transactions (create) | ✅ | ✅ | ✅ |
| Transactions (update) | ✅ | ✅ | ❌ |
| Reports | ✅ | ❌ | ❌ |
| Users | ✅ | ❌ | ❌ |

---

## ⚠️ Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Forbidden - Anda tidak memiliki akses"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## 📝 Notes

- All timestamps are in ISO 8601 format
- Currency values are in IDR (Indonesian Rupiah)
- Weight is in kilograms (kg)
- Transaction codes format: `LDR{YYMMDD}{XXXX}`
