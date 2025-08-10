# SNIA Conference Management System - API Documentation

## 🎯 API Status: **100% PRODUCTION READY**

All CRUD operations have been tested and verified. The backend is ready for frontend development.

## 📋 Base Configuration

**Base URL:** `http://localhost:8080/api/v1`  
**Environment:** Development (ready for production deployment)  
**Authentication:** JWT Bearer Token required for most endpoints

## 🔥 Quick Start for Frontend Team

### 1. Authentication Flow
```javascript
// Login
POST /auth/login
{
    "email": "user@example.com", 
    "password": "password"
}

// Register new user
POST /auth/register  
{
    "first_name": "John",
    "last_name": "Doe", 
    "email": "john@example.com",
    "password": "password123",
    "confirm_password": "password123",
    "institution": "University Name",
    "phone": "+6281234567890",
    "role": "presenter" // or "audience", "reviewer"
}
```

### 2. JWT Token Usage
```javascript
// Include in all protected requests
Headers: {
    "Authorization": "Bearer YOUR_JWT_TOKEN",
    "Content-Type": "application/json"
}
```

## 📊 API Endpoints Overview

### ✅ Health Endpoints (No Auth Required)
- `GET /health` - System health check
- `GET /health/database` - Database connectivity  
- `GET /health/jwt` - JWT configuration status

### 🔐 Authentication Endpoints (No Auth Required)
- `POST /auth/login` - User login
- `POST /auth/register` - User registration
- `POST /auth/verify` - Email verification
- `POST /auth/refresh` - Refresh JWT token
- `POST /auth/logout` - User logout
- `GET /auth/profile` - Get user profile

### 📝 Registration Management (Auth Required)
- `GET /registrations` - List user registrations  
- `POST /registrations/register` - Create new registration
- `GET /registrations/{id}` - Get specific registration
- `PUT /registrations/{id}` - Update registration
- `DELETE /registrations/{id}` - Cancel registration

### 📄 Abstract Management (Auth Required)
- `GET /abstracts` - List user abstracts
- `POST /abstracts` - Submit new abstract
- `GET /abstracts/categories` - List abstract categories
- `GET /abstracts/{id}` - Get abstract details  
- `PUT /abstracts/{id}` - Update abstract
- `GET /abstracts/{id}/download` - Download abstract file
- `POST /abstracts/{id}/revision` - Submit revision

### 💳 Payment System (Auth Required)
- `GET /payments` - List user payments
- `POST /payments` - Create payment
- `GET /payments/{id}` - Get payment details
- `POST /payments/{id}/verify` - Verify payment
- `GET /payments/{id}/invoice` - Download invoice

### 📋 LOA (Letter of Acceptance) System (Auth Required)  
- `GET /loa/my-loas` - List user LOAs
- `GET /loa/generate/{id}` - Generate LOA for registration
- `GET /loa/download/{id}` - Download LOA document

### 🏆 Certificate System (Auth Required)
- `GET /certificates` - List user certificates
- `POST /certificates/request` - Request certificate
- `GET /certificates/{id}` - Get certificate details
- `GET /certificates/{id}/download` - Download certificate

### 🔍 Certificate Verification (No Auth Required)
- `GET /certificates/verify/{code}` - Verify certificate by code

### 🎫 Voucher System (Auth Required)
- `GET /vouchers` - List vouchers (Admin only)
- `POST /vouchers` - Create voucher (Admin only)  
- `POST /vouchers/apply` - Apply voucher to registration
- `GET /vouchers/check/{code}` - Check voucher validity
- `GET /vouchers/my-usage` - Get user voucher history

### 📱 QR Code System (Auth Required)
- `GET /qr/my-codes` - List user QR codes
- `POST /qr/generate` - Generate QR code
- `GET /qr/{id}` - Get QR code details
- `POST /qr/scan` - Scan QR code
- `GET /qr/scan-history` - Get scan history

### 👔 Admin Endpoints (Auth + Admin Role Required)
- `GET /admin/dashboard` - Admin dashboard data
- `GET /admin/users` - List all users  
- `GET /admin/abstracts` - List all abstracts
- `GET /admin/presenter-progress` - Presenter progress tracking
- `PUT /admin/abstracts/{id}/assign` - Assign reviewer
- `GET /admin/export/{type}` - Export data

## 🔥 Response Format

### Success Response
```json
{
    "status": "success",
    "data": { ... },
    "message": "Operation completed successfully"
}
```

### Error Response  
```json
{
    "status": "error", 
    "message": "Error description",
    "errors": { ... } // validation errors if applicable
}
```

## 🚀 Frontend Integration Notes

### 1. State Management
- Store JWT token in localStorage/sessionStorage
- Implement automatic token refresh
- Handle 401/403 responses by redirecting to login

### 2. File Uploads
- Use multipart/form-data for file uploads
- Abstract submissions support PDF files
- Payment receipts support common image formats

### 3. Error Handling
- All endpoints return consistent error format
- HTTP status codes follow REST conventions
- Validation errors include field-specific messages

### 4. Real-time Features
- Payment status updates via polling or webhooks
- Abstract review status changes
- Certificate generation notifications

## 🔧 Development Tools

### Available Test Endpoints
- `GET /test/simple` - Basic system test
- `GET /test/models` - Test all data models  
- `GET /test/database` - Database connectivity test

### Debugging
- Enable debug mode in development
- Check `/writable/logs` for error logs
- Use browser developer tools for API debugging

## 📚 Sample Frontend Code

### React/JavaScript Example
```javascript
// API Service
class SNIAApiService {
    constructor() {
        this.baseURL = 'http://localhost:8080/api/v1';
        this.token = localStorage.getItem('snia_token');
    }

    async login(email, password) {
        const response = await fetch(`${this.baseURL}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            this.token = data.data.token;
            localStorage.setItem('snia_token', this.token);
        }
        return data;
    }

    async getRegistrations() {
        const response = await fetch(`${this.baseURL}/registrations`, {
            headers: { 
                'Authorization': `Bearer ${this.token}`,
                'Content-Type': 'application/json'
            }
        });
        return response.json();
    }

    async submitAbstract(abstractData) {
        const formData = new FormData();
        Object.keys(abstractData).forEach(key => {
            formData.append(key, abstractData[key]);
        });

        const response = await fetch(`${this.baseURL}/abstracts`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${this.token}` },
            body: formData
        });
        return response.json();
    }
}
```

## 🎉 Ready for Production

- **Database:** PostgreSQL with all required tables and relationships
- **Authentication:** JWT-based with role management  
- **File Handling:** Upload/download for abstracts, certificates, LOAs
- **Email System:** SMTP integration for notifications
- **QR Codes:** Generation and scanning functionality
- **Payment Integration:** Ready for Midtrans webhook
- **Admin Panel:** Complete presenter progress tracking
- **Security:** CORS enabled, input validation, SQL injection protection

## 🔗 Next Steps for Frontend Team

1. **Clone Repository** - Get latest backend code from GitHub
2. **Set Environment** - Configure database and email settings  
3. **API Integration** - Use this documentation to implement frontend
4. **Testing** - Use Postman collection for endpoint testing
5. **Authentication** - Implement JWT token management
6. **File Uploads** - Handle multipart forms for abstracts
7. **Admin Dashboard** - Build charts using presenter progress data
8. **Payment Flow** - Integrate with payment gateway
9. **QR Scanner** - Implement camera-based QR scanning
10. **Deployment** - Prepare for production deployment

---

**Generated by SNIA Backend System**  
**Status:** ✅ Production Ready  
**Last Updated:** August 10, 2025  
**Contact:** Backend Development Team