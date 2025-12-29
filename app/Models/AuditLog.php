<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'community_id',
        'actor_user_id',
        'event_key',
        'auditable_type',
        'auditable_id',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::updating(function ($model) {
            throw new \Exception('Audit logs are immutable.');
        });

        static::deleting(function ($model) {
            throw new \Exception('Audit logs cannot be deleted.');
        });
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Scope the query to only include logs visible to the given user.
     */
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole(['board_member', 'accountant'])) {
            return $query->where('community_id', $user->community_id);
        }

        // Residents and Providers can only see their own actions
        return $query->where('actor_user_id', $user->id);
    }
}
