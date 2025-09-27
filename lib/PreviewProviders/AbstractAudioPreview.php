<?php

namespace OCA\AudioCoverPreview\PreviewProviders;

use OC\Preview\ProviderV2;
use OCP\Files\File;
use OCP\IImage;
use OCP\Files\FileInfo;
use \Psr\Log\LoggerInterface;

abstract class AbstractAudioPreview extends ProviderV2 {

    protected $logger;
    protected $appName;

    public function __construct(LoggerInterface $logger,string $appName)
    {
        $this->logger = $logger;
        $this->appName = $appName;
    }
    public function isAvailable(FileInfo $file): bool
    {
        $supportsFfmpeg = shell_exec("which ffmpeg");
        if(str_contains($supportsFfmpeg,"ffmpeg")){
            return true;
        }
        $this->logger->warning('ffmpeg not found.Unable to generate preview for '.$file->getName());
        return false;
    }

    public function getThumbnail(File $file, int $maxX, int $maxY): ?IImage
    {
        $absPath = $this->getLocalFile($file);
        $tmpFilePath = '/tmp/'.md5($file->getName()).'.jpg';
        $error = shell_exec("ffmpeg -i ".escapeshellarg($absPath). " -an -c:v copy ".$tmpFilePath." 2>&1");
      
        if(file_exists($tmpFilePath)) {
            $image = new \OCP\Image();
			$image->loadFromFile($tmpFilePath);
            // For some reason ffmpeg can ouptut files with the wrong image marker
            // this leads to php not being able to read it. Try fixing it with imagemagick here
            // by converting it to the same format but with a fixed marker
            
            if($image->mimeType() !== "image/jpeg"){
                if(!$this->convertWithImagemagick($tmpFilePath)){
                    return null;
                }
                //Try again after imagemagick conversion
                $image->loadFromFile($tmpFilePath);
            }
            $image->scaleDownToFit($maxX, $maxY);
            unlink($tmpFilePath);
            return $image;
        }
        // This also happens when no over is present
        $this->logger->info("Could not generate preview for ".$file->getName(). ". Mybe the file has no cover.",['extra_context'=>$error]);
        return null;
    }

    private function convertWithImagemagick($filepath): bool
    {
        $hasMagick = shell_exec("which magick");
        if(str_contains($hasMagick,"magick")){
            shell_exec("magick ".escapeshellarg($filepath). " ".escapeshellarg($filepath));
            return true;
        }

        //IM6 Fallback
        $hasConvert = shell_exec("which convert");
        if(str_contains($hasConvert,"convert")){
            shell_exec("convert ".escapeshellarg($filepath). " ".escapeshellarg($filepath));
            return true;
        }

        // No IM detected. Not doing anything
        return false;
    }
} 