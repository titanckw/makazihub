# MakaziHub — Module 1: Database Schema & Migrations

## What's Included

```
database/
├── SCHEMA.md                          ← Full schema documentation
├── migrations/
│   ├── 2024_01_01_000001_create_users_table.php
│   ├── 2024_01_01_000002_create_properties_table.php
│   ├── 2024_01_01_000003_create_units_table.php
│   ├── 2024_01_01_000004_create_tenants_table.php
│   ├── 2024_01_01_000005_create_leases_table.php
│   ├── 2024_01_01_000006_create_invoices_table.php
│   ├── 2024_01_01_000007_create_payments_table.php
│   ├── 2024_01_01_000008_create_receipts_table.php
│   ├── 2024_01_01_000009_create_notifications_log_table.php
│   ├── 2024_01_01_000010_create_settings_table.php
│   └── DatabaseSeeder.php
└── README.md                          ← This file
```

---

## Setup Instructions

### 1. Install Laravel
```bash
composer create-project laravel/laravel makazihub
cd makazihub
```

### 2. Install Spatie Permission Package
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 3. Configure your .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=makazihub
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 4. Copy migration files
Copy all files from `migrations/` into your Laravel project's `database/migrations/` folder.
Copy `DatabaseSeeder.php` into `database/seeders/DatabaseSeeder.php`.

### 5. Run migrations and seed
```bash
php artisan migrate
php artisan db:seed
```

### 6. Demo Login Credentials
| Role       | Email                      | Password      |
|------------|----------------------------|---------------|
| SuperAdmin | admin@makazihub.co.ke       | Admin@1234    |
| Manager    | manager@makazihub.co.ke     | Manager@1234  |
| Tenant     | tenant@makazihub.co.ke      | Tenant@1234   |

---

## Tables Summary

| Table | Purpose |
|-------|---------|
| `users` | All users (SuperAdmin, Manager, Tenant) |
| `properties` | Buildings/estates managed by managers |
| `units` | Individual rentable units |
| `tenants` | Extended profile for tenant users |
| `leases` | Contracts linking tenants to units |
| `invoices` | Monthly rent invoices (auto-generated) |
| `payments` | Payment transactions (M-Pesa & manual) |
| `receipts` | PDF receipt records |
| `notifications_log` | Audit trail of SMS/email notifications |
| `settings` | System-wide configuration |

---

## Invoice Status Flow

```
Generated → unpaid
              ↓ (past due_date with balance > 0)
           overdue
              ↓ (partial payment received)
           partial
              ↓ (balance = 0)
            paid ✅
```

## Color-Code Mapping

| Status    | Text Color | Background |
|-----------|------------|------------|
| paid      | #16A34A    | #DCFCE7    |
| partial   | #2563EB    | #DBEAFE    |
| unpaid    | #D97706    | #FEF3C7    |
| overdue   | #DC2626    | #FEE2E2    |
| vacant    | #16A34A    | #DCFCE7    |
| occupied  | #1E293B    | #E2E8F0    |
| maintenance | #D97706  | #FEF3C7    |

---

## Next Module
**Module 2** → Laravel project structure, routing, middleware, and role-based access control.
