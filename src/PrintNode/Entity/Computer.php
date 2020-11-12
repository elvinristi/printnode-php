<?php

namespace PrintNode\Entity;

/**
 * PrintNode_Computer
 *
 * Object representing a Computer in PrintNode API
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $inet
 * @property-read string $inet6
 * @property-read string $version
 * @property-read string $jre
 * @property-read object $systemInfo
 * @property-read boolean $acceptOfflinePrintJobs
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class Computer extends \PrintNode\Entity
{
    /**
     * Computer Id
     * @var int
     */
    protected $id;

    /**
     * Computer Name
     * @var string
     */
    protected $name;

    /**
     * Reserved
     * @var mixed
     */
    protected $inet;

    /**
     * Reserved
     * @var mixed
     */
    protected $inet6;

    /**
     * Reserved
     * @var mixed
     */
    protected $hostname;

    /**
     * Reserved
     * @var mixed
     */
    protected $version;

    /**
     * Reserved
     * @var mixed
     */
    protected $jre;

    /**
     * Reserved
     * @var mixed
     */
    protected $systemInfo;

    /**
     * Reserved
     * @var mixed
     */
    protected $acceptOfflinePrintJobs;

    /**
     * The time and date the computer was first registered with PrintNode
     * @var mixed
     */
    protected $createTimestamp;

    /**
     * Current state of the computer
     * @var mixed
     */
    protected $state;

    /**
     * Returns an array of the printers present on this computer
     *
     * @param int   $offset
     * @param int   $limit
     * @param mixed $printerSet
     *
     * @return array
     * @throws \PrintNode\Exception\HTTPException
     */
    public function viewPrinters($offset = 0, $limit = 500, $printerSet = null)
    {
        return $this->client->viewPrinters($offset, $limit, $printerSet, $this->id);

    }

    /**
     * @param type $deviceName
     * @param type $deviceNumber
     */
    public function viewScales($deviceName = null, $deviceNumber = null)
    {

        return $this->client->viewScales($this->id, $deviceName, $deviceNumber);

    }

    /**
     * @inheritdoc
     */
    public function foreignKeyEntityMap()
    {
        return [];
    }
}
