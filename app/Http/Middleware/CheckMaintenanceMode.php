<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = \App\Models\Setting::get('maintenance_mode', '0');

        if ($maintenanceMode === '1') {
            // Allow admins to bypass
            if ($request->user() && $request->user()->isAdmin()) {
                return $next($request);
            }

            // Allow login, logout, and related auth routes to bypass
            // This is critical so admins can log in to fix it
            $excludedRoutes = [
                'home',
                'login',
                'logout',
                'password.request',
                'password.email',
                'password.reset',
                'password.update',
                'password.confirm',
                'user-password.update',
                'verification.notice',
                'verification.verify',
                'verification.send',
                'verification.resend'
            ];

            if ($request->route() && in_array($request->route()->getName(), $excludedRoutes)) {
                return $next($request);
            }

            // Allow static assets, images, and the login path
            if ($request->is('login', 'css/*', 'js/*', 'images/*', 'storage/*', 'favicon.ico')) {
                return $next($request);
            }

            abort(503, 'The system is currently undergoing maintenance. Please check back later.');
        }

        return $next($request);
    }
}
