# OpenID Connect

OpenID Connect support to the PHP League's OAuth2 Server.

**Compatible with [Laravel Passport](https://laravel.com/docs/10.x/passport)!**

## Requirements

* Requires PHP version `^8.2`.
* [lcobucci/jwt](https://github.com/lcobucci/jwt) version `^4.0`.
* [league/oauth2-server](https://github.com/thephpleague/oauth2-server) `^8.2`.
* Larave 10

## Installation
```sh
composer require ronvanderheijden/openid-connect
```

Now when calling the `/authorize` endpoint, provide the `openid` scope to get an `id_token`.  
Provide more scopes (e.g. `openid profile email`) to receive additional claims in the `id_token`.

For a complete implementation, visit [the OAuth2 Server example](https://github.com/ronvanderheijden/openid-connect/tree/main/example).

## Laravel Passport

You can use this package with Laravel Passport in 2 simple steps.

### 1.) Add the scope in your AuthServiceProvider

You may want to combine existing scope and oauth implementation with the open ID connect.

```php
$scopes = array_merge($yourScope, config('openid.passport.tokens_can'));
Passport::tokensCan($scopes);
````


### 2.) create an entity
Create an entity class in `app/Entities/` named `IdentityEntity` or `UserEntity`. This entity is used to collect the claims.
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

### Publishing the config
In case you want to change the default scopes, add custom claim sets or change the repositories, you can publish the openid config using:
```sh
php artisan vendor:publish --tag=openid
```

## Support
Found a bug? Got a feature request?  [Create an issue](https://github.com/ronvanderheijden/openid-connect/issues).

## License
OpenID Connect is open source and licensed under [the MIT licence](https://github.com/ronvanderheijden/openid-connect/blob/master/LICENSE.txt).
