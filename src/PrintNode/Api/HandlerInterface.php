<?php

namespace PrintNode\Api;

use PrintNode\Response;

interface HandlerInterface
{
    public function setCredentials(string $credentials);
    public function setTimeout(int $timeout);
    public function setHeaders(array $headers);
    public function setChildAuth(array $childAuthHeader);
    public function run(HandlerRequestInterface $request, string $data = null): Response;
}