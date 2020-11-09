<?php

namespace PrintNode\Credentials;

class Username extends \PrintNode\Credentials implements \PrintNode\Api\CredentialsInterface
{
    /**
     * Class constuctor
     * @param string $username Username to be used for authentication
     * @param string $password Password to be used for authentication
     */
    public function __construct ($username, $password)
    {
        
        if (!\is_string($username) || (trim($username) === '')) {
            throw new \PrintNode\Exception\InvalidArgumentException(
                'Argument 1 passed to PrintNode\Credentials\ApiKey::__construct() must be a valid username string'
            );
        }
        
        if (!\is_string($password) || (trim($password) === '')) {
            throw new \PrintNode\Exception\InvalidArgumentException(
                'Argument 2 passed to PrintNode\Credentials\ApiKey::__construct() must be a valid password string'
            );
        }
        
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return (string)\implode(':', [$this->username, $this->password]);
    }
}
