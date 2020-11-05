<?php

namespace PrintNode\Entities;

/**
 * Client
 * Object representing a Client in PrintNode API
 *
 * @property-read int      $id
 * @property-read bool     $enabled
 * @property-read string   $edition
 * @property-read string   $version
 * @property-read string   $os
 * @property-read string   $filename
 * @property-read string   $filesize
 * @property-read string   $sha1
 * @property-read DateTime $releaseTimestamp
 * @property-read string   $url
 */
class Client extends \PrintNode\Entity
{
    protected $id;
    protected $enabled;
    protected $edition;
    protected $version;
    protected $os;
    protected $filename;
    protected $filesize;
    protected $sha1;
    protected $releaseTimestamp;
    protected $url;

    public function formatForPatch()
    {
        return $this->dataToJson(['enabled' => $this->enabled]);
    }

    public function endPointUrlArg()
    {
        return (string)$this->id;
    }

    public function foreignKeyEntityMap()
    {
        return [];
    }
}
