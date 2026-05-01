# Push Notification System - Complete Guide

This guide covers the entire push notification system for Karacabey Gross Market, including backend setup, iOS integration, and testing.

## System Architecture

```
iOS App (APNs) ←→ Apple Push Service ←→ Backend Server
iOS App (FCM)  ←→ Firebase Cloud    ←→ Backend Server
           ↓
   Device Token Registry
           ↓
   Notification Database
```

## Backend Setup

### 1. Database

The system uses two tables:

**notifications** - stores all notifications sent to users
```
- id: UUID primary key
- tenant_id: foreign key to tenants
- user_id: foreign key to users (nullable)
- type: string (order_update, cargo_update, promotion, general)
- title: notification title
- body: notification body text
- data: JSON object with additional data
- read_at: timestamp (null until read)
- sent_at: timestamp when sent
- created_at, updated_at: timestamps
```

**device_tokens** - stores registered device tokens
```
- id: primary key
- user_id: foreign key to users
- token: unique device token string
- device_type: 'ios' or 'android'
- device_name: optional device name
- is_active: boolean (true = receiving notifications)
- created_at, updated_at: timestamps
```

### 2. API Endpoints

#### Register Device Token
```
POST /api/v1/notifications/device-tokens
Authorization: Bearer {token}
Content-Type: application/json

{
  "token": "device_token_string",
  "device_type": "ios",
  "device_name": "iPhone 15 Pro"
}

Response:
{
  "data": {
    "id": 1,
    "status": "registered"
  }
}
```

#### Get Notifications
```
GET /api/v1/notifications?limit=25
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "type": "order_update",
      "title": "Order Shipped",
      "body": "Your order #12345 has been shipped",
      "data": {"order_id": 123, "status": "shipped"},
      "read_at": null,
      "sent_at": "2026-05-01T10:30:00Z",
      "created_at": "2026-05-01T10:30:00Z"
    }
  ],
  "meta": {
    "unread_count": 3
  }
}
```

#### Mark Notification as Read
```
POST /api/v1/notifications/{notificationId}/read
Authorization: Bearer {token}

Response:
{
  "data": { notification object with read_at set }
}
```

#### Mark All as Read
```
POST /api/v1/notifications/read-all
Authorization: Bearer {token}

Response:
{
  "data": { "status": "ok" }
}
```

## Configuration

### Environment Variables

```env
# APNS Configuration
APNS_KEY_PATH=/app/storage/apns/AuthKey.p8    # Path to AuthKey.p8 from Apple
APNS_KEY_ID=ABC123DEF                         # Key ID from Apple Developer
APNS_TEAM_ID=ABCDEF1234                       # Team ID from Apple
APNS_BUNDLE_ID=com.karacabey.grossmarket
APNS_PRODUCTION=false                         # true for production

# FCM Configuration
FCM_SERVER_KEY=your_fcm_server_key            # From Firebase Console
FCM_SENDER_ID=your_fcm_sender_id
```

### Setting Up APNS (iOS)

1. **Get AuthKey from Apple Developer**
   - Go to https://developer.apple.com/
   - Account → Certificates, IDs & Profiles
   - Keys → Create a new key
   - Select "Apple Push Notifications service (APNs)"
   - Download the AuthKey file (AuthKey_XXXXX.p8)
   - Note the Key ID and Team ID

2. **Add AuthKey to Server**
   ```bash
   # Copy AuthKey.p8 to storage directory
   cp AuthKey_XXXXX.p8 storage/apns/AuthKey.p8
   chmod 600 storage/apns/AuthKey.p8
   ```

3. **Update .env**
   ```env
   APNS_KEY_PATH=/app/storage/apns/AuthKey.p8
   APNS_KEY_ID=your_key_id
   APNS_TEAM_ID=your_team_id
   APNS_PRODUCTION=false
   ```

### Setting Up FCM (Android)

1. **Get Server Key from Firebase**
   - Go to https://console.firebase.google.com
   - Select your project
   - Project Settings → Service Accounts
   - Generate a new private key (JSON)
   - Extract `server_key` and `sender_id`

2. **Update .env**
   ```env
   FCM_SERVER_KEY=your_server_key
   FCM_SENDER_ID=your_sender_id
   ```

## iOS Implementation

### 1. AppDelegate Setup

The AppDelegate handles remote notification registration:

```swift
class AppDelegate: NSObject, UIApplicationDelegate {
    func application(
        _ application: UIApplication,
        didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data
    ) {
        let token = deviceToken.map { String(format: "%02.2hhx", $0) }.joined()
        Task {
            await PushNotificationManager.shared.registerDeviceTokenWithBackend(token)
        }
    }
}
```

### 2. PushNotificationManager

Handles notification permissions and registration:

```swift
// Request user permission
let granted = await PushNotificationManager.shared.requestAuthorization()

// Register for remote notifications (if granted)
if granted {
    await PushNotificationManager.shared.registerForRemoteNotifications()
}
```

### 3. Handling Notifications

When a notification arrives, it's passed to the notification handler:

```swift
func userNotificationCenter(
    _ center: UNUserNotificationCenter,
    didReceive response: UNNotificationResponse,
    withCompletionHandler completionHandler: @escaping () -> Void
) {
    let userInfo = response.notification.request.content.userInfo
    
    // Handle different notification types
    if let type = userInfo["type"] as? String {
        switch type {
        case "order_update":
            // Navigate to order details
            break
        case "cargo_update":
            // Navigate to cargo tracking
            break
        default:
            break
        }
    }
}
```

## Testing

### Test API Endpoints

#### 1. Create Test User
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### 2. Login and Get Token
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'

# Response includes "token": "your_token_here"
export TOKEN="your_token_here"
```

#### 3. Register Device Token
```bash
curl -X POST http://localhost:8000/api/v1/notifications/device-tokens \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "token": "test_device_token_12345",
    "device_type": "ios",
    "device_name": "iPhone 15 Pro"
  }'
```

#### 4. Send Test Notification (API Endpoint)
```bash
curl -X POST http://localhost:8000/api/v1/test/notification \
  -H "Authorization: Bearer $TOKEN"
```

#### 5. Get Notifications
```bash
curl -X GET http://localhost:8000/api/v1/notifications \
  -H "Authorization: Bearer $TOKEN"
```

### Test from CLI (Tinker)

```bash
docker-compose exec app php artisan tinker

> $user = User::first();
> $service = new App\Services\PushNotificationService();
> $service->sendToUser($user, 'Test Title', 'Test Body', ['type' => 'test']);

# Check if notification was created
> App\Models\Notification::latest()->first();
```

### Debugging

#### Check Notifications Table
```bash
docker-compose exec app php artisan tinker

> App\Models\Notification::all();
> App\Models\DeviceToken::all();
```

#### Check Server Logs
```bash
# View Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log

# View Docker logs
docker-compose logs -f app
```

#### Check APNS Setup
```bash
# Verify AuthKey file exists
docker-compose exec app ls -la storage/apns/

# Check if file is readable
docker-compose exec app cat storage/apns/AuthKey.p8
```

## iOS Device Testing

### 1. Physical Device Required
- Push notifications work only on physical devices, not simulators
- You need a provisioning profile with Push Notifications capability

### 2. Enable Push Notifications in Xcode
- Select Project → Target
- Capabilities → Add "Push Notifications"
- Signing & Capabilities → Enable "Push Notifications"

### 3. Configure URL Scheme
- Info.plist → Add URL Types
- Add scheme: `karacabey`
- This enables deep linking from notifications

### 4. Test Registration
1. Build and run on device
2. Approve notification permission popup
3. Check device logs to see token registration
4. Verify token is registered in database:
   ```bash
   curl -X GET http://localhost:8000/api/v1/test/notifications \
     -H "Authorization: Bearer $TOKEN"
   ```

### 5. Send Test Notification
```bash
# From backend
$user = User::find(1);
$service = new App\Services\PushNotificationService();
$service->sendToUser($user, 'Test', 'Hello World', ['type' => 'test']);

# You should see notification on device immediately
```

## Production Checklist

- [ ] APNS_PRODUCTION=true in .env
- [ ] Valid AuthKey.p8 in storage/apns/
- [ ] FCM_SERVER_KEY configured for Android
- [ ] Queue worker running: `php artisan queue:work`
- [ ] SSL certificate configured
- [ ] Rate limiting configured
- [ ] Notification database backed up
- [ ] Monitoring/alerting set up
- [ ] Test end-to-end with real device

## Troubleshooting

### Notifications Not Arriving

1. **Check device is registered**
   ```bash
   SELECT * FROM device_tokens WHERE user_id = 1;
   ```

2. **Check notification was created**
   ```bash
   SELECT * FROM notifications WHERE user_id = 1 ORDER BY created_at DESC;
   ```

3. **Check queue worker is running**
   ```bash
   # If using queue
   ps aux | grep "queue:work"
   ```

4. **Check APNS credentials**
   - Verify APNS_KEY_PATH exists
   - Verify APNS_KEY_ID and APNS_TEAM_ID are correct
   - Verify bundle ID matches in .env

5. **Enable debug logging**
   ```env
   LOG_LEVEL=debug
   ```

### Device Token Not Registering

1. Check authorization header is correct
2. Verify user is authenticated
3. Check network connectivity
4. Look at network logs in Xcode

### APNS Errors

```
Error: "InvalidToken"  → Device token is invalid/expired
Error: "TopicDisallowed" → Bundle ID doesn't match
Error: "BadCertificate" → AuthKey.p8 is invalid
```

## Resources

- [Apple Push Notification Documentation](https://developer.apple.com/documentation/usernotifications/)
- [Firebase Cloud Messaging Docs](https://firebase.google.com/docs/cloud-messaging)
- [Pushok PHP Library](https://github.com/edamov/pushok)
- [Laravel Notifications](https://laravel.com/docs/notifications)
