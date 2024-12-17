<?php

namespace OpenIDConnect\Tests\Feature;

use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\Claims\ClaimSet;
use OpenIDConnect\Repositories\IdentityRepository;
use OpenIDConnect\Tests\Config;
use OpenIDConnect\Tests\Factories\AccessTokenFactory;
use OpenIDConnect\Tests\Factories\ConfigutationFactory;
use OpenIDConnect\Tests\Factories\IdTokenResponseFactory;
use OpenIDConnect\Tests\Factories\KeyFactory;
use OpenIDConnect\Tests\Factories\Psr7ResponseFactory;
use OpenIDConnect\Tests\Factories\RefreshTokenFactory;
use OpenIDConnect\Tests\Feature\Traits\WithDefaultAsserts;
use PHPUnit\Framework\TestCase;

class TokensTest extends TestCase
{
    use WithDefaultAsserts;

    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = Config::HTTP_HOST;
    }

    public function test_id_token_is_valid()
    {
        $response = Psr7ResponseFactory::withConfig(
            $accessToken = AccessTokenFactory::withOpenIdScope(),
            RefreshTokenFactory::withAccessToken($accessToken),
            $config = ConfigutationFactory::default(),
        );
        $this->defaultResponseAsserts($response);

        $json = json_decode($response->getBody()->getContents());
        $this->defaultTokenAsserts($json);

        $token = $config->parser()->parse($json->id_token);
        $this->assertInstanceOf(Plain::class, $token);

        $isValid = $config->validator()->validate(
            $token,
            ...[
                new IssuedBy(Config::REQUEST_SCHEME . '://' . Config::HTTP_HOST),
                new PermittedFor(Config::CLIENT_ID),
                new RelatedTo(Config::USER_ID),
                new SignedWith(
                    $config->signer(),
                    KeyFactory::signerKeyFromFile(),
                ),
            ],
        );

        $this->assertTrue($isValid);
    }

    public function test_id_token_with_email_scope_returns_email_claim()
    {
        $scopes = ['openid', 'email'];

        $response = Psr7ResponseFactory::withConfig(
            $accessToken = AccessTokenFactory::withScopes($scopes),
            RefreshTokenFactory::withAccessToken($accessToken),
            $config = ConfigutationFactory::default(),
        );
        $this->defaultResponseAsserts($response);

        $json = json_decode($response->getBody()->getContents());
        $this->defaultTokenAsserts($json);

        /** @var Plain $token */
        $token = $config->parser()->parse($json->id_token);
        $this->assertSame(
            'jon.snow@dorne.com',
            $token->claims()->get('email')
        );
    }

    /**
     * @group dev
     */
    public function test_id_token_with_custom_scope_returns_custom_claim()
    {
        $scopes = ['openid', 'custom'];

        $response = Psr7ResponseFactory::withIdTokenResponse(
            $accessToken = AccessTokenFactory::withScopes($scopes),
            RefreshTokenFactory::withAccessToken($accessToken),
            IdTokenResponseFactory::withConfig(
                new IdentityRepository(),
                new ClaimExtractor(new ClaimSet('custom', ['what_he_knows'])),
                $config = ConfigutationFactory::default(),
            )
        );
        $this->defaultResponseAsserts($response);

        $json = json_decode($response->getBody()->getContents());
        $this->defaultTokenAsserts($json);

        /** @var Plain $token */
        $token = $config->parser()->parse($json->id_token);
        $this->assertSame('Nothing!', $token->claims()->get('what_he_knows'));
    }
}
