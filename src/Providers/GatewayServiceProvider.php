<?php


namespace Yetione\GatewayRequest\Providers;


use Illuminate\Contracts\Container\Container;
use Yetione\GatewayRequest\Enums\GatewayAuth;
use Yetione\GatewayRequest\Auth\FromGatewayGuard;
use Yetione\GatewayRequest\Auth\RequestHeaderUserProvider;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Http\Parser\AuthHeaders;
use Tymon\JWTAuth\Http\Parser\Parser as JwtReader;

class GatewayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerJwtReader();
        $this->registerRequestProvider();
    }

    public function boot()
    {
        $this->registerUserProvider();
        $this->extendAuthGuard();
    }

    protected function registerUserProvider()
    {
        $this->app['auth']->provider(GatewayAuth::AUTH_PROVIDER, function ($app, array $config) {
            return $app->make(RequestHeaderUserProvider::class);
        });
    }

    protected function extendAuthGuard()
    {
        $this->app['auth']->extend(GatewayAuth::AUTH_GUARD, function ($app, $name, array $config) {
            return $app->make(FromGatewayGuard::class);
        });
    }

    protected function registerJwtReader()
    {
        $this->app->singleton(JwtReader::class, function(Container $app) {
            $reader = new JwtReader($app['request'], [new AuthHeaders]);
            $app->refresh('request', $reader, 'setRequest');
            return $reader;
        });
    }

    protected function registerRequestProvider()
    {
        $this->app->singleton(GatewayRequestProvider::class);
    }
}
