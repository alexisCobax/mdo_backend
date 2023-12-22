<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user();

        if ($user && $user->permisos == $permission) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        //abort(403, 'Unauthorized');
    }
}
