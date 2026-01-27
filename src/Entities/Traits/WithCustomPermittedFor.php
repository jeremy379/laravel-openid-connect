<?php

namespace OpenIDConnect\Entities\Traits;

trait WithCustomPermittedFor
{
    /**
     * @param string[] $permittedFor
     */
    protected array $permittedFor = [];

    /**
     * @return string[]
     */
    public function getPermittedFor(): array
    {
        return $this->permittedFor;
    }

    /**
     * @param string[] $permittedFor
     */
    public function setPermittedFor(array $permittedFor): void
    {
        $this->permittedFor = $permittedFor;
    }
}
