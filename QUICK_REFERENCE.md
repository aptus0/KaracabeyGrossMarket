# Karacabey Gross Market - Quick Reference

## 🚀 Start Development

```bash
# 1. Start all services
docker-compose up -d

# 2. Seed test data
docker-compose exec app php artisan db:seed --class=TestDataSeeder

# 3. Get API token
TOKEN=$(docker-compose exec -T app php artisan test:token --user-id=3 2>&1 | tail -1)

# 4. Start development server (if needed)
docker-compose exec app php artisan serve
```

## 📱 Test Notifications

```bash
# Send test notification
curl -X POST http://localhost:8000/api/v1/test/notification \
  -H "Authorization: Bearer $TOKEN"

# Get all notifications
curl -X GET http://localhost:8000/api/v1/notifications \
  -H "Authorization: Bearer $TOKEN"

# Register device for push
curl -X POST http://localhost:8000/api/v1/notifications/device-tokens \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"token":"DEVICE_TOKEN","device_type":"ios","device_name":"iPhone"}'

# Mark notification as read
curl -X POST http://localhost:8000/api/v1/notifications/{id}/read \
  -H "Authorization: Bearer $TOKEN"
```

## 📝 Files Created/Modified

### New Files
```
✅ AppDelegate.swift                    - Handle remote notifications
✅ DeviceTokenRequest.swift             - Device token model
✅ TestController.php                   - Test API endpoints
✅ TestDataSeeder.php                   - Create test data
✅ TestNotification.php                 - Artisan command
✅ GetTestToken.php                     - Generate test token
✅ PUSH_NOTIFICATION_GUIDE.md           - Complete guide
✅ API_REFERENCE.md                     - API documentation
✅ IMPLEMENTATION_SUMMARY.md            - This session summary
✅ QUICK_REFERENCE.md                   - This file
```

### Modified Files
```
✅ PushNotificationManager.swift        - Add backend registration
✅ NotificationController.php           - Use custom models
✅ Karacabey_Gross_MarketApp.swift     - Add AppDelegate
✅ PushNotificationService.php          - Implement APNS with Pushok
✅ Notification.php                     - Add boot method for tenant_id
✅ routes/api.php                       - Add test routes
```

### Removed Files
```
✅ 2026_05_01_100001_create_notifications_table.php - Conflicting migration
```

## 🔧 Configuration

### PayTR (Already Configured ✅)
```env
PAYTR_MERCHANT_ID=686886
PAYTR_MERCHANT_KEY=QNLKTgfi5ykz6ApQ
PAYTR_MERCHANT_SALT=Fuq6fR7qSABW5FxX
PAYTR_TEST_MODE=true
```

### APNS (Need Your Credentials)
1. Go to https://developer.apple.com
2. Get AuthKey_XXXXX.p8 file
3. Copy to `storage/apns/AuthKey.p8`
4. Update .env:
```env
APNS_KEY_ID=your_key_id
APNS_TEAM_ID=your_team_id
APNS_KEY_PATH=/app/storage/apns/AuthKey.p8
APNS_PRODUCTION=false
```

### FCM (Need Your Credentials)
1. Go to https://console.firebase.google.com
2. Get server key
3. Update .env:
```env
FCM_SERVER_KEY=your_server_key
FCM_SENDER_ID=your_sender_id
```

## 📱 iOS App Setup

### 1. Add Capabilities in Xcode
- Target → Signing & Capabilities
- Add "Push Notifications"
- Add "Background Modes" → Remote notifications

### 2. Configure URL Scheme
- Info.plist → Add URL Scheme `karacabey`

### 3. Test on Device
1. Build and run on physical iPhone
2. Approve notification permission
3. Device token automatically registers
4. Send test notification from backend

```bash
# Your token from earlier
TOKEN="your_token_here"

# Send test notification
curl -X POST http://localhost:8000/api/v1/test/notification \
  -H "Authorization: Bearer $TOKEN"

# Notification appears on iPhone within seconds
```

## 🧪 Testing

### Console Commands
```bash
# Send notification to user
docker-compose exec app php artisan test:notification --user-id=3

# Get API token
docker-compose exec app php artisan test:token --user-id=3

# Run migrations
docker-compose exec app php artisan migrate

# View logs
docker-compose logs -f app
```

### Database Queries
```bash
# Enter MySQL
docker-compose exec mysql mysql -ukaracabey -ppassword karacabey_gross_market

# Check notifications
SELECT * FROM notifications;
SELECT * FROM device_tokens;
SELECT * FROM api_tokens;
```

## 🔍 Troubleshooting

### Notifications Not Arriving on iOS
1. Verify device token is registered:
   ```bash
   docker-compose exec mysql mysql -ukaracabey -ppassword karacabey_gross_market
   SELECT * FROM device_tokens;
   ```

2. Check APNS credentials:
   ```bash
   docker-compose exec app ls -la storage/apns/
   ```

3. View logs:
   ```bash
   docker-compose logs -f app | grep -i notification
   ```

### API Token Issues
```bash
# Generate new token
docker-compose exec app php artisan test:token --user-id=3

# Token is valid for 30 days
# Check expiration in database:
docker-compose exec mysql mysql -ukaracabey -ppassword karacabey_gross_market
SELECT * FROM api_tokens WHERE user_id = 3;
```

### Database Issues
```bash
# Reset migrations
docker-compose exec app php artisan migrate:reset

# Run migrations fresh
docker-compose exec app php artisan migrate

# Seed test data
docker-compose exec app php artisan db:seed --class=TestDataSeeder
```

## 📊 System Status

```bash
# Check all containers are running
docker-compose ps

# Check Laravel app is ready
curl http://localhost:8000/health

# Check API is responding
curl http://localhost:8000/api/v1/auth/providers

# Full system test (requires token)
TOKEN="your_token"
curl http://localhost:8000/api/v1/notifications -H "Authorization: Bearer $TOKEN"
```

## 🎯 API Endpoints

All endpoints require authentication except:
- `/auth/register` - Create account
- `/auth/login` - Get token
- `/auth/providers` - List OAuth providers
- `/products` - List products
- `/categories` - List categories
- `/content/*` - Public content

Authenticated endpoints (require Bearer token):
- `/notifications` - Notification management
- `/notifications/device-tokens` - Device registration
- `/test/*` - Testing endpoints
- `/cart` - Shopping cart
- `/orders` - Order history
- `/favorites` - Wishlist

## 📚 Documentation

1. **SETUP_INSTRUCTIONS.md** - Full setup with screenshots
2. **PUSH_NOTIFICATION_GUIDE.md** - Detailed notification docs
3. **API_REFERENCE.md** - All API endpoints
4. **IMPLEMENTATION_SUMMARY.md** - What was built
5. **QUICK_REFERENCE.md** - This file

## 🚢 Production Deployment

When ready to go live:

```bash
# 1. Update environment
APP_ENV=production
APP_DEBUG=false
PAYTR_TEST_MODE=false
APNS_PRODUCTION=true

# 2. Configure SSL
# Use Let's Encrypt or your certificate provider

# 3. Setup queue worker
supervisord

# 4. Run migrations
php artisan migrate --force

# 5. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Monitor
# Setup monitoring, alerting, and backups
```

## 💡 Pro Tips

- Use `docker-compose exec app` prefix for all artisan commands
- Check `docker-compose logs -f app` for real-time errors
- Token expires after 30 days (configurable in ApiTokenIssuer)
- Device tokens are automatically managed (duplicate tokens update)
- Notifications support custom data via JSON
- Test endpoints are only for development (remove in production)

## 📞 Need Help?

1. Check the appropriate documentation file
2. Run diagnostics:
   ```bash
   docker-compose ps  # Check containers
   docker-compose logs -f app  # View logs
   curl http://localhost:8000/health  # Check health
   ```
3. Review the PUSH_NOTIFICATION_GUIDE.md troubleshooting section
4. Check database state in MySQL

---

**Everything is ready to use!** 🚀
Test it, configure APNS/FCM, and deploy to production.
