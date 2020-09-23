<?php


namespace Yetione\GatewayRequest\Logging;


class LumenLoggingServiceProvider extends AbstractLoggingServiceProvider
{

    protected function configureLogging()
    {
        $this->app->configure('logging');
    }
}
