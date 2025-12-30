<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manage', User::class);

        $query = User::with(['roles', 'community'])
            ->latest();

        // Optional filters
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->input('role'));
            });
        }

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        $users = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'status']),
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $user->load(['roles', 'community', 'units.building.community']);

        $roles = [
            ['value' => 'resident', 'label' => 'Resident'],
            ['value' => 'board_member', 'label' => 'Board Member'],
            ['value' => 'accountant', 'label' => 'Accountant'],
            ['value' => 'service_provider', 'label' => 'Service Provider'],
            ['value' => 'admin', 'label' => 'Admin'],
        ];

        $communities = Community::select('id', 'name')->get();

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'communities' => $communities,
            'currentRole' => $user->roles->first()?->name,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => 'required|in:resident,board_member,accountant,service_provider,admin',
            'community_id' => 'nullable|exists:communities,id',
        ]);

        $role = $validated['role'];
        $communityId = $validated['community_id'];

        // Enforce community_id requirement for staff roles
        if (in_array($role, ['board_member', 'accountant', 'service_provider'])) {
            if (!$communityId) {
                return back()->withErrors(['community_id' => 'Community is required for this role.']);
            }
        }

        // Sync role (Spatie)
        $user->syncRoles([$role]);

        // Update community_id
        // For resident: community_id is nullable (they use unit pivot)
        // For staff: community_id is required
        // For admin: community_id can be null (global scope)
        if (in_array($role, ['board_member', 'accountant', 'service_provider', 'admin'])) {
            $user->update(['community_id' => $communityId]);
        } elseif ($role === 'resident') {
            // Resident uses unit pivot, so community_id can be null or inferred
            $user->update(['community_id' => null]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
}
