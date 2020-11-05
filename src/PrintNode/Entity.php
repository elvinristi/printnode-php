<?php

namespace PrintNode;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;
use function json_last_error;
use function json_last_error_msg;
use RuntimeException;

use function get_object_vars;
use function get_class;
use function is_array;
use function is_object;
use function json_encode;
use function preg_match;
use function property_exists;
use function strtolower;
use function substr;

/**
 * Entity
 * Base class for entity objects.
 */
abstract class Entity implements EntityInterface
{
    /**
     * Recursively cast an object into an array.
     *
     * @param mixed $object
     *
     * @return mixed[]
     */
    private static function toArrayRecursive($object)
    {
        $output = get_object_vars($object);

        foreach ($output as $key => $value) {
            if ($value instanceof DateTime) {
                $output[$key] = $value->format('c');
            } elseif (is_object($value)) {
                $output[$key] = static::toArrayRecursive($value);
            }
        }

        return $output;
    }

    /**
     * Map array of data to an entity
     *
     * @param mixed $entityName
     * @param mixed $data
     *
     * @return Entity
     * @throws \Exception
     */
    private static function mapDataToEntity($entityName, \stdClass $data)
    {
        $entity = new $entityName();

        if (!($entity instanceof Entity)) {
            throw new RuntimeException(
                sprintf(
                    'Object "%s" must extend Entity',
                    $entityName
                )
            );
        }
        $foreignKeyEntityMap = $entity->foreignKeyEntityMap();
        $properties = array_keys(get_object_vars($data));

        foreach ($properties as $propertyName) {
            if (!property_exists($entity, $propertyName)) {
                throw new \UnexpectedValueException(
                    sprintf(
                        'Property %s->%s does not exist',
                        get_class($entity),
                        $propertyName
                    )
                );
            }

            if (isset($foreignKeyEntityMap[$propertyName])) {
                $entity->$propertyName = self::mapDataToEntity(
                    $foreignKeyEntityMap[$propertyName],
                    $data->$propertyName
                );
            } elseif (is_string($data->$propertyName) &&
                preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $data->$propertyName)) {
                $entity->$propertyName = new DateTime($data->$propertyName);
            } else {
                $entity->$propertyName = json_decode(json_encode($data->$propertyName), true);
            }
        }

        return $entity;
    }

    /**
     * Cast entity into an array
     *
     * @param void
     *
     * @return mixed[]
     */
    public function toArray()
    {
        return static::toArrayRecursive($this);
    }

    /**
     * Cast entity into a JSON encoded string
     *
     * @param void
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->dataToJson($this->toArray());
    }

    public function dataToJson($data)
    {
        $result = json_encode($data);

        if (json_last_error()) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        return $result;
    }

    /**
     * Set property on entity
     *
     * @param mixed $propertyName
     * @param mixed $value
     *
     * @return void
     */
    public function __set($propertyName, $value)
    {
        if (!property_exists($this, $propertyName)) {
            throw new InvalidArgumentException(
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
     * Get property on entity
     *
     * @param mixed $propertyName
     *
     * @return mixed
     */
    public function __get($propertyName)
    {
        if (!property_exists($this, $propertyName)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }

        return $this->$propertyName;
    }

    /**
     * Property get/set wrapper for those that prefer
     * $entity->get('propertyName') style access
     *
     * @param mixed $name
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!preg_match('/^(get|set)(.+)$/', $name, $matchesArray)) {
            throw new BadMethodCallException(
                sprintf(
                    'method "%s" does not exist on entity "%s"',
                    $name,
                    get_class($this)
                )
            );
        }
        $propertyName = $matchesArray[2];
        $propertyName = strtolower(substr($propertyName, 0, 1)) . substr($propertyName, 1);
        if (!property_exists($this, $propertyName)) {
            throw new BadMethodCallException(
                sprintf(
                    'Entity %s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }
        switch ($matchesArray[1]) {
            case 'set':
                $this->$propertyName = $arguments[0];
                break;
            case 'get':
                return $this->$propertyName;
        }
    }

    /**
     * Make an array of specified entity from a Response
     *
     * @param string       $entityName
     * @param string|array $content
     *
     * @return Entity[]
     * @throws \Exception
     */
    public static function makeFromResponse($entityName, $content)
    {
        $output = [];
        if (is_array($content)) {
            foreach ($content as $entityData) {
                $output[] = self::makeFromResponse($entityName, $entityData);
            }
        } elseif (is_object($content)) {
            $output = self::mapDataToEntity($entityName, $content);
        } else {
            $output = $content;
        }

        return $output;
    }
}
