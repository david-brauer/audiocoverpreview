<?php

namespace OCA\AudioCoverPreview\Settings;

use OCA\AudioCoverPreview\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\Util;

class AudioCoverSettings implements ISettings
{
    public function getForm(): TemplateResponse
    {
        Util::addScript(Application::APP_ID, Application::APP_ID . '-main');
        Util::addStyle(Application::APP_ID, Application::APP_ID . '-main');

        return new TemplateResponse('audiocoverpreview', 'Settings/Audiocover');
    }

    public function getSection(): string
    {
        return 'audiosettings';
    }

    public function getPriority(): int
    {
        return 10;
    }
}
