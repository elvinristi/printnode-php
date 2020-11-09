<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * Printer
 *
 * Object representing printer capabilities in PrintNode API
 *
 * @property-read array $bins;
 * @property-read bool $collate;
 * @property-read int $copies;
 * @property-read bool $color;
 * @property-read string $dpis;
 * @property-read array $extent;
 * @property-read array $medias;
 * @property-read array $nup;
 * @property-read array $papers;
 * @property-read array $printrate;
 * @property-read bool $supports_custom_paper_size;
 * 
 */
class PrinterCapabilities extends Entity
{
    
    protected $bins;
    protected $collate;
    protected $color;
    protected $copies;
    protected $dpis;
    protected $duplex;
    protected $extent;
    protected $medias;
    protected $nup;
    protected $papers;
    protected $printrate;
    protected $supports_custom_paper_size;
    
    /**
     * @inheritDoc
     */
    public function foreignKeyEntityMap()
    {
        return [
            'papers' => \PrintNode\Entity\PrinterCapabilities\Papers::class
        ];
    }
}
