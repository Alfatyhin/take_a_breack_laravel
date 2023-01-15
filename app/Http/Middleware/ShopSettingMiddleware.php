<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
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
            $date_nau = new Carbon();
            $monolog = Storage::disk('logs')->get("laravel.log");
            preg_match_all('/\['.$date_nau->format('Y').'/',  $monolog, $matches);

            $error_log = sizeof($matches[0]);
        }

        $request->message = $message;
        $request->error_log = $error_log;

        return $next($request);
    }
}
