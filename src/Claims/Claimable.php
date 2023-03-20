<?php

namespace OpenIDConnect\Claims;

interface Claimable
{
    /**
     * @return string[]
     */
    public function getClaims(): array;
}
