<?php


namespace App\Services;


use AmoCRM\OAuth\OAuthServiceInterface;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OAuthService implements OAuthServiceInterface
{

    public function saveOAuthToken(AccessTokenInterface $accessToken, string $baseDomain): void
    {
        $testOldToken = AmoCrmServise::getTokens();

        if ($testOldToken->getToken() == $accessToken->getToken()) {
            echo 'access token 1 == acces token 2 <br>';
        } else {
            echo 'access token 1 != acces token 2 <br>';
        }

        if ($testOldToken->getRefreshToken() == $accessToken->getRefreshToken()) {
            echo 'refresh token 1 == refresh token 2 <br>';
        } else {
            echo 'refresh token 1 != refresh token 2 <br>';
        }

        if ($testOldToken->getExpires() == $accessToken->getExpires()) {
            echo 'expires token 1 == expires token 2 <br>';
        } else {
            echo 'expires token 1 != expires token 2 <br>';
        }

//        var_dump('save token oAouthService', $accessToken);

        $timeToken = $accessToken->getExpires();
        $time = time();

        if ($timeToken < $time) {
            var_dump('timeToken < time');
        } else {
            var_dump('timeToken > time');
        }

        if ($accessToken->hasExpired()) {
           var_dump('refresh token');
            $data = [
                'access_token'  => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires'      => $accessToken->getExpires(),
                'baseDomain'   => $baseDomain,
            ];

            Storage::disk('local')->put('data/amo-assets.json', json_encode($data));
        } else {
            $data = [
                'access_token'  => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires'      => $accessToken->getExpires(),
                'baseDomain'   => $baseDomain,
            ];

            Storage::disk('local')->put('data/amo-assets.json', json_encode($data));

            var_dump('not save token');
        }

    }
}
