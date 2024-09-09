<?php

namespace OpenIDConnect\Claims;

interface Claimable
{
    /**
     * @param string[] $scopes Optional scope filter
     *
     * @return string[]
     */
    public function getClaims(array $scopes = []): array;
}
