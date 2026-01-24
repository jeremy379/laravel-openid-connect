<?php

namespace OpenIDConnect\Tests\Feature\Traits;

trait WithSigningKey
{
    protected function ensureSigningKeyExists(): void
    {
        $path = dirname(__DIR__, 3) . '/tmp/private.key';

        if (file_exists($path)) {
            return;
        }

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $res = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);

        openssl_pkey_export($res, $privateKey);
        file_put_contents($path, $privateKey);
    }
}
