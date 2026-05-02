<?php
namespace OCA\AudioCoverPreview\Settings;

use OCA\AudioCoverPreview\Converters\ImagemagickConverter;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;

class AudioCoverSettings implements ISettings {
    private IL10N $l;
    private IAppConfig $config;

    public function __construct(IAppConfig $config, IL10N $l) {
        $this->config = $config;
        $this->l = $l;
    }

    public function getForm() : TemplateResponse {
        $imageFormatSetting = $this->config->getAppValueString('image_format', 'jpg');
        if(!in_array($imageFormatSetting,ImagemagickConverter::$supportedFormats)){
            $imageFormatSetting ='jpg';
        }

        $skipChecks = $this->config->getAppValueBool('skip_checks', false);

        $parameters = [
            'imageFormat' => $imageFormatSetting,
            'skipChecks' => $skipChecks
        ];
        return new TemplateResponse('audiocoverpreview', 'Settings/Audiocover', $parameters,'');
    }

    public function getSection() {
        return 'audiosettings';
    }

    public function getPriority() :int {
        return 10;
    }

}