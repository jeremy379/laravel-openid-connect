<?php

namespace OpenIDConnect\Services;

use OpenIDConnect\Interfaces\CurrentRequestServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

class CurrentRequestService implements CurrentRequestServiceInterface
{
    private ?ServerRequestInterface $request;

    public function getRequest(): ServerRequestInterface
    {
        if ($this->request === null) {
            throw new \RuntimeException('Request not set in CurrentRequestService');
        }
        return $this->request;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
