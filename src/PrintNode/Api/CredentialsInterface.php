<?php

namespace PrintNode\Api;

/**
 * Credentials
 *
 * Credential store used by Request
 * when communicating with API server.
 */
interface CredentialsInterface
{
    /**
     * Constructor
     * @param mixed $username
     * @param mixed $password
     * @return \PrintNode\CredentialsInterface
     */
     
    /**
     * Convert object into a string
     * @param void
     * @return string
     */
    public function __toString();
}
