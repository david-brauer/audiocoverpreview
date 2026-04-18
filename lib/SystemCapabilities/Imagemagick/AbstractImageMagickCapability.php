<?php
namespace OCA\AudioCoverPreview\SystemCapabilities\Imagemagick;

use OCA\AudioCoverPreview\SystemCapabilities\AbstractCapability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Format\ImagemagickFormat;

abstract class AbstractImageMagickCapability extends AbstractCapability
{
    protected array $supportedFormats=[];

    protected function initSupportedFormats():void
    {
        $output = shell_exec('identify -list format');
        if(!is_bool($output)){
            $formats = explode(PHP_EOL,$output);
            if(\count($formats) > 0){
                $this->supportedFormats = $this->parseFormats($formats);
            }
        }
    }

    public function getFormatByName(string $name):?ImagemagickFormat
    {
        foreach($this->supportedFormats as $format){
            if(strtolower($format->getName()) === strtolower($name)){
                return $format;
            }
        }
        return null;
    }

    private function parseFormats(array $lines):array
    {
        $formats=[];
        foreach($lines as $line){
            preg_match('/^\s*([A-Z0-9-]+)[\*\s]{1,}([A-Z0-9*]+)\s{1,}([rw+-]+)\s{1,}([\S ]+)$/',$line,$matches);
            if(\count($matches) <5){
                continue;
            }
            $hasRead=str_contains($matches[3],'r');
            $hasWrite = str_contains($matches[3],'w');
            $format = new ImagemagickFormat($matches[1],$hasRead,$hasWrite,$matches[4]);
            $formats[] = $format;
        }
        return $formats;
    }
}