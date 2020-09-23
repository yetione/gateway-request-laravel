<?php


namespace Yetione\GatewayRequest\Logging;

use Illuminate\Log\LogManager as IlluminateLogManager;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\ProcessableHandlerInterface;

class LogManager extends IlluminateLogManager
{
    protected TagProcessor $tagProcessor;


    public function getTagProcessor(): TagProcessor
    {
        if (!isset($this->tagProcessor)) {
            $this->tagProcessor = new TagProcessor();
        }
        return $this->tagProcessor;
    }

    protected function prepareHandler(HandlerInterface $handler, array $config = [])
    {
        $result = parent::prepareHandler($handler, $config);
        if ($result instanceof ProcessableHandlerInterface) {
            $result->pushProcessor($this->getTagProcessor());
        }
        return $result;
    }
}
