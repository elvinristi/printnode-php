<?php

namespace PrintNode\Entity;

/**
 * Printer
 *
 * Object representing a Printer in PrintNode API
 *
 * @property-read int $id
 * @property-read Computer $computer
 * @property-read string $name
 * @property-read string $description
 * @property-read object $capabilities
 * @property-read boolean $default
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class Printer extends \PrintNode\Entity
{
    /**
     * Printer Id
     * @var int
     */
    protected $id;

    /**
     * The computer object that this printer is attached to.
     * @var \PrintNode\Entity\Computer
     */
    protected $computer;

    /**
     * The name of the printer
     * @var string
     */
    protected $name;

    /**
     * The description of the printer reported by the client
     * @var string
     */
    protected $description;

    /**
     * The capabilities of the printer reported by the client
     * @var \PrintNode\Entity\PrinterCapabilities
     */
    protected $capabilities;

    /**
     * Flag that indicates if this is the default printer for this computer
     * @var bool
     */
    protected $default;

    /**
     * The timestamp of the response
     * @var string
     */
    protected $createTimestamp;

    /**
     * The state of the printer reported by the client
     * @var string
     */
    protected $state;

    public function foreignKeyEntityMap()
    {
        return [
            'computer' => \PrintNode\Entity\Computer::class,
            'capabilities' => \PrintNode\Entity\PrinterCapabilities::class,
        ];
    }
}
