<?php


namespace App\Services;


use AmoCRM\OAuth\OAuthServiceInterface;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OAuthService implements OAuthServiceInterface
{

    public function saveOAuthToken(AccessTokenInterface $accessToken, string $baseDomain): void
    {

        var_dump($accessToken->hasExpired());
        if ($accessToken->hasExpired()) {
           var_dump('refresh token');
            $data = [
                'access_token'  => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires'       => $accessToken->getExpires(),
                'baseDomain'    => $baseDomain,
            ];

            Storage::disk('local')->put('data/amo-assets.json', json_encode($data));
        } else {
            $data = [
                'access_token'  => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires'       => $accessToken->getExpires(),
                'baseDomain'    => $baseDomain,
            ];

            Storage::disk('local')->put('data/amo-assets.json', json_encode($data));

            var_dump('not save token');
        }

    }
}
