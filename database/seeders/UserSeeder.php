<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('secret');

        // Resident (assigned to a unit)
        $resident = User::create([
            'name' => 'John Resident',
            'email' => 'resident@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);
        $resident->assignRole('resident');

        // Attach to first unit if exists
        $unit = Unit::first();
        if ($unit) {
            $resident->units()->attach($unit->id, ['relationship_type' => 'owner']);
        }

        // Fetch Community for Staff scoping
        $community = \App\Models\Community::first();

        // Board Member
        $board = User::create([
            'name' => 'Sarah Board',
            'email' => 'board@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
            'community_id' => $community?->id,
        ]);
        $board->assignRole('board_member');

        // Accountant
        $accountant = User::create([
            'name' => 'Alex Accountant',
            'email' => 'accountant@neighborly.test',
            'password' => $password,
            'locale' => 'pl',
            'email_verified_at' => now(),
            'community_id' => $community?->id,
        ]);
        $accountant->assignRole('accountant');

        // Service Provider
        $provider = User::create([
            'name' => 'FixIt Pro',
            'email' => 'provider@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
            // Provider optionally scoped to community, but access denied by policy/permissions
            'community_id' => $community?->id,
        ]);
        $provider->assignRole('service_provider');

        // Building Manager
        $manager = User::create([
            'name' => 'Mike Manager',
            'email' => 'manager@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
            'community_id' => $community?->id,
            'verification_status' => 'approved',
        ]);
        $manager->assignRole('building_manager');
        
        // Assign to first building
        $firstBuilding = \App\Models\Building::first();
        if ($firstBuilding) {
            $manager->managedBuildings()->attach($firstBuilding);
        }

        // Admin
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
    }
}
