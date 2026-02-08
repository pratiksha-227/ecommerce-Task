<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * CMS = Admin only. Allow only authenticated users with is_admin = true.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please sign in to access the admin area.');
        }

        if (!auth()->user()->is_admin) {
            return redirect()->route('products.index')->with('error', 'Access denied. Admin only.');
        }

        return $next($request);
    }
}
