<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\Community;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log an event to the audit table.
     *
     * @param string $eventKey
     * @param Model|null $auditable
     * @param array $metadata
     * @param Community|null $community
     * @return AuditLog
     */
    public function log(string $eventKey, ?Model $auditable = null, array $metadata = [], ?Community $community = null): AuditLog
    {
        $user = Auth::user();

        // Attempt to resolve community_id
        $communityId = null;
        if ($community) {
            $communityId = $community->id;
        } elseif ($auditable && method_exists($auditable, 'community')) {
             // If auditable has community() relation, use it.
             // But we need to be careful not to trigger lazy loading if not needed, 
             // though accessing community_id directly is preferred if it exists as column.
             if (isset($auditable->community_id)) {
                 $communityId = $auditable->community_id;
             }
        } elseif ($user && $user->community_id) {
            // Fallback to user's community for context, 
            // though strictly speaking some actions might be cross-community (admin).
            // For now, let's keep it null if not explicit.
             if ($user->hasRole(['board_member', 'accountant', 'resident'])) {
                 $communityId = $user->community_id;
             }
        }

        // Sanitize Metadata
        $safeMetadata = $this->sanitizeMetadata($eventKey, $metadata);

        return AuditLog::create([
            'community_id' => $communityId,
            'actor_user_id' => $user?->id,
            'event_key' => $eventKey,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'metadata' => $safeMetadata,
            'ip_address' => Request::ip(),
            'user_agent' => substr(Request::userAgent() ?? '', 0, 65535), // Text column limit
            'created_at' => now(),
        ]);
    }

    /**
     * Sanitize metadata based on allowlist per event key.
     */
    protected function sanitizeMetadata(string $eventKey, array $input): array
    {
        // Define allowlists for known events
        $allowlists = [
            'maintenance.created' => ['maintenance_request_id', 'community_id', 'unit_id', 'status'],
            'maintenance.assigned' => ['maintenance_request_id', 'assigned_to_user_id'],
            'maintenance.status_changed' => ['maintenance_request_id', 'from_status', 'to_status'],
            'finance.entry_created' => ['finance_entry_id', 'type', 'amount_cents', 'category', 'occurred_on'],
            'documents.uploaded' => ['document_id', 'original_name', 'size_bytes', 'mime_type'],
            'documents.downloaded' => ['document_id'],
        ];

        if (!array_key_exists($eventKey, $allowlists)) {
            // Default: allow nothing or very basic keys if we want to be safe
            // Or log everything if we trust the caller? 
            // Instructions say "sanitize metadata using an allowlist".
            // So default is empty or strictly safe keys.
            return array_intersect_key($input, array_flip(['id', 'status'])); 
        }

        return array_intersect_key($input, array_flip($allowlists[$eventKey]));
    }
}
