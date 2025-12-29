<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserVerificationController extends Controller
{
    public function index()
    {
        // Enforce Admin Policy/Role check here or via middleware in routes
        // $this->authorize('viewAny', User::class); // Assuming Policy exists or using Spatie middleware

        $pendingUsers = \App\Models\User::where('verification_status', 'pending')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return \Inertia\Inertia::render('Admin/Users/Pending', [
            'users' => $pendingUsers,
        ]);
    }

    public function approve(\App\Models\User $user)
    {
        // $this->authorize('update', $user);

        $user->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        // TODO: Send AccountApproved Email
        // Mail::to($user)->send(new \App\Mail\AccountApproved($user));

        return back()->with('success', 'User approved successfully.');
    }

    public function reject(\Illuminate\Http\Request $request, \App\Models\User $user)
    {
        // $this->authorize('update', $user);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'verification_status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['reason'] ?? null,
            'verified_at' => null,
        ]);

        // TODO: Send AccountRejected Email
        // Mail::to($user)->send(new \App\Mail\AccountRejected($user));

        return back()->with('success', 'User rejected.');
    }
}
