<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * 
 * @property-read string $Account
 * @property-read string $ApiKeys
 * @property-read string $Tags
 */

class ChildAccount extends Entity
{
    /**
     * A printnode Account object
     * @var \PrintNode\Entity\Account
     */
    protected $Account;
    
    /**
     * An array of API keys
     * @var array
     */
    protected $ApiKeys;
    
    /**
     * An array of tags
     * @var array
     */
    protected $Tags;

    /**
     * @inheritDoc
     */
    public function foreignKeyEntityMap()
    {
        return [
            'Account' => \PrintNode\Entity\Account::class,
        ];
    }
}
