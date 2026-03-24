# AGENTS.md — laravel-openid-connect

## What This Package Does

Adds **OpenID Connect (OIDC) `id_token`** support to [Laravel Passport](https://laravel.com/docs/passport), which is built on top of [league/oauth2-server](https://github.com/thephpleague/oauth2-server).

When a client requests the `openid` scope during an OAuth2 Authorization Code flow, the token response includes a signed JWT `id_token` alongside the standard access token.

---

## Architecture

### Core (framework-agnostic)

| Class | Role |
|-------|------|
| `IdTokenResponse` | Extends the league server's token response to append the `id_token` JWT |
| `Grant/AuthCodeGrant` | Extends the auth code grant to capture and relay `nonce` |
| `ClaimExtractor` | Maps OIDC scopes (`openid`, `profile`, `email`, …) to JWT claims |
| `Claims/ClaimSet` | Associates a scope name with a set of claim keys |
| `Entities/*` | League entity implementations (AccessToken, AuthCode, Client, Identity, etc.) |
| `Repositories/*` | Wires Passport's Eloquent models to league's repository interfaces |
| `Interfaces/IdentityEntityInterface` | Contract for the user-facing identity entity |
| `Interfaces/IdentityRepositoryInterface` | Contract for the identity repository |

### Laravel integration (`src/Laravel/`)

| Class | Role |
|-------|------|
| `PassportServiceProvider` | Registers all bindings, overrides Passport's server builder |
| `DiscoveryController` | Serves `GET /.well-known/openid-configuration` |
| `JwksController` | Serves `GET /.well-known/jwks.json` |
| `config/openid.php` | Package config (scopes, claim sets, repositories, feature flags) |

---

## How the `id_token` Is Built

1. Client requests `/oauth/authorize?scope=openid+email&nonce=xyz`.
2. `AuthCodeGrant` stores the nonce in the auth code payload.
3. On `/oauth/token`, `IdTokenResponse` is invoked after the standard token is issued.
4. `IdentityRepository` instantiates the `IdentityEntity` for the authenticated user.
5. `ClaimExtractor` filters the entity's claims based on the granted scopes.
6. A JWT is built with `lcobucci/jwt`, signed with Passport's RSA private key, and added as `id_token` to the JSON response.

---

## Implementing the Package (Integration Steps)

### 1. Install

```bash
composer require jeremy379/laravel-openid-connect
```

### 2. Register the service provider

In `bootstrap/providers.php`:
```php
\OpenIDConnect\Laravel\PassportServiceProvider::class,
```

Disable Passport's auto-discovered provider in `composer.json` to avoid conflicts:
```json
"extra": {
    "laravel": {
        "dont-discover": ["laravel/passport"]
    }
}
```

Then run `php artisan package:discover`.

### 3. Configure Passport scopes

In your `AuthServiceProvider::boot()`:
```php
Passport::tokensCan(config('openid.passport.tokens_can'));
```

### 4. Register the authorization view

Passport 13+ requires an explicit view binding. In `AuthServiceProvider::boot()` or `AppServiceProvider::register()`:
```php
Passport::authorizationView('auth.oauth.authorize');
// or:
$this->app->bind(AuthorizationViewResponse::class, fn() => new SimpleViewResponse('auth.approve'));
```

Example views are available in [`view-example/`](view-example/).

### 5. Create the IdentityEntity

Create `app/Entities/IdentityEntity.php`:
```php
namespace App\Entities;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use OpenIDConnect\Claims\Traits\WithClaims;
use OpenIDConnect\Entities\Traits\WithCustomPermittedFor;
use OpenIDConnect\Interfaces\IdentityEntityInterface;

class IdentityEntity implements IdentityEntityInterface
{
    use EntityTrait;
    use WithClaims;
    use WithCustomPermittedFor;

    protected User $user;

    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
        $this->user = User::findOrFail($identifier);
    }

    public function getClaims(): array
    {
        return [
            'email' => $this->user->email,
            'name'  => $this->user->name,
        ];
    }
}
```

### 6. Publish and review the config

```bash
php artisan vendor:publish --tag=openid
```

The config file (`config/openid.php`) controls:
- `passport.tokens_can` — available OIDC scopes and their descriptions
- `custom_claim_sets` — map additional scopes to claim keys
- `repositories.identity` — swap in a custom `IdentityRepository`
- `discovery` / `jwks` — enable/disable the discovery and JWKS routes
- `token_headers` — add custom JWT headers (e.g., `kid`)

### 7. Nonce support

Pass `nonce` as a query parameter to the authorization approval route:
```blade
<form method="post" action="{{ route('passport.authorizations.approve').'?nonce='.$request->nonce }}">
```

### 8. Verifying the `id_token` on the client

```php
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

$config = Configuration::forSymmetricSigner(
    new Sha256(),
    InMemory::file(base_path('oauth-public.key'))
);

$token = $config->parser()->parse($idToken);
$valid = $config->validator()->validate(
    $token,
    new \Lcobucci\JWT\Validation\Constraint\SignedWith($config->signer(), $config->signingKey())
);
```

The public key (`oauth-public.key`) is generated by `php artisan passport:keys` and must be shared with consumers.

---

## Running Tests

```bash
composer test          # PHPUnit
composer dev           # PHPUnit --group dev (for WIP tests)
composer ecs-check     # Code style check
composer ecs-fix       # Auto-fix code style
composer lint          # phplint
composer check         # Full suite: lint + style + tests + platform + outdated
```

Tests live in:
- `tests/Feature/` — integration tests covering the full token flow
- `tests/Unit/` — isolated unit tests

---

## Coding Conventions

- PHP **8.2+** minimum. Use typed properties, constructor promotion, and readonly where appropriate.
- Follow the ECS config in `ecs.php`. Run `composer ecs-fix` before every commit.
- Core classes (`src/` outside `src/Laravel/`) must remain **Laravel-agnostic** — no `Illuminate` imports.
- Laravel-specific code belongs exclusively in `src/Laravel/`.
- No inline comments unless a non-obvious logic decision needs clarification.
- Keep the OIDC spec as the source of truth: https://openid.net/specs/openid-connect-core-1_0.html

---

## Key Constraints

- The package **does not provide** authentication routes (login, logout). Those come from the host application.
- The `id_token` is signed with Passport's RSA key pair (`oauth-private.key` / `oauth-public.key`).
- This package is a **fork** of [ronvanderheijden/openid-connect](https://github.com/ronvanderheijden/openid-connect), modernized for Laravel 12 and Passport 13.
- Do **not** add a `Co-authored-by` trailer to commits.
