<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
  public function handle(Request $request, Closure $next, string $role)
{
    if (!Auth::check()) {
        abort(403, 'Unauthorized - Not logged in');
    }

    $user = Auth::user();

    // Debugging: cek apakah relasi 'role' berhasil, dan nilai name-nya
    if (!$user->role) {
        abort(403, 'Unauthorized - User has no role');
    }

    // Log untuk memastikan nilai role
    logger('User role:', [
        'expected' => $role,
        'actual' => $user->role->name,
        'user_id' => $user->id
    ]);

    if (strtolower($user->role->name) !== strtolower($role)) {
        abort(403, 'Unauthorized - Role mismatch. You are: ' . $user->role->name);
    }

    return $next($request);
}

}
