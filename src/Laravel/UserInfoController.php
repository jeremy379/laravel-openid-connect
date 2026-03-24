<?php

namespace OpenIDConnect\Laravel;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenIDConnect\ClaimExtractor;

class UserInfoController
{
    public function __invoke(Request $request, ClaimExtractor $claimExtractor): JsonResponse
    {
        $token = $request->user()->token();

        $identity = app(config('openid.repositories.identity'))
            ->getByIdentifier((string) $request->user()->getAuthIdentifier());

        $claims = $claimExtractor->extract($token->scopes, $identity->getClaims());

        $claims['sub'] = (string) $request->user()->getAuthIdentifier();

        return response()->json($claims);
    }
}
