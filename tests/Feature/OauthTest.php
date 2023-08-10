<?php

namespace OpenIDConnect\Tests\Feature;

use OpenIDConnect\Tests\Factories\AccessTokenFactory;
use OpenIDConnect\Tests\Factories\Psr7ResponseFactory;
use OpenIDConnect\Tests\Factories\RefreshTokenFactory;
use OpenIDConnect\Tests\Feature\Traits\WithDefaultAsserts;
use PHPUnit\Framework\TestCase;

class OauthTest extends TestCase
{
    use WithDefaultAsserts;

    public function test_default_response_has_no_id_token(): void
    {
        $response = Psr7ResponseFactory::default(
            $accessToken = AccessTokenFactory::default(),
            RefreshTokenFactory::withAccessToken($accessToken)
        );
        $this->defaultResponseAsserts($response);

        $json = json_decode($response->getBody()->getContents());
        $this->defaultTokenAsserts($json);

        $this->assertIsObject($json);
        $this->assertFalse(property_exists($json, 'id_token'));
    }
}
