<?php

namespace OpenIDConnect\Laravel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class DiscoveryController
{
    /**
     * Compatible with https://openid.net/specs/openid-connect-discovery-1_0.html, chapter 3
     */
    public function __invoke(Request $request)
    {
        $response = [
            'issuer' => url('/'),
            'authorization_endpoint' => route('passport.authorizations.authorize'),
            'token_endpoint' => route('passport.token'),
            'grant_types_supported' => $this->getSupportedGrantTypes(),
            'response_types_supported' => $this->getSupportedResponseTypes(),
            'subject_types_supported' => [
                'public',
            ],
            'id_token_signing_alg_values_supported' => [
                'RS256',
            ],
            'scopes_supported' => $this->getSupportedScopes(),
            'token_endpoint_auth_methods_supported' => [
                'client_secret_basic',
                'client_secret_post',
            ],
        ];

        if (Route::has('openid.userinfo')) {
            $response['userinfo_endpoint'] = route('openid.userinfo');
        }

        if (Route::has('openid.jwks')) {
            $response['jwks_uri'] = route('openid.jwks');
        }

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Returns JSON array containing a list of the OAuth 2.0 [RFC6749] scope values that this server supports.
     * The server MUST support the openid scope value. Servers MAY choose not to advertise some supported scope values even when this parameter is used,
     * although those defined in [OpenID.Core] SHOULD be listed, if supported.
     */
    private function getSupportedScopes(): array {
        $scopes = array_keys(config('openid.passport.tokens_can'));

        if (!config('openid.hide_scopes', false)) {
            return $scopes;
        }

        /**
         * Otherwise, only return scopes from the OpenID Core Spec, section 5.4
         */
        return array_intersect($scopes, [
            'openid',
            'profile',
            'email',
            'address',
            'phone',
        ]);
    }

    private function getSupportedGrantTypes(): array {
        // See PassportServiceProvider for grant types that cannot be disabled
        $grants = [
            'authorization_code', // Cannot be disabled in Passport
            'client_credentials', // Cannot be disabled in Passport
            'refresh_token',  // Cannot be disabled in Passport
        ];

        if (Passport::$implicitGrantEnabled) {
            $grants[] = "implicit";
        }

        if (Passport::$passwordGrantEnabled) {
            $grants[] = "password";
        }

        return $grants;
    }

    /**
     * Returns JSON array containing a list of the OAuth 2.0 response_type values that this OP supports.
     * Dynamic OpenID Providers MUST support the code, id_token, and the id_token token Response Type values.
     */
    private function getSupportedResponseTypes(): array {
        /**
         * These are always possible with Auth Code Grant
         */
        $response_types = [
            'code',
            /**
             * Passport does not actually support `code id_token` or `id_token` and
             * we return the ID token regardless on all requests.
             *
             * This doesn't form a problem however, even the OIDC spec doesn't do this correctly.
             */
        ];

        if (Passport::$implicitGrantEnabled) {
            /**
             * Return all variants, indicating both Auth Code & implicit are allowed
             */
            return array_merge($response_types, [
                'token',
                /**
                 * Passport doesn't support `code token` either.
                 */
            ]);
        }

        return $response_types;
    }
}
