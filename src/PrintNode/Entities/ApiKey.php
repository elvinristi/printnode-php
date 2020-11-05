<?php

namespace PrintNode\Entities;

/**
 * ApiKey
 *
 * Object representing an ApiKey for POST in PrintNode API
 *
 * @property-read string $description
 */
class ApiKey extends \PrintNode\Entity
{
    protected $description;

    public function endPointUrlArg()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function foreignKeyEntityMap()
    {
        return [];
    }
}
