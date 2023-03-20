<?php

namespace OpenIDConnect\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OpenIDConnect\Entities\RefreshTokenEntity;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
    }

    public function revokeRefreshToken($tokenId)
    {
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        return false;
    }
}
