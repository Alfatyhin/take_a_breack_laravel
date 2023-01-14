<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $error_log= false;
        if (session()->has('message')) {
            $message = session('message');
        }

        if (Storage::disk('logs')->exists("laravel.log")) {
            $error_log = true;
        }

        $request->message = $message;
        $request->error_log = $error_log;

        return $next($request);
    }
}
