<?php

namespace OCA\AudioCoverPreview\Service;

use OCA\AudioCoverPreview\SystemCapabilities\FfmpegCapability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\AbstractImageMagickCapability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Imagemagick6Capability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Imagemagick7Capability;

final class SystemInformationService
{
    // Skips checks for ffmpeg and imagemagick. Should only be set if you are sure you have everything installed and working
    private bool $skipChecks = false;
    private FfmpegCapability $ffmpegCapability;

    private Imagemagick7Capability $im7Capability;
    private Imagemagick6Capability $im6Capability;

    public static array $supportedFormats = [
        'jpg',
        'png',
        'webp'
    ];


    public function __construct(bool $skipChecks)
    {
        $this->skipChecks = $skipChecks;
        $this->ffmpegCapability = new FfmpegCapability($skipChecks);
    }

    public function getAvailableImagemagickCapability(): ?AbstractImageMagickCapability
    {
        // Has IM7
        if (!isset($this->im7Capability)) {
            $this->im7Capability = new Imagemagick7Capability($this->skipChecks);
        }
        if ($this->im7Capability->hasCapability()) {
            return $this->im7Capability;
        }
        // Fallback to IM6
        if (!isset($this->im6Capability)) {
            $this->im6Capability = new Imagemagick6Capability($this->skipChecks);
        }
        if ($this->im6Capability->hasCapability()) {
            return $this->im6Capability;
        }
        // No IM detected
        return null;
    }

    public function getSystemStatusAsJSON()
    {
        $ffmpegCapability = new FfmpegCapability();
        $imagemagickCapability = $this->getAvailableImagemagickCapability();

        $systemStatus = [
            'ffmpeg' => [
                'isAvailable' => $ffmpegCapability->hasCapability(),
                'version' => $ffmpegCapability->getVersionString()
            ],
            'imagemagick' => [
                // Default values which willn be overriden if im is detected
                'isAvailable' =>  false,
                'version' => '',
                'shortVersion' => ''
            ]
        ];
        if ($imagemagickCapability !== null) {
            $systemStatus['imagemagick'] = [
                'isAvailable' => $imagemagickCapability->hasCapability(),
                'version' => $imagemagickCapability->getVersionString(),
                'shortVersion' => $imagemagickCapability->getShortVersion()
            ];
            foreach (self::$supportedFormats as $supportedFormat) {
                $format = $imagemagickCapability->getFormatByName($supportedFormat);
                if ($format === null) {
                    continue;
                }
                $systemStatus['imagemagick']['supportedFormats'][$supportedFormat] = [
                    'name' => $format->getName(),
                    'description' => $format->getDescription(),
                    'read' => $format->canRead(),
                    'write' => $format->canWrite(),
                    'fullSupport' => $format->hasFullSupport()
                ];
            }

        }
        return $systemStatus;
    }

    public function getFfmpegCapability()
    {
        return $this->ffmpegCapability;
    }
}
