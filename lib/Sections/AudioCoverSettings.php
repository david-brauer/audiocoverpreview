<?php
namespace OCA\AudioCoverPreview\Sections;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AudioCoverSettings implements IIconSection {
    private IL10N $l;
    private IURLGenerator $urlGenerator;

    public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
        $this->l = $l;
        $this->urlGenerator = $urlGenerator;
    }

    public function getIcon(): string {
        return $this->urlGenerator->imagePath('core', 'actions/more.svg');
    }

    public function getID(): string {
        return 'audiosettings';
    }

    public function getName(): string {
        return $this->l->t('Audio Cover Preview Settings');
    }

    public function getPriority(): int {
        return 98;
    }
}