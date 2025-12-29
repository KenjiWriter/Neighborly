<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user() ? $request->user()->getRoleNames() : [],
                'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
                'can' => [
                    'viewCommunityPrimary' => $request->user() && ! $request->user()->hasRole('service_provider'),
                    'viewMaintenanceRequests' => $request->user() && $request->user()->can('viewAny', \App\Models\MaintenanceRequest::class),
                    'createMaintenanceRequest' => $request->user() && $request->user()->can('create', \App\Models\MaintenanceRequest::class),
                    // For specific item actions, we usually check on the object, but for global UI elements (like "can I see the assign dropdown generally?"), 
                    // we might need a generic check or just rely on the specific object in the Show page. 
                    // However, 'assign' policy needs an instance usually. 
                    // Let's rely on $page.props.auth.user.roles for general UI hiding if needed, or just specific Checks in Show.vue.
                    // But Plan says "Share capabilities `auth.can.manageMaintenance` etc". 
                    // Let's add generic role-based flags or dummy instance checks if policy forces it.
                    // Actually, generic check `can('create', ...)` works. `can('viewAny')` works. 
                    // For `assign`, it depends on community. 
                    // Let's stick to View/Create for global nav/buttons. detailed actions will be checked in the component using the prop passed from controller.
                ],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => app()->getLocale(),
            'translations' => function () {
                return [
                    'app' => trans('app'),
                ];
            },
        ];
    }
}
