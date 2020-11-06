<?php

namespace PrintNode\Api;

interface MessageInterface
{
    const HEADER_AUTHORIZATION = 'Authorization';

    /**
     * Sets actual headers which have been sent/received by handler.
     *
     * In different circumstances there MAY be more actual headers than has been defined in message.
     * Message original headers MUST retain immutable, therefore extra headers MAY only append
     * and MUST NOT affect anyhow on originally defined in message headers.
     *
     * Headers MUST retain immutable as they were sent by handler.
     *
     * @param string $headers
     *
     * @return void
     */
    public function setActualHeaders(string $headers);

    /**
     * Gets actual headers which have been sent/received by handler.
     *
     * This method MUST return immutable headers as they were set by setActualHeaders method.
     *
     * @return string|null
     */
    public function getActualHeaders();

    /**
     * Sets message's request/response timestamp.
     *
     * Timestamp MUST be float value of current Unix timestamp with microseconds.
     *
     * Timestamp MUST be set at very end right before message dispatch for request and at very beginning
     * right after message has been received for response.
     *
     * @param float $timestamp
     *
     * @return void
     * @since 1.0.0
     */
    public function setTimestamp(float $timestamp);

    /**
     * Gets message's request/response timestamp.
     *
     * This method MUST return immutable timestamp as it was set by setTimestamp method.
     * This method MUST return NULL if no value has been set for timestamp.
     *
     * @return float|null
     */
    public function getTimestamp();

    /**
     * @return string|null
     */
    public function getBody();

    /**
     * @param string|null $body
     *
     * @return string|null
     */
    public function setBody(string $body = null);
}
