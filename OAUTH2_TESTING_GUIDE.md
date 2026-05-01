# OAuth2 Implementation Testing Guide

**Status:** ✅ Backend Complete & Ready for OAuth Provider Configuration  
**Date:** May 1, 2026  
**Version:** 1.0

---

## 🚀 Setup Complete

### What's Done
- ✅ Laravel Socialite installed (v5.27.0)
- ✅ Database migrations completed with OAuth columns
- ✅ UserController created for profile management
- ✅ OAuthController created for OAuth flows
- ✅ API routes configured and tested
- ✅ Frontend AccountSettings component ready
- ✅ Both servers running locally

### Running Servers

**Backend (Laravel):**
```bash
php artisan serve --port=8000
```
- API endpoint: `http://localhost:8000/api/v1/`
- OAuth redirects: `http://localhost:8000/oauth/{provider}/authorize`

**Frontend (Next.js):**
```bash
npm run dev
```
- App: `http://localhost:3001`
- Account Settings: `http://localhost:3001/account/settings`

---

## 🧪 API Testing

### Test User Credentials
```
Email: test@example.com
Password: password123
```

### 1. Get User Profile
```bash
curl -X GET http://localhost:8000/api/v1/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "phone": null,
    "google_id": null,
    "github_id": null,
    "facebook_id": null,
    "email_verified_at": "2026-05-01T09:10:17.000000Z",
    "...": "..."
  }
}
```

### 2. Update Profile
```bash
curl -X PUT http://localhost:8000/api/v1/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Name",
    "email": "newemail@example.com",
    "phone": "5301234567"
  }'
```

### 3. Change Password
```bash
curl -X POST http://localhost:8000/api/v1/auth/change-password \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "password123",
    "new_password": "newpassword456",
    "new_password_confirmation": "newpassword456"
  }'
```

### 4. Disconnect OAuth Provider
```bash
curl -X POST http://localhost:8000/api/v1/oauth/google/disconnect \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

---

## 🔐 OAuth Provider Configuration

### Required Credentials
You need to obtain OAuth credentials from:
1. **Google:** https://console.developers.google.com
2. **GitHub:** https://github.com/settings/developers
3. **Facebook:** https://developers.facebook.com

### Setup Steps

#### 1. Google OAuth
1. Go to [Google Console](https://console.developers.google.com)
2. Create new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials (Web application)
5. Add authorized redirect URIs:
   - `http://localhost:8000/oauth/google/callback`
   - `https://yourdomain.com/oauth/google/callback` (production)
6. Copy Client ID and Secret

#### 2. GitHub OAuth
1. Go to GitHub Settings → [Developer settings](https://github.com/settings/developers)
2. New OAuth App
3. Authorization callback URL:
   - `http://localhost:8000/oauth/github/callback`
   - `https://yourdomain.com/oauth/github/callback` (production)
4. Copy Client ID and Secret

#### 3. Facebook OAuth
1. Go to [Facebook Developers](https://developers.facebook.com)
2. Create app or use existing
3. App Type: Consumer
4. Valid OAuth Redirect URIs:
   - `http://localhost:8000/oauth/facebook/callback`
   - `https://yourdomain.com/oauth/facebook/callback` (production)
5. Copy App ID (Client ID) and App Secret (Secret)

### Add Credentials to .env

```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/oauth/google/callback

# GitHub OAuth
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI=http://localhost:8000/oauth/github/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/oauth/facebook/callback
```

---

## 🧬 OAuth Flow Testing

### 1. Test OAuth Authorization Endpoint
```bash
# Visit in browser
http://localhost:8000/oauth/google/authorize
```
- Should redirect to Google login
- After approval, returns to callback

### 2. OAuth Callback
Backend handles:
- User lookup by OAuth ID
- Automatic user creation if new
- Existing user update with OAuth credentials
- API token generation
- Returns JSON with token and user data

### 3. Frontend OAuth Connection
1. Go to `http://localhost:3001/account/settings`
2. Click "Bağlantılar" (Connections) tab
3. Click provider button (Google, GitHub, Facebook)
4. Authenticate with provider
5. Redirects back to settings page with connected provider

### 4. Disconnect Provider
1. In settings page, OAuth tab
2. Click "Disconnect" button on connected provider
3. Provider removed from user account

---

## 📋 Test Scenarios

### User Registration with OAuth
```
1. Visit homepage
2. Click "Kayıt Ol" (Register)
3. Select provider (Google, GitHub, Facebook)
4. Authenticate and approve
5. New user created automatically
6. Logged in with API token
```

### User Login with OAuth
```
1. Visit homepage
2. Click "Giriş Yap" (Login)
3. Select provider
4. Already have provider connected?
   → Updates credentials
   → Returns new token
5. Logged in
```

### User Account Settings
```
1. Logged in user
2. Go to /account/settings
3. Profil Bilgileri (Profile):
   - Update name, email, phone
4. Güvenlik (Security):
   - Change password
5. Bağlantılar (Connections):
   - Connect/disconnect OAuth providers
```

---

## 🔍 Database Schema

### users table OAuth Columns
```sql
google_id       VARCHAR (nullable, unique)
google_email    VARCHAR (nullable)
github_id       VARCHAR (nullable, unique)
github_email    VARCHAR (nullable)
facebook_id     VARCHAR (nullable, unique)
facebook_email  VARCHAR (nullable)
```

### api_tokens table
```sql
user_id         INTEGER (foreign key)
name            VARCHAR (device name)
token_hash      VARCHAR (SHA256 hash)
abilities       JSON (always ['*'])
last_used_at    TIMESTAMP
expires_at      TIMESTAMP (30 days default)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

## 🛠️ Troubleshooting

### OAuth Callback Fails
**Issue:** Redirect not working or error message
**Solution:**
- Check OAuth provider credentials are correct
- Verify redirect URI matches exactly in provider settings
- Check .env file has correct format

### Token Expires Quickly
**Default:** 30 days  
**Change in:** `app/Services/Auth/ApiTokenIssuer.php`
```php
public const DEFAULT_TTL_DAYS = 30;
```

### User Already Exists with Same Email
**Behavior:** If OAuth provider has different ID than existing user with same email:
- Existing user is found by email
- OAuth credentials are added to existing user
- User can now login via both methods

### Frontend Can't Connect to Backend
**Solution:**
- Verify `NEXT_PUBLIC_API_URL=http://localhost:8000` in .env
- Check both servers running on correct ports
- Clear browser cache/cookies
- Check browser console for CORS errors

---

## 📚 API Reference

### Authentication
All authenticated endpoints require:
```
Authorization: Bearer <token>
Content-Type: application/json
```

### Endpoints

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| GET | `/v1/auth/profile` | ✅ | Get current user |
| PUT | `/v1/auth/profile` | ✅ | Update profile |
| POST | `/v1/auth/change-password` | ✅ | Change password |
| GET | `/oauth/{provider}/authorize` | ❌ | Start OAuth flow |
| GET | `/oauth/{provider}/callback` | ❌ | OAuth callback |
| POST | `/v1/oauth/{provider}/disconnect` | ✅ | Disconnect provider |

---

## ✅ Deployment Checklist

- [ ] OAuth credentials obtained from all providers
- [ ] .env file configured with credentials
- [ ] HTTPS enabled for production
- [ ] Redirect URIs updated in provider settings
- [ ] Database migrations run: `php artisan migrate`
- [ ] Cache cleared: `php artisan config:cache`
- [ ] Frontend built: `npm run build`
- [ ] All OAuth flows tested
- [ ] Profile updates tested
- [ ] Password change tested
- [ ] Token expiration verified

---

**Next Step:** Configure OAuth provider credentials and test flows!
