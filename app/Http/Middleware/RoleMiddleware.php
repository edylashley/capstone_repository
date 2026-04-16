<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        // Normalize roles: support single comma-separated param or multiple params
        $normalized = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $part = strtolower(trim($part));
                if ($part !== '') {
                    $normalized[] = $part;
                }
            }
        }

        // Admins can bypass most role checks for convenience
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (count($normalized) > 0 && ! in_array(strtolower($user->role), $normalized, true)) {
            abort(403, 'Unauthorized. This page is reserved for: ' . implode(', ', $normalized));
        }

        return $next($request);
    }
}
