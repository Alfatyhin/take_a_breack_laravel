<?php


namespace App\Http\Middleware;


use App\Models\WebhookLog;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class IpBloked
{
    private $ips = [
        '5.188.62.76' => true,
    ];

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->getClientIp();

        $ips = $this->ips;

        if (isset($ips[$ip])) {
            WebhookLog::addLog("ip $ip blocked");
            dd("ip - $ip blocked");
        }

        return $next($request);
    }
}