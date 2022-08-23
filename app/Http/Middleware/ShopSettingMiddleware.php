<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Object_;

class ShopSettingMiddleware
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
        $message = false;
        if (session()->has('message')) {
            $message = session('message');
        }

        $request->message = $message;

        return $next($request);
    }
}
