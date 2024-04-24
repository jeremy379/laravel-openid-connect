<?php

use Illuminate\Support\Facades\Route;
use OpenIDConnect\Laravel\DiscoveryController;
use OpenIDConnect\Laravel\JwksController;

if (config('openid.routes.discovery', true)) {
    Route::get('/oauth/jwks', JwksController::class)->name('openid.jwks');
}
if (config('openid.routes.jwks', true)) {
    Route::get('/.well-known/openid-configuration', DiscoveryController::class)->name('openid.discovery');
}
