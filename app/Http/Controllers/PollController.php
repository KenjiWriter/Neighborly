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
        $query = Poll::with('options');

        // Scope by community
        if ($user->community_id) {
            $query->forCommunity($user->community_id);
        }

        // Residents see only active polls
        if ($user->hasRole('resident')) {
            $query->active();
        }

        $polls = $query->latest()->paginate(10);

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
