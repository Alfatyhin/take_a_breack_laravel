<?php


namespace App\Services;


use AmoCRM\OAuth\OAuthConfigInterface;

class OAuthConfig implements OAuthConfigInterface
{

    public function getIntegrationId(): string
    {
        return $_ENV['AMO_CLIENT_ID'];
    }

    public function getSecretKey(): string
    {
        return $_ENV['AMO_CLIENT_SECRET'];
    }

    public function getRedirectDomain(): string
    {
        return $_ENV['AMO_CLIENT_REDIRECT_URI'];
    }
}
