<?php
namespace OCA\AudioCoverPreview\SystemCapabilities\Imagemagick;

use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\AbstractImageMagickCapability;

class Imagemagick6Capability extends AbstractImageMagickCapability {

    public function __construct(bool $skipChecks = false)
    {
        $this->binary = 'convert';
        $this->skipChecks = $skipChecks;
        $this->isCapable = $this->checkCapability();
        
        if(!$skipChecks) {
            $this->initSupportedFormats();
        }
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
        // this cannot be skipped since we need to know if we have IM6 or 7 at some point
        return $this->binaryExists($this->binary);
    }
}
