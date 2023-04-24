<?php


namespace App\Services;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Shopify\Clients\Rest;

class ShopifyClient
{

    private $shop = 'takeabreak-2174.myshopify.com';
    private $v = '2023-01';
    private $token;
    private $headers;

    public function __construct()
    {
        $this->token = env('SHOPIFY_TOKEN');
        $this->headers = [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => $this->token
        ];
    }

    public function get($path)
    {
        $url = "https://$this->shop/admin/api/$this->v/$path.json";

        $response = Http::withHeaders($this->headers)->acceptJson()->get($url);

        return json_decode($response->body(), true);
    }
}