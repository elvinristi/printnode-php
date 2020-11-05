<?php

namespace PrintNode;

class HandlerException extends \Exception
{
    /**
     * @var \PrintNode\Api\HandlerRequestInterface
     */
    private $request;

    /**
     * @var \PrintNode\Response
     */
    private $response;

    /**
     * HandlerException constructor.
     *
     * @param string                      $message
     * @param int                         $code
     * @param \PrintNode\Api\HandlerRequestInterface|null $request
     * @param \PrintNode\Response|null      $response
     * @param \Throwable|null             $previous
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        \PrintNode\Api\HandlerRequestInterface $request = null,
        \PrintNode\Response $response = null,
        \Throwable $previous = null
    ) {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        return $this->response;
    }
}
