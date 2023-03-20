<?php

namespace OpenIDConnect\Tests\Feature;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\FileCouldNotBeRead;
use OpenIDConnect\Tests\Factories\ConfigutationFactory;
use OpenIDConnect\Tests\Factories\KeyFactory;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function test_can_create_configurations()
    {
        $this->assertInstanceOf(
            Configuration::class,
            ConfigutationFactory::default(),
        );
    }

    public function test_can_create_configurations_with_key_from_text()
    {
        $this->assertInstanceOf(
            Configuration::class,
            ConfigutationFactory::withSignerKey(
                KeyFactory::signerKeyFromText('my_secret'),
            ),
        );
    }

    public function test_configuration_needs_correct_signer_key_path()
    {
        $this->expectException(FileCouldNotBeRead::class);
        $this->assertInstanceOf(
            Configuration::class,
            ConfigutationFactory::withSignerKey(
                KeyFactory::signerKeyFromFile('does/not/exist'),
            ),
        );
    }
}
