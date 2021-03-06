<?php

namespace PrintNode\Entity;

/**
 * State
 *
 * Object representing a State in PrintNode API
 *
 * @property-read int $printJobId
 * @property-read string $state
 * @property-read string $message
 * @property-read object $data
 * @property-read string $clientVersion
 * @property-read DateTime $createTimestamp
 * @property-read int $age
 */
class PrintJobState extends \PrintNode\Entity
{
    /**
     * The print job id
     * @var int
     */
    protected $printJobId;

    /**
     * The state code for the print job
     * @var string
     */
    protected $state;

    /**
     * A more verbose status message for this job
     * @var string
     */
    protected $message;

    /**
     *
     * @var mixed
     */
    protected $data;

    /**
     * The version of the client that printed the print job
     * @var string
     */
    protected $clientVersion;

    /**
     * The create timestamp of the status
     * @var string
     */
    protected $createTimestamp;

    /**
     * The age of the state in seconds
     * @var int
     */
    protected $age;

    public function foreignKeyEntityMap()
    {
        return [];
    }
}
