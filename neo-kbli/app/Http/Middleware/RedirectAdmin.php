<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasAnyRole(['Super Admin', 'super-admin', 'Admin', 'Administrator', 'Operator'])) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
