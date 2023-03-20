<?php

namespace OpenIDConnect\Example;

require_once(__DIR__ . '/../vendor/autoload.php');

/**
 * This is an example to Mock an OAuth 2.0 server with OpenID Connect implemented.
 * @see: https://oauth2.thephpleague.com/authorization-server/auth-code-grant/
 */

// Init our repositories

use Exception;
use GuzzleHttp\Psr7;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use League\OAuth2\Server\Exception\OAuthServerException;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\Entities\IdentityEntity;
use OpenIDConnect\IdTokenResponse;
use OpenIDConnect\Repositories\AccessTokenRepository;
use OpenIDConnect\Repositories\AuthCodeRepository;
use OpenIDConnect\Repositories\ClientRepository;
use OpenIDConnect\Repositories\IdentityRepository;
use OpenIDConnect\Repositories\RefreshTokenRepository;
use OpenIDConnect\Repositories\ScopeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

$clientRepository = new ClientRepository(); // instance of ClientRepositoryInterface
$scopeRepository = new ScopeRepository(); // instance of ScopeRepositoryInterface
$accessTokenRepository = new AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
$authCodeRepository = new AuthCodeRepository(); // instance of AuthCodeRepositoryInterface
$refreshTokenRepository = new RefreshTokenRepository(); // instance of RefreshTokenRepositoryInterface

$privateKeyPath = dirname(__DIR__) . '/tmp/private.key';
//$privateKey = new CryptKey('file://path/to/private.key', 'passphrase'); // if private key has a pass phrase
$encryptionKey = 'u9+SjT9FxFsuXAx0S1lDNdf/49GnzUzXZT0o5iYfLqc='; // generate using base64_encode(random_bytes(32))

// [OpenIDConnect] Create the response_type
$responseType = new IdTokenResponse(
    new IdentityRepository(),
    new ClaimExtractor(),
    Configuration::forSymmetricSigner(
        new Sha256(),
        InMemory::file($privateKeyPath),
    )
);

// Setup the authorization server
$server = new \League\OAuth2\Server\AuthorizationServer(
    $clientRepository,
    $accessTokenRepository,
    $scopeRepository,
    $privateKeyPath,
    $encryptionKey,
    // [OpenIDConnect] Add the response_type
    $responseType
);

$grant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
    $authCodeRepository,
    $refreshTokenRepository,
    new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
);

$grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

// Enable the authentication code grant on the server
$server->enableGrantType(
    $grant,
    new \DateInterval('PT1H') // access tokens will expire after 1 hour
);

$app = AppFactory::create();

$app->get('/authorize', function (
    ServerRequestInterface $request,
    ResponseInterface $response
) use ($server) {
    try {
        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($request);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.
        $user = new IdentityEntity();
        $user->setIdentifier('1');

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser($user); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
    } catch (OAuthServerException $exception) {
        // All instances of OAuthServerException can be formatted into a HTTP response
        return $exception->generateHttpResponse($response);
    } catch (Exception $exception) {
        // Unknown exception
        $body = new Psr7\Stream(fopen('php://temp', 'r+'));
        $body->write($exception->getMessage());
        return $response->withStatus(500)->withBody($body);
    }
});

$app->post('/tokens', function (
    ServerRequestInterface $request,
    ResponseInterface $response
) use ($server) {
    try {
        // Try to respond to the request
        return $server->respondToAccessTokenRequest($request, $response);
    } catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {
        // All instances of OAuthServerException can be formatted into a HTTP response
        return $exception->generateHttpResponse($response);
    } catch (Exception $exception) {
        // Unknown exception
        $body = new Psr7\Stream(fopen('php://temp', 'r+'));
        $body->write($exception->getMessage());
        return $response->withStatus(500)->withBody($body);
    }
});

$app->run();
