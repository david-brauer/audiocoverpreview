<?php

namespace OCA\AudioCoverPreview\Service;

use OCP\AppFramework\Services\IAppConfig;

class SettingsService
{
    private IAppConfig $config;

    public function __construct(
        IAppConfig $config,
    ) {
        $this->config = $config;
    }

    public function loadSettingsAndAllowedValues(): array
    {
        $settings = $this->loadSettings();
        return [
            'imageFormat' => [
                'allowedValues' => SystemInformationService::$supportedFormats,
                'selectedValue' => $settings['imageFormat']
            ],
            'skipChecks' => [
                'allowedValues' => [true, false],
                'selectedValue' => $settings['skipChecks']
            ],
        ];
    }

    public function loadSettings(): array
    {
        $imageFormatSetting = $this->config->getAppValueString('image_format', 'jpg');
        if (!in_array($imageFormatSetting, SystemInformationService::$supportedFormats)) {
            $imageFormatSetting = 'jpg';
        }

        $skipChecks = $this->config->getAppValueBool('skip_checks');
        return [
            'imageFormat' => $imageFormatSetting,
            'skipChecks' => $skipChecks
        ];
    }
}
