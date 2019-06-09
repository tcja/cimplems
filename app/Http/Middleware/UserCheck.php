<?php

namespace App\Http\Middleware;

use Closure;

class UserCheck
{
    /**
     * Handle an incoming request and checks if the request comes from an admin or not
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session('admin') === true) {
            return $next($request);
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
