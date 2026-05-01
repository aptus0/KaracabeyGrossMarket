# Karacabey Gross Market - Setup Instructions

## 🚀 Quick Start Guide

### Prerequisites
- PHP 8.2+
- Laravel 11
- MySQL 8.0+
- Node.js 18+
- Xcode 15+ (for iOS development)

### Backend Setup

#### 1. Install Dependencies
```bash
composer install
npm install
```

#### 2. Generate Application Key
```bash
php artisan key:generate
```

#### 3. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed
```

#### 4. Configure PayTR Payment
✅ **Already configured in .env:**
```
PAYTR_MERCHANT_ID=686886
PAYTR_MERCHANT_KEY=QNLKTgfi5ykz6ApQ
PAYTR_MERCHANT_SALT=Fuq6fR7qSABW5FxX
PAYTR_TEST_MODE=true
```

#### 5. Configure Push Notifications

##### Apple Push Notification Service (APNs)
```bash
# 1. Download AuthKey from Apple Developer
# 2. Place at: storage/apns/AuthKey.p8
# 3. Update .env:
APNS_KEY_ID=your_key_id_from_apple
APNS_TEAM_ID=your_team_id_from_apple
APNS_PRODUCTION=false  # Use true for production
```

##### Firebase Cloud Messaging (FCM)
```bash
# 1. Create Firebase project at: https://console.firebase.google.com
# 2. Get Server Key from: Project Settings → Service Accounts
# 3. Update .env:
FCM_SERVER_KEY=your_fcm_server_key
FCM_SENDER_ID=your_fcm_sender_id
```

#### 6. Queue Setup
```bash
# Configure for database queue (already set in .env)
# In production, use Redis queue:
# QUEUE_CONNECTION=redis

# Run queue worker (development)
php artisan queue:work
```

#### 7. Start Development Server
```bash
php artisan serve
# Server runs at: http://localhost:8000
```

### iOS Setup

#### 1. Open Xcode Project
```bash
open "Karacabey Gross Market/Karacabey Gross Market.xcodeproj"
```

#### 2. Configure Signing & Capabilities
- Select project in Xcode
- Go to Signing & Capabilities
- Add "Push Notifications" capability
- Add "Background Modes" → "Remote notifications"

#### 3. Configure App URL Schemes
- Target Settings → URL Types
- Add URL Scheme: `karacabey`
- This enables deep linking from widgets

#### 4. Test Push Notifications
```swift
// In AppDelegate or scene delegate
import UserNotifications

let center = UNUserNotificationCenter.current()
center.requestAuthorization(options: [.alert, .sound, .badge]) { granted, error in
    if granted {
        DispatchQueue.main.async {
            UIApplication.shared.registerForRemoteNotifications()
        }
    }
}
```

#### 5. Build & Run
```bash
# Run on simulator
Cmd + R

# Run on device
Select device → Cmd + R
```

### API Testing

#### Test Payment Endpoint
```bash
curl -X POST http://localhost:8000/api/v1/checkout \
  -H "Content-Type: application/json" \
  -d '{
    "customer": {
      "name": "Test User",
      "email": "test@example.com",
      "phone": "05551234567"
    },
    "shipping": {
      "city": "Istanbul",
      "district": "Beyoglu",
      "address": "Test Address"
    }
  }'
```

#### Test Admin API
```bash
# Get campaigns
curl -X GET http://localhost:8000/api/v1/admin/campaigns \
  -H "Authorization: Bearer {YOUR_TOKEN}"

# Export campaigns as JSON
curl -X GET http://localhost:8000/api/v1/admin/campaigns/export \
  -H "Authorization: Bearer {YOUR_TOKEN}" \
  > campaigns.json
```

#### Test Push Notification
```bash
# Register device token
curl -X POST http://localhost:8000/api/v1/notifications/device-tokens \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {YOUR_TOKEN}" \
  -d '{
    "token": "YOUR_DEVICE_TOKEN",
    "device_type": "ios",
    "device_name": "iPhone 15 Pro"
  }'

# Send test notification
php artisan tinker
> $user = User::first();
> PushNotificationService::sendToUser($user, 'Test Title', 'Test Body', ['type' => 'test']);
```

### Database Schema Overview

#### notifications table
```sql
- id (primary key)
- tenant_id (foreign)
- user_id (foreign)
- type (string: order_update, cargo_update, promotion)
- title (string)
- body (text)
- data (json)
- read_at (timestamp)
- sent_at (timestamp)
```

#### device_tokens table
```sql
- id (primary key)
- user_id (foreign)
- token (unique string)
- device_type (ios/android)
- device_name (string)
- is_active (boolean)
```

### Admin Panel Features

#### Campaign Management
- **Create:** POST `/api/v1/admin/campaigns`
- **Read:** GET `/api/v1/admin/campaigns`
- **Update:** PUT `/api/v1/admin/campaigns/{id}`
- **Delete:** DELETE `/api/v1/admin/campaigns/{id}`
- **Export:** GET `/api/v1/admin/campaigns/export`
- **Reorder:** POST `/api/v1/admin/campaigns/reorder`

#### Story Management
- Same endpoints as campaigns: `/api/v1/admin/stories`

#### Homepage Blocks
- Same endpoints as campaigns: `/api/v1/admin/homepage`

### iOS Widget Setup

#### Order Status Widget
- Shows latest order
- Refreshes every 30 minutes
- Deep link to orders

#### Cargo Tracking Widget
- Shows active shipments
- Updates every 15 minutes
- Real-time status icons

#### Quick Access Widget
- Cart access
- Favorites access
- Search link
- Profile link

To add widgets to your iPhone:
1. Long press on home screen
2. Tap "+" button
3. Search "Karacabey"
4. Select widget and add

### Environment Variables Summary

```env
# PayTR (Already Configured ✅)
PAYTR_MERCHANT_ID=686886
PAYTR_MERCHANT_KEY=QNLKTgfi5ykz6ApQ
PAYTR_MERCHANT_SALT=Fuq6fR7qSABW5FxX

# Apple Push (⏳ Configure)
APNS_KEY_ID=
APNS_TEAM_ID=
APNS_BUNDLE_ID=com.karacabey.grossmarket

# Firebase (⏳ Configure)
FCM_SERVER_KEY=
FCM_SENDER_ID=

# URLs
API_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3001
```

### Troubleshooting

#### Payment not working?
- Check PAYTR credentials in .env
- Verify PAYTR_TEST_MODE=true for testing
- Check server logs: `tail -f storage/logs/laravel.log`

#### Notifications not sending?
- Verify FCM/APNS credentials
- Check queue worker is running: `php artisan queue:work`
- Register device token first
- Check notification data in DB: `SELECT * FROM notifications;`

#### iOS build errors?
- Clean build folder: Cmd + Shift + K
- Delete derived data: Cmd + Shift + Delete
- Verify signing: Xcode → Project → Signing & Capabilities
- Run `pod install` if using CocoaPods

#### Widget not showing?
- Build and run app on device first
- Add WidgetKit capability
- Configure app URL schemes
- Test with: `caracabey://orders`

### Production Deployment

#### Before Going Live:
```bash
# 1. Set production mode
APP_ENV=production
APP_DEBUG=false

# 2. Use production credentials
PAYTR_TEST_MODE=false
APNS_PRODUCTION=true

# 3. Use Redis for queue
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# 4. Run migrations
php artisan migrate --force

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Start queue worker
supervisord  # or use systemd

# 7. Setup SSL certificate
# Use Let's Encrypt or similar
```

### Support & Resources

- **PayTR Documentation:** https://www.paytr.com/api/documentation
- **Apple Developer:** https://developer.apple.com/documentation/usernotifications/
- **Firebase Docs:** https://firebase.google.com/docs/cloud-messaging
- **Laravel Documentation:** https://laravel.com/docs

---

**Happy coding! 🚀**
