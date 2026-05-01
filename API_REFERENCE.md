# Karacabey Gross Market - API Reference

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication

All protected endpoints require Bearer token in Authorization header:
```
Authorization: Bearer {your_token}
```

## Public Endpoints

### Authentication

#### Register
```
POST /auth/register
Content-Type: application/json

{
  "name": "User Name",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response: {
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": { id, name, email }
}
```

#### Login
```
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}

Response: {
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": { id, name, email }
}
```

#### Get Current User
```
GET /auth/me
Authorization: Bearer {token}

Response: { id, name, email }
```

### Products

#### List Products
```
GET /products?page=1&limit=20&sort=newest&filter=...
Authorization: Bearer {token} (optional)

Response: {
  "data": [
    {
      "id": 1,
      "slug": "product-name",
      "name": "Product Name",
      "description": "...",
      "price": 99.99,
      "currency": "TL",
      "discount_percentage": 10,
      "in_stock": true,
      "images": ["url1", "url2"],
      "rating": 4.5,
      "reviews_count": 42
    }
  ],
  "meta": { "total": 100, "per_page": 20, "current_page": 1 }
}
```

#### Get Product Details
```
GET /products/{slug}
Authorization: Bearer {token} (optional)

Response: {
  "id": 1,
  "slug": "product-name",
  "name": "Product Name",
  "description": "...",
  "price": 99.99,
  "discount_percentage": 10,
  "in_stock": true,
  "images": ["url1", "url2"],
  "rating": 4.5,
  "reviews": [
    {
      "id": 1,
      "user_name": "User",
      "rating": 5,
      "title": "Great product",
      "body": "...",
      "created_at": "2026-05-01T10:30:00Z"
    }
  ]
}
```

### Categories

#### List Categories
```
GET /categories
Response: {
  "data": [
    {
      "id": 1,
      "slug": "electronics",
      "name": "Electronics",
      "description": "...",
      "image_url": "...",
      "product_count": 42
    }
  ]
}
```

#### Get Category Details
```
GET /categories/{slug}
Response: {
  "id": 1,
  "slug": "electronics",
  "name": "Electronics",
  "description": "...",
  "image_url": "...",
  "products": [...]
}
```

## Protected Endpoints

### Notifications

#### Get Notifications
```
GET /notifications?limit=25
Authorization: Bearer {token}

Response: {
  "data": [
    {
      "id": 1,
      "type": "order_update",
      "title": "Order Shipped",
      "body": "Your order has been shipped",
      "data": { "order_id": 123 },
      "read_at": null,
      "sent_at": "2026-05-01T10:30:00Z",
      "created_at": "2026-05-01T10:30:00Z"
    }
  ],
  "meta": { "unread_count": 3 }
}
```

#### Register Device Token
```
POST /notifications/device-tokens
Authorization: Bearer {token}
Content-Type: application/json

{
  "token": "device_token_string",
  "device_type": "ios",
  "device_name": "iPhone 15 Pro"
}

Response: {
  "data": {
    "id": 1,
    "status": "registered"
  }
}
```

#### Mark Notification as Read
```
POST /notifications/{notificationId}/read
Authorization: Bearer {token}

Response: { notification object }
```

#### Mark All as Read
```
POST /notifications/read-all
Authorization: Bearer {token}

Response: { "data": { "status": "ok" } }
```

### Cart

#### Get Cart
```
GET /cart
Authorization: Bearer {token}

Response: {
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "product_name": "Product Name",
      "quantity": 2,
      "price": 99.99,
      "total": 199.98
    }
  ],
  "subtotal": 199.98,
  "tax": 31.99,
  "total": 231.97,
  "discount": 0,
  "coupon": null
}
```

#### Add to Cart
```
POST /cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2,
  "options": { "size": "M", "color": "red" }
}

Response: { cart object }
```

#### Update Cart Item
```
PATCH /cart/items/{cartItemId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "quantity": 3
}

Response: { cart object }
```

#### Remove from Cart
```
DELETE /cart/items/{cartItemId}
Authorization: Bearer {token}

Response: { cart object }
```

#### Clear Cart
```
DELETE /cart
Authorization: Bearer {token}

Response: { "status": "ok" }
```

### Favorites

#### Get Favorites
```
GET /favorites
Authorization: Bearer {token}

Response: {
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "product": { product object },
      "added_at": "2026-05-01T10:30:00Z"
    }
  ]
}
```

#### Add to Favorites
```
POST /favorites/{productSlug}
Authorization: Bearer {token}

Response: { "status": "added" }
```

#### Remove from Favorites
```
DELETE /favorites/{productSlug}
Authorization: Bearer {token}

Response: { "status": "removed" }
```

### Orders

#### Get Orders
```
GET /orders?page=1&limit=10&status=all
Authorization: Bearer {token}

Response: {
  "data": [
    {
      "id": 1,
      "number": "ORD-123456",
      "total": 231.97,
      "status": "delivered",
      "created_at": "2026-05-01T10:30:00Z",
      "items_count": 2
    }
  ],
  "meta": { "total": 5, "per_page": 10 }
}
```

#### Get Order Details
```
GET /orders/{orderId}
Authorization: Bearer {token}

Response: {
  "id": 1,
  "number": "ORD-123456",
  "status": "delivered",
  "total": 231.97,
  "items": [
    {
      "product_name": "Product",
      "quantity": 2,
      "price": 99.99,
      "total": 199.98
    }
  ],
  "shipping": {
    "address": "123 Main St",
    "city": "Istanbul",
    "district": "Beyoglu",
    "tracking_number": "TR123456789"
  },
  "payment": {
    "method": "paytr",
    "status": "completed"
  },
  "created_at": "2026-05-01T10:30:00Z"
}
```

### Addresses

#### List Addresses
```
GET /addresses
Authorization: Bearer {token}

Response: {
  "data": [
    {
      "id": 1,
      "title": "Home",
      "address": "123 Main St",
      "city": "Istanbul",
      "district": "Beyoglu",
      "postal_code": "34000",
      "is_default": true
    }
  ]
}
```

#### Create Address
```
POST /addresses
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Home",
  "address": "123 Main St",
  "city": "Istanbul",
  "district": "Beyoglu",
  "postal_code": "34000",
  "is_default": true
}

Response: { address object }
```

#### Update Address
```
PUT /addresses/{addressId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Home",
  "address": "456 Oak Ave",
  "city": "Ankara",
  "district": "Kecioren"
}

Response: { address object }
```

#### Delete Address
```
DELETE /addresses/{addressId}
Authorization: Bearer {token}

Response: { "status": "deleted" }
```

### Checkout

#### Create Order from Cart
```
POST /c
Content-Type: application/json

{
  "customer": {
    "name": "Customer Name",
    "email": "customer@example.com",
    "phone": "05551234567"
  },
  "shipping": {
    "city": "Istanbul",
    "district": "Beyoglu",
    "address": "123 Main St"
  },
  "coupon_code": "SUMMER20"
}

Response: {
  "payment_url": "https://secure.paytr.com/payment/...",
  "order_id": "ORD-123456"
}
```

### Payments

#### Get Payment Status
```
GET /payments/{paymentId}/status
Authorization: Bearer {token}

Response: {
  "id": 1,
  "status": "completed",
  "amount": 231.97,
  "transaction_id": "TR123456789",
  "created_at": "2026-05-01T10:30:00Z"
}
```

## Error Responses

All errors return appropriate HTTP status codes with error details:

```json
{
  "message": "Unauthorized",
  "errors": {
    "field": ["Error message"]
  }
}
```

### Status Codes
- 200: Success
- 201: Created
- 204: No Content
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error

## Rate Limiting

All endpoints are rate limited:
- Public endpoints: 60 requests per minute
- Authenticated endpoints: 120 requests per minute
- Payment endpoints: 30 requests per minute

Response headers:
```
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 119
X-RateLimit-Reset: 1651390203
```

## Testing

### Test with cURL

```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}' \
  | jq -r '.token')

# Get notifications
curl -X GET http://localhost:8000/api/v1/notifications \
  -H "Authorization: Bearer $TOKEN"

# Send test notification
curl -X POST http://localhost:8000/api/v1/test/notification \
  -H "Authorization: Bearer $TOKEN"
```

### Test with Postman

1. Import API collection
2. Set `{{base_url}}` to `http://localhost:8000/api/v1`
3. Set `{{token}}` after login
4. Use requests from collection

## Webhook Events

The system sends webhooks for important events:

```json
{
  "event": "order.created",
  "data": {
    "order_id": 123,
    "total": 231.97,
    "created_at": "2026-05-01T10:30:00Z"
  }
}
```

### Events
- `order.created`
- `order.shipped`
- `order.delivered`
- `order.cancelled`
- `payment.completed`
- `payment.failed`
- `notification.sent`

Configure webhook URL in settings.
