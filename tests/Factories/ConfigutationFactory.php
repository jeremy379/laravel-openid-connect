<?php

namespace OpenIDConnect\Tests\Factories;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;

class ConfigutationFactory
{
    private function build(
        ?Key $signerKey = null,
        ?Signer $signer = null,
    ): Configuration {
        return Configuration::forSymmetricSigner(
            $signer ?? new HmacSha256(),
            $signerKey ?? KeyFactory::signerKeyFromFile(),
        );
    }

    public static function default(): Configuration
    {
        return (new static())->build();
    }

    public static function withSignerKey(Key $signerKey): Configuration
    {
        return (new static())->build($signerKey);
    }

    public static function withRsaSigner(): Configuration
    {
        return (new static())->build(null, new RsaSha256());
    }
}
