<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Community;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $community = Community::create([
            'name' => 'Sunrise Apartments',
            'slug' => 'sunrise-apartments',
        ]);

        $b1 = Building::create([
            'community_id' => $community->id,
            'name' => 'Building A',
        ]);

        $b1->units()->createMany([
            ['community_id' => $community->id, 'label' => '101', 'floor' => 1],
            ['community_id' => $community->id, 'label' => '102', 'floor' => 1],
            ['community_id' => $community->id, 'label' => '201', 'floor' => 2],
        ]);

        $b2 = Building::create([
            'community_id' => $community->id,
            'name' => 'Building B',
        ]);

        $b2->units()->createMany([
            ['community_id' => $community->id, 'label' => 'B-1', 'floor' => 1],
            ['community_id' => $community->id, 'label' => 'B-2', 'floor' => 1],
        ]);
    }
}
