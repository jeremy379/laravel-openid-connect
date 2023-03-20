<?php

namespace OpenIDConnect\Tests\Feature\Traits;

use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

trait WithDefaultAsserts
{
    public function defaultResponseAsserts(ResponseInterface $response)
    {
        $this->assertInstanceOf(Psr7\Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            'application/json; charset=UTF-8',
            $response->getHeader('content-type')[0]
        );

        $response->getBody()->rewind();
    }

    public function defaultTokenAsserts($json)
    {
        $this->assertSame('Bearer', $json->token_type);
        $this->assertSame(3600, $json->expires_in);
        $this->assertObjectHasAttribute('access_token', $json);
        $this->assertObjectHasAttribute('refresh_token', $json);
    }
}
