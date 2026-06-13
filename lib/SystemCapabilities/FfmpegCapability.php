<?php

namespace OCA\AudioCoverPreview\SystemCapabilities;

class FfmpegCapability extends AbstractCapability
{
	public function __construct(bool $skipChecks = false)
	{
		$this->binary = 'ffmpeg';
		if ($skipChecks) {
			$this->isCapable = true;
			return;
		}
		$this->isCapable = $this->checkCapability();

	}

	public function getVersionString(): string
	{
		$output = shell_exec($this->binary . ' -version');
		if (!is_bool($output)) {
			$versionParts = explode(PHP_EOL, $output);
			if (\count($versionParts) > 0) {
				return $versionParts[0];
			}
		}
		return '';
	}

	protected function checkCapability(): bool
	{
		return $this->binaryExists();
	}
}
