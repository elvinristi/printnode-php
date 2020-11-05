<?php

namespace PrintNode;

use RuntimeException;

use function get_class;
use function property_exists;
use function sprintf;

class Credentials implements CredentialsInterface
{
    /**
     * @var string|null
     */
    private $apiKey;

    /**
     * @var string|null
     */
    private $emailPassword;

    /**
     * return correct authentication credentials
     *
     * @param void
     *
     * @return string
     * */
    public function __toString()
    {
        return (string)($this->apiKey ?? $this->emailPassword);
    }

    /**
     * Set email and password for Email:Password authentication.
     *
     * @param string $email
     * @param string $password
     *
     * @return Credentials
     * */
    public function setEmailPassword(string $email, string $password)
    {
        if (isset($this->apiKey)) {
            throw new RuntimeException(
                "ApiKey already set."
            );
        }

        $this->emailPassword = $email . ': ' . $password;

        return $this;
    }

    /**
     * Set email and password for Email:Password authentication.
     *
     * @param string $apiKey
     *
     * @return Credentials
     * */
    public function setApiKey($apiKey)
    {
        if (isset($this->emailPassword)) {
            throw new RuntimeException(
                "EmailPassword already set."
            );
        }

        $this->apiKey = $apiKey . ':';

        return $this;
    }

    /**
     * Set property on object
     *
     * @param mixed $propertyName
     * @param mixed $value
     *
     * @return void
     * */
    public function __set($propertyName, $value)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }

        $this->$propertyName = $value;
    }

    /**
     * Get property on object
     *
     * @param mixed $propertyName
     *
     * @return mixed
     * */
    public function __get($propertyName)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }

        return $this->$propertyName;
    }
}
