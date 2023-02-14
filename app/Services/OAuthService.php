<?php


namespace App\Services;


use AmoCRM\OAuth\OAuthServiceInterface;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OAuthService implements OAuthServiceInterface
{

    public function saveOAuthToken(AccessTokenInterface $accessToken, string $baseDomain): void
    {

        if ($accessToken->hasExpired()) {
            $data = [
                'access_token'  => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires'       => $accessToken->getExpires(),
                'baseDomain'    => $baseDomain,
            ];

            WebhookLog::addLog('AMO CRM token - ', 'save');
            Storage::disk('local')->put('data/amo-assets.json', json_encode($data));
        } else {

            WebhookLog::addLog('AMO CRM token - ', 'not save');

        }

    }
}
