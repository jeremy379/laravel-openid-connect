<?php

namespace OpenIDConnect\Claims\Traits;

trait WithClaims
{
    /**
     * @param string[] $claims
     */
    protected array $claims;

    /**
     * @return string[]
     */
    public function getClaims(): array
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
