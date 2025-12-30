<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'community_id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'created_by_user_id',
        'audience_type',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function targetedRoles()
    {
        return $this->belongsToMany(Role::class, 'poll_role_visibility', 'poll_id', 'role_name', 'id', 'name');
    }

    public function targetedUnits()
    {
        return $this->belongsToMany(Unit::class, 'poll_unit_visibility');
    }

    public function targetedBuildings()
    {
        return $this->belongsToMany(Building::class, 'poll_building_visibility');
    }

    public function scopeActive($query)
    {
        return $query->where('starts_at', '<=', now())->where('ends_at', '>=', now());
    }

    public function scopeForCommunity($query, $communityId)
    {
        return $query->where('community_id', $communityId);
    }

    public function hasUserVoted($userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function scopeVisibleTo($query, User $user)
    {
        // Admin sees all
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Community scope
        $query->where('community_id', $user->community_id);

        // Only active polls for residents; board/admin can preview
        if (!$user->hasRole(['admin', 'board_member'])) {
            $query->active();
        }

        // Audience targeting
        $query->where(function ($q) use ($user) {
            // community_all: everyone in community (except building managers)
            if (!$user->hasRole('building_manager') || $user->hasRole(['resident', 'board_member', 'accountant', 'service_provider'])) {
                $q->orWhere('audience_type', 'community_all');
            }

            // residents_all: only residents
            if ($user->hasRole('resident')) {
                $q->orWhere('audience_type', 'residents_all');
            }

            // staff_all: board/accountant/provider
            if ($user->hasRole(['board_member', 'accountant', 'service_provider'])) {
                $q->orWhere('audience_type', 'staff_all');
            }

            // roles_selected: user must have one of targeted roles
            $q->orWhereHas('targetedRoles', function ($roleQuery) use ($user) {
                $roleQuery->whereIn('role_name', $user->getRoleNames());
            });

            // units_selected: user must belong to one of targeted units (residents)
            if ($user->hasRole('resident')) {
                $q->orWhereHas('targetedUnits', function ($unitQuery) use ($user) {
                    $unitQuery->whereIn('unit_id', $user->units->pluck('id'));
                });
            }

            // buildings_selected: user must belong to unit in one of targeted buildings (residents)
            if ($user->hasRole('resident')) {
                $q->orWhereHas('targetedBuildings', function ($buildingQuery) use ($user) {
                    $buildingQuery->whereIn('building_id', $user->units->pluck('building_id'));
                });
            }

            // building_manager: see polls for managed buildings
            if ($user->hasRole('building_manager')) {
                $q->orWhere(function ($managerQ) use ($user) {
                     $managerQ->where('audience_type', 'buildings_selected')
                              ->whereHas('targetedBuildings', function ($bq) use ($user) {
                                  $bq->whereIn('poll_building_visibility.building_id', $user->managedBuildings->pluck('id'));
                              });
                });
            }
        });

        return $query;
    }
}
