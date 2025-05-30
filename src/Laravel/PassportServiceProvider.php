<?php

namespace OpenIDConnect\Laravel;

use Illuminate\Encryption\Encrypter;
use Laravel\Passport;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\ClientRepository;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Nyholm\Psr7\Response;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\Claims\ClaimSet;
use OpenIDConnect\Grant\AuthCodeGrant;
use OpenIDConnect\IdTokenResponse;

class PassportServiceProvider extends Passport\PassportServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(
            __DIR__ . '/config/openid.php',
            'openid'
        );
    }

    public function boot(): void
    {
        parent::boot();

        $this->publishes([
            __DIR__ . '/config/openid.php' => $this->app->configPath('openid.php'),
        ], ['openid', 'openid-config']);

        $this->loadRoutesFrom(__DIR__."/routes/web.php");

        $this->registerClaimExtractor();
    }

    public function makeAuthorizationServer(?ResponseTypeInterface $responseType = null): AuthorizationServer
    {
        $cryptKey = $this->makeCryptKey('private');
        $encryptionKey = $this->getEncryptionKey(app(Encrypter::class)->getKey());

        $customClaimSets = config('openid.custom_claim_sets');

        $claimSets = array_map(function ($claimSet, $name) {
            return new ClaimSet($name, $claimSet);
        }, $customClaimSets, array_keys($customClaimSets));

        $responseType = new IdTokenResponse(
            app(config('openid.repositories.identity')),
            new ClaimExtractor(...$claimSets),
            Configuration::forSymmetricSigner(
                app(config('openid.signer')),
                InMemory::plainText($cryptKey->getKeyContents(), $cryptKey->getPassPhrase() ?? '')
            ),
            config('openid.token_headers'),
            config('openid.use_microseconds'),
            app(LaravelCurrentRequestService::class),
            $encryptionKey,
            config('openid.issuedBy', 'laravel')
        );

        return new AuthorizationServer(
            app(ClientRepository::class),
            app(AccessTokenRepository::class),
            app(Passport\Bridge\ScopeRepository::class),
            $cryptKey,
            $encryptionKey,
            $responseType,
        );
    }

    /**
     * Build the Auth Code grant instance.
     *
     * @return AuthCodeGrant
     */
    protected function buildAuthCodeGrant(): AuthCodeGrant
    {
        return new AuthCodeGrant(
            $this->app->make(Passport\Bridge\AuthCodeRepository::class),
            $this->app->make(Passport\Bridge\RefreshTokenRepository::class),
            new \DateInterval('PT10M'),
            new Response(),
            $this->app->make(LaravelCurrentRequestService::class),
        );
    }

    /**
     * Get encryption key as string or a Key instance
     *
     * Based on https://github.com/laravel/passport/pull/820
     *
     * @param string $keyBytes Encryption key as string
     *
     * @return \Defuse\Crypto\Key|string
     */
    protected function getEncryptionKey($keyBytes)
    {
        // For BC reasons we return string, but implementations can override this method to return an object.
        // As mentioned in https://github.com/laravel/passport/pull/820 it gives better performance.
        return $keyBytes;
    }

    public function registerClaimExtractor() {
        $this->app->singleton(ClaimExtractor::class, function () {
            $customClaimSets = config('openid.custom_claim_sets');

            $claimSets = array_map(function ($claimSet, $name) {
                return new ClaimSet($name, $claimSet);
            }, $customClaimSets, array_keys($customClaimSets));

            return new ClaimExtractor(...$claimSets);
        });
    }
}
