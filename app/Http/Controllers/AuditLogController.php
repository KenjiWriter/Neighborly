<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', \App\Models\AuditLog::class);

        $query = \App\Models\AuditLog::visibleTo($request->user())
            ->with('actor') // Eager load actor
            ->latest();

        // Optional Filters
        if ($request->filled('event_key')) {
            $query->where('event_key', $request->input('event_key'));
        }

        $logs = $query->paginate(20)->withQueryString();

        // Transform logs for frontend (Sanitize IP/UA for non-admins if needed, though policy handles rows)
        // Prompt says: "Do NOT display IP/user-agent for Resident/Provider"
        // We can handle this via API Resource or map here.
        
        $logs->through(function ($log) use ($request) {
            $user = $request->user();
            $hideSensitiveCtx = $user->hasRole(['resident', 'service_provider']);

            return [
                'id' => $log->id,
                'created_at' => $log->created_at,
                'event_key' => $log->event_key,
                'actor' => $log->actor ? [
                    'id' => $log->actor->id,
                    'name' => $log->actor->name,
                ] : null,
                'metadata' => $log->metadata, // Already sanitized by Logger
                // Hide IP/UA for regular users
                'ip_address' => $hideSensitiveCtx ? null : $log->ip_address,
                'user_agent' => $hideSensitiveCtx ? null : $log->user_agent,
                'auditable_type' => $log->auditable_type,
                'auditable_id' => $log->auditable_id,
            ];
        });

        return \Inertia\Inertia::render('Audit/Index', [
            'logs' => $logs,
            'filters' => $request->only(['event_key']),
        ]);
    }
}
