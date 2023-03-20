<?php

namespace OpenIDConnect\Tests\Factories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use OpenIDConnect\Entities\ClientEntity;
use OpenIDConnect\Tests\Config;

class ClientFactory
{
    private function build(?string $identifier = null): ClientEntityInterface
    {
        $client = new ClientEntity();
        $client->setIdentifier($identifier ?? Config::CLIENT_ID);
        $client->setName('a_third_party_client');
        $client->setRedirectUri('https://' . Config::HTTP_HOST . '/');
        $client->setConfidential();

        return $client;
    }

    public static function default(): ClientEntityInterface
    {
        return (new static())->build();
    }

    public static function withClient(string $identifier): ClientEntityInterface
    {
        return (new static())->build($identifier);
    }
}
