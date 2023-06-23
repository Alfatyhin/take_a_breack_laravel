<?php


namespace App\Services;


use Google\Client;
use Illuminate\Support\Facades\Storage;

class GClientService
{
    private $client;

    public function __construct()
    {
        $path = Storage::path('gapi/gdocs.json');
        $client = new Client();
        $client->setAuthConfig($path);

        $this->client = $client;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }
    public function getClient()
    {
        return $this->client;
    }
}