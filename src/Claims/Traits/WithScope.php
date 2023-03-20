<?php

namespace OpenIDConnect\Claims\Traits;

trait WithScope
{
    protected string $scope;

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }
}
