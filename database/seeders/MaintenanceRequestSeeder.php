<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaintenanceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $resident = User::where('email', 'resident@neighborly.test')->first();
        $provider = User::where('email', 'provider@neighborly.test')->first();
        $board = User::where('email', 'board@neighborly.test')->first();
        
        $community = Community::first();
        $unit = $resident->units()->first(); // Resident's unit

        // 1. Open Request by Resident
        MaintenanceRequest::create([
            'title' => 'Leaky Faucet',
            'description' => 'The kitchen faucet is leaking properly.',
            'status' => 'open',
            'community_id' => $community->id,
            'unit_id' => $unit?->id,
            'created_by_user_id' => $resident->id,
        ]);

        // 2. In Progress, Assigned to Provider (created by Resident)
        MaintenanceRequest::create([
            'title' => 'Broken Light in Hallway',
            'description' => 'Second floor hallway light is flickering.',
            'status' => 'in_progress',
            'community_id' => $community->id,
            'unit_id' => $unit?->id, // or common area (null unit)
            'created_by_user_id' => $resident->id,
            'assigned_to_user_id' => $provider->id,
        ]);

        // 3. Closed Request
        MaintenanceRequest::create([
            'title' => 'Door Lock Stuck',
            'description' => 'Fixed the front door lock.',
            'status' => 'closed',
            'community_id' => $community->id,
            'unit_id' => $unit?->id,
            'created_by_user_id' => $resident->id,
            'assigned_to_user_id' => $provider->id,
            'closed_by_user_id' => $provider->id,
            'closed_at' => now(),
        ]);
        
        // 4. Request by Board Member (for comparison)
        MaintenanceRequest::create([
            'title' => 'Roof Inspection',
            'description' => 'Annual roof checkup.',
            'status' => 'open',
            'community_id' => $community->id,
            'created_by_user_id' => $board->id,
        ]);
    }
}
