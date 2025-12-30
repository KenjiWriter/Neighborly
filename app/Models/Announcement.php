<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Announcement extends Model
{
    protected $fillable = [
        'community_id',
        'title',
        'body',
        'published_at',
        'created_by_user_id',
        'audience_type',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function targetedRoles()
    {
        return $this->belongsToMany(SpatieRole::class, 'announcement_role_visibility', 'announcement_id', 'role_name', 'id', 'name');
    }

    public function targetedUnits()
    {
        return $this->belongsToMany(Unit::class, 'announcement_unit_visibility');
    }

    public function targetedBuildings()
    {
        return $this->belongsToMany(Building::class, 'announcement_building_visibility');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeForCommunity($query, $communityId)
    {
        return $query->where('community_id', $communityId);
    }

    public function scopeVisibleTo($query, User $user)
    {
        // Admin sees all
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Community scope
        $query->where('community_id', $user->community_id);

        // Only published for non-creators
        if (!$user->hasRole(['admin', 'board_member'])) {
            $query->published();
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

            // building_manager: see announcements for managed buildings
            if ($user->hasRole('building_manager')) {
                $q->orWhere(function ($managerQ) use ($user) {
                     $managerQ->where('audience_type', 'buildings_selected')
                              ->whereHas('targetedBuildings', function ($bq) use ($user) {
                                  $bq->whereIn('announcement_building_visibility.building_id', $user->managedBuildings->pluck('id'));
                              });
                });
            }
        });

        return $query;
    }
}
