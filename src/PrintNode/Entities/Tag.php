<?php

namespace PrintNode\Entities;

/**
 * Tag
 * Object representing a Tag for POST in PrintNode API
 *
 * @property-read String $name
 * @property-read String $value
 */
class Tag extends \PrintNode\Entity
{
    protected $name;
    protected $value;

    public function endPointUrlArg()
    {
        return $this->name;
    }

    public function formatForPost()
    {
        return $this->dataToJson($this->value);
    }

    public function foreignKeyEntityMap()
    {
        return [];
    }
}
