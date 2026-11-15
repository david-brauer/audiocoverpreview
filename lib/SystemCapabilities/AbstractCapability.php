<?php

namespace OCA\AudioCoverPreview\SystemCapabilities;

abstract class AbstractCapability
{
    protected string $binary = '';
    protected bool $isCapable = false;
    protected bool $skipChecks = false;

    abstract protected function checkCapability(): bool;
    abstract public function getVersionString(): string;


    protected function binaryExists(): bool
    {
        $binary = shell_exec("which " . $this->binary);
        if (str_contains($binary, $this->binary)) {
            return true;
        }
        return false;
    }

    public function hasCapability(): bool
    {
        return $this->isCapable;
    }

    public function getBinary(): string
    {
        return $this->binary;
    }
}
