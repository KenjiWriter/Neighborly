<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create permissions
        // Minimal core permissions
        $permissions = [
            'community.view',
            'building.view',
            'unit.view',
            'users.manage', // Admin only
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create roles and assign permissions
        
        // Resident
        // Note: Resident access is strictly scoped via data policies (unit_user), 
        // but we give them basic view permissions to allow passing the gate check.
        $resident = Role::firstOrCreate(['name' => 'resident']);
        $resident->givePermissionTo(['community.view', 'building.view', 'unit.view']);

        // Board Member
        $board = Role::firstOrCreate(['name' => 'board_member']);
        $board->givePermissionTo(['community.view', 'building.view', 'unit.view']);

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo(['community.view', 'building.view', 'unit.view']);

        // Building Manager
        $manager = Role::firstOrCreate(['name' => 'building_manager']);
        $manager->givePermissionTo(['community.view', 'building.view', 'unit.view']);

        // Service Provider
        // Phase 2: Explicitly denied access to Community resources.
        // They only get dashboard/profile access by default (authenticated).
        $provider = Role::firstOrCreate(['name' => 'service_provider']);
        // No permissions assigned to provider for core domain resources.

        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo($permissions);
    }
}
