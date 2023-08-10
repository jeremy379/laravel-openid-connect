<?php

namespace OpenIDConnect\Tests\Feature;

use OpenIDConnect\ClaimExtractor;
use OpenIDConnect\Claims\ClaimSet;
use OpenIDConnect\Exceptions\ProtectedScopeException;
use PHPUnit\Framework\TestCase;

class ClaimExtractorTest extends TestCase
{
    public static function protected_claim_sets()
    {
        yield 'profile' => ['name' => 'profile'];
        yield 'email' => ['name' => 'email'];
        yield 'address' => ['name' => 'address'];
        yield 'phone' => ['name' => 'phone'];
    }

    /**
     * @dataProvider protected_claim_sets
     */
    public function test_default_claim_sets_exist(string $name)
    {
        $extractor = new ClaimExtractor();
        $this->assertTrue($extractor->hasClaimSet($name));
    }

    /**
     * @dataProvider protected_claim_sets
     */
    public function test_cannot_override_protected_scope(string $name)
    {
        $this->expectException(ProtectedScopeException::class);
        $this->expectExceptionMessage("The scope '{$name}' is a protected scope.");
        new ClaimExtractor(new ClaimSet($name, ['custom_claim']));
    }

    /**
     * @dataProvider protected_claim_sets
     */
    public function test_can_get_scope_by_name(string $name)
    {
        $claimset = (new ClaimExtractor())->getClaimSet($name);
        $this->assertEquals($claimset->getScope(), $name);
    }

    public function test_can_set_and_extract_custom_claim_set()
    {
        $claimSet = new ClaimSet('custom_set', ['custom_claim']);
        $extractor = new ClaimExtractor($claimSet);
        $this->assertTrue($extractor->hasClaimSet('custom_set'));

        $result = $extractor->extract(['custom_set'], ['custom_claim' => 'value']);
        $this->assertEquals($result['custom_claim'], 'value');
    }

    public function test_can_safely_get_uknown_claim_set()
    {
        $extractor = new ClaimExtractor();
        $this->assertNull($extractor->getClaimSet('unknown'));
    }

    public function test_can_safely_extract_uknown_claim()
    {
        $extractor = new ClaimExtractor();
        $result = $extractor->extract(['custom_set'], ['uknown' => 'uknown']);
        $this->assertEmpty($result);
    }

    public function test_can_safely_extract_known_claim_set()
    {
        $extractor = new ClaimExtractor();
        $result = $extractor->extract(['profile'], ['name' => 'John Snow']);
        $this->assertEquals($result['name'], 'John Snow');
    }

    public function test_can_safely_extract_invalid_claim_set()
    {
        $extractor = new ClaimExtractor();
        $result = $extractor->extract(['profile'], ['invalid' => 'invalid']);
        $this->assertEmpty($result);
    }
}
