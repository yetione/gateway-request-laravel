<?php


namespace Yetione\GatewayRequest\Logging;


use Yetione\GatewayRequest\Enums\GatewayHttpHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

abstract class AbstractLoggingServiceProvider extends ServiceProvider
{
    protected string $logManagerAlias = LoggerInterface::class;

    public function register()
    {
        $this->registerLogManager($this->logManagerAlias);
        $this->registerEvents();
    }

    protected function registerLogManager(string $name)
    {
        $this->app->singleton($name, function () {
            $this->configureLogging();
            return new LogManager($this->app);
        });
    }

    protected function registerEvents()
    {
        $this->app->resolving(LogManager::class, function (LogManager $logManager) {
            $this->setRequestId($logManager);
        });
    }

    protected function setRequestId(LogManager $logManager)
    {
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        if ($request->headers->has(GatewayHttpHeaders::REQUEST_ID)) {
            $logManager->getTagProcessor()->setRequestId($request->header(GatewayHttpHeaders::REQUEST_ID));
        }
        return $logManager;
    }

    protected function configureLogging()
    {
    }
}
