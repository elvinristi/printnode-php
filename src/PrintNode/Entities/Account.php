<?php

namespace PrintNode\Entities;

/**
 * Account
 *
 * Object representing an Account to POST in PrintNode API
 *
 * @property-read string[string] $Account
 * @property-read string[] $ApiKeys
 * @property-read string[string] $Tags
 */
class Account extends \PrintNode\Entity
{
    protected $Account;
    protected $ApiKeys;
    protected $Tags;

    public function formatForPatch()
    {
        return $this->dataToJson($this->Account);
    }

    /**
     * @inheritDoc
     */
    public function foreignKeyEntityMap()
    {
        return [];
    }
}
