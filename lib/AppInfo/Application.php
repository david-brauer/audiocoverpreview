<?php

declare(strict_types=1);

namespace OCA\AudioCoverPreview\AppInfo;

use OCA\AudioCoverPreview\PreviewProviders\FLAC;
use OCA\AudioCoverPreview\PreviewProviders\M4A;
use OCA\AudioCoverPreview\PreviewProviders\OGG;
use OCA\AudioCoverPreview\PreviewProviders\MP3;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'audiocoverpreview';

	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerPreviewProvider(M4A::class , "/audio\/mp4/");
		$context->registerPreviewProvider(OGG::class , "/audio\/ogg/");
		$context->registerPreviewProvider(FLAC::class , "/audio\/flac/");
		$context->registerPreviewProvider(MP3::class , "/audio\/mpeg/");
	}

	public function boot(IBootContext $context): void {
	}
}
