<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dynamic role middleware.
 *
 * Usage: ->middleware('role:admin') or ->middleware('role:moderator,faculty')
 *
 * The role is read from the authenticated user record on the server. Nothing
 * sent by the browser (query string, form field, header, cookie) is consulted,
 * so a client cannot grant itself access by editing a request.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Guests never reach a role check: send them through the normal login flow.
        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('login'));
        }

        $allowed = array_values(array_filter(array_map('trim', $roles), fn ($r) => $r !== ''));

        if (! in_array($user->role, $allowed, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your role cannot perform this action.',
                    'required_roles' => $allowed,
                    'your_role' => $user->role,
                ], 403);
            }

            abort(403, 'Your role cannot open this page.');
        }

        return $next($request);
    }
}
