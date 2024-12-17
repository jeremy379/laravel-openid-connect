
[![PHP 8.2](https://github.com/jeremy379/laravel-openid-connect/actions/workflows/php82.yml/badge.svg)](https://github.com/jeremy379/laravel-openid-connect/actions/workflows/php82.yml)

# OpenID Connect for Laravel

OpenID Connect support to the PHP League's OAuth2 Server.

This is a fork of [ronvanderheijden/openid-connect](https://github.com/ronvanderheijden/openid-connect).

It's made to support only Laravel and [Laravel Passport](https://laravel.com/docs/10.x/passport).

## Requirements

* Requires PHP version `^8.2`.
* [lcobucci/jwt](https://github.com/lcobucci/jwt) version `^4.0`.
* [league/oauth2-server](https://github.com/thephpleague/oauth2-server) `^8.2`.
* Laravel 10 or Laravel 11
* Laravel Passport installed and configured

## Installation

```sh
composer require jeremy379/laravel-openid-connect
```

Now when calling the `oauth/authorize` endpoint, provide the `openid` scope to get an `id_token`.  
Provide more scopes (e.g. `openid profile email`) to receive additional claims in the `id_token`.

The id_token will be returned after the call to the `oauth/token` endpoint. 

### Laravel 11

On Laravel 11 you may need to register the package: https://github.com/jeremy379/laravel-openid-connect/issues/31 

## Configuration

### 1.) Add the scope in your AuthServiceProvider in boot() method.

```php
Passport::tokensCan(config('openid.passport.tokens_can'));
````

You may want to combine existing scope and oauth implementation with the open ID connect.

```php
$scopes = array_merge($yourScope, config('openid.passport.tokens_can'));
Passport::tokensCan($scopes);
````

### 2.) create an entity
Create an entity class in `app/Entities/` named `IdentityEntity` or `UserEntity`. This entity is used to collect the claims.

You can customize the entity setup by using another IdentityRepository, this is customizable in the config file.

```php
# app/Entities/IdentityEntity.php
namespace App\Entities;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use OpenIDConnect\Claims\Traits\WithClaims;
use OpenIDConnect\Interfaces\IdentityEntityInterface;

class IdentityEntity implements IdentityEntityInterface
{
    use EntityTrait;
    use WithClaims;

    /**
     * The user to collect the additional information for
     */
    protected User $user;

    /**
     * The identity repository creates this entity and provides the user id
     * @param mixed $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
        $this->user = User::findOrFail($identifier);
    }

    /**
     * When building the id_token, this entity's claims are collected
     */
    public function getClaims(): array
    {
        return [
            'email' => $this->user->email,
        ];
    }
}
```

### The id token is a JWT and the client should verify the signature.

Here is an example to verify the signature with lcobucci/jwt

```php 
  $config = Configuration::forSymmetricSigner(
    new \Lcobucci\JWT\Signer\Rsa\Sha256(),
    InMemory::file(base_path('oauth-public.key')) //This is the public key generate by passport. You need to share it.
  );
  
  //Parse the token
  
  $token = $config->parser()->parse($idtoken);
  
  $signatureValid = $config->validator()->validate($token, new \Lcobucci\JWT\Validation\Constraint\SignedWith($config->signer(), $config->signingKey()));
```

### Publishing the config
In case you want to change the default scopes, add custom claim sets or change the repositories, you can publish the openid config using:
```sh
php artisan vendor:publish --tag=openid
```

### Using nonce

When `nonce` is required, you need to pass it as a query parameter to `passport.authorizations.approve` during authorization step.

Example based on default Passport's `authorize.blade.php`:
```
<form method="post" action="{{ route('passport.authorizations.approve').'?nonce='.$request->nonce }}">
```

### Optional Configuration
You can add any JWT Token Headers that you want to the `token_headers` array in your `openid` configuration file.

This can be useful to define things like the [`kid`(Key ID)](https://datatracker.ietf.org/doc/html/rfc7517#section-4.5).  The `kid` can be any string as long as it can uniquely identify the key you want to use in your [JWKS](https://datatracker.ietf.org/doc/html/rfc7517#section-5). This can be useful when changing or rolling keys.

Example:

```php
'token_headers' => ['kid' => base64_encode('public-key-added-2023-01-01')]
```

Additionally, you can configure the JWKS url and some settings for discovery in the config file.

_Note: If you define a `kid` header, it will be added to the JWK returned at the jwks_url (if `jwks` is enabled in the configuration)._

## Support

You can fill an issue in the github section dedicated for that. I'll try to maintain this fork.

Are you actively using this package and wanna help too? Reach out to me, I'm looking for help to maintain this package.

## License
OpenID Connect is open source and licensed under [the MIT licence](https://github.com/ronvanderheijden/openid-connect/blob/master/LICENSE.txt).
