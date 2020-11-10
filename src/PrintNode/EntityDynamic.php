<?php

namespace PrintNode;

use function get_object_vars;

abstract class EntityDynamic extends Entity
{
    /**
     * Set property on entity
     * @param mixed $propertyName
     * @param mixed $value
     * @return void
     */
    public function __set($propertyName, $value)
    {
        if (isset(self::$protectedProperties[$propertyName])) {
            throw new \PrintNode\Exception\InvalidArgumentException($propertyName . ' is a protected property.');
        }
        
        $this->$propertyName = $value;
    }
    
    /**
     * Maps a json object to this entity
     * 
     * @param array $json The JSON to map to this entity
     * @return bool
     */
    public function mapValuesFromJson($json)
    {
        foreach ($json as $key => $value) {
            $this->$key = $value;
        }
        
        return true;
    }
    
    /**
     * Implements the jsonSerialize method
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        $properties = get_object_vars($this);

        foreach ($properties as $property => $value) {
            if (isset(self::$protectedProperties[$property])) {
                continue;
            }
            
            $json[$property] = $value;
        }
        
        return $json;
    }
    
}