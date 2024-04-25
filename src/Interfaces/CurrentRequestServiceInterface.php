<?php

namespace OpenIDConnect\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

/**
 * A service in charge of returning the current request.
 *
 * This should be implemented by the application using this package (a default Laravel implementation is provided)
 *
 * We need this because due to the architecture of the League package, the request is not available in the
 * grant classes. But we need access to the "nonce" parameter in the request to be able to include it in the
 * ID token.
 */
interface CurrentRequestServiceInterface
{
    public function getRequest(): ServerRequestInterface;
}
