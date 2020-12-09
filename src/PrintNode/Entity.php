<?php

namespace PrintNode;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;
use RuntimeException;

use function get_object_vars;
use function get_class;
use function is_array;
use function is_object;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function preg_match;
use function property_exists;
use function strtolower;
use function substr;

/**
 * Entity
 * Base class for entity objects.
 */
abstract class Entity extends \StdClass implements EntityInterface, \JsonSerializable
{
    /**
     * @var array
     */
    protected static $protectedProperties = [
        'client' => true,
    ];

    /**
     * Reference to the client
     * @var \PrintNode\Request
     */
    protected $client;

    /**
     * @param \PrintNode\Request $parentClient
     */
    public function __construct(\PrintNode\Request $parentClient)
    {
        $this->client = $parentClient;
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
     * Recursively cast an object into an array.
     *
     * @param mixed $object
     *
     * @return mixed[]
     */
    private static function toArrayRecursive($object)
    {
        $properties = get_object_vars($object);

        foreach ($properties as $property => $value) {
            if (isset(self::$protectedProperties[$property])) {
                continue;
            }

            if ($value instanceof DateTime) {
                $properties[$property] = $value->format('c');
            } elseif (is_object($value)) {
                $properties[$property] = static::toArrayRecursive(clone $value);
            }
        }

        if (!empty(self::$protectedProperties)) {
            foreach (\array_keys(self::$protectedProperties) as $property) {
                unset($properties[$property]);
            }
        }

        return $properties;
    }

    /**
     * Map array of data to an entity
     *
     * @param Request $client
     * @param mixed $entityName
     * @param mixed $data
     *
     * @return Entity
     * @throws \Exception
     */
    private static function mapDataToEntity(Request $client, $entityName, \stdClass $data)
    {
        $entity = new $entityName($client);

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

        $entity->mapValuesFromJson($data);

        foreach ($properties as $propertyName) {
            if (isset($foreignKeyEntityMap[$propertyName]) && isset($data->$propertyName)) {
                $entity->$propertyName = self::mapDataToEntity(
                    $client,
                    $foreignKeyEntityMap[$propertyName],
                    $data->$propertyName
                );
            /*
            } elseif (is_string($data->$propertyName) &&
                preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $data->$propertyName)) {
                $entity->$propertyName = new DateTime($data->$propertyName);
            } else {
                $entity->$propertyName = json_decode(json_encode($data->$propertyName), true);
            */
            }
        }

        return $entity;
    }

    /**
     * Make an array of specified entity from a Response
     *
     * @param Request      $client
     * @param string       $entityName
     * @param string|array|\stdClass $content
     *
     * @return string|Entity|Entity[]
     * @throws \Exception
     */
    public static function makeFromResponse(Request $client, $entityName, $content)
    {
        $output = [];
        if (is_array($content)) {
            foreach ($content as $entityData) {
                $output[] = self::makeFromResponse($client, $entityName, $entityData);
            }
        } elseif (is_object($content)) {
            $output = self::mapDataToEntity($client, $entityName, $content);
        } else {
            $output = $content;
        }

        return $output;
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

    public function jsonSerialize()
    {
        return $this->toArray();
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
        if (isset(self::$protectedProperties[$propertyName])) {
            throw new \PrintNode\Exception\InvalidArgumentException($propertyName . ' is a protected property.');
        }

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
     * @return mixed|void
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
     * @inheritDoc
     */
    public function foreignKeyEntityMap()
    {
        return [];
    }
}
