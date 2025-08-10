# 🔧 QRCodeService Documentation
**SNIA Conference Management System - QR Code Integration**

---

## 📊 **IMPLEMENTATION SUMMARY**

### ✅ **COMPLETED FEATURES**
- **QR Code Generation**: Full integration with registration system
- **QR Code Validation**: Secure hash-based verification
- **Database Integration**: Complete qr_codes and qr_scans table support
- **QR Scanning**: Attendance tracking and scan history
- **Security**: Hash verification and expiration handling

### 🔧 **TECHNICAL SPECIFICATIONS**
- **Library**: Endroid QR Code v6.0.9
- **Database**: PostgreSQL with proper foreign keys
- **Framework**: CodeIgniter 4 Service Pattern
- **Security**: SHA256 hash verification with encryption key

---

## 📚 **QRCodeService API REFERENCE**

### **1. generateUserQRCode(int $registrationId, array $options = [])**
Generates QR code for a specific registration.

```php
$qrService = new \App\Services\QRCodeService();
$result = $qrService->generateUserQRCode($registrationId);

// Returns:
[
    'success' => true,
    'message' => 'QR Code generated successfully',
    'qr_code' => [
        'id' => 1,
        'user_id' => 21,
        'qr_data' => '{"registration_id":3,"user_id":"21",...}',
        'qr_image' => 'base64_encoded_png_image',
        'qr_hash' => 'sha256_hash',
        'status' => 'active',
        'is_verified' => true,
        'expires_at' => '2025-08-09 18:16:36'
    ],
    'qr_data' => [
        'registration_id' => 3,
        'user_id' => 21,
        'event_id' => 1,
        'registration_type' => 'audience',
        'payment_status' => 'paid',
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'event_title' => 'SNIA 2024',
        'hash' => 'verification_hash'
    ]
]
```

### **2. validateQRCode(string $qrDataJson)**
Validates QR code data and returns registration information.

```php
$result = $qrService->validateQRCode($qrJsonString);

// Returns:
[
    'success' => true,
    'message' => 'QR Code is valid',
    'qr_code' => [...], // QR code database record
    'qr_data' => [...], // Decoded QR data
    'registration' => [...] // Full registration details
]
```

### **3. getUserQRCode(int $userId, string $status = 'active')**
Retrieves user's existing QR code.

```php
$result = $qrService->getUserQRCode($userId);

// Returns:
[
    'success' => true,
    'qr_code' => [...] // User's QR code data
]
```

### **4. recordQRScan(...)**
Records QR code scan for attendance tracking.

```php
$result = $qrService->recordQRScan(
    $qrCodeId, 
    $userId, 
    'check_in',
    $scannerUserId,
    [
        'location' => 'Main Hall',
        'ip_address' => '192.168.1.100',
        'user_agent' => 'QR Scanner App',
        'notes' => 'Event check-in'
    ]
);

// Returns:
[
    'success' => true,
    'message' => 'QR scan recorded successfully',
    'scan_id' => 1
]
```

### **5. updateQRCodeData(int $qrCodeId, array $data)**
Updates QR code properties (status, verification, etc.).

```php
$result = $qrService->updateQRCodeData($qrCodeId, [
    'status' => 'inactive',
    'is_verified' => false
]);
```

### **6. deleteUserQRCode(int $userId, bool $hardDelete = false)**
Deactivates or deletes user's QR code.

```php
// Soft delete (recommended)
$result = $qrService->deleteUserQRCode($userId, false);

// Hard delete
$result = $qrService->deleteUserQRCode($userId, true);
```

---

## 🗄️ **DATABASE SCHEMA**

### **qr_codes Table**
```sql
- id (SERIAL PRIMARY KEY)
- user_id (INTEGER, FK to users.id)
- qr_data (TEXT, JSON encoded QR data)
- qr_image (LONGTEXT, Base64 encoded PNG)
- qr_hash (VARCHAR(255), SHA256 hash)
- status (ENUM: active, inactive, expired)
- is_verified (BOOLEAN, payment verification)
- expires_at (TIMESTAMP)
- last_scanned_at (TIMESTAMP)
- scan_count (INTEGER, default 0)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### **qr_scans Table**
```sql
- id (SERIAL PRIMARY KEY)
- qr_code_id (INTEGER, FK to qr_codes.id)
- user_id (INTEGER, FK to users.id)
- scan_type (VARCHAR: check_in, check_out, session_access, etc.)
- scanner_user_id (INTEGER, FK to users.id)
- location (VARCHAR(255))
- ip_address (VARCHAR(45))
- user_agent (TEXT)
- scan_result (VARCHAR: success, failed, expired, etc.)
- notes (TEXT)
- scanned_at (TIMESTAMP)
- created_at (TIMESTAMP)
```

### **registrations Table Updates**
```sql
- qr_code (VARCHAR(255), reference to QR hash)
```

---

## 🔒 **SECURITY FEATURES**

### **Hash Verification**
- QR data includes SHA256 hash for tamper detection
- Hash combines QR data with encryption key from environment
- Database stores separate hash for verification

### **Expiration Handling**
- QR codes can have expiration dates
- Automatic validation of expired codes
- Configurable expiration periods

### **Status Management**
- QR codes can be active, inactive, or expired
- Soft delete functionality preserves audit trail
- Payment status integration

---

## 🎯 **INTEGRATION WITH SNIA SYSTEM**

### **Registration Flow Integration**
1. **User Registration** → Creates registration record
2. **Payment Confirmation** → Triggers QR generation
3. **QR Code Email** → Sends QR to user via EmailService
4. **Event Day** → QR scanning for attendance

### **Payment System Integration**
```php
// In PaymentApiController::verify()
if ($payment['status'] === 'paid') {
    $qrService = new \App\Services\QRCodeService();
    $qrResult = $qrService->generateUserQRCode($registrationId);
    
    if ($qrResult['success']) {
        // Send QR via EmailService
        $emailService->sendPaymentConfirmationWithQR(...);
    }
}
```

### **Email Service Integration**
```php
// In EmailService
public function sendPaymentConfirmationWithQR($email, $name, $qrImage) {
    // Attach QR code as inline image
    $this->email->attach($qrImage, 'inline', 'qr_code.png', 'image/png');
}
```

---

## 🧪 **TESTING**

### **Run QR Service Tests**
```bash
php spark test:qr
```

### **Test Coverage**
- ✅ QR Code Generation
- ✅ QR Code Validation  
- ✅ Hash Verification
- ✅ Database Integration
- ✅ Scan Recording
- ✅ User QR Retrieval

---

## 🚀 **NEXT STEPS FOR INTEGRATION**

### **Phase 1: Payment Integration** (Next)
```php
// Update PaymentApiController::verify()
// Add QR generation after payment success
// Update EmailService for QR attachments
```

### **Phase 2: Scanner Interface**
```php
// Create QRScannerController
// Build admin scanner interface
// Add real-time attendance tracking
```

### **Phase 3: API Endpoints**
```php
// api/v1/qr/generate
// api/v1/qr/validate  
// api/v1/qr/scan
// api/v1/qr/attendance/:event_id
```

---

## 📋 **CONFIGURATION**

### **Environment Variables**
```env
# QR Code Settings
encryption.key = your_encryption_key_here
QR_CODE_SIZE = 300
QR_CODE_MARGIN = 10
QR_EXPIRATION_DAYS = 365
```

### **Service Registration**
```php
// In Config/Services.php
public static function qrcode($getShared = true)
{
    if ($getShared) {
        return static::getSharedInstance('qrcode');
    }
    
    return new \App\Services\QRCodeService();
}
```

---

## 🎉 **IMPLEMENTATION STATUS: COMPLETED** ✅

**QRCodeService is ready for integration with SNIA payment and email systems!**

### **Features Delivered:**
- ✅ Complete QR generation and validation
- ✅ Database integration with proper foreign keys
- ✅ Security with hash verification
- ✅ Attendance tracking via QR scans  
- ✅ Error handling and logging
- ✅ Comprehensive test coverage
- ✅ CodeIgniter 4 service pattern compliance

### **Ready for:**
- 🔄 PaymentApiController integration
- 📧 EmailService QR attachment  
- 📱 Admin QR scanner interface
- 📊 Attendance reporting dashboard

**The QR system foundation is solid and ready for the next phase of SNIA integration!** 🚀