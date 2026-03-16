<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // 1. ROLES
        // -------------------------------------------------------
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $manager    = Role::firstOrCreate(['name' => 'manager']);
        $tenant     = Role::firstOrCreate(['name' => 'tenant']);

        // -------------------------------------------------------
        // 2. PERMISSIONS
        // -------------------------------------------------------
        $permissions = [
            // Properties
            'view properties', 'create properties', 'edit properties', 'delete properties',
            // Units
            'view units', 'create units', 'edit units', 'delete units',
            // Tenants
            'view tenants', 'create tenants', 'edit tenants', 'delete tenants',
            // Leases
            'view leases', 'create leases', 'edit leases', 'terminate leases',
            // Invoices
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices',
            // Payments
            'view payments', 'record payments', 'confirm payments', 'reverse payments',
            // Receipts
            'view receipts', 'download receipts', 'send receipts',
            // Reports
            'view reports', 'export reports',
            // Settings
            'view settings', 'edit settings',
            // Users
            'view users', 'create users', 'edit users', 'delete users',
            // Notifications
            'send notifications', 'view notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // SuperAdmin gets all permissions
        $superadmin->syncPermissions(Permission::all());

        // Manager permissions
        $manager->syncPermissions([
            'view properties', 'edit properties',
            'view units', 'create units', 'edit units',
            'view tenants', 'create tenants', 'edit tenants',
            'view leases', 'create leases', 'edit leases', 'terminate leases',
            'view invoices', 'create invoices', 'edit invoices',
            'view payments', 'record payments', 'confirm payments',
            'view receipts', 'download receipts', 'send receipts',
            'view reports',
            'send notifications', 'view notifications',
        ]);

        // Tenant permissions
        $tenant->syncPermissions([
            'view invoices',
            'view payments',
            'view receipts', 'download receipts',
        ]);

        // -------------------------------------------------------
        // 3. DEFAULT SUPERADMIN USER
        // -------------------------------------------------------
        $admin = User::firstOrCreate(
            ['email' => 'admin@makazihub.co.ke'],
            [
                'name'     => 'MakaziHub Admin',
                'phone'    => '+254700000000',
                'password' => Hash::make('Admin@1234'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->assignRole('superadmin');

        // -------------------------------------------------------
        // 4. DEMO MANAGER
        // -------------------------------------------------------
        $demoManager = User::firstOrCreate(
            ['email' => 'manager@makazihub.co.ke'],
            [
                'name'     => 'Jane Manager',
                'phone'    => '+254711000001',
                'password' => Hash::make('Manager@1234'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $demoManager->assignRole('manager');

        // -------------------------------------------------------
        // 5. DEMO TENANT
        // -------------------------------------------------------
        $demoTenantUser = User::firstOrCreate(
            ['email' => 'tenant@makazihub.co.ke'],
            [
                'name'     => 'John Tenant',
                'phone'    => '+254722000001',
                'password' => Hash::make('Tenant@1234'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $demoTenantUser->assignRole('tenant');

        // Create tenant profile
        \App\Models\Tenant::firstOrCreate(
            ['user_id' => $demoTenantUser->id],
            [
                'id_number'               => '12345678',
                'emergency_contact_name'  => 'Mary Tenant',
                'emergency_contact_phone' => '+254733000001',
                'occupation'              => 'Software Engineer',
                'employer'                => 'Tech Company Ltd',
            ]
        );

        // -------------------------------------------------------
        // 6. DEFAULT SETTINGS
        // -------------------------------------------------------
        $settings = [
            // General
            ['key' => 'app_name',           'value' => 'MakaziHub',              'group' => 'general',       'description' => 'Application name'],
            ['key' => 'app_currency',        'value' => 'KES',                   'group' => 'general',       'description' => 'Default currency'],
            ['key' => 'app_timezone',        'value' => 'Africa/Nairobi',        'group' => 'general',       'description' => 'System timezone'],
            ['key' => 'company_name',        'value' => 'MakaziHub Properties',   'group' => 'general',       'description' => 'Company name on documents'],
            ['key' => 'company_phone',       'value' => '+254700000000',         'group' => 'general',       'description' => 'Company phone'],
            ['key' => 'company_email',       'value' => 'info@makazihub.co.ke',   'group' => 'general',       'description' => 'Company email'],
            ['key' => 'company_address',     'value' => 'Nairobi, Kenya',        'group' => 'general',       'description' => 'Company address'],

            // Invoicing
            ['key' => 'invoice_prefix',      'value' => 'INV',                   'group' => 'invoicing',     'description' => 'Invoice number prefix'],
            ['key' => 'receipt_prefix',      'value' => 'REC',                   'group' => 'invoicing',     'description' => 'Receipt number prefix'],
            ['key' => 'invoice_day',         'value' => '1',                     'group' => 'invoicing',     'description' => 'Day of month to generate invoices'],
            ['key' => 'late_fee_enabled',    'value' => 'true',                  'group' => 'invoicing',     'description' => 'Enable late fees'],
            ['key' => 'late_fee_days',       'value' => '5',                     'group' => 'invoicing',     'description' => 'Days after due date to apply late fee'],
            ['key' => 'late_fee_amount',     'value' => '500',                   'group' => 'invoicing',     'description' => 'Late fee amount in KES'],
            ['key' => 'invoice_footer',      'value' => 'Thank you for your payment. Please retain this receipt for your records.', 'group' => 'invoicing', 'description' => 'Invoice footer text'],

            // M-Pesa
            ['key' => 'mpesa_enabled',           'value' => 'false',             'group' => 'mpesa',         'description' => 'Enable M-Pesa payments'],
            ['key' => 'mpesa_env',               'value' => 'sandbox',           'group' => 'mpesa',         'description' => 'sandbox or production'],
            ['key' => 'mpesa_shortcode',         'value' => '',                  'group' => 'mpesa',         'description' => 'M-Pesa business shortcode'],
            ['key' => 'mpesa_consumer_key',      'value' => '',                  'group' => 'mpesa',         'description' => 'Daraja consumer key'],
            ['key' => 'mpesa_consumer_secret',   'value' => '',                  'group' => 'mpesa',         'description' => 'Daraja consumer secret'],
            ['key' => 'mpesa_passkey',           'value' => '',                  'group' => 'mpesa',         'description' => 'Daraja passkey'],
            ['key' => 'mpesa_callback_url',      'value' => '',                  'group' => 'mpesa',         'description' => 'STK Push callback URL'],

            // Notifications
            ['key' => 'sms_enabled',             'value' => 'false',             'group' => 'notifications', 'description' => 'Enable SMS notifications'],
            ['key' => 'sms_provider',            'value' => 'africastalking',    'group' => 'notifications', 'description' => 'SMS provider'],
            ['key' => 'africastalking_username',  'value' => '',                  'group' => 'notifications', 'description' => "Africa's Talking username"],
            ['key' => 'africastalking_api_key',   'value' => '',                  'group' => 'notifications', 'description' => "Africa's Talking API key"],
            ['key' => 'sms_sender_id',           'value' => 'MAKAZIHUB',          'group' => 'notifications', 'description' => 'SMS sender ID'],
            ['key' => 'email_enabled',           'value' => 'true',              'group' => 'notifications', 'description' => 'Enable email notifications'],
            ['key' => 'notify_on_invoice',       'value' => 'true',              'group' => 'notifications', 'description' => 'Notify tenant when invoice is generated'],
            ['key' => 'notify_on_payment',       'value' => 'true',              'group' => 'notifications', 'description' => 'Notify tenant when payment is confirmed'],
            ['key' => 'notify_overdue',          'value' => 'true',              'group' => 'notifications', 'description' => 'Send overdue reminders'],
            ['key' => 'overdue_reminder_days',   'value' => '1,7,14',            'group' => 'notifications', 'description' => 'Days after due date to send overdue reminders'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Demo Credentials:');
        $this->command->info('─────────────────────────────────────');
        $this->command->info('SuperAdmin → admin@makazihub.co.ke    | Admin@1234');
        $this->command->info('Manager   → manager@makazihub.co.ke  | Manager@1234');
        $this->command->info('Tenant    → tenant@makazihub.co.ke   | Tenant@1234');
        $this->command->info('─────────────────────────────────────');
    }
}
