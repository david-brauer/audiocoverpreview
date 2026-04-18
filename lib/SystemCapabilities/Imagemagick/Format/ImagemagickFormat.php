<?php
namespace OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Format;

class ImagemagickFormat {
    private string $name;
    private bool $read=false;
    private bool $write=false;
    private string $description = '';

    public function __construct(string $name, bool $read, bool $write, string $description)
    {
        $this->name = $name;
        $this->read = $read;
        $this->write = $write;
        $this->description = $description;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getDescription():string
    {
        return $this->description;
    }

    public function canRead():bool
    {
        return $this->read;
    }

    public function canWrite():bool
    {
        return $this->write;
    }

    public function hasFullSupport():bool
    {
        return $this->read && $this->write;
    }
}