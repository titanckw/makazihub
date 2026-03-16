<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminRoutesTest extends TestCase
{
    public function test_superadmin_can_access_notifications_log()
    {
        // ensure the superadmin role exists
        Role::firstOrCreate(['name' => 'superadmin']);

        $user = User::factory()->create();
        $user->assignRole('superadmin');

        $response = $this->actingAs($user)->get(route('superadmin.notifications.log'));
        $response->assertStatus(200);
    }

    public function test_non_superadmin_cannot_access_superadmin_notifications_log()
    {
        // create another role (manager) for test
        Role::firstOrCreate(['name' => 'manager']);

        $user = User::factory()->create();
        $user->assignRole('manager');

        $response = $this->actingAs($user)->get(route('superadmin.notifications.log'));
        $response->assertStatus(403);
    }
}
