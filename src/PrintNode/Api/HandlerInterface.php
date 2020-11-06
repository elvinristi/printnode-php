<?php

namespace PrintNode\Api;

interface HandlerInterface
{
    public function setCredentials(CredentialsInterface $credentials);
    public function setTimeout(int $timeout);
    public function setHeaders(array $headers);
    public function setChildAuth(array $childAuthHeader);

    /**
     * @param \PrintNode\Api\HandlerRequestInterface $request
     *
     * @return \PrintNode\Api\ResponseInterface
     * @throws \PrintNode\Exception\HandlerException
     */
    public function run(HandlerRequestInterface $request): ResponseInterface;
}
