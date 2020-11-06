<?php

namespace PrintNode\Exception;

class HandlerException extends \Exception
{
    /**
     * @var \PrintNode\Api\HandlerRequestInterface
     */
    private $request;

    /**
     * @var \PrintNode\Api\ResponseInterface
     */
    private $response;

    /**
     * HandlerException constructor.
     *
     * @param string                                      $message
     * @param int                                         $code
     * @param \PrintNode\Api\HandlerRequestInterface|null $request
     * @param \PrintNode\Api\ResponseInterface|null       $response
     * @param \Throwable|null                             $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        \PrintNode\Api\HandlerRequestInterface $request = null,
        \PrintNode\Api\ResponseInterface $response = null,
        \Throwable $previous = null
    ) {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return \PrintNode\Api\HandlerRequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \PrintNode\Api\ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
