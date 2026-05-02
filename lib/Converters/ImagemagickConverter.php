<?php
namespace OCA\AudioCoverPreview\Converters;

use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\AbstractImageMagickCapability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Imagemagick7Capability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Imagemagick6Capability;

class ImagemagickConverter
{
    private string $sourceExtension='jpg';
    private string $targetExtension='jpg';

    private bool $skipChecks = false;

    public static array $supportedFormats =[
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    protected Imagemagick7Capability $im7Capability;
    protected Imagemagick6Capability $im6Capability;

    public function __construct(bool $skipChecks = false)
    {
        $this->skipChecks = $skipChecks;
        $this->im7Capability = new Imagemagick7Capability($skipChecks);
        $this->im6Capability = new Imagemagick6Capability($skipChecks);
    }

    public function convertWithImagemagick(AbstractImageMagickCapability $imCapability, string $filepath): bool
    {
        if(!$this->skipChecks){
            //Check if the target image format is supported
            if(!$this->isFormatSupported($this->targetExtension)){
                return false;
            }

            if(!$this->isConversionPossible($imCapability)){
                return false;
            }
        }
        $fullSourcePath=$filepath.'.'.$this->sourceExtension;
        $fullTargetPath=$filepath.'.'.$this->targetExtension;
        shell_exec($imCapability->getBinary()." ".escapeshellarg($fullSourcePath). " ".escapeshellarg($fullTargetPath));
        return true;
    }

    public function getAvailableCapability():?AbstractImageMagickCapability
    {
        // Has IM7
        if($this->im7Capability->hasCapability()){
            return $this->im7Capability;
        }
        // Fallback to IM6
        if($this->im6Capability->hasCapability()){
            return $this->im6Capability;
        }
        // No IM detected
        return null;
    }

    public function isFormatSupported(string $format):bool
    {
        if($this->skipChecks){
            return true;
        }

        if(!in_array($format, self::$supportedFormats)){
            return false;
        }

        return true;
    }

    private function isConversionPossible(AbstractImageMagickCapability $imCapability):bool
    {
        //Check if the specific formats requested are available
        $sourceFormatSupport = $imCapability->getFormatByName($this->sourceExtension);
        $targetFormatSupport = $imCapability->getFormatByName($this->targetExtension);
        if($sourceFormatSupport === null || $targetFormatSupport === null){
            return false;
        }
        if(!$sourceFormatSupport->canRead() || !$targetFormatSupport->canWrite()){
            return false;
        }
        return true;
    }

    public function getSourceExtension():string
    {
        return $this->sourceExtension;
    }

    public function setSourceExtension(string $sourceExtension):void
    {
        $this->sourceExtension = $sourceExtension;
    }

    public function getTargetExtension(): string
    {
        return $this->targetExtension;
    }

    public function setTargetExtension(string $targetExtension):void
    {
        $this->targetExtension = $targetExtension;
    }
}