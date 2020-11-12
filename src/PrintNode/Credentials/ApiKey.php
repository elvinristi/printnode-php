<?php

namespace PrintNode\Credentials;

class ApiKey extends \PrintNode\Credentials implements \PrintNode\Api\CredentialsInterface
{
    /**
     * @param string $apiKey API Key to be used for authentication
     */
    public function __construct ($apiKey)
    {
        if (!\is_string($apiKey) || (trim($apiKey) === '')) {
            throw new \PrintNode\Exception\InvalidArgumentException(
                'Argument 1 passed to PrintNode\Credentials\ApiKey::__construct() must be a valid ApiKey string'
            );
        }
        
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return (string)$this->apiKey;
    }
}
