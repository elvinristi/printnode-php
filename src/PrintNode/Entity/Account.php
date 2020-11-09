<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * Account
 *
 * Object representing an Account to POST in PrintNode API
 *
 * @property-read string[string] $Account
 * @property-read string[] $ApiKeys
 * @property-read string[string] $Tags
 */
class Account extends Entity
{

    /**
     * Account Id
     * @var int
     */
    public $id;

    /**
     * First Name
     * @var string
     */
    public $firstname;

    /**
     * Last Name
     * @var string
     */
    public $lastname;

    /**
     * Email Address
     * @var string
     */
    public $email;

    /**
     * Password
     * @var string
     */
    public $password;

    /**
     * Creator Reference
     * @var string
     */
    public $creatorRef;
}
