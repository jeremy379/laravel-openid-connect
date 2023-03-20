<?php

namespace OpenIDConnect\Tests\Factories;

use DateInterval;
use DateTimeImmutable;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use OpenIDConnect\Entities\AccessTokenEntity;
use OpenIDConnect\Tests\Config;

class AccessTokenFactory
{
    private function build(
        ?CryptKey $cryptKey = null,
        ?array $scopes = null,
        ?ClientEntityInterface $client = null,
    ): AccessTokenEntityInterface {
        $accessToken = new AccessTokenEntity();
        $accessToken->setPrivateKey($cryptKey ?? KeyFactory::cryptKey());
        $accessToken->setClient($client ?? ClientFactory::default());
        $accessToken->setUserIdentifier(Config::USER_ID);
        $accessToken->setIdentifier('access_token_id');
        $accessToken->setExpiryDateTime(
            (new DateTimeImmutable())->add(new DateInterval('PT1H'))
        );

        if ($scopes) {
            array_walk($scopes, function (string $scope) use ($accessToken) {
                $accessToken->addScope(ScopeFactory::default($scope));
            });
        }

        return $accessToken;
    }

    public static function default(): AccessTokenEntityInterface
    {
        return (new static())->build();
    }

    public static function withOpenIdScope(): AccessTokenEntityInterface
    {
        return (new static())->build(null, ['openid']);
    }

    public static function withCryptKey(CryptKey $cryptKey): AccessTokenEntityInterface
    {
        return (new static())->build($cryptKey);
    }

    public static function withScopes(array $scopes): AccessTokenEntityInterface
    {
        return (new static())->build(null, $scopes);
    }

    public static function withCryptKeyAndScopes(CryptKey $cryptKey, array $scopes): AccessTokenEntityInterface
    {
        return (new static())->build($cryptKey, $scopes);
    }
}
