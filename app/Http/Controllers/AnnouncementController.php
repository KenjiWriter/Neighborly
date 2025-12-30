<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Announcement::class);

        $user = $request->user();
        $query = Announcement::with('creator');

        // Scope by community
        if ($user->community_id) {
            $query->forCommunity($user->community_id);
        }

        // Residents see only published
        if ($user->hasRole('resident')) {
            $query->published();
        }

        $announcements = $query->latest()->paginate(10);

        return Inertia::render('Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $announcement->load('creator');

        return Inertia::render('Announcements/Show', [
            'announcement' => $announcement,
        ]);
    }
}
