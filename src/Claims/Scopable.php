<?php

namespace OpenIDConnect\Claims;

interface Scopable
{
    public function getScope(): string;
}
