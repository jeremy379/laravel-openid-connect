<?php

namespace OpenIDConnect\Interfaces;

use League\OAuth2\Server\Entities\UserEntityInterface as OAuth2UserEntityInterface;
use OpenIDConnect\Claims\Claimable;

interface IdentityEntityInterface extends Claimable, OAuth2UserEntityInterface
{
    public function getIdentifier();
}
