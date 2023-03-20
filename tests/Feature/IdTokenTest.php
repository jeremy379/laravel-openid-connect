<?php

namespace OpenIDConnect\Tests\Feature;

use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\Claims\ClaimSet;
use OpenIDConnect\IdTokenResponse;
use OpenIDConnect\Repositories\IdentityRepository;
use OpenIDConnect\Tests\Config;
use OpenIDConnect\Tests\Factories\AccessTokenFactory;
use OpenIDConnect\Tests\Factories\IdTokenResponseFactory;
use OpenIDConnect\Tests\Factories\Psr7ResponseFactory;
use OpenIDConnect\Tests\Factories\RefreshTokenFactory;
use OpenIDConnect\Tests\Feature\Traits\WithDefaultAsserts;
use PHPUnit\Framework\TestCase;

class IdTokenTest extends TestCase
{
    use WithDefaultAsserts;

    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = Config::HTTP_HOST;
    }

    public function test_can_create_id_token_responses()
    {
        $idTokenResponse = IdTokenResponseFactory::default(
            new IdentityRepository(),
            new ClaimExtractor(),
        );
        $this->assertInstanceOf(IdTokenResponse::class, $idTokenResponse);
        $this->assertInstanceOf(BearerTokenResponse::class, $idTokenResponse);
    }

    public function test_can_create_id_token_responses_with_openid_claim_set()
    {
        $claimSet = new ClaimSet('custom', ['custom_claim']);
        $idTokenResponse = IdTokenResponseFactory::default(
            new IdentityRepository(),
            new ClaimExtractor($claimSet),
        );
        $this->assertInstanceOf(IdTokenResponse::class, $idTokenResponse);
        $this->assertInstanceOf(BearerTokenResponse::class, $idTokenResponse);
    }

    public function test_receive_id_token_with_open_id_scope()
    {
        $response = Psr7ResponseFactory::default(
            $accessToken = AccessTokenFactory::withOpenIdScope(),
            RefreshTokenFactory::withAccessToken($accessToken),
        );
        $this->defaultResponseAsserts($response);

        $json = json_decode($response->getBody()->getContents());
        $this->defaultTokenAsserts($json);

        $this->assertObjectHasAttribute('id_token', $json);
    }
}
