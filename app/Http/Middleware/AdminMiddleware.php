<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->is_admin) {
            return inertia('Forbidden')->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}