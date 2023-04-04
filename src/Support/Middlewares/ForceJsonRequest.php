<?php

namespace Support\Middlewares;

use Illuminate\Http\Request;

class ForceJsonRequest
{
    public function handle(Request $request, \Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
