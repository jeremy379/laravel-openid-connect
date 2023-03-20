<?php

namespace OpenIDConnect\Tests\Factories;

use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use League\OAuth2\Server\CryptKey;

class KeyFactory
{
    public static function privateKeyPath()
    {
        return dirname(__DIR__, 2) . '/tmp/private.key';
    }

    public static function signerKeyFromText(string $contents): Key
    {
        return InMemory::plainText($contents);
    }

    public static function signerKeyFromFile(?string $path = null): Key
    {
        return InMemory::file($path ?? self::privateKeyPath());
    }

    public static function cryptKey(?string $path = null): CryptKey
    {
        return new CryptKey($path ?? self::privateKeyPath());
    }
}
