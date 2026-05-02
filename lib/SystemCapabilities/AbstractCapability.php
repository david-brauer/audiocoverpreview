<?php
namespace OCA\AudioCoverPreview\SystemCapabilities;

abstract class AbstractCapability {
    protected $binary = '';
    protected $isCapable= false;
    protected bool $skipChecks = false;
    protected abstract function checkCapability():bool;
    public abstract function getVersionString():string;


    protected function binaryExists():bool
    {
        $binary = shell_exec("which ".$this->binary);
        if(str_contains($binary,$this->binary)){
            return true;
        }
        return false;
    }

    public function hasCapability():bool
    {
        return $this->isCapable;
    }

    public function getBinary():string
    {
        return $this->binary;
    }
}