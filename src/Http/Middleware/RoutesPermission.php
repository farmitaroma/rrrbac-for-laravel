<?php

namespace Farmit\RrrbacForLaravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoutesPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route_name = $request->route()?->getName();

        if (in_array($route_name, config('rrrbac.authorized_routes'), true)) {
            return $next($request);
        }

        if ($request->user()->cannot("route::$route_name")) {
            abort(403);
        }

        return $next($request);
    }
}
