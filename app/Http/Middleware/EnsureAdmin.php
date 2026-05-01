<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $usuario = Auth::user();
        
        // Verificar que sea admin (permisos >= 2 o permisos == 99)
        if (!($usuario->permisos >= 2 || $usuario->permisos == 99)) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'No tienes permisos de administrador']);
        }

        return $next($request);
    }
}

