<?php

namespace PrintNode;

use InvalidArgumentException;
use PrintNode\Api\ResponseInterface;
use PrintNode\Message\AbstractMessage;

use function preg_grep;
use function preg_match;

/**
 * Response
 * HTTP response object.
 */
class Response extends AbstractMessage implements ResponseInterface
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $reasonPhrase;

    public function __construct(
        int $code = self::CODE_OK,
        string $reasonPhrase = ''
    ) {
        $this->checkStatusCode($code);
        $this->checkReasonPhrase($reasonPhrase);

        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * Checks code is integer and between expected range
     *
     * @param $code
     *
     * @return void
     */
    private function checkStatusCode($code)
    {
        if (false === (is_int($code)
                || filter_var($code, FILTER_VALIDATE_INT, ['options' => ['min_range' => 100, 'max_range' => 599]])
            )) {
            throw new InvalidArgumentException('Code is expected to be int and between 100 and 599');
        }
    }

    /**
     * Checks reason phrase is string type
     *
     * @param $reasonPhrase
     *
     * @return void
     */
    private function checkReasonPhrase($reasonPhrase)
    {
        if (false === is_string($reasonPhrase)) {
            throw new InvalidArgumentException('Reason phrase expected to be type of string');
        }
    }

    /**
     * Extract the HTTP status code and message
     * from the Response headers
     *
     * @param void
     *
     * @return mixed[]
     */
    private function getStatus()
    {
        if (!($statusArray = preg_grep('/^HTTP\/(1.0|1.1)\s+(\d+)\s+(.+)/', $this->getHeaders()))) {
            throw new \RuntimeException('Could not determine HTTP status from API response');
        }

        if (!preg_match('/^HTTP\/(1.0|1.1)\s+(\d+)\s+(.+)/', $statusArray[0], $matchesArray)) {
            throw new \RuntimeException('Could not determine HTTP status from API response');
        }

        return [
            'code' => $this->code,
            'message' => $matchesArray[3],
        ];
    }

    /**
     * Get Response body
     *
     * @param void
     *
     * @return string
     */
    public function getContent()
    {
        return parent::getBody();
    }

    /**
     * Get Response headers
     *
     * @param void
     *
     * @return mixed[]
     */
    public function getHeaders()
    {
        return \explode("\r\n", parent::getActualHeaders());
    }

    /**
     * @inheritDoc
     */
    public function getDecodedContent()
    {
        return $this->getBody() ? \json_decode($this->getBody(), true) : null;
    }

    /**
     * @inheritDoc
     */
    public function getDecodedAsEntity(string $class)
    {
        $content = $this->getBody() ? \json_decode($this->getBody(), false) : new \stdClass();

        return Entity::makeFromResponse($class, $content);
    }

    /**
     * Get HTTP status code
     *
     * @param void
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->getStatus()['message'] ?? null;
    }
}
