<?php

namespace OpenIDConnect\Tests\Factories;

use GuzzleHttp\Psr7;
use Lcobucci\JWT\Configuration;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\IdTokenResponse;
use OpenIDConnect\Repositories\IdentityRepository;

class Psr7ResponseFactory
{
    private function build(
        AccessTokenEntityInterface $accessToken,
        RefreshTokenEntityInterface $refreshToken,
        ?IdTokenResponse $response = null,
        ?CryptKey $privateKey = null,
    ): Psr7\Response {
        $response = $response ?? IdTokenResponseFactory::default(
            new IdentityRepository(),
            new ClaimExtractor()
        );

        $response->setPrivateKey($privateKey ?? KeyFactory::cryptKey());
        $response->setEncryptionKey(base64_encode(random_bytes(32)));
        $response->setAccessToken($accessToken);
        $response->setRefreshToken($refreshToken);

        return $response->generateHttpResponse(new Psr7\Response());
    }

    public static function default(
        AccessTokenEntityInterface $accessToken,
        RefreshTokenEntityInterface $refreshToken,
    ): Psr7\Response {
        return (new static())->build($accessToken, $refreshToken);
    }

    public static function withIdTokenResponse(
        AccessTokenEntityInterface $accessToken,
        RefreshTokenEntityInterface $refreshToken,
        IdTokenResponse $response
    ): Psr7\Response {
        return (new static())->build($accessToken, $refreshToken, $response);
    }

    public static function withConfig(
        AccessTokenEntityInterface $accessToken,
        RefreshTokenEntityInterface $refreshToken,
        Configuration $config,
    ): Psr7\Response {
        return (new static())->build(
            $accessToken,
            $refreshToken,
            IdTokenResponseFactory::withConfig(
                new IdentityRepository(),
                new ClaimExtractor(),
                $config
            )
        );
    }
}
