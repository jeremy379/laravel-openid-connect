<?php

namespace OpenIDConnect\Repositories;

use OpenIDConnect\Interfaces\IdentityEntityInterface;
use OpenIDConnect\Interfaces\IdentityRepositoryInterface;

class IdentityRepository implements IdentityRepositoryInterface
{
    public function getByIdentifier(string $identifier): IdentityEntityInterface
    {
        /**
         * Try to resolve UserEntity and IdentityEntity for Laravel Passport
         */
        if (class_exists(\App\Entities\UserEntity::class)) {
            $className = \App\Entities\UserEntity::class;
        } elseif (class_exists(\App\Entities\IdentityEntity::class)) {
            $className = \App\Entities\IdentityEntity::class;
        } else {
            $className = \OpenIDConnect\Entities\IdentityEntity::class;
        }

        $identityEntity = new $className();
        $identityEntity->setIdentifier($identifier);
        return $identityEntity;
    }
}
