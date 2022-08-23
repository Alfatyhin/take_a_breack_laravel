<?php


namespace App\Services;


class SendpulseService
{

    public function __construct()
    {
    }

    public static function sendLostCart(array $data)
    {
        $url = 'https://events.sendpulse.com/events/id/9ec0818a41cd4b3b82b3b7031e3d5ab6/7922200';

        $request_data = [
            'url' => $url,
            'method' => "POST",
            'headers' => [
                'Content-Type2' => 'text/plain'
            ],
            'data' => $data
        ];

        $ClientRequest = new ClientRequestService($request_data);

        $res = $ClientRequest->request();

        return $res;
    }
}
