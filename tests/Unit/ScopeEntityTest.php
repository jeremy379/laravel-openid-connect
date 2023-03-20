<?php

namespace OpenIDConnect\Tests\Unit;

use OpenIDConnect\Entities\ScopeEntity;
use PHPUnit\Framework\TestCase;

class ScopeEntityTest extends TestCase
{
    public function test_scope_has_entity_identifier_property()
    {
        $this->assertTrue(property_exists(new ScopeEntity(), 'identifier'));
    }

    public function test_scope_entity_has_get_identifier_method()
    {
        $this->assertTrue(method_exists(new ScopeEntity(), 'getIdentifier'));
    }

    public function test_scope_entity_set_identifier_method()
    {
        $this->assertTrue(method_exists(new ScopeEntity(), 'setIdentifier'));
    }
}
