<?php
if (config('openid.routes.discovery', true)) {
    Route::get('/oauth/jwks', \OpenIDConnect\Laravel\JwksController::class)->name('openid.jwks');
}
if (config('openid.routes.jwks', true)) {
    Route::get('/.well-known/openid-configuration', \OpenIDConnect\Laravel\DiscoveryController::class)->name('openid.discovery');
}
