<?php

namespace OpenIDConnect\Grant;

use DateInterval;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use League\OAuth2\Server\ResponseTypes\RedirectResponse;
use OpenIDConnect\Interfaces\CurrentRequestServiceInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This class extends the default AuthCodeGrant class to add support for the nonce parameter.
 *
 * The nonce parameter is
 */
class AuthCodeGrant extends \League\OAuth2\Server\Grant\AuthCodeGrant
{
    private ResponseInterface $psr7Response;
    private CurrentRequestServiceInterface $currentRequestService;

    /**
     * @param AuthCodeRepositoryInterface $authCodeRepository
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     * @param DateInterval $authCodeTTL
     * @param ResponseInterface $psr7Response An empty PSR-7 Response object
     * @param CurrentRequestServiceInterface $currentRequestService A service that returns the current request. Used to get the nonce parameter.
     * @throws \Exception
     */
    public function __construct(AuthCodeRepositoryInterface $authCodeRepository, RefreshTokenRepositoryInterface $refreshTokenRepository, DateInterval $authCodeTTL, ResponseInterface $psr7Response, CurrentRequestServiceInterface $currentRequestService)
    {
        parent::__construct($authCodeRepository, $refreshTokenRepository, $authCodeTTL);
        $this->psr7Response = $psr7Response;
        $this->currentRequestService = $currentRequestService;
    }

    /**
     * {@inheritdoc}
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest)
    {
        // See https://github.com/steverhoades/oauth2-openid-connect-server/issues/47#issuecomment-1228370632

        /** @var RedirectResponse $response */
        $response = parent::completeAuthorizationRequest($authorizationRequest);

        $queryParams = $this->currentRequestService->getRequest()->getQueryParams();

        if (isset($queryParams['nonce'])) {
            // The only way to get the redirect URI is to generate the PSR7 response (The RedirectResponse class does not have a getter for the redirect URI)
            $httpResponse = $response->generateHttpResponse($this->psr7Response);
            $redirectUri = $httpResponse->getHeader('Location')[0];
            $parsed = parse_url($redirectUri);

            parse_str($parsed['query'], $query);

            $authCodePayload = json_decode($this->decrypt($query['code']), true, 512, JSON_THROW_ON_ERROR);

            $authCodePayload['nonce'] = $queryParams['nonce'];

            $query['code'] = $this->encrypt(json_encode($authCodePayload, JSON_THROW_ON_ERROR));

            $parsed['query'] = http_build_query($query);

            $response->setRedirectUri($this->unparse_url($parsed));
        }

        return $response;
    }

    /**
     * Inverse of parse_url
     *
     * @param mixed $parsed_url
     * @return string
     */
    private function unparse_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
