<?php
namespace OCA\AudioCoverPreview\SystemCapabilities\Imagemagick;

use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\AbstractImageMagickCapability;

class Imagemagick6Capability extends AbstractImageMagickCapability {

    public function __construct()
    {
        $this->binary = 'convert';
        $this->isCapable = $this->checkCapability();
        $this->initSupportedFormats();
    }

    public function getVersionString():string
    {
        $output = shell_exec($this->binary.' -version');
        if(!is_bool($output)){
            $versionParts = explode(PHP_EOL,$output);
            if(\count($versionParts) >0){
                return $versionParts[0];
            }
        }
        return '';
    }

    protected function checkCapability():bool
    {
        return $this->binaryExists($this->binary);
    }
}
