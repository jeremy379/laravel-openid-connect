<?php

namespace OpenIDConnect\Laravel;

use Nyholm\Psr7\Factory\Psr17Factory;
use OpenIDConnect\Interfaces\CurrentRequestServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class LaravelCurrentRequestService implements CurrentRequestServiceInterface
{

    public function getRequest(): ServerRequestInterface
    {
        return (new PsrHttpFactory(
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory
        ))->createRequest(request());
    }
}
