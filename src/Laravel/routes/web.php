<?php

use Illuminate\Support\Facades\Route;
use OpenIDConnect\Laravel\DiscoveryController;
use OpenIDConnect\Laravel\JwksController;
use OpenIDConnect\Laravel\UserInfoController;

if (config('openid.routes.jwks', true)) {
    Route::get(config('openid.routes.jwks_url', '/oauth/jwks'), JwksController::class)->name('openid.jwks');
}
if (config('openid.routes.discovery', true)) {
    Route::get('/.well-known/openid-configuration', DiscoveryController::class)->name('openid.discovery');
}
if (config('openid.routes.userinfo', false)) {
    Route::get('/oauth/userinfo', UserInfoController::class)->middleware('auth:api')->name('openid.userinfo');
}
