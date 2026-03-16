# MakaziHub - Database Schema Documentation

## Entity Relationship Overview

```
users
 ├── properties (via manager_id)
 │    └── units
 │         └── leases (via tenant_id)
 │              ├── invoices
 │              │    └── payments
 │              │         └── receipts
 │              └── notifications (log)
 └── tenants (profile extension)
```

---

## Tables

### 1. users
Core authentication table. All three roles live here.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | Auto-increment |
| name | VARCHAR(255) | Full name |
| email | VARCHAR(255) | Unique |
| phone | VARCHAR(20) | For SMS notifications |
| password | VARCHAR(255) | Hashed |
| email_verified_at | TIMESTAMP | Nullable |
| remember_token | VARCHAR(100) | Nullable |
| is_active | BOOLEAN | Default true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Roles (via Spatie):** `superadmin`, `manager`, `tenant`

---

### 2. properties
A property is a building or estate managed by a Manager.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| manager_id | FK → users.id | The assigned manager |
| name | VARCHAR(255) | e.g. "Sunset Apartments" |
| address | TEXT | Full address |
| city | VARCHAR(100) | |
| county | VARCHAR(100) | Kenyan county |
| property_type | ENUM | apartment, maisonette, commercial, bedsitter |
| total_units | INT | Computed or manually set |
| description | TEXT | Nullable |
| is_active | BOOLEAN | Default true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 3. units
Individual rentable units within a property.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| property_id | FK → properties.id | |
| unit_number | VARCHAR(50) | e.g. "A1", "Unit 3B" |
| unit_type | ENUM | studio, 1br, 2br, 3br, commercial |
| floor | INT | Nullable |
| rent_amount | DECIMAL(10,2) | Monthly rent in KES |
| deposit_amount | DECIMAL(10,2) | Security deposit in KES |
| status | ENUM | vacant, occupied, maintenance |
| description | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 4. tenants
Extended profile for users with the tenant role.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| user_id | FK → users.id | One-to-one |
| id_number | VARCHAR(20) | National ID / Passport |
| emergency_contact_name | VARCHAR(255) | Nullable |
| emergency_contact_phone | VARCHAR(20) | Nullable |
| occupation | VARCHAR(255) | Nullable |
| employer | VARCHAR(255) | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 5. leases
The active contract between a tenant and a unit.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| tenant_id | FK → tenants.id | |
| unit_id | FK → units.id | |
| start_date | DATE | Lease start |
| end_date | DATE | Nullable (month-to-month) |
| rent_amount | DECIMAL(10,2) | Locked rent at signing |
| deposit_amount | DECIMAL(10,2) | Deposit paid |
| deposit_status | ENUM | paid, partial, unpaid |
| payment_day | TINYINT | Day of month rent is due (e.g. 1, 5) |
| status | ENUM | active, expired, terminated |
| notes | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 6. invoices
Auto-generated monthly rent invoices per lease.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| invoice_number | VARCHAR(50) | Unique, e.g. "INV-2025-001" |
| lease_id | FK → leases.id | |
| tenant_id | FK → tenants.id | Denormalized for speed |
| unit_id | FK → units.id | Denormalized for speed |
| amount_due | DECIMAL(10,2) | Base rent |
| late_fee | DECIMAL(10,2) | Default 0.00 |
| total_amount | DECIMAL(10,2) | amount_due + late_fee |
| amount_paid | DECIMAL(10,2) | Running total of payments |
| balance | DECIMAL(10,2) | total_amount - amount_paid |
| due_date | DATE | Payment due date |
| invoice_date | DATE | Date generated |
| billing_period | VARCHAR(20) | e.g. "2025-01" |
| status | ENUM | unpaid, partial, paid, overdue |
| notes | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 7. payments
Every payment transaction, M-Pesa or manual.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| invoice_id | FK → invoices.id | |
| tenant_id | FK → tenants.id | |
| payment_method | ENUM | mpesa, cash, bank_transfer, cheque |
| amount | DECIMAL(10,2) | Amount paid |
| mpesa_transaction_id | VARCHAR(50) | M-Pesa confirmation code, Nullable |
| mpesa_phone | VARCHAR(20) | Phone used for M-Pesa, Nullable |
| mpesa_receipt_number | VARCHAR(50) | Daraja receipt, Nullable |
| reference | VARCHAR(100) | Manual reference if not M-Pesa |
| payment_date | DATE | |
| status | ENUM | pending, confirmed, failed, reversed |
| confirmed_at | TIMESTAMP | When M-Pesa webhook confirmed |
| recorded_by | FK → users.id | Manager who recorded (if manual) |
| notes | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 8. receipts
PDF receipt record for each confirmed payment.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| payment_id | FK → payments.id | One-to-one |
| receipt_number | VARCHAR(50) | Unique, e.g. "REC-2025-001" |
| file_path | VARCHAR(255) | Path to stored PDF |
| sent_via_email | BOOLEAN | Default false |
| sent_via_sms | BOOLEAN | Default false |
| sent_at | TIMESTAMP | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 9. notifications_log
Audit trail of every SMS/email notification sent.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| user_id | FK → users.id | Recipient |
| type | ENUM | invoice_generated, payment_confirmed, overdue_reminder, lease_expiry, welcome |
| channel | ENUM | email, sms |
| recipient | VARCHAR(255) | Email address or phone number |
| subject | VARCHAR(255) | Nullable (email only) |
| message | TEXT | Full message body |
| status | ENUM | sent, failed |
| provider_response | TEXT | API response, Nullable |
| sent_at | TIMESTAMP | |
| created_at | TIMESTAMP | |

---

### 10. settings
System-wide and per-property configuration.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | |
| key | VARCHAR(100) | Unique setting key |
| value | TEXT | Setting value |
| group | VARCHAR(50) | e.g. "mpesa", "notifications", "invoicing" |
| description | VARCHAR(255) | Human-readable description |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## Key Relationships Summary

- **User** has one **Tenant** profile (if role = tenant)
- **User** has many **Properties** (if role = manager)
- **Property** has many **Units**
- **Unit** has many **Leases** (only one active at a time)
- **Lease** has many **Invoices** (one per billing period)
- **Invoice** has many **Payments** (supports partial payments)
- **Payment** has one **Receipt**

## Invoice Status Flow
```
[Generated] → unpaid → partial → paid
                  ↓
               overdue (if past due_date and balance > 0)
```

## Color Coding Map
| Status | Badge Color | Hex |
|--------|-------------|-----|
| paid | Emerald | #16A34A / #DCFCE7 |
| partial | Blue/Info | #2563EB / #DBEAFE |
| unpaid | Amber | #D97706 / #FEF3C7 |
| overdue | Red | #DC2626 / #FEE2E2 |
| vacant (unit) | Emerald | #16A34A / #DCFCE7 |
| occupied (unit) | Navy | #1E293B / #E2E8F0 |
| maintenance | Amber | #D97706 / #FEF3C7 |
