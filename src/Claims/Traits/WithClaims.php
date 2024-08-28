<?php

namespace OpenIDConnect\Claims\Traits;

trait WithClaims
{
    /**
     * @param string[] $claims
     */
    protected array $claims;

    /**
     * @param string[] $scopes Optional scope filter
     *
     * @return string[]
     */
    public function getClaims(array $scopes = []): array
    {
        return $this->claims;
    }

    /**
     * @param string[] $claims
     */
    public function setClaims(array $claims): void
    {
        $this->claims = $claims;
    }
}
