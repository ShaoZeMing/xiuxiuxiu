<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HashIdsDecrypt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $ids =  $request->route()->parameters;
        if(count($ids)){
            foreach($ids as $k => &$v){
                $v = hashidsDecode($v);
            }
            $request->route()->parameters = $ids;
        };
        return $next($request);
    }
}
