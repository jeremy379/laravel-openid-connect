<?php

namespace OpenIDConnect\Tests\Factories;

use Lcobucci\JWT\Configuration;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\IdTokenResponse;
use OpenIDConnect\Interfaces\IdentityRepositoryInterface;

class IdTokenResponseFactory
{
    private function build(
        IdentityRepositoryInterface $identityRepository,
        ClaimExtractor $claimExtractor,
        ?Configuration $config = null,
    ): BearerTokenResponse {
        return new IdTokenResponse(
            $identityRepository,
            $claimExtractor,
            $config ?? ConfigutationFactory::default(),
        );
    }

    public static function default(
        IdentityRepositoryInterface $identityRepository,
        ClaimExtractor $claimExtractor,
    ): BearerTokenResponse {
        return (new static())->build($identityRepository, $claimExtractor);
    }

    public static function withConfig(
        IdentityRepositoryInterface $identityRepository,
        ClaimExtractor $claimExtractor,
        Configuration $config
    ): BearerTokenResponse {
        return (new static())->build($identityRepository, $claimExtractor, $config);
    }
}
