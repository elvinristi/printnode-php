<?php

namespace PrintNode\Message;

use PrintNode\Api\HandlerRequestInterface;

class ServerRequest implements HandlerRequestInterface
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $method;
    private $body;

    public function __construct(
        string $uri,
        string $method = null
    ) {
        $this->uri = $uri;
        $this->method = $method ?? HandlerRequestInterface::METHOD_GET;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function setBody(string $body = null)
    {
        return $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }
}
