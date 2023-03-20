<?php

namespace OpenIDConnect\Tests\Factories;

use DateInterval;
use DateTimeImmutable;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use OpenIDConnect\Entities\RefreshTokenEntity;

class RefreshTokenFactory
{
    private function build(
        ?AccessTokenEntityInterface $accessToken = null,
    ): RefreshTokenEntityInterface {
        $refreshToken = new RefreshTokenEntity();
        $refreshToken->setAccessToken($accessToken ?? AccessTokenFactory::default());
        $refreshToken->setIdentifier('refresh_token_id');
        $refreshToken->setExpiryDateTime(
            (new DateTimeImmutable())->add(new DateInterval('PT1H'))
        );

        return $refreshToken;
    }

    public static function default(): RefreshTokenEntityInterface
    {
        return (new static())->build();
    }

    public static function withAccessToken(
        AccessTokenEntityInterface $accessToken
    ): RefreshTokenEntityInterface {
        return (new static())->build($accessToken);
    }
}
