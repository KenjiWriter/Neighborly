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
        // Phase 1: Explicitly fetch the first seeded community
        $community = Community::withCount(['buildings', 'units'])->firstOrFail();

        return Inertia::render('Communities/Show', [
            'community' => $community,
        ]);
    }
}
