<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function showPrimary(Request $request): Response
    {
        $user = $request->user();
        
        // 1. Determine which community to show based on Role/Scope
        $community = null;

        if ($user->hasRole('admin')) {
             // Admin sees the first one for now (or could be list in future)
             $community = Community::firstOrFail();
        } elseif ($user->hasRole(['board_member', 'accountant'])) {
             // Staff sees their assigned community
             $community = Community::find($user->community_id);
        } elseif ($user->hasRole('resident')) {
             // Resident sees community of their first unit
             $unit = $user->units()->with('community')->first();
             $community = $unit?->community;
        }

        // 2. Deny if no community found or if Provider (implied by null)
        if (! $community) {
            abort(403, 'Unauthorized');
        }

        // 3. Authorize action (double check via Policy)
        // This will trigger CommunityPolicy::view, which explicitly denies providers
        // and checks scopes again (defense in depth).
        $this->authorize('view', $community);

        // 4. Load relationships only if authorized
        $community->loadCount(['buildings', 'units']);

        return Inertia::render('Communities/Show', [
            'community' => $community,
        ]);
    }
}
