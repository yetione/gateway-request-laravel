<?php


namespace Yetione\GatewayRequest\Logging;


class LaravelLoggingServiceProvider extends AbstractLoggingServiceProvider
{
    protected string $logManagerAlias = 'log';
}
