<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status !== 'aktif' && !$request->routeIs('menunggu-persetujuan', 'logout', 'profile.*')) {
            return redirect()->route('menunggu-persetujuan');
        }

        return $next($request);
    }
}