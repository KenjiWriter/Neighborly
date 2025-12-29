<?php

namespace App\Http\Controllers;

use App\Models\FinanceEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();

        // Capabilities / Policy Checks
        // We do not have a generic 'viewAny' for finance that applies to everyone in the same way.
        // Residents: denied viewAny in policy, but allowed here via role check to see Aggregated.
        // Staff: allowed viewAny.

        $canViewDetails = $user->can('viewAny', FinanceEntry::class);
        $isResident = $user->hasRole('resident');

        if (!$canViewDetails && !$isResident) {
             abort(403, 'Unauthorized');
        }

        // Determine Scope
        $communityId = null;
        if ($user->community_id) {
            $communityId = $user->community_id;
        } elseif ($isResident) {
            $unit = $user->units()->first();
            $communityId = $unit?->community_id;
        }

        if (!$communityId) {
             abort(403, 'No community context.');
        }

        // Aggregated Data (Allowed for All)
        // Group by Type (Income/Expense) and Month? Or just Totals?
        // Prompt: "aggregated overview (totals, monthly summary)"
        
        $stats = [
            'total_income' => FinanceEntry::forCommunity($communityId)
                ->where('type', 'income')
                ->sum('amount_cents'),
            'total_expense' => FinanceEntry::forCommunity($communityId)
                ->where('type', 'expense')
                ->sum('amount_cents'),
            'monthly' => FinanceEntry::forCommunity($communityId)
                ->select(DB::raw('sum(amount_cents) as total'), 'type', DB::raw("DATE_FORMAT(occurred_on, '%Y-%m') as month"))
                ->groupBy('month', 'type')
                ->orderBy('month', 'desc')
                ->limit(12) // Last 6-12 months
                ->get()
                ->groupBy('month'),
        ];

        $recentEntries = null;

        if ($canViewDetails) {
            // Staff / Admin: Show table logic
            $recentEntries = FinanceEntry::forCommunity($communityId)
                ->with('creator') // Maybe not needed for list
                ->orderBy('occurred_on', 'desc')
                ->paginate(10);
        }

        return Inertia::render('Finances/Overview', [
            'stats' => $stats,
            'recentEntries' => $recentEntries,
            'canViewDetails' => $canViewDetails,
        ]);
    }
}
