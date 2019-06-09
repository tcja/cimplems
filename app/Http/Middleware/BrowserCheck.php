<?php

namespace App\Http\Middleware;

use Closure;
//use function GuzzleHttp\json_encode;
//use Illuminate\Http\Response;

class BrowserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (preg_match('#MSIE|Internet Explorer|Trident/7\.0; rv:11\.0#i', $request->server('HTTP_USER_AGENT'))) {
            if($request->ajax()) {
                return response()->json('updateBrowser');
            } else {
                return response()->view('update_browser');
            }
        } else {
            return $next($request);
        }
    }
}
