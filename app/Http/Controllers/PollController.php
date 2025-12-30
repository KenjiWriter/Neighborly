<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PollController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Poll::class);

        $user = $request->user();
        $polls = Poll::visibleTo($user)
            ->with('options')
            ->latest()
            ->paginate(10);

        // Check if user has voted on each poll
        $polls->getCollection()->transform(function ($poll) use ($user) {
            $poll->user_has_voted = $poll->hasUserVoted($user->id);
            return $poll;
        });

        return Inertia::render('Polls/Index', [
            'polls' => $polls,
        ]);
    }

    public function show(Poll $poll)
    {
        $this->authorize('view', $poll);

        $poll->load('options');
        $poll->user_has_voted = $poll->hasUserVoted(auth()->id());

        return Inertia::render('Polls/Show', [
            'poll' => $poll,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Poll::class);

        return Inertia::render('Polls/Create', [
            'roles' => \Spatie\Permission\Models\Role::pluck('name'),
            'buildings' => \App\Models\Building::forCommunity(auth()->user()->community_id)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Poll::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
            'audience_type' => 'required|in:community_all,residents_all,staff_all,roles_selected,units_selected,buildings_selected',
            'roles_selected' => 'required_if:audience_type,roles_selected|array',
            'buildings_selected' => 'required_if:audience_type,buildings_selected|array',
            'units_selected' => 'required_if:audience_type,units_selected|array',
        ]);

        $poll = Poll::create([
            'community_id' => $request->user()->community_id,
            'created_by_user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'audience_type' => $validated['audience_type'],
        ]);

        foreach ($validated['options'] as $optionLabel) {
            $poll->options()->create(['label' => $optionLabel]);
        }

        $this->syncTargeting($poll, $validated);

        return redirect()->route('polls.index')->with('success', 'Poll created.');
    }

    public function edit(Poll $poll)
    {
        $this->authorize('update', $poll);

        $poll->load(['options', 'targetedRoles', 'targetedBuildings']);

        return Inertia::render('Polls/Edit', [
            'poll' => $poll,
            'roles' => \Spatie\Permission\Models\Role::pluck('name'),
            'buildings' => \App\Models\Building::forCommunity(auth()->user()->community_id)->get(['id', 'name']),
            'targeted_roles' => $poll->targetedRoles->pluck('name'),
            'targeted_buildings' => $poll->targetedBuildings->pluck('id'),
        ]);
    }

    public function update(Request $request, Poll $poll)
    {
        $this->authorize('update', $poll);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'audience_type' => 'required|in:community_all,residents_all,staff_all,roles_selected,units_selected,buildings_selected',
            'roles_selected' => 'required_if:audience_type,roles_selected|array',
            'buildings_selected' => 'required_if:audience_type,buildings_selected|array',
            'units_selected' => 'required_if:audience_type,units_selected|array',
        ]);

        $poll->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'audience_type' => $validated['audience_type'],
        ]);

        // Note: Options editing is complex if votes exist. For now, let's assume options cannot be changed after creation or handle it separately.
        // Or strictly strictly only allow changing meta.

        $this->syncTargeting($poll, $validated);

        return redirect()->route('polls.index')->with('success', 'Poll updated.');
    }

    protected function syncTargeting(Poll $poll, array $data)
    {
        if ($data['audience_type'] === 'roles_selected') {
            $poll->targetedRoles()->sync(\Spatie\Permission\Models\Role::whereIn('name', $data['roles_selected'])->pluck('id'));
        } else {
            $poll->targetedRoles()->detach();
        }

        if ($data['audience_type'] === 'buildings_selected') {
            $poll->targetedBuildings()->sync($data['buildings_selected']);
        } else {
            $poll->targetedBuildings()->detach();
        }

         if ($data['audience_type'] === 'units_selected' && isset($data['units_selected'])) {
             $poll->targetedUnits()->sync($data['units_selected']);
        } else {
            $poll->targetedUnits()->detach();
        }
    }

    public function vote(Request $request, Poll $poll)
    {
        $this->authorize('vote', $poll);

        $validated = $request->validate([
            'poll_option_id' => 'required|exists:poll_options,id',
        ]);

        // Verify option belongs to this poll
        $option = $poll->options()->findOrFail($validated['poll_option_id']);

        PollVote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => $request->user()->id,
            'created_at' => now(),
        ]);

        return redirect()->route('polls.show', $poll)->with('success', 'Vote recorded successfully.');
    }
}
