<?php


namespace Yetione\GatewayRequest\Logging;

use Monolog\Processor\TagProcessor as MonologTagProcessor;
use Webpatser\Uuid\Uuid;

class TagProcessor extends MonologTagProcessor
{
    protected string $requestId;

    public const REQUEST_ID = 'request_id';

    public function __construct(array $tags = [])
    {
        parent::__construct($tags);
        $this->setRequestId(Uuid::generate(4)->string);
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     * @return TagProcessor
     */
    public function setRequestId(string $requestId): TagProcessor
    {
        $this->requestId = $requestId;
        $this->addTags([static::REQUEST_ID=>$requestId]);
        return $this;
    }
}
