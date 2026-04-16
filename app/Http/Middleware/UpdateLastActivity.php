<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Update only if older than 1 minute to reduce writes
            $last = $user->last_activity_at;
            if (! $last || $last->diffInSeconds(now()) > 60) {
                $user->last_activity_at = now();
                $user->saveQuietly();
            }
        }

        return $next($request);
    }
}
