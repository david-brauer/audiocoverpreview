<?php

declare(strict_types=1);

namespace OCA\AudioCoverPreview\Controller;

use OCA\AudioCoverPreview\Service\SettingsService;
use OCA\AudioCoverPreview\Service\SystemInformationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IRequest;

class SettingsController extends Controller
{
    private SettingsService $settingsService;
    private SystemInformationService $systemInfoService;
    private IAppConfig $config;

    public function __construct(
        string $appName,
        IRequest $request,
        IAppConfig $config,
        SettingsService $settingsService
    ) {
        $this->config = $config;
        $this->settingsService = $settingsService;
        $this->systemInfoService = new SystemInformationService(false);
        return parent::__construct($appName, $request);
    }

    #[FrontpageRoute(verb: 'POST', url: '/changeFormat')]
    public function changeFormat(string $value): JSONResponse
    {
        if (!in_array($value, SystemInformationService::$supportedFormats)) {
            $value = 'jpg';
        }
        $this->config->setAppValueString('image_format', $value);
        return new JSONResponse(['value' => $value]);
    }

    #[FrontpageRoute(verb: 'POST', url: '/changeSkipCheck')]
    public function changeSkipCheck(bool $value): JSONResponse
    {
        $this->config->setAppValueBool('skip_checks', $value);
        return new JSONResponse(['value' => $value]);
    }

    #[FrontpageRoute(verb: 'GET', url: '/getSettings')]
    #[NoCSRFRequired]
    public function getSettings(): JSONResponse
    {
        return new JSONResponse($this->settingsService->loadSettingsAndAllowedValues());

    }

    #[FrontpageRoute(verb: 'GET', url: '/getCapabilities')]
    #[NoCSRFRequired]
    public function getCapabilities(): JSONResponse
    {
        $json = $this->systemInfoService->getSystemStatusAsJSON();
        return new JSONResponse($json);

    }
}
