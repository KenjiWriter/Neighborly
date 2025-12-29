<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not logged in, pass through (let auth middleware handle it)
        if (! $user) {
             return $next($request);
        }

        // If user is approved (or super admin?), allow
        // Assuming "approved" status covers admin too, if seeded correctly.
        if ($user->verification_status === 'approved') {
            return $next($request);
        }

        // Allow access to status pages and logout/profile routes to prevent loop
        // Also "account.status", "verification.notice" etc.
        if ($request->routeIs('account.status', 'account.pending', 'account.rejected', 'logout', 'profile.*', 'locale.switch')) {
             return $next($request);
        }

        // Redirect based on status
        if ($user->verification_status === 'rejected') {
            return redirect()->route('account.rejected');
        }

        // Default to pending
        return redirect()->route('account.pending');
    }
}
