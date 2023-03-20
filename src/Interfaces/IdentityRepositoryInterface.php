<?php

namespace OpenIDConnect\Interfaces;

use League\OAuth2\Server\Repositories\RepositoryInterface;

interface IdentityRepositoryInterface extends RepositoryInterface
{
    public function getByIdentifier(string $identifier): IdentityEntityInterface;
}
