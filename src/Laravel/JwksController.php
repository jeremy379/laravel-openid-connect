<?php

namespace OpenIDConnect\Laravel;

use Illuminate\Config\Repository as Config;
use Laravel\Passport\Passport;

class JwksController
{
    public function __invoke() {
        $publicKey = $this->getPublicKey();

        // Source: https://www.tuxed.net/fkooman/blog/json_web_key_set.html
        $keyInfo = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));

        $jsonData = [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'n' => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['n'])), '='),
                    'e' => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['e'])), '='),
                ],
            ],
        ];

        return response()->json($jsonData, 200, [], JSON_PRETTY_PRINT);
    }

    private function getPublicKey(): string {
        $publicKey = str_replace('\\n', "\n", config('passport.public_key', ''));

        if (!$publicKey) {
            $publicKey = 'file://'.Passport::keyPath('oauth-public.key');
        }

        return $publicKey;
    }
}
