<?php

namespace PrintNode\Api;

interface HandlerInterface
{
    public function setTimeout(int $timeout);

    /**
     * @param \PrintNode\Api\HandlerRequestInterface $request
     *
     * @return \PrintNode\Api\ResponseInterface
     * @throws \PrintNode\Exception\HandlerException
     */
    public function run(HandlerRequestInterface $request): ResponseInterface;
}
