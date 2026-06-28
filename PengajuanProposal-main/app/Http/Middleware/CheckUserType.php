<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$userTypes)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        if (!in_array($user->role, $userTypes)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
