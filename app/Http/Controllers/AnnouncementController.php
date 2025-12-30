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
        $announcements = Announcement::visibleTo($user)
            ->with('creator')
            ->latest()
            ->paginate(10);

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

    public function create()
    {
        $this->authorize('create', Announcement::class);

        return Inertia::render('Announcements/Create', [
            'roles' => \Spatie\Permission\Models\Role::pluck('name'),
            'buildings' => \App\Models\Building::forCommunity(auth()->user()->community_id)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'audience_type' => 'required|in:community_all,residents_all,staff_all,roles_selected,units_selected,buildings_selected',
            'roles_selected' => 'required_if:audience_type,roles_selected|array',
            'buildings_selected' => 'required_if:audience_type,buildings_selected|array',
            'units_selected' => 'required_if:audience_type,units_selected|array',
            'published' => 'boolean',
        ]);

        $announcement = Announcement::create([
            'community_id' => $request->user()->community_id,
            'created_by_user_id' => $request->user()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'audience_type' => $validated['audience_type'],
            'published_at' => $request->boolean('published') ? now() : null,
        ]);

        $this->syncTargeting($announcement, $validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement created.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->load(['targetedRoles', 'targetedBuildings', 'targetedUnits']);

        return Inertia::render('Announcements/Edit', [
            'announcement' => $announcement,
            'roles' => \Spatie\Permission\Models\Role::pluck('name'),
            'buildings' => \App\Models\Building::forCommunity(auth()->user()->community_id)->get(['id', 'name']),
            'targeted_roles' => $announcement->targetedRoles->pluck('name'),
            'targeted_buildings' => $announcement->targetedBuildings->pluck('id'),
            // 'targeted_units' => $announcement->targetedUnits->pluck('id'), // If implementing unit selection UI
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'audience_type' => 'required|in:community_all,residents_all,staff_all,roles_selected,units_selected,buildings_selected',
            'roles_selected' => 'required_if:audience_type,roles_selected|array',
            'buildings_selected' => 'required_if:audience_type,buildings_selected|array',
            'units_selected' => 'required_if:audience_type,units_selected|array',
            'published' => 'boolean',
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'audience_type' => $validated['audience_type'],
            'published_at' => $request->boolean('published') ? ($announcement->published_at ?? now()) : null,
        ]);

        $this->syncTargeting($announcement, $validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated.');
    }

    protected function syncTargeting(Announcement $announcement, array $data)
    {
        if ($data['audience_type'] === 'roles_selected') {
            $announcement->targetedRoles()->sync(\Spatie\Permission\Models\Role::whereIn('name', $data['roles_selected'])->pluck('id'));
        } else {
            $announcement->targetedRoles()->detach();
        }

        if ($data['audience_type'] === 'buildings_selected') {
            $announcement->targetedBuildings()->sync($data['buildings_selected']);
        } else {
            $announcement->targetedBuildings()->detach();
        }

        // Simplification: Units often selected by building, but if direct unit selection implemented:
        if ($data['audience_type'] === 'units_selected' && isset($data['units_selected'])) {
             $announcement->targetedUnits()->sync($data['units_selected']);
        } else {
            $announcement->targetedUnits()->detach();
        }
    }
}
