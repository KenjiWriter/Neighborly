<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\FinanceEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $community = Community::first();
        $admin = User::where('email', 'admin@neighborly.test')->first() ?? User::factory()->create(['email' => 'admin@neighborly.test']);
        
        // Income
        FinanceEntry::create([
            'community_id' => $community->id,
            'type' => 'income',
            'category' => 'Maintenance Fees',
            'amount_cents' => 1500000, // 15,000.00
            'occurred_on' => Carbon::now()->startOfMonth(),
            'description' => 'Monthly maintenance fees collection.',
            'created_by_user_id' => $admin->id,
        ]);

        FinanceEntry::create([
            'community_id' => $community->id,
            'type' => 'income',
            'category' => 'Parking Rental',
            'amount_cents' => 250000, // 2,500.00
            'occurred_on' => Carbon::now()->subMonth()->startOfMonth(),
            'description' => 'Parking space rentals.',
            'created_by_user_id' => $admin->id,
        ]);

        // Expenses
        FinanceEntry::create([
            'community_id' => $community->id,
            'type' => 'expense',
            'category' => 'Utilities',
            'amount_cents' => 450000, // 4,500.00
            'occurred_on' => Carbon::now()->subDays(5),
            'description' => 'Electricity and Water bill.',
            'created_by_user_id' => $admin->id,
        ]);

        FinanceEntry::create([
            'community_id' => $community->id,
            'type' => 'expense',
            'category' => 'Repairs',
            'amount_cents' => 120000, // 1,200.00
            'occurred_on' => Carbon::now()->subDays(15),
            'description' => 'Elevator maintenance.',
            'created_by_user_id' => $admin->id,
        ]);
    }
}
