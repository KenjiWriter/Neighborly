<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserVerificationController extends Controller
{
    public function index()
    {
        $this->authorize('manage', \App\Models\User::class);

        $pendingUsers = \App\Models\User::where('verification_status', 'pending')
            ->with(['units.building.community'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return \Inertia\Inertia::render('Admin/Users/Pending', [
            'users' => $pendingUsers,
        ]);
    }

    public function approve(\App\Models\User $user)
    {
        $this->authorize('approve', $user);

        $user->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        // Audit log
        app(\App\Services\Audit\AuditLogger::class)->log(
            'users.approved',
            $user,
            ['user_id' => $user->id]
        );

        // TODO: Send AccountApproved Email
        // Mail::to($user)->send(new \App\Mail\AccountApproved($user));

        return back()->with('success', 'User approved successfully.');
    }

    public function reject(\Illuminate\Http\Request $request, \App\Models\User $user)
    {
        $this->authorize('reject', $user);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $reason = $validated['reason'] ? trim($validated['reason']) : null;

        $user->update([
            'verification_status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'verified_at' => null,
        ]);

        // Audit log
        app(\App\Services\Audit\AuditLogger::class)->log(
            'users.rejected',
            $user,
            [
                'user_id' => $user->id,
                'reason' => $reason,
            ]
        );

        // TODO: Send AccountRejected Email
        // Mail::to($user)->send(new \App\Mail\AccountRejected($user));

        return back()->with('success', 'User rejected.');
    }
}
