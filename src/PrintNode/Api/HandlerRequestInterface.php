<?php

namespace PrintNode\Api;

interface HandlerRequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    public function getUri(): string;
    public function getMethod(): string;
    public function getBody();

    /**
     * @param string|null $body
     *
     * @return string|null
     */
    public function setBody(string $body = null);
}
