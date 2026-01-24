<?php

namespace OpenIDConnect\Tests\Feature;

use OpenIDConnect\Interfaces\IdentityEntityInterface;
use OpenIDConnect\Interfaces\IdentityRepositoryInterface;
use OpenIDConnect\Tests\Factories\ConfigutationFactory;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\IdTokenResponse;
use OpenIDConnect\Tests\Config;
use OpenIDConnect\Tests\Factories\AccessTokenFactory;
use OpenIDConnect\Tests\Factories\ClientFactory;
use OpenIDConnect\Tests\Feature\Traits\WithSigningKey;
use PHPUnit\Framework\TestCase;
use OpenIDConnect\Entities\IdentityEntity;

class IdTokenCustomAudTest extends TestCase
{
    use WithSigningKey;
    protected function setUp(): void
    {
        $this->ensureSigningKeyExists();
    }

    private function getAudClaimFromIdToken(array $customAudiences): array
    {
        $identity = new IdentityEntity();
        $identity->setIdentifier(Config::USER_ID);
        $identity->setPermittedFor($customAudiences);

        $identityRepository = new class ($identity) implements IdentityRepositoryInterface {
            private $identity;

            public function __construct($identity)
            {
                $this->identity = $identity;
            }

            public function getByIdentifier(string $identifier): IdentityEntityInterface
            {
                return $this->identity;
            }
        };

        $accessToken = AccessTokenFactory::withOpenIdScope();
        $accessToken->setUserIdentifier(Config::USER_ID);
        $accessToken->setClient(ClientFactory::default());

        $config = ConfigutationFactory::default();

        $idTokenResponse = new IdTokenResponse(
            $identityRepository,
            new ClaimExtractor(),
            $config
        );

        $reflection = new \ReflectionClass($idTokenResponse);
        $getExtraParams = $reflection->getMethod('getExtraParams');
        $getExtraParams->setAccessible(true);

        $params = $getExtraParams->invoke($idTokenResponse, $accessToken);

        $this->assertArrayHasKey('id_token', $params);

        $jwt = $config->parser()->parse($params['id_token']);

        return (array) $jwt->claims()->get('aud');
    }

    public function test_id_token_aud_with_only_client_id_when_no_custom_audiences()
    {
        $aud = $this->getAudClaimFromIdToken([]);

        $this->assertEqualsCanonicalizing(
            [Config::CLIENT_ID],
            $aud
        );
    }

    public function test_id_token_aud_includes_client_and_custom_audiences()
    {
        $customAudiences = [
            'https://api.example.com/v1/resource',
            'custom aud 2',
        ];

        $aud = $this->getAudClaimFromIdToken($customAudiences);

        $expected = array_merge([Config::CLIENT_ID], $customAudiences);

        $this->assertEqualsCanonicalizing($expected, $aud);
    }

}