# OpenID Connect

OpenID Connect support to the PHP League's OAuth2 Server.

This is a fork of ronvanderheijden/openid-connect.

It's made to support only Laravel and laravel Passport.

**Compatible with [Laravel Passport](https://laravel.com/docs/10.x/passport)!**

## Requirements

* Requires PHP version `^8.2`.
* [lcobucci/jwt](https://github.com/lcobucci/jwt) version `^4.0`.
* [league/oauth2-server](https://github.com/thephpleague/oauth2-server) `^8.2`.
* Larave 10

## Installation

Add in your composer.json

````json
 "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jeremy379/openid-connect"
        }
    ],
````

```sh
composer require ronvanderheijden/openid-connect:dev-main
```

Now when calling the `oauth/authorize` endpoint, provide the `openid` scope to get an `id_token`.  
Provide more scopes (e.g. `openid profile email`) to receive additional claims in the `id_token`.

The id_token will be returned after the call to the `oauth/token` endpoint. 

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

## Support

You can fill an issue in the github section dedicated for that. I'll try to maintain this fork.

## License
OpenID Connect is open source and licensed under [the MIT licence](https://github.com/ronvanderheijden/openid-connect/blob/master/LICENSE.txt).
