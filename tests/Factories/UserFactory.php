<?php

namespace OpenIDConnect\Tests\Factories;

use OpenIDConnect\Entities\IdentityEntity;
use OpenIDConnect\Interfaces\IdentityEntityInterface;

class UserFactory
{
    private function build(
        string $identifier,
        ?array $claims = null,
    ): IdentityEntityInterface {
        $entity = new IdentityEntity();
        $entity->setIdentifier($identifier);
        $entity->setClaims($claims);
        return $entity;
    }

    public static function default(string $identifier): IdentityEntityInterface
    {
        return (new static())->build($identifier);
    }

    public static function withClaims(
        string $identifier,
        array $claims,
    ) {
        return (new static())->build($identifier, $claims);
    }
}
