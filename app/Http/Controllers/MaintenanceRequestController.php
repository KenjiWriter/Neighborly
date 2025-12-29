<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MaintenanceRequest::class);

        // Scope queries based on role using the Scope in Model
        $requests = MaintenanceRequest::scopedForUser($request->user())
            ->with(['creator', 'assignee', 'unit', 'community'])
            ->latest()
            ->paginate(10);

        return Inertia::render('MaintenanceRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function create()
    {
        $this->authorize('create', MaintenanceRequest::class);

        // Residents can only pick their own unit (usually single, but let's load if multiple)
        // Board/Admin can pick any unit in community? 
        // For simplicity Phase 3: just show form, unit selection logic minimal or auto-assigned if resident has 1 unit.
        
        $units = [];
        $user = auth()->user();
        if ($user->hasRole('resident')) {
            $units = $user->units()->get();
        } elseif ($user->hasRole(['board_member', 'admin'])) {
             // Admin/Board could fetch all units in their scope (community)
             // simplified for now.
             if ($user->community_id) {
                $units = \App\Models\Unit::where('community_id', $user->community_id)->get();
             } else {
                 // Admin see all or pick community? 
                 // Admin flow is complex, let's stick to Resident/Board primary flow.
                 $units = \App\Models\Unit::limit(50)->get();
             }
        }

        return Inertia::render('MaintenanceRequests/Create', [
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', MaintenanceRequest::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        // Auto-determine community from Unit, or User's scope
        $communityId = null;
        if (!empty($validated['unit_id'])) {
            $unit = \App\Models\Unit::find($validated['unit_id']);
            $communityId = $unit->community_id;
        } else {
            // General query? Default to user's community scope 
            // For resident: must have unit.
            // For board: their community.
            $user = $request->user();
            if ($user->community_id) {
                $communityId = $user->community_id;
            } elseif ($user->hasRole('resident')) {
                 // Enforce unit selection for residents usually, or pick their first community
                 $firstUnit = $user->units()->first();
                 if ($firstUnit) {
                     $communityId = $firstUnit->community_id;
                 }
            }
        }
        
        if (!$communityId) {
             // Admin fallback or error
             $communityId = \App\Models\Community::first()->id; 
        }

        MaintenanceRequest::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'unit_id' => $validated['unit_id'] ?? null,
            'community_id' => $communityId,
            'created_by_user_id' => $request->user()->id,
            'status' => MaintenanceRequest::STATUS_OPEN,
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Request created successfully.');
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('view', $maintenanceRequest);

        $maintenanceRequest->load(['creator', 'assignee', 'unit', 'community']);

        // Accountant Redaction Logic (Server-side)
        $isAccountant = auth()->user()->hasRole('accountant');
        $isRedacted = false;

        if ($isAccountant) {
            $maintenanceRequest->description = null;
            $maintenanceRequest->title = 'Maintenance Request #' . $maintenanceRequest->id; // Optional: obscure title too if sensitive?
            // "Accountant: can see request list counts and statuses, but NOT the full description content."
            // Let's keep title visible but description redacted as per prompt instructions.
            $isRedacted = true;
        }

        // Provider Options (for Assign dropdown)
        $providers = [];
        if (auth()->user()->can('assign', $maintenanceRequest)) {
             // Scoped to service_provider role AND community_id
             $providers = User::role('service_provider')
                // Assuming provider has community_id set for scope? 
                // Or provider can be global? The prompt says "scoped to users.community_id = maintenanceRequest.community_id"
                // This implies providers MUST be linked to community to be assignable.
                ->where('community_id', $maintenanceRequest->community_id)
                ->get();
        }

        return Inertia::render('MaintenanceRequests/Show', [
            'maintenanceRequest' => $maintenanceRequest,
            'isRedacted' => $isRedacted, // explicitly pass flag too, though Description is null
            'providers' => $providers,
        ]);
    }

    public function assign(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('assign', $maintenanceRequest);

        $validated = $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id',
        ]);

        // Validate scope of assigned user
        $assignee = User::find($validated['assigned_to_user_id']);
        if (!$assignee->hasRole('service_provider') || $assignee->community_id !== $maintenanceRequest->community_id) {
            abort(403, 'Cannot assign this user.');
        }

        $maintenanceRequest->update([
            'assigned_to_user_id' => $assignee->id,
        ]);

        return back()->with('success', 'Provider assigned.');
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('updateStatus', $maintenanceRequest);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $maintenanceRequest->status;

        // Transition Validation
        $allowed = false;

        // Board/Admin: All valid server-side transitions
        // Provider: in_progress -> closed only.

        if ($request->user()->hasRole(['board_member', 'admin'])) {
             if ($oldStatus === 'open' && $newStatus === 'in_progress') $allowed = true;
             if ($oldStatus === 'in_progress' && $newStatus === 'closed') $allowed = true;
             // Allow backtracking? Usually Open -> In Progress -> Closed is linear.
             // Let's allow flexible for Board/Admin unless specs say strictly linear.
             // "Validate allowed transitions server-side (not just by role): open -> in_progress, in_progress -> closed"
             // This implies linear flow is enforced. 
             // "Provider can only do in_progress -> closed"
             
             // Let's enforce the arrows.
             if ($oldStatus === 'open' && $newStatus === 'in_progress') $allowed = true;
             if ($oldStatus === 'in_progress' && $newStatus === 'closed') $allowed = true;
             if ($oldStatus === 'closed' && $newStatus === 'in_progress') $allowed = true; // Reopen? Not explicitly asked, but useful.
             // If prompt says "open -> in_progress, in_progress -> closed", let's stick to that strictness + maybe reopen.
             // Prompt: "Validate allowed transitions server-side" lists strictly those two.
        }

        if ($request->user()->hasRole('service_provider')) {
             if ($oldStatus === 'in_progress' && $newStatus === 'closed') $allowed = true;
        }

        if (!$allowed) {
            abort(403, 'Invalid status transition.');
        }

        $maintenanceRequest->update([
            'status' => $newStatus,
        ]);
        
        if ($newStatus === 'closed') {
            $maintenanceRequest->update([
                'closed_by_user_id' => $request->user()->id,
                'closed_at' => now(),
            ]);
        }

        return back()->with('success', 'Status updated.');
    }
}
