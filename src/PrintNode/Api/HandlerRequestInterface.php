<?php

namespace PrintNode\Api;

interface HandlerRequestInterface extends MessageInterface
{
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';
    const METHOD_OPTIONS = 'OPTIONS';

    public function getUri(): string;
    public function getMethod(): string;
}
