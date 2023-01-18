<?php


namespace App\Http\Middleware;


use App\Models\WebhookLog;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IpBloked
{

    public function handle(Request $request, Closure $next)
    {

        $ips = [];

        $file = "data/black_list_ip.json";
        if (Storage::exists($file)) {
            $black_list = Storage::get($file);
            $ips = json_decode($black_list, true);
        }

        $ip = $request->getClientIp();


        if (isset($ips[$ip])) {
            WebhookLog::addLog("ip $ip blocked");
            dd("ip - $ip blocked");
        }

        return $next($request);
    }
}