<?php

namespace PrintNode\Message;

abstract class AbstractMessage
{
    /**
     * Actual headers which have been sent/received by handler.
     *
     * @var string
     */
    private $actualHeaders;

    /**
     * Message's request/response timestamp.
     *
     * @var float
     */
    private $timestamp;

    /**
     * @var string|null
     */
    private $body;

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

    public function setActualHeaders(string $headers)
    {
        $this->actualHeaders = $headers;
    }

    /**
     * @inheritdoc
     */
    public function getActualHeaders()
    {
        return $this->actualHeaders;
    }

    /**
     * @inheritdoc
     */
    public function setTimestamp(float $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
