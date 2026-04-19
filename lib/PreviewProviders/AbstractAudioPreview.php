<?php

namespace OCA\AudioCoverPreview\PreviewProviders;

use OC\Preview\ProviderV2;
use OCA\AudioCoverPreview\Converters\ImagemagickConverter;
use OCA\AudioCoverPreview\SystemCapabilities\FfmpegCapability;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Files\File;
use OCP\IImage;
use OCP\Files\FileInfo;
use \Psr\Log\LoggerInterface;

abstract class AbstractAudioPreview extends ProviderV2 {

    protected LoggerInterface $logger;
    protected IAppConfig $config;
    protected string $appName;

    protected FfmpegCapability $ffmpegCapability;
    protected ImagemagickConverter $imConverter;


    public function __construct(LoggerInterface $logger, IAppConfig $config, string $appName)
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->appName = $appName;
        $this->ffmpegCapability = new FfmpegCapability();
        $this->imConverter = new ImagemagickConverter();
    }
    public function isAvailable(FileInfo $file): bool
    {
        if($this->ffmpegCapability->hasCapability()){
            return true;
        }
        $this->logger->warning('ffmpeg not found.Unable to generate preview for '.$file->getName());
        return false;
    }

    public function getThumbnail(File $file, int $maxX, int $maxY): ?IImage
    {
        $absPath = $this->getLocalFile($file);
        $extension = '.jpg'; // This is fixed by design. ffmpeg seems to only support jpg for images in my testing
        $tmpFilePathNoExt='/tmp/'.md5($file->getId().time());
        $tmpFilePath = $tmpFilePathNoExt.$extension;
        $error = shell_exec(
            $this->ffmpegCapability->getBinary().
            " -y -i ".escapeshellarg($absPath). " -an -c:v copy -frames:v 1 -update true ".$tmpFilePath." 2>&1"
            );

        if(!file_exists($tmpFilePath)) {
            // This also happens when no cover is present
            $this->logger->info("Could not generate preview for ".$file->getName(). ". Mybe the file has no cover.",['extra_context'=>$error]);
            return null;
        }

        // Get format and try to re-encode if it is not jpg
        $imExtension = $this->config->getAppValueString('image_format', 'jpg');
        if(!in_array($imExtension, ImagemagickConverter::$supportedFormats)){
            $imExtension ='jpg';
        }

        if($imExtension !== 'jpg'){
            $this->imConverter->setTargetExtension($imExtension);
            if($this->convertWithImIfPossible($tmpFilePathNoExt)){
                $image = $this->createImageFromPath($tmpFilePathNoExt.'.'.$imExtension,$maxX,$maxY);
                unlink($tmpFilePath);
                return $image;
            };
        }

        $image = $this->createImageFromPath($tmpFilePath, $maxX, $maxY);
        // For some reason ffmpeg can ouptut files with the wrong image marker
        // this leads to php-gd not being able to read it. Try fixing it with imagemagick here
        // by converting it to the same format but with a fixed marker
        if($image === null){
            if(!$this->convertWithImIfPossible($tmpFilePathNoExt)){
                unlink($tmpFilePath);
                return null;
            }
            //Try again after imagemagick conversion
            $image = $this->createImageFromPath($tmpFilePathNoExt.'.'.$imExtension,$maxX,$maxY);
        }
        unlink($tmpFilePath);
        return $image;
    }

    private function convertWithImIfPossible(string $filepath):bool {
        $imCapability = $this->imConverter->getAvailableCapability();
        if($imCapability === null){
            return false;
        }
        return $this->imConverter->convertWithImagemagick($imCapability,$filepath);
    }

    private function createImageFromPath(string $tmpFilePath, int $maxX, int $maxY){
        $image = new \OCP\Image();
		$image->loadFromFile($tmpFilePath);
        if(!$image->valid()){
            return null;
        }
        $image->scaleDownToFit($maxX, $maxY);
        unlink($tmpFilePath);
        return $image;
    }
} 