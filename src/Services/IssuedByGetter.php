<?php

namespace OpenIDConnect\Services;

use OpenIDConnect\Interfaces\CurrentRequestServiceInterface;

class IssuedByGetter
{
    public static function get(?CurrentRequestServiceInterface $currentRequestService, string $issuedByConfigured = 'laravel'): string
    {
        if($issuedByConfigured === 'laravel' && $currentRequestService) {
            $uri = $currentRequestService->getRequest()->getUri();
            return $uri->getScheme() . '://' . $uri->getHost() . ($uri->getPort() ? ':' . $uri->getPort() : '');
        }

        if($issuedByConfigured === 'server' || ($issuedByConfigured === 'laravel' && !$currentRequestService)) {
            $host = $_SERVER['HTTP_HOST'] ?? null;

            $scheme = $_SERVER['REQUEST_SCHEME'] ?? null;

            if (empty($scheme)) {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            }

            return $scheme . '://' . $host;
        }

        return $issuedByConfigured;
    }
}
