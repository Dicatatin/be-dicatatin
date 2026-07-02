<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * Allows the request only if the authenticated user has the 'admin' role.
     * Returns a standard JSON error response on 401/403 to maintain API consistency.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guard: user must be authenticated first
        if (! $request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid Bearer token.',
                'data'    => null,
                'errors'  => ['auth' => ['Token tidak ditemukan atau tidak valid.']],
            ], 401);
        }

        // Guard: user must have the admin role
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Anda tidak memiliki akses sebagai admin.',
                'data'    => null,
                'errors'  => ['auth' => ['Akses ditolak. Role admin diperlukan.']],
            ], 403);
        }

        return $next($request);
    }
}
