<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // If no specific roles required, just need to be authenticated (which auth middleware already does)
        // But for admin dashboard, we might want to check if they have at least one of the roles
        if (empty($roles)) {
            return $next($request);
        }

        // Super Admin or Admin always bypass role check if accessing admin area
        if ($request->user()->hasAnyRole(['super-admin', 'superadmin', 'Super Admin', 'admin', 'administrator', 'Administrator'])) {
            return $next($request);
        }

        foreach ($roles as $role) {
            // Handle comma-separated role strings if passed as a single argument
            $subRoles = explode(',', $role);
            foreach ($subRoles as $r) {
                $r = trim($r);
                if ($request->user()->hasRole($r)) {
                    return $next($request);
                }
            }
        }

        // If they don't have the required role, abort with 403
        abort(403, 'Aksi tidak diizinkan.');
    }
}
