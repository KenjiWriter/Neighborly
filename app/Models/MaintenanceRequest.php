<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'community_id',
        'unit_id',
        'title',
        'description',
        'status',
        'created_by_user_id',
        'assigned_to_user_id',
        'closed_by_user_id',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_CLOSED = 'closed';

    public function community(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function closer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function scopeScopedForUser($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('service_provider')) {
            return $query->where('assigned_to_user_id', $user->id);
        }

        if ($user->hasRole(['board_member', 'accountant'])) {
            return $query->where('community_id', $user->community_id);
        }

        if ($user->hasRole('resident')) {
            return $query->where('created_by_user_id', $user->id);
        }

        return $query->whereRaw('0 = 1'); // Deny by default
    }
}
