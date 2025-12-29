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

        // Attach to first unit if exists
        $unit = Unit::first();
        if ($unit) {
            $resident->units()->attach($unit->id, ['relationship_type' => 'owner']);
        }

        // Board Member
        User::create([
            'name' => 'Sarah Board',
            'email' => 'board@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);

        // Accountant
        User::create([
            'name' => 'Alex Accountant',
            'email' => 'accountant@neighborly.test',
            'password' => $password,
            'locale' => 'pl', // Demo: seeding a PL user
            'email_verified_at' => now(),
        ]);

        // Service Provider
        User::create([
            'name' => 'FixIt Pro',
            'email' => 'provider@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);

        // Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@neighborly.test',
            'password' => $password,
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);
    }
}
