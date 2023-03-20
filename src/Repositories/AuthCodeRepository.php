<?php

namespace OpenIDConnect\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use OpenIDConnect\Entities\AuthCodeEntity;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
    }

    public function revokeAuthCode($codeId)
    {
    }

    public function isAuthCodeRevoked($codeId)
    {
        return false;
    }
}
