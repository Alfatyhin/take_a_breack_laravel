<?php


namespace App\Services;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Shopify\Clients\Graphql;
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


    public function getClients()
    {

        $reguest['query'] = 'query {
    customers(first: 10) {
      edges {
        node {
          id
        }
      }
    }
  }';
        return $this->getGraphql($reguest);
    }


    public function getOrderById($id)
    {

        $reguest['query'] = 'query {
     order(id: "gid://shopify/Order/'.$id.'") {
        note
    }
  }';
        return $this->getGraphql($reguest);
    }


    public function setOrderNote($id)
    {

        $reguest['query'] = 'query {
     order(id: "gid://shopify/Order/'.$id.'") {
        note
    }
  }';
        return $this->getGraphql($reguest);
    }

    public function createClient()
    {
        $query = 'mutation customerCreate($input: CustomerInput!) 
        { customerCreate(input: $input) 
        { userErrors { field message }
         customer { id email phone taxExempt acceptsMarketing firstName lastName ordersCount 
         totalSpent smsMarketingConsent { marketingState marketingOptInLevel } 
         addresses { address1 city country phone zip } } } }';


        $variables['input'] = [
            "email" => "steve.lastnameson@example.com",
            "phone" => "+16465555555",
            "firstName" => "Steve",
            "lastName" => "Lastname",
            "acceptsMarketing" => true,
            'addresses' => [
                [
                    "address1" => "412 fake st",
                    "city" => "Ottawa",
                    "province" => "ON",
                    "phone" => "+16469999999",
                    "zip" => "A1A 4A1",
                    "lastName" => "Lastname",
                    "firstName" => "Steve",
                    "country" => "CA",
                ]
            ]
        ];

        $reguest['query'] = $query;
        $reguest['variables'] = [$variables];


        dd($reguest);


    }

    public function getGraphql($query)
    {
        $url = "https://$this->shop/admin/api/2023-04/graphql.json";

        $response = Http::withHeaders($this->headers)->acceptJson()->post($url, $query);

        return json_decode($response->body(), true);
    }


}