<?php

namespace OpenIDConnect\Tests\Feature;

use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\Tests\Config;
use OpenIDConnect\Tests\Factories\UserFactory;
use PHPUnit\Framework\TestCase;

class UserInfoTest extends TestCase
{
    public function test_userinfo_returns_sub_and_no_extra_claims_without_scopes(): void
    {
        $identity = UserFactory::withClaims(Config::USER_ID, [
            'email' => 'jon.snow@dorne.com',
            'name' => 'Jon Snow',
        ]);

        $claims = (new ClaimExtractor())->extract([], $identity->getClaims());
        $claims['sub'] = $identity->getIdentifier();

        $this->assertSame(Config::USER_ID, $claims['sub']);
        $this->assertArrayNotHasKey('email', $claims);
        $this->assertArrayNotHasKey('name', $claims);
    }

    public function test_userinfo_with_email_scope_returns_email_claim(): void
    {
        $identity = UserFactory::withClaims(Config::USER_ID, [
            'email' => 'jon.snow@dorne.com',
        ]);

        $claims = (new ClaimExtractor())->extract(['email'], $identity->getClaims());
        $claims['sub'] = $identity->getIdentifier();

        $this->assertSame(Config::USER_ID, $claims['sub']);
        $this->assertSame('jon.snow@dorne.com', $claims['email']);
    }

    public function test_userinfo_with_profile_scope_returns_profile_claims(): void
    {
        $identity = UserFactory::withClaims(Config::USER_ID, [
            'name' => 'Jon Snow',
            'email' => 'jon.snow@dorne.com',
        ]);

        $claims = (new ClaimExtractor())->extract(['profile'], $identity->getClaims());
        $claims['sub'] = $identity->getIdentifier();

        $this->assertSame(Config::USER_ID, $claims['sub']);
        $this->assertSame('Jon Snow', $claims['name']);
        $this->assertArrayNotHasKey('email', $claims);
    }

    public function test_userinfo_with_multiple_scopes_returns_merged_claims(): void
    {
        $identity = UserFactory::withClaims(Config::USER_ID, [
            'name' => 'Jon Snow',
            'email' => 'jon.snow@dorne.com',
            'phone_number' => '0031 493 123 456',
        ]);

        $claims = (new ClaimExtractor())->extract(['profile', 'email', 'phone'], $identity->getClaims());
        $claims['sub'] = $identity->getIdentifier();

        $this->assertSame(Config::USER_ID, $claims['sub']);
        $this->assertSame('Jon Snow', $claims['name']);
        $this->assertSame('jon.snow@dorne.com', $claims['email']);
        $this->assertSame('0031 493 123 456', $claims['phone_number']);
    }

    public function test_sub_always_overrides_claim_from_entity(): void
    {
        $identity = UserFactory::withClaims(Config::USER_ID, [
            'sub' => 'should-be-overridden',
        ]);

        $claims = (new ClaimExtractor())->extract([], $identity->getClaims());
        $claims['sub'] = $identity->getIdentifier();

        $this->assertSame(Config::USER_ID, $claims['sub']);
    }
}
